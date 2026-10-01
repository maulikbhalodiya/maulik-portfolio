# Error handling and resilience

Review target: the `maulik-dev` WordPress **block theme**. A theme that fails loudly
and locally is fixable. A theme that fails quietly produces a blank page, a white
footer, or a silent data problem that someone notices months later.

---

## 1. Reporting standard

1. **Report only what you can point at.** Every finding needs a file path and a line
   number. No file and no line means no finding. Do not report speculative concerns
   about code that does not exist.
2. **Cite the exact sniff name** where one applies, for example
   `WordPress.PHP.DevelopmentFunctions.error_log_error_log`.
3. **Never propose `phpcs:ignore` or a ruleset change.** Neither fixes anything.
4. **Never propose weakening or deleting a test or a CI assertion.**
5. **Your proposed code must pass `phpcs.xml.dist` with zero errors and zero warnings.**
6. **No em dashes and no en dashes** in your output. Rephrase with commas, colons,
   parentheses, or full stops. CI greps for U+2014 and U+2013 and fails on any hit, and
   that applies to review comments too.

---

## 2. Critical architecture rules, error handling angle

### 2.1 `render.php` returns an empty string, it never emits whitespace

This is the single most important rule in this document.

A block's `render.php` is `require`d once per block instance. Its entire output is
whatever the file `return`s. So:

```php
<?php
/**
 * Render callback.
 *
 * @package Maulik_Dev
 */

defined( 'ABSPATH' ) || exit;

$wrapper_attributes = get_block_wrapper_attributes();

$content = '';
if ( ! empty( $attributes['label'] ) ) {
	$content = '<p>' . esc_html( $attributes['label'] ) . '</p>';
}

return $content;
```

Return `''` when there is nothing to show. Do not `echo`. And here is the trap that
makes this more than a style rule:

**Any characters printed before the `return` become visible page output.** A single
newline at the end of a PHP file, or a blank line after the opening `<?php`, emits a
literal whitespace node into the DOM. That node sits between the block wrappers, and it
is a direct cause of **CLS**, which is the one Core Web Vital we hold to the tightest
budget in this project at **0.05 or lower**.

So the rules for `render.php` specifically:

- One opening `<?php` tag, and **nothing after it except the docblock and the guard**
  that is followed immediately by code, with no trailing whitespace after the guard
  line.
- Build into a variable, `return` once at the end.
- Return `''` for the empty case so the block wrapper vanishes entirely rather than
  rendering an empty bordered box.
- Guard with `get_block_wrapper_attributes()` and never hand-build
  `class="wp-block-foo"`, because Core needs to inject its own layout classes there.

Flag as blocking: any `echo`, `print`, or `printf` in `render.php`. Any output before
the return. A missing `''` return path.

### 2.2 `render.php` declares nothing

Also from the architecture rules, and the error-handling angle matters here: a
`function` or `class` declared at the top level of `render.php` is declared again on
the second instance of the same block on the page, which is a fatal error, a white
screen, and a 500 in the log with a stack trace pointing at the wrong place.

`require_once` from `inc/`, or use the `render_$block_type` naming convention so each
function is declared exactly once per request.

### 2.3 Patterns run on `init`, so error handling there is different

A pattern is included during `init`. That means:

- There is no query context, so there is nothing to fail on, but there is also no
  meaningful "not found" state. A pattern that tries to branch on
  `is_single()` is a bug, not a fallback.
- Calling a function that depends on request state returns something meaningless, not
  an error. This is the more insidious failure: no exception, no warning, just a wrong
  value rendered into the page.
- Allowed: `esc_html_e()`, `esc_html()`, `esc_attr()`, `get_theme_file_uri()`,
  `get_bloginfo()`, iteration over static data.

If you find a query conditional or a loop function in `patterns/`, that is the finding.
Report it as blocking.

### 2.4 Every PHP file opens with the guard

```php
defined( 'ABSPATH' ) || exit;
```

Direct request access on an included PHP file can expose internals or trigger a fatal
against an undefined function. The one exception is a namespaced file, where
`namespace X;` must come first because PHP requires it to be the very first
statement, as in `inc/helpers.php`.

