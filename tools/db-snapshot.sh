#!/usr/bin/env bash
#
# Snapshot the WordPress database so content changes are reversible.
#
# WHY THIS EXISTS. Code lives in git, so a bad deploy is reverted by reverting a
# commit. Page records and post content live in the database, so a bad content
# change has no git history and no revert at all. Today every page on this site
# is created by wp-cli against the live database, which means the only copy of
# that content is the live database itself. Losing it loses the work.
#
# This runs nightly and also immediately before any content change, so the
# answer to "what did this page look like yesterday" is always a file on disk.
#
# The dump is written as www-data because that is the only identity mysqldump
# accepts for this database. The directory is 750 and owned by www-data so the
# dump is not world readable.
set -euo pipefail

WP_PATH="${WP_PATH:-/var/www/maulik-dev}"
SNAP_DIR="${SNAP_DIR:-/var/backups/maulik-dev-db}"
STAMP="$(date +%Y%m%d-%H%M%S)"
TARGET="${SNAP_DIR}/${STAMP}.sql"

mkdir -p "${SNAP_DIR}"
# mysqldump cannot open an output file itself: it is confined by AppArmor and
# gets permission denied on any path outside the database data directory.
# Exporting to stdout and letting this shell write the file sidesteps that
# entirely, because the write is then performed by the invoking user.
sudo -u www-data wp --allow-root --path="${WP_PATH}" db export - --quiet > "${TARGET}"

# A dump that is empty or truncated is worse than no dump, because it looks
# like a backup. Prove the file has content and that it parses as SQL.
local_bytes=$(wc -c < "${TARGET}")
if [ "${local_bytes}" -lt 10000 ]; then
	echo "FAIL: snapshot is only ${local_bytes} bytes, which cannot be a full dump" >&2
	exit 1
fi

tables=$(grep -cE '^CREATE TABLE' "${TARGET}" || true)
if [ "${tables}" -lt 10 ]; then
	echo "FAIL: snapshot has ${tables} CREATE TABLE statements, expected at least 10" >&2
	exit 1
fi

# Keep 14 days. An unbounded snapshot directory fills the disk, and a full disk
# takes the site down, which is the exact failure this script exists to prevent.
find "${SNAP_DIR}" -name '*.sql' -type f -mtime +14 -delete

echo "ok: ${TARGET} (${local_bytes} bytes, ${tables} tables)"
