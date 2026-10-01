#!/usr/bin/env bash
#
# Deploy the theme from the working repository into the WordPress install and
# activate it, so the live staging site reflects the working tree.
#
# WHY THIS IS A COPY AND NOT A SYMLINK
#
# A symlink from wp-content/themes/maulik-portfolio back to the repository
# renders a zero byte page. WordPress recognises the theme, wp_is_block_theme()
# returns true, and file_exists() passes on templates/index.html, so nothing
# errors and nothing is logged. The page simply comes back empty.
#
# A real copy of the same files renders correctly. The cause was not worth
# further time, and a copy has one genuine advantage over a symlink: Apache
# serves the theme files directly without a second filesystem hop, which is
# marginally faster and removes a whole class of permission and resolution
# surprises.
#
# The cost is that the two trees drift unless this script is run. So run it.
#
# USAGE
#   ./tools/deploy-theme.sh            deploy, activate, verify
#   ./tools/deploy-theme.sh --verify   verify only, deploy nothing
#
set -euo pipefail

REPO_DIR="${REPO_DIR:-/home/ubuntu/maulik-dev}"
WP_PATH="${WP_PATH:-/var/www/maulik-dev}"
THEME_SLUG="maulik-portfolio"
THEME_DEST="${WP_PATH}/wp-content/themes/${THEME_SLUG}"
SITE_URL="${SITE_URL:-https://maulik-dev.duckdns.org}"

log() { printf '\033[1;34m==>\033[0m %s\n' "$*"; }
die() { printf '\033[1;31mFAIL\033[0m %s\n' "$*" >&2; exit 1; }

# Pre-deploy gate for failure C: a theme callback pointing at a function that no
# longer exists. WordPress does not resolve add_action callbacks at registration
# time, it resolves them when the hook fires, so a dangling reference does not
# fail on deploy and does not fail on the pages that never reach that hook. It
# fires the first time the hook runs and is fatal there. That is exactly how a
# deleted maulik_portfolio_noindex took down wp_head and left a 200 with no body.
#
# Only string literals starting maulik_portfolio_ are checked: those are the
# theme's own callbacks, and those are the ones this gate can resolve. Core
# callbacks such as __return_true are deliberately out of scope.
#
# Reads the working tree, so it runs before anything is copied to the live theme.
gate_dangling_callbacks() {
	local report
	command -v python3 >/dev/null 2>&1 || die "python3 is required for the dangling callback gate, refusing to deploy"

	report=$(python3 - "${REPO_DIR}" <<'PY'
import pathlib, re, sys

root = pathlib.Path(sys.argv[1])
targets = [root / "functions.php"] + sorted((root / "inc").glob("*.php"))

# Only the two argument form with a maulik_portfolio_ string literal is matched.
# Anything else (arrays, variables, core callbacks) cannot be resolved statically
# and is left alone rather than guessed at.
call_re = re.compile(
    r"add_(?:action|filter)\s*\(\s*['\"](?P<hook>[^'\"]+)['\"]\s*,\s*"
    r"['\"](?P<cb>maulik_portfolio_[A-Za-z0-9_]+)['\"]"
)
declared = set()
dangling = []

for path in targets:
    if not path.is_file():
        continue
    src = path.read_text(encoding="utf-8", errors="replace")
    # Collect declarations from every file first, since a callback may be hooked
    # in one file and defined in another.
    declared |= set(re.findall(r"function\s+(maulik_portfolio_[A-Za-z0-9_]+)\s*\(", src))

for path in targets:
    if not path.is_file():
        continue
    src = path.read_text(encoding="utf-8", errors="replace")
    for m in call_re.finditer(src):
        cb = m.group("cb")
        if cb not in declared:
            line = src[: m.start()].count("\n") + 1
            rel = path.relative_to(root)
            dangling.append(f"{rel}:{line}: hook '{m.group('hook')}' -> '{cb}'")

print("\n".join(dangling))
sys.exit(1 if dangling else 0)
PY
	) && return 0

	# Print each offender on its own line so the cause is unambiguous.
	while IFS= read -r line; do
		[ -n "${line}" ] || continue
		die "dangling theme callback, refusing to deploy: ${line}. WordPress resolves this when the hook fires, so it fatals at runtime, not now."
	done <<< "${report}"
	return 0
}

