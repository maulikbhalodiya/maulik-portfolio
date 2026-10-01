# RESEARCH 04: WPCS, TOOLING, SCSS BUILD, RELEASE
### Source: lib-7 lane · verified against live sources 2026-10-01

Condensed to what changes the build. Full lane output in session log.

---

## 0. THE HEADLINE: WPCS 3.4.1 IS A SECURITY RELEASE

**WPCS 3.4.1 (2026-07-27)** fixed arbitrary command execution in the
`WordPress.WP.EnqueuedResourceParameters` sniff when run over untrusted PHP
(GHSA-3pwp-g2mj-5p3v). A CI job that lints fork PRs is exactly that scenario.

**Rule: WPCS `^3.4.1` minimum, no exceptions, in every environment that touches
untrusted code.** Do not pin 3.4.0 "because it works".

Canonical repo is `WordPress/WordPress-Coding-Standards`. `WordPress/WordPressCS`
404s.

Install:
```bash
composer config allow-plugins.dealerdirect/phpcodesniffer-composer-installer true
composer require --dev "wp-coding-standards/wpcs:^3.4.1"
```
Composer is the **only** supported install method since WPCS 3.0.0.

---

## 1. Version pins (all verified)

| Component | Version | Note |
|---|---|---|
| WPCS | **3.4.1** | security release, mandatory |
| PHP_CodeSniffer | `^3.13.5` | required by WPCS 3.4.1 |
| `@wordpress/scripts` | **36.0.0** | 2026-09-23 |
| Node for wp-scripts 36 | `^22.22.2 \|\| ^24.15.0 \|\| >=26.0.0` | 35.0.0 required only 20.19+ |
| Dart Sass | 1.105.1 | `@import` deprecated since 1.80.0 |
| PHPStan | 2.2.16 | level 5 only, see below |
| `szepeviktor/phpstan-wordpress` | v2.0.4 | pairing unverified, check composer.json |
| `@axe-core/cli` | 4.13.0 | primary a11y gate |
| Theme Check | 20260901 | the actual submission gate |

---

## 2. Four corrections to the premises in the original brief

| Brief said | Reality |
|---|---|
| `wp-scripts theme-zip` | **Does not exist.** Only `plugin-zip`. Use `wp dist-archive` |
| `wp-scripts lint-js-md` | **Does not exist.** It's `lint-md-docs` (renamed, old name removed in 23.0.0) |
| `theme.json` has a `"translations"` key | **Does not exist.** See §7 |
| `@wordpress/create-block` is right for a theme | **Wrong tool, it scaffolds a plugin** |

Also: `wp-scripts` 36.0.0 **removed Jest**. `test-unit-js` now needs a
consumer-installed Vitest 5. Irrelevant to us, but it means don't copy old
tutorial test setups.

---

## 3. THE wp.org RULE THAT SHAPES EVERYTHING

From the Theme Review Guidelines, verbatim:

> "Shortcodes, custom post types, and custom blocks are not allowed in themes."
> "Themes are not allowed to include plugins and non-design related
> functionalities such as forms."

**We chose Path B (no submission), so this is not a hard blocker. But we build to
it anyway, because it forces good architecture:**

- Everything design-related goes through `theme.json`, `styles/*.json` style
  variations, block patterns, and CSS.
- The portfolio's structured project data lives in **block attributes inside
  `post_content`**, not in a CPT. See §5.
- The four canvas visuals are **CSS and `<canvas>` view scripts attached to
  pattern-level markup**, not registered block types.

This is also the cheaper path: no `register_post_type`, no capability map, no
rewrite flushing, no taxonomy UI to build, and content stays fully editable in
the Site Editor by anyone.

---

## 4. `phpcs.xml.dist` essentials

Key decisions, not the whole file (the fixer lane has the full template):

```xml
<rule ref="WordPress-Extra"/>   <!-- includes WordPress-Core -->
<rule ref="WordPress-Docs"/>
<rule ref="Generic.Commenting.Todo"/>
<rule ref="PHPCompatibilityWP"/>

<config name="testVersion" value="7.4-"/>          <!-- match our Requires PHP -->
<config name="minimum_wp_version" value="6.7"/>    <!-- match our Requires at least -->
```

Three gotchas:

1. **`minimum_wp_version` is a global `<config>`, not a sniff property.** It takes
   priority. Set it honestly to our `Requires at least:`. As of WPCS 3.4.0 the
   default is 6.7, but inflating it to silence warnings is a lie the codebase
   will eventually rely on.
2. **`WordPress.WP.I18n` needs an explicit text domain allow-list**, and the theme
   requires **max two** text domains.
3. **`WordPress.WP.YodaConditions` is NOT auto-fixable by `phpcbf`**, by design.
   phpcbf refuses to reorder operands because it is a readability preference. Same
   for escaping, sanitizing, nonces, and i18n. **Budget manual time for those.**

