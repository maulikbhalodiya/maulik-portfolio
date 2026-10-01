#!/usr/bin/env bash
#
# Unattended health probe for the live staging site.
#
# WHY THIS IS A SEPARATE SCRIPT
#
# deploy-theme.sh only runs when a human is already looking at the terminal, so
# it cannot notice anything that breaks after it exits. The site here has broken
# three separate ways that all rendered an HTTP 200, which means every check that
# existed before this one was blind to them:
#
#   A. A PHP fatal partway through render returned 200 with about 65KB of
#      content, a complete <head>, and no <body> at all. Byte count gates
#      passed it because 65KB is a lot of bytes. Assert on <body and <main>
#      presence, never on size alone.
#
#   B. A docroot .htaccess missing the RewriteCond %{REQUEST_FILENAME} !-f and
#      !-d lines 404'd every static asset: the four self hosted woff2 fonts,
#      assets/css/theme.css, and even a WordPress core script module. The HTML
#      still rendered fine, so a page load check proved nothing. Fetch the real
#      files and require real bytes back.
#
#   C. A dangling add_action pointing at a deleted function fataled inside
#      wp_head. That is a static condition, so deploy-theme.sh gates it before a
#      deploy. This script only reports fatals it can see after the fact.
#
# The owner cannot see the server, so this has to run on a timer and print
# nothing at all when the site is fine. --quiet exists for that.
#
# USAGE
#   ./tools/healthcheck.sh                    verbose, exit 1 on any failure
#   ./tools/healthcheck.sh --quiet            print only on failure, for cron
#   ./tools/healthcheck.sh --once-per-interval 600
#
# Exit 0 healthy, exit 1 if any check failed.
set -euo pipefail

SITE_URL="${SITE_URL:-https://maulik-dev.duckdns.org}"
WP_PATH="${WP_PATH:-/var/www/maulik-dev}"
THEME_SLUG="${THEME_SLUG:-maulik-portfolio}"
HEALTH_STATE="${HEALTH_STATE:-/tmp/maulik-health.state}"
ERROR_LOG="${ERROR_LOG:-/var/log/apache2/maulik-dev-error.log}"

# A 200 with 65KB of head and no body is the failure in case A, so the minimum
# size here is deliberately low and exists only to catch a truly empty reply.
MIN_DOC_BYTES=1000
# A static asset under this size is a stub or an error page, not a real font.
MIN_ASSET_BYTES=1000

QUIET=0
INTERVAL=0
FAILURES=0
WORKDIR=""

say()  { [ "${QUIET}" -eq 1 ] || printf '%s\n' "$*"; }
fail() { FAILURES=$(( FAILURES + 1 )); printf 'FAIL: %s\n' "$*" >&2; }

cleanup() { [ -n "${WORKDIR}" ] && rm -rf "${WORKDIR}"; return 0; }
trap cleanup EXIT

# grep -c counts matching lines, not matches. These documents are pretty printed
# but a whole template can sit on one line, so counting lines would report a
# second <h1> as one. Count occurrences instead.
count() { grep -o -- "$2" "$1" 2>/dev/null | wc -l || true; }

# Everything after </head> is what the theme actually rendered. Used for the
# dash check below.
body_of() { awk '/<\/head>/{f=1;next} f' "$1"; }

while [ $# -gt 0 ]; do
	case "$1" in
		--quiet) QUIET=1 ;;
		--once-per-interval)
			shift || die
			INTERVAL="${1:-0}"
			;;
		-h|--help)
			sed -n '2,30p' "$0"
			exit 0
			;;
		*) printf 'unknown option: %s\n' "$1" >&2; exit 2 ;;
	esac
	shift
done

# Cron plus a manual run means the same break gets reported every few minutes.
# When an interval is requested, skip entirely if the last run was recent.
if [ "${INTERVAL}" -gt 0 ]; then
	STAMP="${HEALTH_STATE}.stamp"
	NOW=$(date +%s)
	if [ -f "${STAMP}" ]; then
		LAST=$(cat "${STAMP}" 2>/dev/null || echo 0)
		case "${LAST}" in
			''|*[!0-9]*) LAST=0 ;;
		esac
		if [ $(( NOW - LAST )) -lt "${INTERVAL}" ]; then
			exit 0
		fi
	fi
	printf '%s\n' "${NOW}" > "${STAMP}"
fi

WORKDIR=$(mktemp -d)

say "healthcheck: ${SITE_URL}"

