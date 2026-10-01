# Code quality and WordPress conventions

Review target: the `maulik-dev` WordPress **block theme**. Everything below is a hard
requirement of this repository, not a preference. Treat any violation as a blocking
finding.

---

## 1. Reporting standard, read this first

1. **Report only what you can point at.** Every finding needs a file path and a line
   number. If you cannot produce both, you do not have a finding, you have a feeling.
   Do not report it.
2. **Cite the exact sniff name.** Write it out, for example
   `WordPress.WhiteSpace.ControlStructureSpacing`. A finding a human cannot reproduce
   with a single command is not a finding.
3. **Never propose `phpcs:ignore` or `phpcs:disable`.** These do not fix anything.
   They hide the problem and they teach the next reader that the standard is
   optional. The fix is the code change.
4. **Never propose changing `phpcs.xml.dist`.** Weakening a ruleset to make a
   violation disappear is not a fix, it is deleting the alarm and leaving the fire.
   The one legitimate exception is a documented rule that is factually wrong for this
   codebase, and even then it needs a separate PR with a comment explaining why.
5. **Never propose weakening or deleting a test, a CI step, or an assertion.** The
   gates are the product. A PR that only passes by removing a gate is not a passing
   PR.
6. **Your proposed code must itself pass `phpcs.xml.dist` with zero errors and zero
   warnings.** This repository treats warnings as failures. Write code you would
   accept in review, not code that looks roughly right.

---

## 2. Critical architecture rules

These are the constraints that make this a block theme rather than a classic theme
with block markup bolted on. Violating any of them produces a site that looks fine in
development and breaks in ways that are hard to trace.

### 2.1 WPCS 3.4.1 or later is mandatory

`wp-coding-standards/wpcs` must be pinned to `^3.4.1` in `composer.json`. Anything at
or below 3.4.0 is vulnerable to **GHSA-3pwp-g2mj-5p3v**, a remote code execution issue
that fires when PHP_CodeSniffer lints untrusted code. CI lints pull requests, including
those from forks, which is exactly the situation the advisory describes. There is a CI
assertion that fails the build below 3.4.1. Do not lower the constraint and do not
propose removing that assertion.

### 2.2 The two asset filters must both be `true`

`should_load_separate_core_block_assets` and `should_load_block_assets_on_demand` must
both be filtered to `true` in `inc/setup.php`. This is the single biggest performance
lever a block theme has: together they let Core enqueue per-block assets only for the
blocks actually rendered on the current page. Remove either and the front end ships
every core block stylesheet and script on every page.

`should_load_remote_block_patterns` must be filtered to `false`. This theme makes no
outbound HTTP requests, and a remote fetch is both a privacy leak and an unbounded
latency risk on someone else's server.

### 2.3 The block theme marker pair

`style.css` and `templates/index.html` must both exist. If either is missing, WordPress
does not recognise this as a block theme and falls back to classic theme handling.
Both are mandatory in the first commit and must stay.

### 2.4 Never create `block-templates/` or `block-template-parts/`

These are the legacy pre-6.1 block theme directories. Core checks `file_exists()` on
the stylesheet directory and switches to legacy handling if **either** exists. The
failure is silent and it breaks `/templates/` entirely, so template parts stop resolving
and you get blank headers and footers. Use `templates/` and `parts/`. Flag any commit
that adds either legacy directory.

### 2.5 Template parts are `.html` and cannot contain PHP

`parts/*.html` are parsed as block markup. They are never executed. They cannot call
`esc_html_e()`, they cannot read options, they cannot loop. Anything in a template part
that needs translation or a dynamic value belongs in `patterns/*.php` instead, because
patterns are real PHP files.

Flag any PHP tags, `<?php`, `<?=`, or shortcode calls inside `parts/` or `templates/`.

### 2.6 Patterns execute on `init`, not at render

A pattern is included during `init`, long before any request context exists. Inside a
pattern these are all unavailable or meaningless:

- `is_single()`, `is_page()`, `is_archive()` and every other conditional tag
- `get_post()`, `get_the_ID()`, `the_title()`, `the_content()`, the loop
- `get_header()`, `get_footer()`, `wp_head()`, `wp_footer()`
- any request-scoped global

What does work in a pattern: `esc_html_e()`, `esc_attr_e()`, `esc_html()`,
`esc_attr()`, `get_theme_file_uri()`, `get_bloginfo()`, `get_theme_file_path()`, and
iteration over static data.

Flag any query conditional or loop function called from `patterns/`.

### 2.7 Every PHP file opens with the guard and a docblock

```php
<?php
/**
 * One line on what this file is for.
 *
 * @package Maulik_Dev
 */

defined( 'ABSPATH' ) || exit;
```

Two exceptions and only two:

1. A **namespaced** file must put `namespace X;` immediately after the docblock and
   before the guard. PHP requires the namespace declaration to be the very first
   statement. See `inc/helpers.php`.
2. A file with no side effects may omit the guard, but must still have the docblock.

There is no third exception. A missing guard is a blocking finding.

### 2.8 `render.php` must be side-effect free

A block's `render.php` is `require`d once per block instance on a page. Anything
declared at its top level, a function, a class, a constant, is declared again on the
next instance and produces a fatal redeclaration error. `require_once` from `inc/`
instead, or use `render_$block_type` naming so each function is declared once.

The body must build a string and `return` it. Never `echo`. Never emit a stray newline
before the markup, because that whitespace becomes visible output between block
wrappers.

Flag any `function`, `class`, or `const` declaration inside `render.php`.

### 2.9 `block.json` attributes are a type assertion, not sanitization

Declaring `"type": "string"` tells the editor and the REST schema what shape to expect.
It performs **no** validation or escaping. Attribute values arrive at `render.php`
exactly as stored and are fully attacker-controlled through the block editor and the
REST API. Escape at the point of output, for the context you are outputting into. See
the security document for the per-context mapping.

Never assume a value is safe because `block.json` described it.

### 2.10 No custom post types, taxonomies, shortcodes, or form handling

None of these are in scope for this theme. Project data lives in block attributes
inside post content, which means it is editable in the site editor and requires no
database schema.

We hold ourselves to WordPress.org review standards as a discipline even though we are
not submitting to the directory. That discipline is the point. Flag any proposed
`register_post_type()`, `register_taxonomy()`, `add_shortcode()`, `register_block_type()`
in PHP, or any handler for submitted form data.

`register_block_type()` deserves a specific note: for a block theme, block registration
happens automatically from `blocks/*/block.json`. A manual PHP registration for a block
that already has a `block.json` causes a duplicate registration warning.

### 2.11 Never emit a raw email address or a `mailto` link

Contact is a form with an intent dropdown. Not an address in the markup, not a
`mailto` link. Raw addresses get scraped by every spam bot on the internet the moment
the site goes live, and `mailto` links are collected before anyone ever sees the page.

CI greps the whole repository for both. If your suggestion introduces either, it is a
blocking finding.

### 2.12 `noindex` while in development

`inc/seo.php` provides `maulik_dev_is_development()` and `maulik_dev_noindex()`. The
flag is gated behind `WP_DEBUG` **and** an explicit opt-in, deliberately, so that a
stray constant cannot deindex production. Do not weaken that gate, do not remove the
`WP_DEBUG` check, and do not move the logic somewhere the gate is easy to forget.

### 2.13 No em dashes and no en dashes

Not in code, not in comments, not in user-facing copy, not in commit messages, not in
documentation. Use a comma, a colon, a semicolon, a full stop, or parentheses. This is
a hard content rule and CI greps the entire repository for U+2014 and U+2013 and fails
on any hit.

This rule applies to your own output too. When you write a suggestion, do not use a
dash. Reword it.

### 2.14 No skill bars, progress meters, or star ratings

No percentages, no progress meters, no star ratings, in any generated output or
suggested markup. A portfolio that quantifies a person's ability with a bar is making a
claim the site cannot substantiate and the site has no data source for. Keep the claim
qualitative or make the numbers real.

