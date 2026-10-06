#!/usr/bin/env bash
#
# Gate: every GitHub Actions reference in .github/workflows/ must be pinned to a
# full 40 character commit SHA. A floating tag such as @v4 or a short SHA is a
# failure.
#
# WHY THIS EXISTS
#
# Every uses: line in this repository was pinned to a SHA by hand, once. That
# work is only worth anything if it cannot be undone silently, because a
# floating tag is the common way an action reference regresses: someone adds a
# step, copies an example out of the action's README, and the tag comes with
# it. Nothing else in CI looks at that line, so without this gate the pinning
# silently decays back to @v4 over a series of unremarkable pull requests.
#
# WHAT IT CHECKS
#
# Every uses: line in every file under .github/workflows/. The reference must be
# owner/repo@<40 hex characters>. YAML allows the value unquoted, single quoted
# or double quoted, so all three are accepted. Everything from a # onward is
# stripped first, because these files carry trailing version comments such as
# "uses: actions/checkout@<sha> # v7.0.1" and the comment is not part of the
# reference.
#
# A "./relative/path" reference is a local action in this same repository. It is
# legitimately not a SHA, so it is counted and reported as local rather than
# failed.
#
# USAGE
#   tools/check-action-pins.sh                     scans <repo root>/.github/workflows
#   tools/check-action-pins.sh <workflows dir>    scans the given directory
#
# Exit 0 when every reference is pinned, exit 1 when any is floating.
set -euo pipefail

# The repository root is the parent of the directory holding this script, so the
# gate reads the same files no matter which directory the caller is in. CI runs
# from the checkout root anyway, but a local run from a subdirectory must not
# quietly scan nothing and pass.
script_dir="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
repo_root="$(cd -- "${script_dir}/.." && pwd)"
workflows_dir="${1:-${repo_root}/.github/workflows}"

if [ ! -d "${workflows_dir}" ]; then
	printf '::error::No workflow directory at %s\n' "${workflows_dir}" >&2
	exit 1
fi

if [ -n "${1:-}" ]; then
	printf 'action pins: scanning %s\n' "${workflows_dir}"
else
	printf 'action pins: scanning %s\n' "${workflows_dir#"${repo_root}/"}"
fi

trim() {
	local s="$1"
	s="${s#"${s%%[![:space:]]*}"}"
	s="${s%"${s##*[![:space:]]}"}"
	printf '%s' "${s}"
}

total=0
local_total=0
floating_total=0

# Sorted so the output order does not depend on the filesystem.
for file in $(find "${workflows_dir}" -type f \( -name '*.yml' -o -name '*.yaml' \) | sort); do
	rel="${file#"${workflows_dir}"/}"
	lineno=0
	file_total=0
	file_local=0
	file_floating=0
	while IFS= read -r raw || [ -n "${raw}" ]; do
		lineno=$(( lineno + 1 ))
		# Comments are stripped before anything else so a trailing
		# "# v7.0.1" cannot turn a valid SHA into a mismatch.
		line="${raw%%#*}"
		case "$(trim "${line}")" in
			*uses:*) ;;
			*) continue ;;
		esac
		# Match on the key with leading whitespace, an optional YAML list dash
		# and optional space, so "with:" and a prose "uses:" inside a run: block
		# are not mistaken for a reference.
		if ! printf '%s' "${line}" | grep -qE '^[[:space:]]*-?[[:space:]]*uses:[[:space:]]'; then
			continue
		fi
		value="$(trim "${line#*uses:}")"
		# Unwrap a fully quoted value. A single character is not a quoted pair,
		# so guard on length.
		case "${value}" in
			\'*\'|\"*\")
				if [ "${#value}" -ge 2 ]; then
					value="${value:1:${#value}-2}"
				fi
				;;
		esac
		if [ -z "${value}" ]; then
			continue
		fi

		total=$(( total + 1 ))
		file_total=$(( file_total + 1 ))
		if [ "${value#./}" != "${value}" ]; then
			local_total=$(( local_total + 1 ))
			file_local=$(( file_local + 1 ))
			continue
		fi
		if ! printf '%s' "${value}" | grep -qE '^[^@[:space:]]+@[0-9a-fA-F]{40}$'; then
			floating_total=$(( floating_total + 1 ))
			file_floating=$(( file_floating + 1 ))
			# file, line and the whole reference, because the fix is a single
			# edit at a known place and nothing else is needed to find it.
			printf '::error::%s:%s has a floating action reference: uses: %s\n' \
				"${rel}" "${lineno}" "${value}"
		fi
	done < "${file}"
	printf '    %-16s %2d reference(s), %d local, %d floating\n' \
		"${rel}" "${file_total}" "${file_local}" "${file_floating}"
done

if [ "${floating_total}" -ne 0 ]; then
	printf '::error::%d of %d action reference(s) are not pinned to a 40 character commit SHA.\n' \
		"${floating_total}" "${total}" >&2
	printf 'Pin each one to owner/repo@<full commit SHA>, with the version in a trailing comment.\n' >&2
	exit 1
fi

printf 'OK: %d action reference(s) checked, all pinned to a commit SHA (%d local).\n' \
	"${total}" "${local_total}"
exit 0