# ---------------------------------------------------------------------------
# Docroot sanity, read only. Never written to from here.
# ---------------------------------------------------------------------------
if [ ! -d "${WP_PATH}" ]; then
	fail "docroot ${WP_PATH} does not exist"
elif [ ! -d "${WP_PATH}/wp-content/themes/${THEME_SLUG}" ]; then
	fail "theme ${THEME_SLUG} is not installed at ${WP_PATH}/wp-content/themes"
else
	say "    docroot           : ${WP_PATH} (read only)"
fi

# ---------------------------------------------------------------------------
# Routes. Status is not enough: a 200 with no body is failure A, so every route
# is also required to contain a <body, a <main, and exactly one <h1>. Two <h1>
# means a layout regression that is invisible in a screenshot.
# ---------------------------------------------------------------------------
ROUTES="/ /about/ /resume/ /projects/ /rankkernel/ /contact/"

for route in ${ROUTES}; do
	# Pre-create the target: on a connection failure curl writes no file at all,
	# and the redirect in wc would then error to stderr before the real FAIL line.
	page="${WORKDIR}/page.html"
	: > "${page}"
	# curl can print a partial code and still exit non zero, so the fallback has
	# to overwrite rather than append, otherwise the code reads back as 000000.
	code=$(curl -sS --max-time 30 -o "${page}" -w '%{http_code}' "${SITE_URL}${route}" 2>/dev/null) || code=000
	bytes=$(wc -c < "${page}" | tr -d ' ')

	if [ "${code}" != "200" ]; then
		fail "${route} returned HTTP ${code}, expected 200"
		continue
	fi
	if [ "${bytes}" -lt "${MIN_DOC_BYTES}" ]; then
		fail "${route} returned only ${bytes} bytes"
		continue
	fi
	if [ "$(count "${page}" '<body')" -lt 1 ]; then
		fail "${route} has no <body. A fatal during render returns a complete head and a 200 with no body."
	fi
	if [ "$(count "${page}" '<main')" -lt 1 ]; then
		fail "${route} has no <main landmark"
	fi
	h1=$(count "${page}" '<h1')
	if [ "${h1}" -ne 1 ]; then
		fail "${route} has ${h1} <h1> elements, expected exactly 1"
	fi
	say "    ok                : ${route} (200, ${bytes} bytes, ${h1} h1)"
done

# A 404 that renders no body is still a broken 404, so the body check applies
# here too. Only the status expectation differs.
page="${WORKDIR}/404.html"
: > "${page}"
code=$(curl -sS --max-time 30 -o "${page}" -w '%{http_code}' "${SITE_URL}/nope/" 2>/dev/null) || code=000
if [ "${code}" != "404" ]; then
	fail "/nope/ returned HTTP ${code}, expected 404. Unknown routes are serving content."
elif [ "$(count "${page}" '<body')" -lt 1 ]; then
	fail "/nope/ returns 404 with no <body, so the 404 template is broken"
else
	say "    ok                : /nope/ (404 with a rendered body)"
fi

# ---------------------------------------------------------------------------
# Static assets, failure B. A broken .htaccess 404s every one of these while the
# HTML renders perfectly, so the page checks above cannot see it. The core
# script module is included deliberately: it is the asset with the shortest path
# back to WordPress itself, and it 404'd in the same incident.
# ---------------------------------------------------------------------------
ASSETS="
/wp-content/themes/${THEME_SLUG}/assets/css/theme.css
/wp-content/themes/${THEME_SLUG}/assets/fonts/syne-latin-700-800.woff2
/wp-content/themes/${THEME_SLUG}/assets/fonts/plus-jakarta-sans-latin-400-600.woff2
/wp-content/themes/${THEME_SLUG}/assets/fonts/ibm-plex-mono-latin-400.woff2
/wp-content/themes/${THEME_SLUG}/assets/fonts/ibm-plex-mono-latin-600.woff2
/wp-includes/js/dist/script-modules/interactivity/index.min.js
"

