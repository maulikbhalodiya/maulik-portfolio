# PHASE 9 · GitHub and ongoing maintenance

**Status: NOT STARTED** · partly parallel, Phase 0 did the foundations

The repository is the portfolio's second face. A recruiter who clicks through
from the site will read it.

---

## 9.1 Repository

- [x] `maulikbhalodiya/maulik-portfolio` created, public
- [x] `main` as default branch, tracking origin
- [x] Two commits with a meaningful message history
- [ ] **README that reads as engineering evidence, not a readme**
- [ ] Theme screenshot in the README
- [ ] Architecture section explaining the no-CPT decision and the why
- [ ] Performance budget stated
- [ ] Licence correct, GPL-2.0-or-later

### The README should answer four questions

1. What is this
2. Why is it built this way, specifically the block-theme and no-CPT choices
3. What are the quality gates
4. How do I run it locally

## 9.2 Branch protection

- [ ] `main` protected
- [ ] CI required before merge
- [ ] Require branches to be up to date
- [ ] No direct pushes, or admin only
- [ ] Deletion protection

## 9.3 The AI reviewer

- [x] CodeRabbit config
- [x] `ai-review.yml` on pull requests
- [x] `ai-audit.yml` nightly, creating issues
- [x] Four `.audit-prompts/` standards documents
- [ ] **A model API key set as a repo secret.** Without it both workflows fail on
      first run. `ci.yml` needs nothing and works today.
- [ ] Test a real pull request end to end
- [ ] Test the opt-in `/fix` path, including the fork refusal
- [ ] Confirm the project context names the theme correctly after the rename

## 9.4 The profile, outside this repo

- [ ] **Pin `rankkernel` first on the profile.** Four stars on student repos
      currently rank above it
- [ ] **Create a profile README.** None exists
- [ ] **Create the actual RankKernel repository.** The site currently links to the
      GitHub profile rather than a repository, so "View on GitHub" is misleading
- [ ] Pin order: RankKernel, then the professional work
- [ ] Bio consistent with the site's positioning

## 9.5 Corrections to make

- [ ] `resume.md` says **Qrologic**, everything else says **Qrolic**. Standardise
- [ ] Résumé contact line shows `github.com/XXXXXXXX`, a placeholder
- [ ] LinkedIn shown with a trailing dash, `linkedin.com/in/maulik-bhalodiya-`.
      The real URL has none. Verify.

## 9.6 Ongoing

- [ ] Nightly audit issues triaged weekly
- [ ] Dependabot PRs reviewed, not auto-merged
- [ ] `gh release` per version
- [ ] Changelog maintained in `readme.txt`
- [ ] Review any workflow change for gate weakening before merging

### A standing rule worth writing down

The AI fix job duplicates the gate suite rather than reusing `ci.yml`, so an
edit to one cannot silently weaken the other. That is intentional. Preserve it.

---

## Definition of done

- [ ] README reads as engineering evidence
- [ ] Branch protection on `main` with CI required
- [ ] Model API key set, AI reviewer verified on a real PR
- [ ] RankKernel pinned first, profile README written
- [ ] Actual RankKernel repository created
- [ ] Qrolic spelling standardised everywhere
- [ ] All CI gates green

**Commit:** varies per item
