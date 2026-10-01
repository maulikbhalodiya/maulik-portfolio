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

verify() {
	local bytes handle palette tt5 tpl pat
	bytes=$(curl -sS --max-time 30 -o /tmp/deploy-verify.html -w '%{size_download}' "${SITE_URL}/")
	handle=$(grep -c 'maulik-portfolio-style' /tmp/deploy-verify.html || true)
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
