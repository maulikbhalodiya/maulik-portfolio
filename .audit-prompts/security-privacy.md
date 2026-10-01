# Security and privacy

Review target: the `maulik-portfolio` WordPress **block theme**. This theme is a public
portfolio with a public contact form. There is no user data to store, no accounts to
protect, and no custom database schema, which means the entire security surface is a
short list that can be held to a high standard.

---

## 1. Reporting standard

1. **Report only what you can point at.** Every finding needs a file path and a line
   number. If you cannot produce both, you do not have a finding, do not report it.
2. **Cite the exact sniff name.** For example `WordPress.Security.NonceVerification`,
   `WordPress.Security.EscapeOutput`, `WordPress.DB.PreparedSQL`. A finding nobody can
   reproduce with one command is not a finding.
3. **Never propose `phpcs:ignore` or a ruleset change.** Suppressing a security sniff is
   strictly worse than the violation it hides, because it also disables the check for
   the next person.
4. **Never propose weakening or deleting a test or a CI assertion.**
5. **Your proposed code must pass `phpcs.xml.dist` with zero errors and zero warnings.**
6. **Severity matters.** Say whether an issue is exploitable by an anonymous visitor, by
   a subscriber, or by an administrator. "Security issue" on its own is not useful.

---

## 2. Critical architecture rules, security angle

### 2.1 WPCS 3.4.1 or later, and why CI linting is itself an attack surface

`wp-coding-standards/wpcs` must be pinned to `^3.4.1`. At or below 3.4.0 it is
vulnerable to **GHSA-3pwp-g2mj-5p3v**, a remote code execution issue triggered when
PHP_CodeSniffer lints untrusted code. CI in this repository lints pull requests,
including pull requests from forks, which is precisely the scenario the advisory
describes. A fork PR is attacker-controlled code being linted on infrastructure with
repository write tokens available to the workflow.

There is a CI assertion that fails the build below 3.4.1. Never lower the constraint.
Never propose removing that assertion. If a Dependabot PR wants to move backwards,
reject it.

Related and equally deliberate: the AI review and AI audit workflows **refuse to act on
fork pull requests**. The `fix` job compares `headRepositoryOwner.login` to the
repository owner and exits if they differ. This is the same class of problem: a
workflow with write permissions must not execute code from a fork.

### 2.2 No outbound HTTP requests

`should_load_remote_block_patterns` is filtered to `false`. There are no
`wp_remote_get()`, `wp_remote_post()`, `file_get_contents()` against a remote URL, no
`curl`, and no front end `fetch()`.

Remote pattern loading is a privacy leak by design: it tells a third party server which
WordPress install is rendering and from where, on every request. Combined with a slow or
hostile third party, it is also an unbounded latency dependency.

**No analytics, no tracking pixels, no third party embeds, no CDN.** Fonts are
self-hosted. This keeps the privacy story something the site can honestly state in one
sentence.

Report any proposal to add a remote call as blocking.

### 2.3 Never emit a raw email address or a `mailto` link

Contact is a form with an intent dropdown. Not an address in the markup, not a `mailto`
link, not an obfuscated address, not a contact page that renders the address from a
constant.

The reason is not theoretical. Automated scrapers crawl the entire public web looking
for the pattern `[a-z0-9._%-]+@[a-z0-9.-]+\.[a-z]{2,}`, and they find a public address
within hours of a site going live. Obfuscation does not help, the patterns are good. The
only structural answer is to not have the address in the HTML.

CI greps the repository for `mailto` and for an email regex and fails on any hit. If
your suggestion introduces either, it is blocking.

### 2.4 No skill bars, progress meters, or star ratings

Not a security rule, but it is listed here because it belongs to the same family of
things that look like data and are not. A percentage has no source, so it is a claim the
site cannot substantiate and cannot verify. Keep it qualitative or make the number real.

### 2.5 No custom post types, no taxonomies, no shortcodes, no form handling in the scaffold

Project data lives in block attributes inside post content. That is not a stylistic
preference: it means the theme introduces no database tables, no custom tables with
their own `$wpdb` query surface, and no unauthenticated write endpoints.

Shortcodes and form handlers are where injection bugs live. Flag any proposal to add
them as blocking.

### 2.6 `block.json` attributes are untrusted at render time