---

## 3. `WP_Error` handling

Every WordPress API function that can fail returns `WP_Error` rather than throwing. The
default behaviour when you ignore the return value is silent failure, and silent
failure is the failure mode this document exists to prevent.

```php
$result = wp_remote_get( $url );  // this theme: not allowed, see section 3.4

if ( is_wp_error( $result ) ) {
	return '';
}
```

Rules:

- **Check every call that can return `WP_Error`**, before using the result. The
  functions most often missed: `wp_remote_get()`, `wp_remote_post()`,
  `wp_insert_post()`, `wp_update_post()`, `wp_delete_post()`, `get_posts()` with a
  `meta_query`, `wp_get_attachment_metadata()`, `wp_insert_term()`,
  `wp_update_term()`, `wp_set_object_terms()`, `wp_insert_user()`, `WP_Image_Editor`
  methods.
- **Do not use `WP_Error` as an exception.** WordPress does not throw. Writing
  `try`/`catch` around a WordPress call gives false confidence that the failure is
  handled.
- **Do not call `wp_die()` on the front end** to handle a recoverable condition. It
  kills the whole page for what is usually one block. Reserve it for genuinely
  unrecoverable cases, and note that even then a `render.php` must return `''` rather
  than dying.
- **Return a sane empty value on the front end.** In `render.php`, that means `''`. In a
  filter callback, return the original unmodified value. In an action with no output,
  return early.

### 3.4 This theme makes no outbound HTTP requests

There are no `wp_remote_get()`, `wp_remote_post()`, `file_get_contents()` against a
remote URL, `curl`, or front end `fetch()` calls anywhere in this theme, and
`should_load_remote_block_patterns` is filtered to `false` to match. Fonts are
self-hosted, there is no analytics, there is no CDN fetch.

If you propose adding one, it is a blocking finding against architecture rule 2.15, not
a suggestion.

### 3.5 Never swallow an error

This covers the cases where no exception is thrown at all.

- **`@` suppression.** Never use the error control operator on a WordPress call. It
  hides the failure and, in PHP 8, it no longer silences fatal errors anyway, so it
  costs you debugging information and buys nothing. Flag every `@`.
- **Empty `catch` blocks.** If you must catch, log and handle. An empty catch block is
  the same as swallowing the error with extra steps.
- **`if ( ! is_wp_error( $x ) ) { use( $x ); }` with no else.** You have decided that
  failure is impossible and provided no evidence. That is the wrong instinct on
  untrusted input.
- **Commented-out validation.** `// TODO: sanitize this` next to an unvalidated value
  ships the vulnerability with a note attached. Flag it as blocking.
- **`Generic.Commenting.Todo`** is enabled in `phpcs.xml.dist` precisely so that TODO
  comments are visible. A new one is a finding.

---

## 4. Defensive coding worth having

Not paranoia. Each of these has caused a real bug on a real site.

### 4.1 Trust nothing, but do not nest defensively

Validate and escape at the output boundary, and do not sprinkle
`isset()`/`empty()` through the function body. Deeply defensive code hides which
invariant actually failed. One check at the boundary, with a clear name, beats five
checks scattered inside.

The exception is array access into `$attributes`, which is genuinely optional and
genuinely absent in many cases:

```php
$label = isset( $attributes['label'] ) ? $attributes['label'] : '';
```

`$attributes['label']` alone will emit a PHP notice on a block with no label set, and
a notice in a `render.php` is output that lands on the page.

### 4.2 Type-juggling

`===` and `!==`, never `==` and `!=`. `'0' == 0` is true in PHP 7 and `'abc' == 0` was
true in PHP 7 too. This theme targets PHP 7.4 minimum, where both of those hold.
Loose comparison has produced authentication and redirect bugs in every codebase that
was not careful about it.

`in_array()` always takes the third strict argument: `in_array( $v, $a, true )`.

### 4.3 Filesystem failures

- `file_get_contents()` on a local path returns `false` on failure. Check it. Every
  function that returns `false` or `null` on failure needs the check.
