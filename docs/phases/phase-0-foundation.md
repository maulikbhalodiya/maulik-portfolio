# PHASE 0 · Repository foundation

**Status: COMPLETE** · 2 commits · `github.com/maulikbhalodiya/maulik-portfolio`

The infrastructure exists so every later phase is verifiable rather than hopeful.
Nothing user-facing was built here.

---

## What was built

- [x] Repo initialised, 53 files
- [x] `composer.json`, `package.json`, `phpcs.xml.dist`, `phpstan.neon`
- [x] `.stylelintrc.json`, `eslint.config.mjs`, `.editorconfig`, `.nvmrc`
- [x] `functions.php` as a thin loader, all logic in `inc/`
- [x] `inc/` split: setup, assets, seo, helpers
- [x] Three workflows: `ci.yml`, `ai-review.yml`, `ai-audit.yml`
- [x] Four `.audit-prompts/` standards documents
- [x] Dependabot for composer, npm, github-actions
- [x] Dart Sass CLI build, not wp-scripts
- [x] Token generator reading `theme.json`

## Gates, verified independently

| Gate | Result |
|---|---|
| `phpcs` WordPress-Extra + Docs | 7/7 clean, 0 errors 0 warnings |
| PHPStan level 5 | no errors |
| `composer validate --strict` | valid |
| `php -l` all files | valid |
| stylelint | clean |
| eslint | clean |
| `sass` build | succeeds |
| Dash sweep | 0 hits |

---

## Defects found and fixed in this phase

Recording these because each was invisible to the agent that wrote the code, and
each would have caused real damage later.

### 1. Three `theme.json` keys at the wrong nesting level

`defaultFontSizes`, `defaultSpacingSizes` and `defaultGradients` sat directly
under `settings`, where they are not valid. The real keys live under
`typography`, `spacing` and `color`.

This was not cosmetic. `version: 3` **flips the first two defaults to `true`**,
so the misplaced keys meant nothing was suppressing the core light-mode palette.
Every page would have rendered with a stray grey palette against our dark theme.

Fixed by placing `false` explicitly in the correct locations. An assertion now
checks this.

### 2. The palette was provisional, not the design

Six invented colours, and `system` / `mono` as font families. Now the real thing:
15 colours, Syne / Plus Jakarta Sans / IBM Plex Mono, `wideSize: 90rem` matching
the 1440px container, `border.radius: false` and no shadow presets, because the
design has zero rounded corners and zero shadows everywhere.

### 3. Two silent CI bugs on a single line

The line was:

```yaml
run: vendor/bin/phpcs --standard=phpcs.xml.dist --report=checkstyle | cs2pr
```

**Bug one.** phpcs writes its progress dots to stdout ahead of the checkstyle
XML. `cs2pr` then tried to parse `....... 7 / 7 (100%)` as XML and died with
`Start tag expected, '<' not found on line 1`. It failed on a *clean* tree,
which is a confusing way to discover the gate works. Fixed with `-q`.

**Bug two, and the worse one.** Without `pipefail`, a pipeline's exit status is
the status of its **last** command. `cs2pr` succeeds, so the step passed even
when phpcs reported violations. Demonstrated:

```
without pipefail -> exit 0   (violations silently pass)
with    pipefail -> exit 2   (gate works)
```

The standards gate was reporting green and enforcing nothing. Every other step
in that file already had `set -euo pipefail`; this one line did not. Fixed in
both `ci.yml` and `ai-review.yml`.

### 4. A real Gmail address in git history

The first commit carried a real Gmail address as the author. Pushing a
public repo publishes commit metadata permanently, and research established this
address is spam-listed within 48 hours of appearing on a site.

Caught one commit before the first push. History rewritten to the noreply
address, local git config updated, verified zero Gmail addresses in the history
and zero in any file's contents.

### 5. Five agent-reported bugs, for the record

Found during the agent's own verification: `phpstan.neon` had `includes` nested
inside `parameters`; `inc/helpers.php` had `namespace` after the ABSPATH guard,
a hard syntax error; `maulik-dev` was invalid as a PHP symbol prefix; `@forward
... as tokens:` is not valid Sass; and the CI grep patterns matched the
workflow files that described them.

---

## Open items carried forward

- [ ] No model API key set, so `ai-review.yml` and `ai-audit.yml` will fail on
      first run. `ci.yml` needs nothing and works today.
- [ ] `screenshot.png` absent, a release blocker
- [ ] `screenshot.png` is 1200x900 and required by Theme Check
