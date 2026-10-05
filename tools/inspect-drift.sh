#!/usr/bin/env bash
#
# Read-only pre-deploy drift inspector.
#
# WHAT THIS IS FOR
#
# tools/deploy-theme.sh does `sudo rm -rf` on the live theme and then copies the
# repository over it. Whatever is in the repository wins, unconditionally, and
# the previous live copy is destroyed before anything is read from it. That is
# fine when the repository is the single source of truth. It is a data loss
# event when somebody edited the live theme directly, or when a previous deploy
# shipped a build that was never committed.
#
# This script answers one question, read only: does the live theme hold any
# work that is not in the repository working tree, and therefore not in git at
# all? Nothing is written to the live theme, to the database, or to any branch.
#
# MODES
#
#   (default)              compare live against the repo working tree, HEAD and
#                          origin/main. Exits 1 if any live file diverges, so it
#                          can gate a deploy.
#   --json                 same findings as JSON on stdout, human noise on stderr.
#   --save-baseline FILE   write the current live manifest to FILE.
#   --baseline FILE        compare live against a saved manifest, not the repo,
#                          so change over time is detectable.
#
# EXIT CODES
#
#   0  live matches the comparison source
#   1  divergence found
#   2  usage or environment error (missing repo, unreadable live, bad argument)
#
# USAGE
#   ./tools/inspect-drift.sh
#   ./tools/inspect-drift.sh --json | python3 -m json.tool
#   ./tools/inspect-drift.sh --save-baseline /tmp/live.txt
#   ./tools/inspect-drift.sh --baseline /tmp/live.txt
#
set -uo pipefail

REPO_DIR="${REPO_DIR:-/home/ubuntu/maulik-dev}"
WP_PATH="${WP_PATH:-/var/www/maulik-dev}"
THEME_SLUG="maulik-portfolio"
THEME_DEST="${WP_PATH}/wp-content/themes/${THEME_SLUG}"
REF_HEAD="HEAD"
REF_MAIN="origin/main"
WORKTREE_GLOB="/tmp/opencode/wt-*"

# Directories that exist for developer convenience only. They are reported, not
# compared: 54000 dependency files in production is itself a finding, but
# comparing them produces noise that hides the real rows.
SKIP_DIRS=(node_modules vendor .git)

MODE="repo"
JSON=0
BASELINE=""
SAVE_BASELINE=""

usage() {
	sed -n '2,40p' "$0" | sed 's/^# \{0,1\}//'
}

note() { printf '%s\n' "$*" >&2; }

json_mode_off=0
out() {
	if [ "${JSON}" -eq 1 ]; then
		return 0
	fi
	printf '%s\n' "$*"
}

fatal() {
	note "inspect-drift: $*"
	exit 2
}

while [ "$#" -gt 0 ]; do
	case "$1" in
		--json) JSON=1 ;;
		--baseline)
			[ "$#" -ge 2 ] || fatal "--baseline needs a file argument"
			BASELINE="$2"
			shift
			;;
		--baseline=*)
			BASELINE="${1#*=}"
			;;
		--save-baseline)
			[ "$#" -ge 2 ] || fatal "--save-baseline needs a file argument"
			SAVE_BASELINE="$2"
			shift
			;;
		--save-baseline=*)
			SAVE_BASELINE="${1#*=}"
			;;
		-h|--help)
			usage
			exit 0
			;;
		*) fatal "unknown argument: $1" ;;
	esac
	shift
done

[ -n "${SAVE_BASELINE}" ] && MODE="save"
[ -n "${BASELINE}" ] && MODE="baseline"
[ -n "${SAVE_BASELINE}" ] && [ -n "${BASELINE}" ] && fatal "--save-baseline and --baseline are mutually exclusive"

# ---------------------------------------------------------------- environment

[ -d "${REPO_DIR}" ] || fatal "repo not found: ${REPO_DIR}"
command -v git >/dev/null 2>&1 || fatal "git is required"
command -v md5sum >/dev/null 2>&1 || fatal "md5sum is required"

cd "${REPO_DIR}" || fatal "cannot enter ${REPO_DIR}"

