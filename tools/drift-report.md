# Drift inspector: how to read `tools/inspect-drift.sh`

## The problem

`tools/deploy-theme.sh` does this:

```bash
sudo rm -rf "${THEME_DEST}"
sudo cp -r "${REPO_DIR}" "${THEME_DEST}"
```

The live tree is destroyed before a single byte of it is read. Whatever the
repository holds wins, unconditionally. That is correct when the repository is
the only place work exists. It is silent data loss when somebody edited the live
theme directly, or when a previous deploy shipped a build that was never
committed.

The inspector exists to make that class of loss visible before the `rm`.

## What it checks

| Check | What it answers |
|---|---|
| live vs repo working tree | Does live hold bytes the repo does not? |
| live vs `git show HEAD:<path>` | Was the live edit made on top of the current commit? |
| live vs `git show origin/main:<path>` | Does live match an older published build? |
| live path in any branch | Does this file exist in git at all, on any branch? |
| live-only work in `/tmp/opencode/wt-*` | Is there an uncommitted copy in a local worktree? |

The third check matters because of a specific observed state: the live
`assets/css/theme.css` differed from the repo copy, from `main`, and from an
agent worktree simultaneously, with no record of which was authoritative. Four
md5 columns side by side make that ambiguity visible instead of resolving it by
accident in whichever direction the deploy happened to run.

## Modes

```bash
./tools/inspect-drift.sh                          # default, gates on exit code
./tools/inspect-drift.sh --json                   # machine readable
./tools/inspect-drift.sh --save-baseline FILE     # record live manifest
./tools/inspect-drift.sh --baseline FILE          # compare live to manifest
```

`--save-baseline` writes one `md5 size path` record per line. It is deliberately
not `md5sum` output format, because the size is needed for the delta column.
Writing is atomic (temp file then `mv`) and only ever touches the file you name.

`--baseline` compares live against a manifest instead of the repo, so you can
tell "live changed" apart from "live never matched the repo".

## Exit codes

| Code | Meaning |
|---|---|
| 0 | live matches the comparison source |
| 1 | divergence found, deploy is gated |
| 2 | usage or environment error (missing repo, unreadable live, no usable sudo) |

Exit code 2 exists for one specific reason. If the live tree needs `sudo` and
`sudo -n` does not work, the inspector cannot read live honestly. Rather than
reporting a clean run it cannot substantiate, it exits 2 and says so.

## Reading the table

```
PATH                          LIVE MD5   REPO MD5   HEAD MD5   ORIGIN/MAIN MD5   SIZE DELTA  KIND      IN BRANCH
assets/css/theme.css          cb845e...   5ef0c6...  5ef0c6...  5ef0c6...         1845        differs   yes
```

| Column | Meaning |
|---|---|
| `LIVE MD5` | md5 of the file on the live server |
| `REPO MD5` | md5 of the same path in the working tree, `-` if absent |
| `HEAD MD5` | md5 of `HEAD:<path>`, `-` if not in HEAD |
| `ORIGIN/MAIN MD5` | md5 of `origin/main:<path>`, `-` if not there |
| `SIZE DELTA` | live bytes minus repo bytes. A large value means more than a whitespace change. `n/a` when the repo has no copy |
| `KIND` | `differs` (both sides exist, bytes differ), `live-only` (repo has no such file), `repo-only` (live has no such file) |
| `IN BRANCH` | `no` means the path is tracked by no local or remote branch. This is the dangerous column. |

`-` in a git column is not "differs". It means the path does not exist at that
ref. A live file whose `HEAD MD5` is `-` is untracked or uncommitted work.

`repo-only` rows are informational and are not counted as divergence for the exit
code, because a deploy legitimately adds them. They are listed so both
directions of drift are visible.

## Excluded directories

`node_modules`, `vendor` and `.git` are pruned from the scan and reported with a
file count instead:

```
node_modules: PRESENT, 54152 files excluded from comparison
vendor: absent
```

54000 dev-dependency files in production is itself a finding worth surfacing,
but comparing them would bury the real rows in noise. Symlinks are never
followed. Filenames with spaces, quotes and other unusual characters are handled:
paths are never word split.

## Case 1: live-only work, in no branch

```
orphan dir/live only block.php   2418ee...   -   -   -   n/a   live-only   no
```

Every git column is `-`, and `IN BRANCH` is `no`. There is exactly one copy of
this file on the machine. A deploy deletes it. The inspector then checks the
local worktrees:

```
PATH                          WORKTREE                  MATCHES   GIT STATUS
orphan dir/live only block.php /tmp/opencode/wt-contact  yes       ??
```

`MATCHES yes` means an identical copy exists there. `??` means git does not know
about it, so it is uncommitted. One matching worktree does not make this safe: an
untracked file is destroyed by `git worktree remove`, by a `git clean -fdx`, and
by an operating system cleanup, none of which ask.

## Case 2: live matches an old build

```
functions.php   960898...   e35337...   960898...   960898...   -61   differs   yes
```

`REPO MD5` differs from `HEAD MD5`, which equals `ORIGIN/MAIN MD5`. The working
tree has uncommitted edits, live still holds the committed version. Deploying is
safe with respect to `functions.php`: the committed bytes are preserved. It is
*not* safe with respect to whatever the working tree edits are, because those
have never been reviewed. Read the row as a prompt to commit or stash, not as a
reassurance.

A row where all four columns are equal except `LIVE MD5` means live holds a build
that matches nothing in git. That is Case 1 wearing a disguise.

## Recovering live-only work safely

The rule: get it into git before the deploy, not after.

```bash
cd /home/ubuntu/maulik-dev
git switch -c rescue/live-drift-$(date +%Y%m%d)
sudo cp /var/www/maulik-dev/wp-content/themes/maulik-portfolio/inc/whatever.php inc/whatever.php
git add inc/whatever.php
git commit -m "Rescue live-only file from before deploy"
git push -u origin rescue/live-drift-$(date +%Y%m%d)
```

Then re-run the inspector. The `in no branch` count should be 0 and the
`live-only` count should be 0.

If the live file is not something you want in the theme at all, copy it to
`docs/rescued/` in the same branch and commit it there. What matters is that it
exists in a commit somewhere, not which directory it lands in.

Then deploy, then compare live against the baseline rather than against the repo:

```bash
./tools/inspect-drift.sh --save-baseline /tmp/live-after.txt
./tools/inspect-drift.sh --baseline /tmp/live-after.txt
```

That mode ignores the repo entirely, so it answers a different question: did
anything on the server change during or after the deploy.

## As a deploy gate

```bash
./tools/inspect-drift.sh || { echo "live drift, not deploying"; exit 1; }
./tools/deploy-theme.sh
```

The inspector is deliberately not wired into `deploy-theme.sh`. Adding it would
mean editing a script whose behaviour is load bearing, and the gate is more
useful as an explicit human decision anyway.

## What it will not do

Read only. It never writes to the live theme, never touches the database, never
runs `wp`, and never runs the content migrator. `sudo` is used only to read.