`"type": "string"` is a type assertion used by the editor and the REST schema. It
performs **no** validation and **no** escaping. Attribute values reach `render.php`
exactly as stored, and any user who can edit a post can set any attribute value,
including HTML.

So: escape at the point of output, for the context you are outputting into. Never assume
a value is safe because `block.json` described it.

### 2.7 Every PHP file opens with the guard

```php
defined( 'ABSPATH' ) || exit;
```

Without it, a direct request to an included PHP file can expose internals or trigger a
fatal. The single exception is a namespaced file, where `namespace X;` must be the very
first statement, as in `inc/helpers.php`.

### 2.8 `render.php` must be side-effect free

`render.php` is included once per block instance. A `function` or `class` declared at
its top level is declared again on the next instance, a fatal error, and a 500 with a
stack trace pointing at the wrong file. `require_once` from `inc/` instead.

### 2.9 The `noindex` gate must stay double gated

`inc/seo.php` enables `noindex, nofollow` only when `WP_DEBUG` **and** an explicit
opt-in are both true. The double gate is deliberate: a single leftover constant in
`wp-config.php` cannot deindex production. Removing the `WP_DEBUG` check is blocking.

### 2.10 No em dashes, no en dashes

Content rule, CI enforced. Applies to your own review output too.

---

## 3. Authentication: nonce AND capability, never one alone

This is the rule most often half-implemented in WordPress code, so state it plainly.

**On every write path, check both:**

1. **The nonce**, which proves the request came from a page this user actually loaded
   and was not forged from a third party site.
2. **The capability**, which proves this specific user is allowed to do this specific
   thing.

Either one alone is insufficient:

- **Nonce alone** is a CSRF defence and nothing more. Any logged-in user can generate a
  valid nonce. A Subscriber could obtain one and use it to do something only an
  Administrator should be able to do.
- **Capability alone** is missing the CSRF defence entirely. An attacker cannot forge a
  capability, but they can ride an authenticated administrator's browser to a page they
  control and have that browser submit the request.

```php
if ( ! isset( $_POST['maulik_portfolio_nonce'] )
	|| ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['maulik_portfolio_nonce'] ) ), 'maulik_portfolio_save' )
) {
	wp_die( esc_html__( 'Security check failed.', 'maulik-portfolio' ), 403 );
}

if ( ! current_user_can( 'edit_posts' ) ) {
	wp_die( esc_html__( 'You are not allowed to do that.', 'maulik-portfolio' ), 403 );
}
```

Nonce actions must be **specific**. `wp_create_nonce( 'maulik_portfolio_save' )`, never
`wp_create_nonce( 'nonce' )` and never a nonce name that is reused across unrelated
actions. A shared nonce means a CSRF token from one form is valid on another.

Never read a nonce from `$_GET` on a state-changing request. Nonces in URLs leak through
Referer headers, browser history, and access logs.

---

## 4. Capabilities, never roles

Check `current_user_can()`, never `current_user_can( 'administrator' )` or any
`user_can( $user, 'role' )` comparison.

- **Roles are not a permission model.** A role is a bundle of capabilities that changes
  between WordPress versions and between plugins that add roles. Code that checks roles
  breaks the moment a site adds a custom role or a capability-mapping plugin.
- **Capabilities are granular.** `edit_posts`, `manage_options`, `edit_theme_options`
  are the real permission checks and they survive role customisation.
- **Custom capabilities must be declared** with `map_meta_cap` and granted in the theme
  setup, rather than assuming the role name exists.

---

## 5. Escaping late, per context

Sanitize on the way in, escape on the way out, and **escape at the point of output**
rather than at the start of the data flow. Escaping early means the value carries
escaped entities through the rest of the code, and then somebody adds a second layer of
escaping at the output and the user sees `&amp;amp;`.

| Output context | Function |
|---|---|
| HTML text node | `esc_html()` |
| HTML attribute | `esc_attr()` |
| JavaScript string, JSON array, `<script>` block | `wp_json_encode()` |
| URL in `href` or `src` | `esc_url()` |
| URL going into a database query or a redirect | `esc_url_raw()` |
| A database value | `$wpdb->prepare()` |
| A term or post title on output | `esc_html()` |
| Text handed to translators | `translate()` then escape the result |
| HTML with **no** escaping needed | `wp_kses_post()` when third party HTML is genuinely required |