branch=$(git rev-parse --abbrev-ref HEAD 2>/dev/null) || fatal "not a git repository: ${REPO_DIR}"
head_commit=$(git rev-parse HEAD 2>/dev/null) || fatal "cannot resolve HEAD"

if git rev-parse --verify --quiet "${REF_MAIN}" >/dev/null 2>&1; then
	main_commit=$(git rev-parse "${REF_MAIN}")
else
	main_commit=""
	note "inspect-drift: ${REF_MAIN} is not available locally, comparing against local main instead"
	if git rev-parse --verify --quiet main >/dev/null 2>&1; then
		main_commit=$(git rev-parse main)
		REF_MAIN="main"
	fi
fi

live_exists=0
if sudo -n test -d "${THEME_DEST}" 2>/dev/null; then
	live_exists=1
elif [ -d "${THEME_DEST}" ]; then
	live_exists=1
fi

# Reading the live tree needs sudo. Without a working passwordless sudo the
# comparison cannot be done honestly, so say so instead of reporting a clean run.
if [ "${live_exists}" -eq 1 ]; then
	if [ -r "${THEME_DEST}" ]; then
		SUDO=""
	else
		sudo -n true 2>/dev/null || fatal "live theme exists at ${THEME_DEST} but needs sudo and 'sudo -n' does not work. Refusing to report a clean run without reading live."
		SUDO="sudo -n"
	fi
else
	SUDO=""
fi

# ---------------------------------------------------------------- helpers