**What `phpcbf` fixes:** whitespace, tabs, array syntax, `=>` alignment, operator
spacing, control-structure spacing, trailing whitespace.
**What it never fixes:** escaping, sanitizing, nonces, text domains, prepared
SQL, Yoda conditions, file naming.

---

## 5. Data modelling, restated against §3

Given no CPT is allowed, the recommendation from lane 5 lands differently than
expected. Do not register post meta for project data. Put it in block attributes:

- Attributes are stored in `post_content`, so they are available to
  `render.php` with **zero extra registration**.
- Block attributes are typed by the `block.json` schema, which is real
  validation, not a comment.
- No `custom-fields` support needed, so no legacy Custom Fields metabox in the
  editor.
- No `register_post_meta`, so no double-registration hazard, no `auth_callback`
  signature to get wrong.

Reserve `register_post_meta` for the rare case where a value must be queried by
`WP_Query`, which cannot read block attributes. For a portfolio of six projects,
that case does not arise.

**The `block.json` trap, stated plainly:** `"type": "string"` is a type
assertion, not sanitization. A value typed `string` in the schema is still
untrusted at render time and must still be escaped per context on output.

---

## 6. SCSS: use the Dart Sass CLI, not wp-scripts

Stock `wp-scripts` gives **no `includePaths`**, and only compiles SCSS that is
imported from a JS entry point. It will not turn `style.scss` into `style.css`.
Overriding `webpack.config.js` works but the wp-scripts README explicitly warns
against it.

Recommended instead:

```json
"build:css": "sass --no-source-map --style=expanded --load-path=assets/styles src/style.scss:build/style.css"
```

**Write `@use`/`@forward`, never `@import`.** `@import` is deprecated in Sass
1.80.0 and removed in Dart Sass 3.0.0.

**A real constraint worth knowing:** stock wp-scripts does **not** expose
`silenceDeprecations`. You cannot suppress a Sass deprecation without
overriding the loader. So `@use`-based code is not the modern preference, it is
the only workable option. The lane flagged that the Sass 3.0.0 removal date could
not be verified from an official source; the removal itself is not in doubt.

**Cascade layers are the real concern, not file-count conventions.** The lane
could not find any authoritative source for the "7-1" or "8-3" patterns and
correctly declined to guess. What matters for a block theme is that compiled
theme CSS either stays **unlayered** (so it can override block defaults) or sits
in a layer we explicitly order, and that our specificity does not out-battle
`theme.json` presets. A flat-ish `abstracts/base/components/utilities` layout
serves that fine.

### Tokens: never hand-duplicate a hex value

`theme.json` already emits every token as a custom property, free:
`--wp--preset--color--primary`, `--wp--preset--font-size--large`,
`--wp--preset--spacing-50`, and so on.

So:
- **Colours, fonts, spacing sizes** → read the custom property. Zero drift, and
  the user can override it in the Site Editor.
- **Compile-time-only values** (breakpoints, mixin params, `contentWidth`) → a
  prebuild script reads `theme.json` and emits
  `assets/styles/generated/_theme-json-tokens.scss`, committed and marked
  `linguist-generated`, with a CI step that fails if it is stale.

### Avoid double-enqueued styles

1. `style.css` is enqueued automatically under the handle `my-theme`. Do **not**
   re-register it.
2. Keep `.scss` sources out of anything that gets enqueued.
3. Editor styles register separately on `enqueue_block_editor_assets`, never
   in the front-end queue.
4. Add a CI assertion: `grep -rnE "\.scss['\"]" --include=*.php .` must be empty.

---

## 7. theme.json translation: the positional-context footgun

There is no `translations` key. Core loads `wp-includes/theme-i18n.json` as an
i18n schema and walks it with `translate_with_gettext_context()`, using **the
JSON key path as the gettext context**:

```
msgctxt "settings.color.palette.0.name"
msgid "Primary"
```

**Consequence: reordering or inserting a palette entry shifts the array indices
and silently repoints existing translations.** Append new entries at the end.
Never reorder. This is a direct reading of core's own translation function, and
the lane was right to flag that no official core statement documents it. Treat it
as a hard rule regardless.

---

## 8. Release: commit `build/`, use `wp dist-archive`

**wp.org reviewers install the ZIP. They never run `npm install`.** Compiled
assets must be in the ZIP, and if a minified file ships, its source must ship
too.

Decision: **commit `build/` and `assets/css/`.** Any checkout can produce the
ZIP, the diff is reviewable, and bisect works. Mark generated files
`linguist-generated=true` in `.gitattributes`.

ZIP built with:
```bash
wp dist-archive . dist --exclude='.git,.github,node_modules,vendor,dist,tests,*.zip,*.map'
```