verify() {
	local bytes handle palette tt5 tpl pat
	bytes=$(curl -sS --max-time 30 -o /tmp/deploy-verify.html -w '%{size_download}' "${SITE_URL}/")
	handle=$(grep -c 'maulik-portfolio-style' /tmp/deploy-verify.html || true)

	# A fatal partway through render still returns HTTP 200, and it still returns
	# a large body, because the <head> is already complete by the time it dies:
	# the recorded failure was 200 with about 65KB of content, a full <head>, and
	# no <body> whatsoever. The byte check below passes that without noticing.
	# The only thing that distinguishes a real render is the body itself.
	grep -q '<body' /tmp/deploy-verify.html \
		|| die "front page returned 200 with ${bytes} bytes but no <body. A PHP fatal during render produces a complete head and no body."
	grep -q '<main' /tmp/deploy-verify.html \
		|| die "front page has no <main landmark, so the template did not render the content block."
	palette=$(grep -ci '0a0a0b' /tmp/deploy-verify.html || true)
	tt5=$(grep -c 'twentytwentyfive\|canary' /tmp/deploy-verify.html || true)

	# Templates and patterns, read from Core rather than from disk. A file on
	# disk that Core does not recognise renders nothing, so counting files
	# proves nothing. This is the check that would have caught the stale pattern
	# cache on its first run instead of after a debugging session.
	tpl=$(wp --allow-root --path="${WP_PATH}" eval \
		'echo count( get_block_templates( array(), "wp_template" ) );' 2>/dev/null || echo -1)
	pat=$(wp --allow-root --path="${WP_PATH}" eval \
		'echo count( wp_get_theme()->get_block_patterns() );' 2>/dev/null || echo -1)

	printf '    bytes            : %s\n' "${bytes}"
	printf '    theme stylesheet : %s\n' "${handle}"
	printf '    palette present  : %s\n' "${palette}"
	printf '    other theme refs : %s\n' "${tt5}"
	printf '    templates in core: %s\n' "${tpl}"
	printf '    patterns in core : %s\n' "${pat}"

	# A zero byte body with a 200 is the exact failure this script exists to
	# catch, and it is invisible in a screenshot. Assert on the byte count.
	[ "${bytes}" -gt 1000 ] || die "front page served ${bytes} bytes. The theme is active but rendering empty."
	[ "${handle}" -ge 1 ]   || die "front page does not load ${THEME_SLUG}-style. Another theme is serving."
	[ "${tt5}" -eq 0 ]      || die "front page still references Twenty Twenty-Five."
	[ "${tpl}" -ge 2 ]      || die "Core recognises ${tpl} templates. Expected at least index and 404."
	# Zero patterns is the stale cache failure mode, so treat it as fatal rather
	# than advisory. The templates render fine without patterns, which is exactly
	# why it went unnoticed.
	[ "${pat}" -ge 1 ]      || die "Core recognises ${pat} patterns. If this is 0 the pattern cache is stale."

	# Static assets are checked separately because a broken docroot .htaccess 404s
	# every one of them while the HTML still renders perfectly. A .htaccess that
	# was missing the RewriteCond %{REQUEST_FILENAME} !-f and !-d lines sent the
	# front end into the WordPress rewrite for files that plainly existed, which
	# 404'd the four self hosted woff2 fonts, assets/css/theme.css, and even a core
	# script module. Nothing about the page load looks wrong, so this only fails
	# if the assets themselves are fetched. The core file is in the list on purpose:
	# it 404'd in the same incident and is the one that cannot be blamed on the
	# theme.
	for asset in \
		"/wp-content/themes/${THEME_SLUG}/assets/css/theme.css" \
		"/wp-content/themes/${THEME_SLUG}/assets/fonts/syne-latin-700-800.woff2" \
		"/wp-content/themes/${THEME_SLUG}/assets/fonts/plus-jakarta-sans-latin-400-600.woff2" \
		"/wp-content/themes/${THEME_SLUG}/assets/fonts/ibm-plex-mono-latin-400.woff2" \
		"/wp-content/themes/${THEME_SLUG}/assets/fonts/ibm-plex-mono-latin-600.woff2" \
		"/wp-includes/js/dist/script-modules/interactivity/index.min.js"
	do
		result=$(curl -sS --max-time 30 -o /dev/null -w '%{http_code} %{size_download}' "${SITE_URL}${asset}" 2>/dev/null) || result="000 0"
		code=${result%% *}
		size=${result##* }
		[ "${code}" = "200" ] || die "${asset} returned HTTP ${code}. A broken .htaccess 404s every static asset while the HTML still renders."
		[ "${size}" -gt 1000 ] || die "${asset} returned ${size} bytes, too small to be the real file."
		printf '    asset ok          : %s (%s bytes)\n' "${asset}" "${size}"
	done

	# wp-login.php was caught in a self redirect loop once, which locked everyone
	# out of the admin while every public page looked perfect. A public route
	# check cannot see that, so the login endpoint is fetched directly. Not
	# following redirects: a loop never settles, and the exit code alone would
	# not distinguish a loop from a missing file.
	code=$(curl -sS --max-time 30 -o /dev/null -w '%{http_code}' "${SITE_URL}/wp-login.php" 2>/dev/null) || code=000
	[ "${code}" = "200" ] || die "/wp-login.php returned HTTP ${code}, expected 200. The admin login is unreachable."

	log "verified: the live site is serving ${THEME_SLUG}"
}

if [ "${1:-}" = "--verify" ]; then
	log "verifying ${SITE_URL} only"
	verify
	exit 0
fi

log "building CSS"
cd "${REPO_DIR}"
npm run tokens >/dev/null
npm run build:css >/dev/null

log "gating before deploy"
vendor/bin/phpcs --standard=phpcs.xml.dist . >/dev/null || die "phpcs failed, refusing to deploy"
composer validate --strict >/dev/null 2>&1 || die "composer.json is invalid, refusing to deploy"

if grep -rn --include='*.php' -E "wp_enqueue_style\([^)]*\.scss" --exclude-dir=vendor --exclude-dir=node_modules . >/dev/null; then
	die "a .scss file is enqueued from PHP, refusing to deploy"
fi

if git grep -qI -P '[\x{2014}\x{2013}]' -- . ':(exclude)node_modules' ':(exclude)vendor' ':(exclude)package-lock.json'; then
	die "an em dash or en dash is present, refusing to deploy"
fi

gate_dangling_callbacks

log "copying to ${THEME_DEST}"
sudo rm -rf "${THEME_DEST}"
sudo cp -r "${REPO_DIR}" "${THEME_DEST}"
# The repository carries its own dev dependencies; they are not shipped.
sudo rm -rf "${THEME_DEST}/node_modules" "${THEME_DEST}/vendor" "${THEME_DEST}/.git"
sudo chown -R www-data:www-data "${THEME_DEST}"

log "activating"
wp --allow-root --path="${WP_PATH}" theme activate "${THEME_SLUG}" >/dev/null || die "activation failed"

# Core caches a theme's block patterns in a transient keyed by theme Version, and
# only invalidates that cache when the Version changes or the site is in theme
# development mode. Copying new files into patterns/ therefore does not register
# them: core keeps serving the array it cached the previous time, which can be
# the empty one captured before the first pattern existed.
#
# This cost real debugging time and is invisible from the front end, because the
# templates do not depend on patterns. A theme with zero registered patterns
# still renders every page correctly.
wp --allow-root --path="${WP_PATH}" transient delete --all >/dev/null 2>&1 || true
wp --allow-root --path="${WP_PATH}" cache flush >/dev/null 2>&1 || true

log "verifying the live site"
sleep 2
verify
log "done"