md5_of() { md5sum -- "$1" 2>/dev/null | cut -d' ' -f1; }
size_of() { stat -c %s -- "$1" 2>/dev/null || echo 0; }
json_escape() {
	local s=${1-}
	s=${s//\\/\\\\}
	s=${s//\"/\\\"}
	s=${s//$'\t'/\\t}
	s=${s//$'\r'/\\r}
	s=${s//$'\n'/\\n}
	printf '%s' "${s}"
}

# Path lists of a tree, newline separated, one relative path per line, with the
# skip directories pruned. Symlinks are never followed: find runs with -P (the
# default) and only regular files are collected.
scan_tree() {
	local root="$1" prune
	prune=$(printf ',%s' "${SKIP_DIRS[@]}")
	(
		cd "${root}" 2>/dev/null || exit 1
		find . -mindepth 1 \
			\( -name node_modules -o -name vendor -o -name .git \) -type d -prune -o \
			-type f -print | sed 's|^\./||'
	)
}

# Count files under the skip directories, for the report.
skip_dir_report() {
	local root="$1" name n
	for name in "${SKIP_DIRS[@]}"; do
		n=0
		if [ "${root}" = "${REPO_DIR}" ]; then
			[ -d "${root}/${name}" ] && n=$(find "${root}/${name}" -type f 2>/dev/null | wc -l)
		elif [ "${live_exists}" -eq 1 ]; then
			n=$(${SUDO} find "${root}/${name}" -type f 2>/dev/null | wc -l)
		fi
		printf '%s %s\n' "${name}" "${n}"
	done
}

# path -> md5 map for a git ref, via blob content rather than the index hash,
# so it is directly comparable to md5sum of a file on disk.
declare -A REF_MD5=()
declare -A REF_SIZE=()
build_ref_map() {
	local ref="$1" oid path rest
	[ -n "${ref}" ] || return 0
	git rev-parse --verify --quiet "${ref}^{commit}" >/dev/null 2>&1 || return 1
	while IFS=' ' read -r oid rest; do
		[ -n "${oid}" ] || continue
		path=${rest# }
		REF_MD5["${ref}|${path}"]=$(git cat-file blob "${oid}" 2>/dev/null | md5sum | cut -d' ' -f1)
		REF_SIZE["${ref}|${path}"]=$(git cat-file -s "${oid}" 2>/dev/null || echo 0)
	done < <(git ls-tree -r -z --format='%(objectname) %(path)' "${ref}" 2>/dev/null | tr '\0' '\n')
	return 0
}

build_ref_map "${REF_HEAD}" || fatal "cannot read ${REF_HEAD}"
[ -n "${main_commit}" ] && build_ref_map "${REF_MAIN}"

# Every path tracked by any local or remote branch. A live file outside this set
# exists in no branch, which is the case that matters most: there is exactly one
# copy of it on the whole machine.
declare -A ANY_BRANCH=()
while IFS= read -r ref; do
	[ -n "${ref}" ] || continue
	while IFS= read -r p; do
		[ -n "${p}" ] || continue
		ANY_BRANCH["${p}"]=1
	done < <(git ls-tree -r --name-only "${ref}" 2>/dev/null)
done < <(git for-each-ref --format='%(refname)' refs/heads refs/remotes)

# -------------------------------------------------------------- live scan

declare -a LIVE_PATHS=()
declare -A LIVE_SET=()

if [ "${live_exists}" -eq 1 ]; then
	while IFS= read -r p; do
		[ -n "${p}" ] || continue
		LIVE_PATHS+=("${p}")
		LIVE_SET["${p}"]=1
	done < <(scan_tree "${THEME_DEST}")
fi

live_symlinks=0
if [ "${live_exists}" -eq 1 ]; then
	live_symlinks=$(${SUDO} find "${THEME_DEST}" -mindepth 1 -type l 2>/dev/null | wc -l)
fi

# ---------------------------------------------------------------- save mode

if [ "${MODE}" = "save" ]; then
	[ "${live_exists}" -eq 1 ] || fatal "cannot save a baseline, live theme does not exist: ${THEME_DEST}"
	tmp="${SAVE_BASELINE}.inspect-tmp.$$"
	: >"${tmp}" || fatal "cannot write ${SAVE_BASELINE}"
	# Manifest format is "md5 size path", one record per line, so it is stable and
	# diffable. It is not md5sum format: the size is needed for the delta column.
	(
		cd "${THEME_DEST}" || exit 1
		while IFS= read -r -d '' f; do
			printf '%s %s %s\n' "$(md5sum -- "$f" | cut -d' ' -f1)" "$(stat -c %s -- "$f")" "${f#./}"
		done < <(find . -mindepth 1 \
			\( -name node_modules -o -name vendor -o -name .git \) -type d -prune -o \
			-type f -print0)
	) >"${tmp}" || { rm -f "${tmp}"; fatal "cannot read the live tree for a baseline"; }
	mv -- "${tmp}" "${SAVE_BASELINE}" || { rm -f "${tmp}"; fatal "cannot write ${SAVE_BASELINE}"; }
	if [ "${JSON}" -eq 1 ]; then
		printf '{"tool":"inspect-drift","mode":"save-baseline","baseline":"%s","files":%d,"live":"%s","repo":"%s","branch":"%s","head":"%s","divergence":0}\n' \
			"$(json_escape "${SAVE_BASELINE}")" "${#LIVE_PATHS[@]}" \
			"$(json_escape "${THEME_DEST}")" "$(json_escape "${REPO_DIR}")" \
			"$(json_escape "${branch}")" "$(json_escape "${head_commit}")"
	fi
	note "wrote ${#LIVE_PATHS[@]} live files to ${SAVE_BASELINE}"
	exit 0
fi

# ------------------------------------------------------------ baseline mode

declare -A BASE_MD5=()
declare -A BASE_SIZE=()
if [ "${MODE}" = "baseline" ]; then
	[ -f "${BASELINE}" ] || fatal "baseline file not found: ${BASELINE}"
	# Read into three named fields rather than word splitting, so a path with a
	# space in it survives the round trip.
	while read -r base_md5 base_size base_path || [ -n "${base_md5:-}" ]; do
		[ -n "${base_path:-}" ] || continue
		BASE_MD5["${base_path}"]="${base_md5}"
		BASE_SIZE["${base_path}"]="${base_size}"
	done <"${BASELINE}"
fi

# -------------------------------------------------------------- repo scan

declare -A REPO_MD5=()
declare -A REPO_SIZE=()
if [ "${MODE}" = "repo" ]; then
	while IFS= read -r p; do
		[ -n "${p}" ] || continue
		case "${p}" in
			.git/*|node_modules/*|vendor/*) continue ;;
		esac
		REPO_MD5["${p}"]=$(md5_of "${REPO_DIR}/${p}")
		REPO_SIZE["${p}"]=$(size_of "${REPO_DIR}/${p}")
	done < <(scan_tree "${REPO_DIR}")
fi

# ---------------------------------------------------------------- worktrees

worktrees=()
for d in ${WORKTREE_GLOB}; do
	[ -d "$d" ] && worktrees+=("$d")
done

# ---------------------------------------------------------------- compare

n_identical=0
n_divergent=0
n_live_only=0
n_repo_only=0
n_no_branch=0
n_symlink_note=0

declare -a ROW_PATH=() ROW_LIVE=() ROW_OTHER=() ROW_HEAD=() ROW_MAIN=() ROW_DELTA=() ROW_KIND=() ROW_NOBRANCH=()

# path, worktree, md5, matches_live, git_state
declare -a WT_PATH=() WT_DIR=() WT_MD5=() WT_MATCH=() WT_STATE=()

add_row() {
	ROW_PATH+=("$1"); ROW_LIVE+=("$2"); ROW_OTHER+=("$3"); ROW_HEAD+=("$4")
	ROW_MAIN+=("$5"); ROW_DELTA+=("$6"); ROW_KIND+=("$7"); ROW_NOBRANCH+=("$8")
}

record_worktree_copies() {
	local rel="$1" live_md5="$2" wt f m st
	for wt in ${worktrees[@]+"${worktrees[@]}"}; do
		f="${wt}/${rel}"
		if [ -f "$f" ] && [ ! -L "$f" ]; then
			m=$(md5_of "$f")
			if [ "${m}" = "${live_md5}" ]; then
				WT_MATCH+=(1)
			else
				WT_MATCH+=(0)
			fi
			st=$(git -C "${wt}" status --porcelain -- "${rel}" 2>/dev/null | head -1)
			if [ -z "${st}" ]; then
				st="clean"
			else
				st="${st:0:2}"
			fi
			WT_PATH+=("${rel}"); WT_DIR+=("${wt}"); WT_MD5+=("${m}"); WT_STATE+=("${st}")
		fi
	done
}

compare_one() {
	local rel="$1"
	local live_md5 live_size other_md5 other_size head_md5 main_md5 delta kind nobranch
	live_md5=$( ${SUDO} md5sum -- "${THEME_DEST}/${rel}" 2>/dev/null | cut -d' ' -f1 )
	[ -n "${live_md5}" ] || return 0
	live_size=$( ${SUDO} stat -c %s -- "${THEME_DEST}/${rel}" 2>/dev/null || echo 0 )

	head_md5="${REF_MD5[${REF_HEAD}|${rel}]:-}"
	main_md5=""; [ -n "${main_commit}" ] && main_md5="${REF_MD5[${REF_MAIN}|${rel}]:-}"
	nobranch=0
	[ -n "${ANY_BRANCH[${rel}]:-}" ] || nobranch=1

	if [ "${MODE}" = "baseline" ]; then
		other_md5="${BASE_MD5[${rel}]:-}"
		other_size="${BASE_SIZE[${rel}]:-}"
		if [ -z "${other_md5}" ]; then
			delta="new"
			kind="live-only"
		elif [ "${other_md5}" = "${live_md5}" ]; then
			n_identical=$((n_identical + 1))
			return 0
		else
			delta=$(( live_size - other_size ))
			kind="changed"
		fi
	else
		other_md5="${REPO_MD5[${rel}]:-}"
		other_size="${REPO_SIZE[${rel}]:-}"
		if [ -z "${other_md5}" ]; then
			delta="n/a"
			kind="live-only"
		elif [ "${other_md5}" = "${live_md5}" ]; then
			n_identical=$((n_identical + 1))
			return 0
		else
			delta=$(( live_size - other_size ))
			kind="differs"
		fi
	fi

	# Row columns: path, live, other side, HEAD, main, delta, kind, no-branch.
	if [ "${MODE}" = "baseline" ]; then
		# The other side is the baseline, so the git columns only inform.
		other_md5="${other_md5:--}"
	else
		other_md5="${other_md5:--}"
	fi
	n_divergent=$((n_divergent + 1))
	case "${kind}" in
		live-only) n_live_only=$((n_live_only + 1)) ;;
	esac
	[ "${nobranch}" -eq 1 ] && n_no_branch=$((n_no_branch + 1))
	add_row "${rel}" "${live_md5}" "${other_md5}" "${head_md5:--}" "${main_md5:--}" "${delta}" "${kind}" "${nobranch}"

	if [ "${kind}" = "live-only" ] || [ "${nobranch}" -eq 1 ]; then
		record_worktree_copies "${rel}" "${live_md5}"
	fi
}

if [ "${live_exists}" -eq 0 ]; then
	note "inspect-drift: live theme does not exist yet at ${THEME_DEST}, nothing to compare"
	if [ "${JSON}" -eq 1 ]; then
		printf '{"tool":"inspect-drift","mode":"%s","live":"%s","repo":"%s","branch":"%s","head":"%s","live_exists":false,"summary":{"files_compared":0,"identical":0,"divergent":0,"live_only":0,"repo_only":0,"live_not_in_any_branch":0},"divergent_files":[],"skipped_dirs":[],"symlinks":0,"divergence":0}\n' \
			"${MODE}" "$(json_escape "${THEME_DEST}")" "$(json_escape "${REPO_DIR}")" \
			"$(json_escape "${branch}")" "$(json_escape "${head_commit}")"
	fi
	exit 0
fi

for rel in ${LIVE_PATHS[@]+"${LIVE_PATHS[@]}"}; do
	compare_one "${rel}"
done

# Files the repo holds and live does not. Not a gate: a deploy legitimately adds
# them. Reported so the two directions of drift are both visible.
if [ "${MODE}" = "repo" ]; then
	# Sorted, because an associative array iterates in hash order and an
	# unsorted report is unreadable when there are many rows.
	while IFS= read -r rel; do
		[ -n "${rel}" ] || continue
		[ -n "${LIVE_SET[${rel}]:-}" ] && continue
		n_repo_only=$((n_repo_only + 1))
		add_row "${rel}" "-" "${REPO_MD5[${rel}]}" \
			"${REF_MD5[${REF_HEAD}|${rel}]:--}" "${REF_MD5[${REF_MAIN}|${rel}]:--}" \
			"$(( ${REPO_SIZE[${rel}]} - 0 ))" "repo-only" "0"
	done < <(printf '%s\n' "${!REPO_MD5[@]}" | sort)
fi

# ------------------------------------------------------------------ output

skip_summary=""
for name in "${SKIP_DIRS[@]}"; do
	n=0
	if [ "${MODE}" = "repo" ] && [ -d "${REPO_DIR}/${name}" ]; then
		n=$(find "${REPO_DIR}/${name}" -type f 2>/dev/null | wc -l)
	elif [ -d "${THEME_DEST}/${name}" ]; then
		n=$(${SUDO} find "${THEME_DEST}/${name}" -type f 2>/dev/null | wc -l)
	fi
	skip_summary+="${skip_summary:+;}${name}:${n}"
done

if [ "${JSON}" -eq 1 ]; then
	printf '{\n'
	printf '  "tool": "inspect-drift",\n'
	printf '  "mode": "%s",\n' "$(json_escape "${MODE}")"
	printf '  "live": "%s",\n' "$(json_escape "${THEME_DEST}")"
	printf '  "repo": "%s",\n' "$(json_escape "${REPO_DIR}")"
	printf '  "branch": "%s",\n' "$(json_escape "${branch}")"
	printf '  "head": "%s",\n' "$(json_escape "${head_commit}")"
	printf '  "origin_main": "%s",\n' "$(json_escape "${main_commit:-unavailable}")"
	[ -n "${BASELINE}" ] && printf '  "baseline": "%s",\n' "$(json_escape "${BASELINE}")"
	printf '  "live_exists": true,\n'
	printf '  "summary": {\n'
	printf '    "files_compared": %d,\n' "${#LIVE_PATHS[@]}"
	printf '    "identical": %d,\n' "${n_identical}"
	printf '    "divergent": %d,\n' "${n_divergent}"
	printf '    "live_only": %d,\n' "${n_live_only}"
	printf '    "repo_only": %d,\n' "${n_repo_only}"
	printf '    "live_not_in_any_branch": %d\n' "${n_no_branch}"
	printf '  },\n'
	printf '  "skipped_dirs": ['
	first=1
	for name in "${SKIP_DIRS[@]}"; do
		n=0
		if [ -d "${REPO_DIR}/${name}" ]; then n=$(find "${REPO_DIR}/${name}" -type f 2>/dev/null | wc -l); fi
		liven=0
		if [ -d "${THEME_DEST}/${name}" ]; then liven=$(${SUDO} find "${THEME_DEST}/${name}" -type f 2>/dev/null | wc -l); fi
		[ "${first}" -eq 1 ] || printf ','
		printf '\n    {"name": "%s", "present_in_live": %s, "files_in_live": %d, "files_in_repo": %d}' \
			"$(json_escape "${name}")" "$([ "${liven}" -gt 0 ] && echo true || echo false)" "${liven}" "${n}"
		first=0
	done
	printf '\n  ],\n'
	printf '  "symlinks_in_live": %d,\n' "${live_symlinks}"
	printf '  "worktrees_scanned": ['
	first=1
	for wt in ${worktrees[@]+"${worktrees[@]}"}; do
		[ "${first}" -eq 1 ] || printf ','
		printf '"%s"' "$(json_escape "${wt}")"
		first=0
	done
	printf '],\n'
	printf '  "divergent_files": ['
	first=1
	for ((i = 0; i < ${#ROW_PATH[@]}; i++)); do
		[ "${first}" -eq 1 ] || printf ','
		printf '\n    {"path": "%s", "live_md5": "%s", "repo_or_baseline_md5": "%s", "head_md5": "%s", "origin_main_md5": "%s", "size_delta": "%s", "kind": "%s", "in_any_branch": %s}' \
			"$(json_escape "${ROW_PATH[i]}")" "$(json_escape "${ROW_LIVE[i]}")" \
			"$(json_escape "${ROW_OTHER[i]}")" "$(json_escape "${ROW_HEAD[i]}")" \
			"$(json_escape "${ROW_MAIN[i]}")" "$(json_escape "${ROW_DELTA[i]}")" \
			"$(json_escape "${ROW_KIND[i]}")" "$([ "${ROW_NOBRANCH[i]}" -eq 1 ] && echo false || echo true)"
		first=0
	done
	[ "${first}" -eq 1 ] || printf '\n  '
	printf '],\n'
	printf '  "worktree_copies": ['
	first=1
	for ((i = 0; i < ${#WT_PATH[@]}; i++)); do
		[ "${first}" -eq 1 ] || printf ','
		printf '\n    {"path": "%s", "worktree": "%s", "md5": "%s", "matches_live": %s, "git_status": "%s"}' \
			"$(json_escape "${WT_PATH[i]}")" "$(json_escape "${WT_DIR[i]}")" "$(json_escape "${WT_MD5[i]}")" \
			"$([ "${WT_MATCH[i]}" -eq 1 ] && echo true || echo false)" "$(json_escape "${WT_STATE[i]}")"
		first=0
	done
	[ "${first}" -eq 1 ] || printf '\n  '
	printf '],\n'
	if [ "${n_divergent}" -gt 0 ]; then printf '  "divergence": 1\n}\n'; else printf '  "divergence": 0\n}\n'; fi
	# Human noise belongs on stderr so stdout stays parseable, but the exit code
	# stays the same as mode 1 so a JSON gate works the same way.
	note "inspect-drift: ${#LIVE_PATHS[@]} live files compared, ${n_identical} identical, ${n_divergent} divergent, ${n_live_only} live-only, ${n_no_branch} in no branch"
	[ "${n_divergent}" -gt 0 ] && exit 1
	exit 0
fi

# ------------------------------------------------------------ human output

out "== inspect-drift: live theme versus $( [ "${MODE}" = "baseline" ] && echo "baseline ${BASELINE}" || echo "repository working tree" )"
out ""
out "  live          : ${THEME_DEST}"
if [ "${MODE}" = "repo" ]; then
	out "  repo          : ${REPO_DIR} (branch ${branch}, HEAD ${head_commit:0:7})"
	out "  origin/main   : ${main_commit:-unavailable}"
else
	n_base=0
	for _k in ${BASE_MD5[@]+"${!BASE_MD5[@]}"}; do n_base=$((n_base + 1)); done
	out "  baseline      : ${BASELINE} (${n_base} files recorded)"
fi
out "  worktrees     : ${worktrees[*]:-none found}"
out ""
out "  excluded directories (reported, never compared):"
while IFS=' ' read -r name n; do
	if [ "${n}" -gt 0 ]; then
		out "    ${name}: PRESENT, ${n} files excluded from comparison"
	else
		out "    ${name}: absent"
	fi
done < <(skip_dir_report "${THEME_DEST}")
out "  symlinks in live: ${live_symlinks} (not followed, not compared)"
out ""

if [ "${n_divergent}" -gt 0 ]; then
	out "== divergent files"
	out ""
	if [ "${MODE}" = "baseline" ]; then
		printf '%-52s %-34s %-34s %-10s %-8s %s\n' "PATH" "LIVE MD5" "BASELINE MD5" "SIZE DELTA" "KIND" "IN BRANCH"
	else
		printf '%-52s %-34s %-34s %-34s %-34s %-9s %-10s %s\n' "PATH" "LIVE MD5" "REPO MD5" "HEAD MD5" "ORIGIN/MAIN MD5" "SIZE DELTA" "KIND" "IN BRANCH"
	fi
	out "--------------------------------------------------------------------------------------------------------------"
	for ((i = 0; i < ${#ROW_PATH[@]}; i++)); do
		if [ "${MODE}" = "baseline" ]; then
			printf '%-52s %-34s %-34s %-10s %-8s %s\n' \
				"${ROW_PATH[i]}" "${ROW_LIVE[i]}" "${ROW_OTHER[i]}" "${ROW_DELTA[i]}" "${ROW_KIND[i]}" \
				"$([ "${ROW_NOBRANCH[i]}" -eq 1 ] && echo no || echo yes)"
		else
			printf '%-52s %-34s %-34s %-34s %-34s %-9s %-10s %s\n' \
				"${ROW_PATH[i]}" "${ROW_LIVE[i]}" "${ROW_OTHER[i]}" "${ROW_HEAD[i]}" "${ROW_MAIN[i]}" \
				"${ROW_DELTA[i]}" "${ROW_KIND[i]}" \
				"$([ "${ROW_NOBRANCH[i]}" -eq 1 ] && echo no || echo yes)"
		fi
	done
	out ""
fi

if [ "${n_no_branch}" -gt 0 ]; then
	out "== live files that exist in NO git branch"
	out ""
	for ((i = 0; i < ${#ROW_PATH[@]}; i++)); do
		[ "${ROW_NOBRANCH[i]}" -eq 1 ] || continue
		out "  ${ROW_PATH[i]}  (live md5 ${ROW_LIVE[i]})"
	done
	out ""
fi

if [ "${#WT_PATH[@]}" -gt 0 ]; then
	out "== copies of live-only work found in local worktrees"
	out ""
	printf '%-44s %-30s %-34s %-9s %s\n' "PATH" "WORKTREE" "MD5" "MATCHES" "GIT STATUS"
	out "--------------------------------------------------------------------------------------------------------------"
	for ((i = 0; i < ${#WT_PATH[@]}; i++)); do
		printf '%-44s %-30s %-34s %-9s %s\n' \
			"${WT_PATH[i]}" "${WT_DIR[i]}" "${WT_MD5[i]}" \
			"$([ "${WT_MATCH[i]}" -eq 1 ] && echo yes || echo no)" "${WT_STATE[i]}"
	done
	out ""
fi

out "== summary"
out ""
out "  live files scanned     : ${#LIVE_PATHS[@]}"
out "  identical              : ${n_identical}"
out "  divergent              : ${n_divergent}"
out "  live only (not in repo): ${n_live_only}"
out "  repo only (not in live): ${n_repo_only}"
out "  in no git branch       : ${n_no_branch}"
out ""
out "  summary: ${#LIVE_PATHS[@]} live files compared, ${n_identical} identical, ${n_divergent} divergent, ${n_live_only} live-only, ${n_no_branch} in no branch"

if [ "${n_divergent}" -gt 0 ]; then
	[ "${n_no_branch}" -gt 0 ] && out "  WARNING: ${n_no_branch} live file(s) exist in no git branch. A deploy would destroy the only copy."
	[ "${n_live_only}" -gt 0 ] && out "  WARNING: ${n_live_only} live file(s) are absent from the repo. A deploy would delete them."
	exit 1
fi

out "  no divergence found"
exit 0