Common mistakes to report as blocking:

- `echo $value` where the value came from anywhere. `WordPress.Security.EscapeOutput`.
- `echo esc_html( $x )` where `$x` is then used again unescaped in an attribute later.
  That is the classic "escaped once, used twice" bug, and it is an XSS.
- `esc_attr()` on a value that lands in a URL. Wrong function; use `esc_url()`.
- `esc_url()` on a value that lands in an attribute alongside other text. Use
  `esc_attr()` for the whole attribute.
- **Double escaping.** Escaping a value that is already escaped produces visible
  `&amp;` and `&#039;` in the page. If output shows literal entities, look for a
  second `esc_html()`.
- Outputting a block attribute with `echo` at all. Use the escaped form for its context.

---

## 6. Input handling

### 6.1 `wp_unslash()` always, before sanitizing

WordPress adds slashes to all superglobals. `$_POST` and `$_GET` arrive slashed.
Sanitizing **before** unslashing means the sanitizer sees backslashes it does not expect
and strips or mangles legitimate values.

The order is always:

```php
$value = isset( $_POST['field'] )
	? sanitize_text_field( wp_unslash( $_POST['field'] ) )
	: '';
```

`wp_unslash()` first, then the context-specific sanitizer. Not the other way round.
`WordPress.Security.ValidatedSanitizedInput` catches a lot of this and is enabled here
via `WordPress-Extra`.

### 6.2 Sanitize per context, not with a blanket call

| Input | Sanitizer |
|---|---|
| Single line text | `sanitize_text_field()` |
| Multi line text | `sanitize_textarea_field()` |
| Integer | `absint()` |
| Key or identifier | `sanitize_key()` |
| Email | `sanitize_email()` |
| URL for storage | `esc_url_raw()` |
| HTML that must keep formatting | `wp_kses_post()` |
| SQL | never a sanitizer, use `$wpdb->prepare()` |
| Slug | `sanitize_title()` |

`sanitize_text_field()` is not an HTML sanitizer and is not a SQL sanitizer. Using it as
a general purpose "make it safe" call is a common and serious mistake. Say which context
you are in.

### 6.3 Validate shape and presence

Sanitizing does not validate. A sanitizer on an array value returns an empty string
rather than telling you the input was wrong.

```php
$raw = isset( $_POST['field'] ) ? wp_unslash( $_POST['field'] ) : '';

if ( ! is_string( $raw ) ) {
	return new WP_Error( 'maulik_portfolio_invalid', __( 'Invalid input.', 'maulik-portfolio' ) );
}
```

Always `isset()` or `array_key_exists()` before indexing a superglobal. Direct access to
a missing key emits a notice, and a notice in `render.php` becomes output on the page.

---

## 7. SQL

This theme does not currently run custom SQL. If it ever must, all of the following
applies and the absence of `$wpdb->prepare()` is a blocking finding:

```php
global $wpdb;

$results = $wpdb->get_results(
	$wpdb->prepare(
		"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %d",
		$key,
		$value
	)
);
```

- **Every** value goes through `%s`, `%d`, or `%f`. Never string-concatenate a value
  into SQL. `$wpdb->prepare()` is the only correct answer and there is no exception
  that makes concatenating acceptable.
- **Identifiers** (table name, column name, `ORDER BY` direction) cannot be passed as
  placeholders. Whitelist them against an explicit array. Never pass an identifier
  straight from user input.
- Use `{$wpdb->posts}`, `{$wpdb->postmeta}` rather than literal table names, so the
  prefix is correct on every install. `$wpdb->prefix . 'table'` in a query string is a
  common and real bug.
- `esc_sql()` is **not** a substitute for `prepare()`. It is a string escaper for
  identifiers and table names only, and using it on a value gives false confidence.
- Prefer `WP_Query`, `get_posts()`, or `get_terms()` over hand written SQL in nearly
  every case. They cache, they escape, and they handle the prefix.
- `PHPCompatibilityWP` and `WordPress.DB.PreparedSQL` are both enabled here.

---

## 8. Filesystem and includes

- **`get_theme_file_uri()` and `get_theme_file_path()` always.**
  **Never `get_template_directory_uri()` or `get_template_directory()`.** The
  `template` functions resolve to the **parent** theme and bypass the child theme
  entirely, so a child theme can never override anything they return. In theme code use
  `get_parent_theme_file_path()`, which resolves against the parent but stays
  child-overridable.