Then smoke-test the ZIP: required files present (`style.css`, `readme.txt`,
`theme.json`, `templates/index.html`, `screenshot.png`), forbidden entries
absent (`node_modules`, `vendor`, `.git/`, `__MACOSX`, `Thumbs.db`).

**Cache-busting:** bump `Version:` in `style.css` and `readme.txt` every
release, and pass `wp_get_theme()->get( 'Version' )` as `$ver`.

---

## 9. Testing: what is and is not worth it

**Required:** Theme Check passes with **0 errors**. That is the actual
submission gate. Three or more distinct issues can get a theme closed.

**Not required by wp.org:** unit tests, static analysis, a build step.

Recommendations for us:
- **Theme Check 0 errors**: yes, in CI, non-negotiable.
- **WPCS clean**: yes, in CI, warnings treated as errors.
- **PHPStan level 5**: yes, on the small PHP surface we have. Level 6+ generates
  constant false positives around `wpdb` and dynamic properties.
- **axe-core against every template in `templates/`**: yes. axe is the engine
  WordPress core's own a11y suite uses, so it is the aligned choice.
- **PHPUnit**: no. For a block theme the WP test suite has almost nothing to
  bite on. Near-zero ROI.
- **Jest/Vitest**: no. We ship no browser JS worth testing.

**Automated a11y tooling catches roughly 30 to 40 percent of real issues.** It
cannot verify focus order, focus visibility, reading order, meaningful link
text, or whether the skip link works. A manual keyboard pass is required and must
be budgeted. Zoom to 400 percent and check reflow. Screen reader pass on home
and single templates.

---

## 10. Security checklist for the theme

Five checks, in the order the review cares about:

1. **Validate and sanitize** on the way in. `wp_unslash()` always before
   sanitizing. No unslashed input ever reaches a sanitizer.
2. **Escape late**, per context, at the last possible moment. `esc_html`,
   `esc_attr`, `esc_url`, `wp_kses_post`. Note `get_block_wrapper_attributes()`
   output is already escaped, do not double-escape it.
3. **Capabilities, never roles.** Use meta capabilities (`edit_post`) so
   object-level ownership works. Nonce AND capability, never one alone.
4. **`$wpdb->prepare()`** if SQL is ever needed. Prefer avoiding SQL entirely,
   which we can do.
5. **Path handling.** `get_theme_file_uri()` / `get_theme_file_path()` only, so
   child themes can override. Never `get_template_directory_uri()`, which does
   not fall back to the parent. Never user input in `include`.

Banned outright by Theme Check: `eval`, `base64_decode`, `str_rot13`,
`uudecode`, and any `system`/`exec`/`passthru`/`shell_exec`/`popen` call.

Also: WPCS 3.4.0 changed deprecation handling. Anything deprecated **before**
`minimum_wp_version` is an **error**, not a warning. Set the config honestly.

---

## 11. CI gates we will actually run

| Job | Tool | Blocking |
|---|---|---|
| PHP standards | `phpcs` WordPress-Extra + Docs | yes, 0 errors 0 warnings |
| PHP auto-fix available | `phpcbf` | advisory, run in a separate job |
| Static analysis | PHPStan level 5 | yes |
| Theme Check | Theme Check plugin, `--extra` | yes, 0 errors |
| CSS | stylelint via wp-scripts, SCSS-aware | yes |
| Token drift | `git diff --exit-code` on generated/ | yes |
| No double-enqueue | grep assertion | yes |
| Dependency audit | `composer audit --locked` | yes |
| A11y | axe-core on every template | yes |

**CI security posture, non-negotiable:**
- `permissions: contents: read` on PR-triggered jobs.
- **Never** `pull_request_target` combined with repo config. That runs the PR's
  code on a privileged context.
- WPCS `^3.4.1` because fork PR linting is arbitrary code execution.

---

## 12. Items flagged unverified by the lane

1. PHPStan 2.2.x paired with `phpstan-wordpress` v2.0.4, no matrix published.
   Check its `composer.json` before pinning both.
2. `php-stubs/wordpress-stubs` current version, not queried.
3. PHPUnit version compatible with current `wp-phpunit`, use the suite's own
   declared constraint.
4. PHPCS 4.x `installed_paths` migration, unconfirmed. Does not bite now, WPCS
   3.4.1 requires PHPCS `^3.13.5`.
5. "7-1" / "8-3" SCSS patterns, no authoritative source, not WordPress concepts.
6. Dart Sass 3.0.0 removal date, announced but target unconfirmed.
7. Any new 2025 to 2026 wp.org theme security requirement, absence of evidence
   rather than confirmed absence.
8. The `theme-i18n.json` positional-context hazard, correct reading of core code
   but no official core warning exists.