### 2.15 No outbound HTTP requests

No `wp_remote_get()`, `wp_remote_post()`, `file_get_contents()` against a remote URL, no
`curl`, no `fetch()` in front end code. No analytics, no CDN fetch, no font fetch from a
third party. Fonts are self-hosted. This keeps the privacy story simple and the
performance budget reachable.

---

## 3. Coding standards, in detail

### 3.1 Indentation

- **One real tab per indent level.** Not spaces pretending to be a tab, not a tab plus
  spaces, not two spaces. `WordPress.WhiteSpace.Indent`.
- Never use spaces for alignment inside an indented block.

### 3.2 Spacing inside control structures

Always spaces inside the parentheses:

```php
if ( $value ) {          // correct
foreach ( $items as $i ) { // correct
```

WordPress style is `if ( $x )`, not `if($x)` and not `if( $x )`. This applies to
`if`, `elseif`, `else`, `foreach`, `for`, `while`, `switch`, `catch`, and `match`.

### 3.3 Spacing inside function calls

One space between the function name and the open paren, and one space after each comma:

```php
wp_enqueue_style( 'maulik-dev-style', $uri, array(), $version ); // correct
```

### 3.4 Yoda conditions

Constants and literals go on the left:

```php
if ( 42 === $count )    // correct
if ( $count === 42 )    // wrong
if ( null === $result ) // correct
if ( true === $flag )   // correct
```

`phpcbf` deliberately does **not** rewrite this, because mechanically swapping operands
can change evaluation order and side effects. Write it correctly by hand.

Use `elseif`. Never `else if`. They are not interchangeable: `else if` is a nested `if`
inside the `else` block and can silently change control flow.

### 3.5 Strings

Single quotes unless you are interpolating or need an escape. `array()` in full, never
`[]`. Concatenate with `.` and put spaces around the operator.

### 3.6 Whitespace and arrays

No trailing whitespace on any line. One newline at end of file. Aligned `=>` in array
literals, and the whole array re-aligned when any value changes. Blank line before the
return statement. Blank line after the opening brace of a function.

### 3.7 Docblocks are mandatory, not optional

Every class, method, property, function, and constant gets a docblock with
`@param`, `@return`, and `@var` where applicable. This repository enables
`WordPress-Docs` on purpose. A missing `@param` tag is a blocking finding, not a nit.

```php
/**
 * Enqueue the theme stylesheet.
 *
 * Longer description of why, if the why is not obvious.
 *
 * @since 0.1.0
 *
 * @return void
 */
```

### 3.8 Prefixing

| Thing | Prefix |
|---|---|
| Functions, globals, variables | `maulik_dev` |
| Constants | `MAULIK_DEV` |
| Text domain, enqueue handles, CSS classes | `maulik-dev` |
| Internal namespaced helpers | `namespace Maulik_Dev;` |

The text domain is exactly `maulik-dev`. Not `maulik_dev`, not `MaulikDev`, not
`maulikdev`. `WordPress.WP.I18n` is configured to enforce this and it is checked in CI.

Note that `maulik-dev` is **not** in the `PrefixAllGlobals` array, because a hyphen is
not legal in a PHP symbol name and the sniff warns on it. That is deliberate.

### 3.9 Escaping happens late, per context

The rule is: sanitize on the way in, escape on the way out, and escape at the exact
point of output rather than earlier in the data flow.

| Output context | Function |
|---|---|
| HTML text node | `esc_html()` |
| HTML attribute | `esc_attr()` |
| JavaScript string, JSON, `<script>` | `wp_json_encode()` |
| URL in `href` / `src` | `esc_url()` |
| URL for a database query | `esc_url_raw()` |
| SQL | `$wpdb->prepare()` |
| Text for translators | `translate()`, then escape the result |
| Block attribute into HTML | the matching `esc_*()` for where it lands |

Sanitizing once at storage time and assuming it is still safe at output time is a
security bug, because context changes and a value safe in one context is not safe in
another. See the security document.

### 3.10 `phpcbf` cannot fix what matters