for asset in ${ASSETS}; do
	result=$(curl -sS --max-time 30 -o /dev/null -w '%{http_code} %{size_download}' "${SITE_URL}${asset}" 2>/dev/null) || result="000 0"
	acode=$(printf '%s' "${result}" | cut -d' ' -f1)
	abytes=$(printf '%s' "${result}" | cut -d' ' -f2)
	case "${abytes}" in
		''|*[!0-9]*) abytes=0 ;;
	esac

	if [ "${acode}" != "200" ]; then
		fail "${asset} returned HTTP ${acode}. A broken .htaccess 404s every static asset while the HTML still renders."
	elif [ "${abytes}" -le "${MIN_ASSET_BYTES}" ]; then
		fail "${asset} returned ${abytes} bytes, which is too small to be the real file"
	else
		say "    ok                : ${asset} (200, ${abytes} bytes)"
	fi
done

# ---------------------------------------------------------------------------
# New PHP fatals in the Apache error log. This is the only check that can see a
# fatal which happened outside a request we just made.
#
# The log is already full of older fatals (37 of them, last one at 10:32 on the
# day this was written), so reporting from the top of the file would fail on the
# first run forever and mean nothing. Track a line count instead and only report
# what appeared since the previous run.
#
# On the very first run, and after a rotation, there is no baseline to compare
# against. Rebaseline and say so rather than reporting the whole history. Missing
# log is not a failure: the site can be perfectly healthy with logging off.
# ---------------------------------------------------------------------------
if [ -r "${ERROR_LOG}" ]; then
	total=$(wc -l < "${ERROR_LOG}" | tr -d ' ')
	last=0
	if [ -f "${HEALTH_STATE}" ]; then
		read -r last _ < "${HEALTH_STATE}" 2>/dev/null || last=0
		case "${last}" in
			''|*[!0-9]*) last=0 ;;
		esac
	fi

	if [ "${last}" -eq 0 ]; then
		say "    log baseline      : set to ${total} lines (${ERROR_LOG})"
	elif [ "${total}" -lt "${last}" ]; then
		# Fewer lines than we recorded means logrotate moved the file. The old
		# offset no longer means anything, so start again from the new file.
		say "    log rotated       : rebaselining at ${total} lines"
		last=0
	else
		new=$(tail -n "+$(( last + 1 ))" "${ERROR_LOG}" | grep 'PHP Fatal' || true)
		if [ -n "${new}" ]; then
			while IFS= read -r line; do
				[ -n "${line}" ] || continue
				ts="${line%%]*}"
				msg="${line#*PHP Fatal}"
				fail "new PHP Fatal at ${ts}: PHP Fatal${msg}"
			done <<< "${new}"
		else
			say "    php fatals        : none since last check"
		fi
	fi

	printf '%s %s\n' "${total}" "$(date +%s)" > "${HEALTH_STATE}"
else
	say "    error log         : ${ERROR_LOG} is absent, skipping the fatal scan"
fi

# ---------------------------------------------------------------------------
# Content requirements, all read only. These are hard project rules, so a
# regression should page someone rather than sit there looking fine.
# ---------------------------------------------------------------------------
home="${WORKDIR}/home.html"
curl -sS --max-time 30 -o "${home}" "${SITE_URL}/" 2>/dev/null || true

if [ -s "${home}" ]; then
	mails=$(grep -oE '[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}' "${home}" | sort -u | tr '\n' ' ' || true)
	if [ -n "${mails}" ]; then
		fail "homepage exposes raw email addresses: ${mails}"
	fi

	mailto=$(count "${home}" 'mailto:')
	if [ "${mailto}" -ne 0 ]; then
		fail "homepage has ${mailto} mailto: links. Contact details must not be exposed."
	fi

	# Scoped to the body on purpose. The rendered <head> carries exactly four en
	# dashes, all of them the SEO plugin's configurable title separator: it puts
	# the separator between the page name and the site name in <title>, og:title,
	# twitter:title and the schema JSON, so "Home - Maulik Dev" becomes one dash
	# in each of the four. That is a plugin setting read from the database, not
	# theme source, and the theme has zero dashes. Asserting on the whole document
	# would fail forever on a perfectly healthy site and train us to ignore it, so
	# the check covers only what the theme rendered.
	dashes=$(body_of "${home}" | grep -o -e $'\u2014' -e $'\u2013' | wc -l || true)
	if [ "${dashes}" -ne 0 ]; then
		fail "homepage body has ${dashes} em or en dash characters"
	fi

	say "    content rules     : no emails, no mailto, no dashes in the body"
else
	fail "could not fetch the homepage for the content checks"
fi

if [ "${FAILURES}" -eq 0 ]; then
	say "healthy: ${SITE_URL} passed every check"
	exit 0
fi

printf '%d check(s) failed\n' "${FAILURES}" >&2
exit 1