- `include` and `require` with a **variable** path must be preceded by
  `realpath()` or an equivalent check that the resolved path is inside the theme
  directory. Otherwise a tainted value reads an arbitrary file.
- Use `require_once` in `functions.php`, and never a variable path at all there.
- Never `unlink()`, `file_put_contents()`, `fopen()` with a write mode, or
  `move_uploaded_file()` in theme code. Themes do not write files.
- `wp_upload_dir()`, not hardcoded `/wp-content/uploads/`.
- No `eval`, no `create_function()`, no `call_user_func()` on a user-supplied
  callable, no variable variables.

### 8.1 Theme Check banned functions

WordPress.org Theme Check rejects all of these outright, and this repository holds
itself to that standard as a discipline:

`eval`, `base64_decode`, `str_rot13`, `uudecode`, `system`, `exec`, `passthru`,
`shell_exec`, `popen`

Plus, in the same spirit though not on the literal list: `proc_open`, `pcntl_exec`,
`assert`, `create_function`, `preg_replace` with the `/e` modifier, and any direct
`$GLOBALS` assignment.

None of these have a legitimate use in a block theme portfolio. Any occurrence is
blocking.

---

## 9. Theme Check and repository hygiene

Theme Check is the closest thing to a security and quality gate for a WordPress theme,
and this repository runs its rules by discipline even though it is not submitting.

- **Readme header fields complete** in `style.css`: `Requires at least`, `Tested up to`,
  `Requires PHP`, `License`, `License URI`, `Text Domain`, `Tags`. Theme Check fails on
  missing or malformed ones. Current values: WordPress 6.7 and up, tested to 6.9, PHP
  7.4, GPL-2.0-or-later, `maulik-portfolio`.
- **No `screenshot.png` placeholder hacks.** `screenshot.png` must be 1200x900. The file
  is intentionally absent from the scaffold and is a release blocker, documented in
  `README.md`. Do not commit a fake or a stock image to satisfy a checker.
- **`screenshot.png` must not contain a browser chrome mockup.** Real themes screenshot
  the theme as it appears, without fake browser UI.
- No `readme.txt` in a separate format, no bundled plugin code, no minified third party
  libraries without their licence, no tracker.
- **`style.css` must contain only theme headers.** It is not auto-enqueued in a block
  theme, which is why `inc/assets.php` enqueues it explicitly. Do not assume Core loads
  it.

---

## 10. Content rules CI enforces

These are cheap to satisfy and worth stating so they are not violated accidentally:

- **No em dashes, no en dashes** anywhere. CI greps for U+2014 and U+2013.
- **No `mailto`** anywhere.
- **No raw email address** anywhere.
- **No `.scss` file enqueued from PHP.** The browser cannot execute Sass.

---

## 11. Output formatting rules worth being strict about

- Text domain is exactly `maulik-portfolio`. Not `maulik_portfolio`, not `MaulikPortfolio`.
- Every user-facing string goes through `esc_html_e()`, `esc_attr_e()`, `esc_html__()`,
  `esc_attr__()`, `translate()`, or `_x()` with context.
- Every string carries a `/* translators: */` comment when a translator could plausibly
  get it wrong, especially when it contains a placeholder.
- Never build a sentence by concatenating translated fragments. Use placeholders inside
  one translatable string, or `sprintf()` with a translated format string. Several
  locales need to reorder grammatical parts and concatenation makes that impossible.
- **Palette entries in `theme.json` must be appended to, never reordered.** Translation
  of theme.json names uses the JSON key path as the gettext context, so reordering an
  entry silently detaches its translations. This is documented in
  `languages/maulik-portfolio.pot`.
- WordPress.org review requires **no gendered language** in user-facing copy. Use "they"
  or restructure. Applies to the site copy and to the portfolio content.

---

## 12. What good output looks like

Ranked by exploitability: anonymous visitor first, then authenticated low privilege user
(subscriber, contributor), then editor, then administrator. Say which tier can exploit
it.

For each finding: file and line, the sniff or rule by name, the concrete attack path,
and replacement code that passes `phpcs.xml.dist` cleanly.

Do not pad with praise. Do not report a theoretical concern on a file that does not
exist. If the diff is clean, say so in one line and stop.