- `file_exists()` before `filemtime()` is not enough, because the file can exist and be
  unreadable. `is_readable()` is the correct predicate. See
  `maulik_dev_asset_version()` in `inc/assets.php` for the pattern: check
  `is_readable()`, check that `filemtime()` did not return `false`, fall back to the
  theme version. That function is the reference implementation for this pattern.
- `mkdir()` and `mkdir()` failure: check the return, and remember `mkdir()` returning
  `false` because the directory already exists is not an error.

### 4.4 Never emit partial markup

Build the complete string, then return it. Do not start an HTML element, then hit an
error path that returns early, and leave an unclosed tag in the document. Unclosed tags
cause the parser to restructure the entire rest of the page, which produces layout
damage far from the actual cause and CLS damage as a result.

In practice this means: no `echo '<div>'` followed by an early `return`. Build the
string, validate the inputs, return once.

### 4.5 Templates and parts fail silently by design, so constrain them

`templates/*.html` and `parts/*.html` cannot execute PHP and cannot branch on state. If
something in a part needs to be conditional, the correct answer is a pattern or a block,
not PHP smuggled into an HTML file. A `.html` template part containing `<?php` will
render the PHP tags as literal text, which is a visible, embarrassing, hard to trace
failure.

---

## 5. The `noindex` gate

`inc/seo.php` must stay safe by construction. The development flag is enabled only when
**both** conditions hold:

1. `WP_DEBUG` is true, which WordPress sets only on development installs
2. `MAULIK_DEV_NOINDEX` is explicitly defined and true, **or** the
   `maulik_dev_noindex` option is truthy

The point of the double gate is that no single stray constant or option can deindex a
production site. The trap being defended against is a leftover `define()` in a
`wp-config.php` that gets deployed.

Report as blocking: removing the `WP_DEBUG` check, adding an `is_admin()` style bypass
that weakens it, or any change that lets one flag alone enable the tag.

---

## 6. Performance resilience

A theme that ignores the performance budget degrades into an outage under traffic, so
these belong in an error-handling review as well.

- **CLS is 0.05 or lower.** The two things that break it are emitted whitespace and
  late-loading fonts and images. See section 2.1 for the whitespace cause.
- **The asset filters must both be `true`.** `should_load_separate_core_block_assets` and
  `should_load_block_assets_on_demand`, in `inc/setup.php`. Without them the front end
  ships every core block asset on every page. That is a performance defect and, on a
  slow connection, a user-visible failure.
- **`filemtime()` versioning for assets** so a cache bust does not depend on a human
  remembering to bump a version string, and so it degrades to the theme version when the
  file is missing from a distributed ZIP.
- **Never branch a template on `is_user_logged_in()`.** A page cached for anonymous users
  then serves logged-in markup to everyone, which is a correctness bug that shows up as
  a security bug, or the reverse, which shows up as an admin bar that never appears.
  Keep templates identical for all users.
- **The LCP image needs `fetchpriority="high"`.** Without it the browser treats the
  largest above the fold image as an ordinary image and it becomes the LCP element by
  default order, not by priority.

The full budget and the query-level traps, including `no_found_rows` and the N+1 cache
priming trap, are in `.audit-prompts/performance-efficiency.md`.

---

## 7. Content rules that produce silent quality failures

- **No em dashes, no en dashes.** Anywhere: code, comments, copy, docs, commit messages.
  CI greps the repository for U+2014 and U+2013. This applies to your own output.
- **No raw email addresses, no `mailto` links.** Contact is a form with an intent
  dropdown. Addresses get scraped within hours of a site going live.
- **No skill percentage bars, progress meters, or star ratings.** There is no data
  source for them, so they are decorative lies.
- **Never create `block-templates/` or `block-template-parts/`.** Core's `file_exists()`
  check switches to legacy handling if either exists, and it breaks `templates/` with no
  error message. If a diff adds one, that is blocking.

---

## 8. What good output looks like

Blocking first, then warning, then suggestion. For each finding: file and line, the
rule broken by name, why it matters here specifically, and replacement code that passes
`phpcs.xml.dist` cleanly.

Do not pad with praise. Do not summarise files with no issues. Do not raise a concern
you cannot point at. If the diff is clean, say so in one line and stop.