`phpcbf` fixes whitespace, indentation, brace and parenthesis placement, and some
naming and spacing sniffs. It does **not** fix escaping, sanitizing, missing nonces,
i18n problems, or unprepared SQL.

Do not accept "I ran phpcbf" as evidence that code is safe. Read the diff.

---

## 4. i18n

- Text domain is `maulik-dev`, exactly.
- Every user-facing string goes through `esc_html_e()`, `esc_attr_e()`, `esc_html__()`,
  `esc_attr__()`, `translate()`, or `_x()` when context is needed.
- Add a translator comment for any string a translator could plausibly get wrong,
  especially strings containing a placeholder.
- Never concatenate translated strings to build a sentence. WordPress does not let you
  reorder grammatical parts, and several locales need to. Use a placeholder inside a
  single translatable string, or use `sprintf()` with a translated format string.
- Mark placeholders with `sprintf()` style `%%1$s` and document them.
- **Palette entries in `theme.json` must be appended to, never reordered.** Translation
  of theme.json names uses the JSON key path as the gettext context, so reordering or
  renaming an entry silently detaches its translations. This is documented in
  `languages/maulik-dev.pot`.

Regenerate the POT with:

```
wp i18n make-pot . languages/maulik-dev.pot --domain=maulik-dev
```

---

## 5. Filesystem and paths

- `get_theme_file_uri()` and `get_theme_file_path()` always. Never
  `get_template_directory_uri()` or `get_template_directory()`, which bypass the child
  theme entirely and make overriding impossible.
- `get_parent_theme_file_path()` is the correct choice inside theme code.
- Never hardcode a path into a template. Use `get_theme_file_uri()` so the URL tracks
  the active theme.

---

## 6. The Sass and CSS layer

- `@use` and `@forward` only. **Never `@import`.** It was deprecated in Dart Sass 1.80.0
  and is removed in 3.0.0.
- **Never hand-write a hex colour** in a `.scss` file when the value belongs in
  `theme.json`. The palette lives in `theme.json` and reaches the stylesheet at runtime
  as `--wp--preset--color--*`. A hex in Sass is a second source of truth and it will
  drift the first time someone tweaks the palette in the site editor.
- Compile-time-only values, breakpoints, content width, mixin parameters, are fine as
  SCSS variables. `assets/styles/generated/_theme-json-tokens.scss` is generated from
  `theme.json`; never edit it by hand. Run `npm run tokens` and commit the result. CI
  regenerates and fails on drift.
- No `.scss` file is ever enqueued from PHP. CI asserts this.

---

## 7. Performance budget, quality-adjacent version

These numbers are the standard a change is measured against. Report a finding when a
change threatens one of them.

| Metric | Target |
|---|---|
| Lighthouse Performance | 95 or better mobile, 98 or better desktop |
| LCP | under 1.8s |
| INP | under 150ms |
| CLS | **0.05 or lower** |
| TTFB | under 400ms at p75 |
| TBT | 100ms or lower |
| Front end JS | 150KB compressed or less |
| CSS | 50KB compressed or less |
| Web fonts | 2 maximum, self-hosted WOFF2 |
| jQuery | none, anywhere |

Font fallbacks must be metric-matched with `size-adjust`, `ascent-override`,
`descent-override`, and `line-gap-override`. Multiple fallbacks are required, not one.
The reason is concrete: Arial, the usual second choice in a fallback stack, does not
exist on Android, so a stack that relies on it silently drops to a wider default and
re-flows the page, which shows up directly as CLS.

The full performance reasoning, including the `no_found_rows` and cache-priming traps,
is in `.audit-prompts/performance-efficiency.md`.

---

## 8. What good output looks like

Rank findings by severity: blocking first (a rule above is broken), then warning, then
suggestion. For each:

- file and line
- the rule broken, by name
- why it matters here, specifically, not generically
- the exact replacement code, written so it passes `phpcs.xml.dist` cleanly

Do not pad the report with praise, do not summarise files you found no issues in, and
do not raise a concern you cannot point at. If the diff is clean, say it is clean.
That is a useful answer and a short one.