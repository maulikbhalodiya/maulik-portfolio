# Performance and efficiency

Review target: the `maulik-portfolio` WordPress **block theme**. Performance here is not a
nice-to-have. It is the reason this theme exists in the shape it does, and the budgets
below are hard targets that CI and the audit both check.

---

## 1. Reporting standard

1. **Report only what you can point at.** Every finding needs a file path and a line
   number. No file and no line means no finding, do not report it.
2. **Cite the exact sniff name** where one applies, for example
   `WordPress.WP.QueryArgs.NoFound`.
3. **Never propose `phpcs:ignore` or a ruleset change.**
4. **Never propose weakening or deleting a test or a CI assertion.**
5. **Your proposed code must pass `phpcs.xml.dist` with zero errors and zero warnings.**
6. **Quantify the finding.** Say what the change costs: bytes, queries, milliseconds,
   which budget line it threatens. A performance finding with no number attached is a
   guess.
7. **No em dashes and no en dashes** in your output. Rephrase. CI greps for U+2014 and
   U+2013.

---

## 2. The budget

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

The theme must remain usable and score well on a mid-range Android device over a slow
4G connection. Assume the reader is on mobile and on a budget phone, because for a
personal portfolio that is the majority of the traffic.

---

## 3. The two asset filters, the biggest single lever

These two must both be filtered to `true`, in `inc/setup.php`:

```php
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
add_filter( 'should_load_block_assets_on_demand', '__return_true' );
```

What they do, and why it is the biggest win available:

- **`should_load_separate_core_block_assets`** stops Core from concatenating every core
  block stylesheet into one global stylesheet loaded on every page. Instead each block
  gets its own stylesheet and Core enqueues only the ones the current template renders.
- **`should_load_block_assets_on_demand`** stops Core from enqueuing every registered
  block script up front, and defers to the blocks actually present.

A typical portfolio page renders maybe a dozen distinct blocks out of the ninety or so
core registers. Without these filters, the visitor downloads the whole catalogue on
every page. That is usually 100KB or more of JavaScript and CSS that is never executed.

Remove either filter and report it as blocking. It is the single highest severity
performance finding in this codebase.

For `should_load_separate_core_block_assets` to work correctly with this theme's own
stylesheet, the handle must have a known path. That is what this does in
`inc/assets.php`:

```php
wp_style_add_data( 'maulik-portfolio-style', 'path', get_parent_theme_file_path( 'style.css' ) );
```

Core uses that path to build per-block style dependencies. Without it, Core cannot see
the relationship and the on-demand heuristics do not work for this theme.

`should_load_remote_block_patterns` must be filtered to `false`. This theme makes no
outbound HTTP requests, and a remote pattern fetch is unbounded latency on someone
else's server plus a privacy leak.

---

## 4. Query performance

### 4.1 `no_found_rows => true`

```php
$query = new WP_Query(
	array(
		'no_found_rows' => true,
	)
);
```

By default `WP_Query::get_posts()` runs a **second** `SELECT FOUND_ROWS()` query to
find the total row count, which exists only so that pagination can render "Page 2 of 7".
That is one extra full-table-count query per query, on every request, forever.

Set `no_found_rows => true` unless pagination is genuinely rendering. On a
`no_found_rows => true` query, `max_num_pages` is `0`, so `paginate_links()` renders
nothing. If you need both, you need the count.

Also `update_post_meta_cache => false` and `update_post_term_cache => false` when you
genuinely do not need them, but read section 4.2 before setting the last one.

### 4.2 The N+1 cache priming trap, and why `update_post_term_cache => false` is worse than it looks

The intuition behind `no_found_rows` is sound: the cache priming queries that
`WP_Query` runs generate hundreds of queries on a site with many terms. So setting
`update_post_term_cache => false` looks like the obvious next step. Do not, without
understanding the following.

Here is what happens with `update_post_term_cache => false`.

1. `WP_Query` returns post IDs only. No term data is primed.
2. The template calls `the_terms()` or `get_the_terms()` for the first post.
3. `get_the_terms()` needs the term relationships for that post. They are not in the
   object cache, so it issues a query.
4. The template calls `get_the_terms()` for the second post. Another query.
5. And again for the third. And the fourth.

**N queries for N posts.** The original single cache priming query has been converted
into one query per item in the loop. This is strictly worse than not touching the flag
at all, because now the queries are spread across template execution where they cannot
be batched, and they are invisible to anyone reading the `WP_Query` call.

Now the part that surprises people, and the reason it is called out separately:
**setting `update_post_term_cache => false` also disables `lazy_load_term_meta`.**

`lazy_load_term_meta` is the flag that keeps Core from running a metadata query for
every term. It defaults to `true` and it is why term queries are not themselves an N+1
storm. But `update_post_term_cache` is the gate for that priming. Turn it off and
`lazy_load_term_meta` has nothing to lazily load from, so term metadata queries come
back too, on top of the per-post term queries you just created.

**The correct answer is almost always: leave `update_post_term_cache` at its default.**

If a real query genuinely needs fewer queries, the fixes that work are:

- Limit `posts_per_page` to what the page actually shows.
- Set `fields => 'ids'` when you only need IDs.
- Use `'no_found_rows' => true`.
- Reduce the number of terms queried by making the taxonomy smaller.

Turn `update_post_term_cache => false` only for a query whose posts you are certain
you will never call `get_the_terms()` on inside the loop, and say in a comment why you
are certain.

### 4.3 Avoid `is_user_logged_in()` branches in templates

A template that branches on `is_user_logged_in()` produces different HTML for
different users from the same cache entry. Object caching is keyed by URL, not by user,
so the first render wins and gets served to everybody.

Two failure directions, and both have been real incidents:

- **Admin bar leaked to everyone.** A logged-in user's render fills the cache, every
  anonymous visitor receives markup containing the admin bar and links to
  `wp-admin`. That is a security incident.
- **Content leaked to anonymous users.** The reverse. The logged-in view gets cached and
  served to everyone, including private content.

The same trap applies to any request-varying condition in a cached page: cookies,
geolocation, `is_admin()`, a user role check, `get_current_user_id()`.

Keep templates identical for all users. If something genuinely differs per user, it
belongs in a block that renders on demand, not in a template branch. Core's own solution
to the admin bar problem is the admin bar being printed in `wp_footer()`, and that is
the pattern to follow.

### 4.4 No `cURL`, no `file_get_contents()` for remote URLs

There are no outbound HTTP requests in this theme. Fonts are self-hosted, there is no
analytics, there is no CDN. Every remote fetch is an unbounded latency risk on a
third party's uptime and a privacy leak. Report any proposal to add one as blocking.

---

## 5. Assets

### 5.1 CSS

Budget: **50KB compressed**. Compiled from `assets/styles/main.scss` with the Dart Sass
CLI:

```
npm run build:css
```

- One entry point, `assets/styles/main.scss`. Nothing else is compiled directly.
- **`@use` and `@forward` only, never `@import`.** Deprecated in Dart Sass 1.80.0,
  removed in 3.0.0. Each directory is a partial with an `_index.scss` that forwards
  its partials.
- **Never duplicate a theme.json token as a hex value in Sass.** Colours, fonts and
  spacing arrive at runtime as `--wp--preset--*`. A hex in Sass is a second source of
  truth and it drifts.
- **No `.scss` file is enqueued from PHP.** The browser cannot execute Sass. CI asserts
  this.
- Compiled `style.css` is committed, and `/build/` and `/assets/css/` are deliberately
  **not** in `.gitignore`, so any checkout can produce the release ZIP.

### 5.2 JavaScript

Budget: **150KB compressed**. Reality: this theme should ship close to zero front end
JavaScript.

**No jQuery, anywhere, ever.** WordPress still ships jQuery on the front end and it is
the single largest JS cost most themes pay for. This theme does not use it, does not
depend on it, and does not enqueue it. The only JavaScript in the repository is the
build-time SCSS token generator in `scripts/`, which never reaches a browser.

No front end build step. `@wordpress/scripts` is present for stylelint, eslint and
formatting only, **not** for compiling SCSS, because stock wp-scripts has no
`includePaths`, will not compile a standalone `style.scss`, and does not expose
`silenceDeprecations`.

### 5.3 Images

- The **LCP image needs `fetchpriority="high"`.** The browser does not infer which
  image is the largest contentful paint element. It guesses based on order and
  heuristics, so the hero image competes on equal terms with a small logo above it and
  often loses. This single attribute is usually the difference between an LCP of 1.2s
  and one of 2.4s. It is a block attribute in the Core Image and Cover blocks.
- Always set `width` and `height`, or an `aspect-ratio`, so the browser reserves space.
  A missing intrinsic size is the most common cause of CLS, and CLS is the metric with
  the tightest budget here at 0.05.
- Use `loading="lazy"` on below-the-fold images, but **never** on the LCP image. Lazy
  loading the LCP image makes it slower, not faster, because you have told the browser
  not to fetch the thing the page is waiting for.

---

## 6. Fonts, and the CLS trap

Budget: **2 web fonts maximum**, self-hosted, WOFF2.

The reason this is written so precisely is a concrete mobile failure that is easy to get
wrong:

**Arial, the usual second entry in a fallback font stack, does not exist on Android.**
A stack like `Inter, Arial, sans-serif` looks correct in a desktop dev environment and
on iOS, and silently falls through to the Android system default on Android. That
default has different metrics, which re-flows every line of text on the page after the
web font loads. The result is a large layout shift, and CLS is the metric this project
holds to 0.05 or lower. **A "reasonable looking" fallback stack is a CLS bug on Android.**

The fix is metric-matched fallback `@font-face` declarations using
`size-adjust`, `ascent-override`, `descent-override`, and `line-gap-override`, and
**multiple fallbacks are required**, not one. Each fallback is adjusted to occupy the
same space as the web font, so no reflow occurs whichever one actually resolves.

```css
@font-face {
	font-family: "Inter Fallback";
	src: local( "Arial" );
	ascent-override: 90.20%;
	descent-override: 22.48%;
	line-gap-override: 0.00%;
	size-adjust: 107.40%;
}
```

The stack must then be ordered web font first, then every metric-matched fallback, then
a generic `sans-serif` as the last resort.

Other font rules:

- **Never fetch fonts from a third party.** No Google Fonts, no Bunny, no CDN. Self-host
  the WOFF2 files. A third party font request is a render blocking connection to
  somebody else's uptime, and it hands them a log of every visitor.
- Self-host to avoid the extra DNS lookup, TLS handshake, and connection setup that cost
  more than the font bytes.
- **Subset aggressively.** Latin only unless the copy needs more. Subsetting a 200KB
  family to 30KB is a larger win than any other single optimisation in this list.
- **`font-display: swap`** so text is visible while the font loads. Pair it with the
  metric-matched fallbacks above: `swap` without them is exactly what causes the CLS.
- Use at most two weights per family. Every additional weight is another file and
  another potential shift.

---

## 7. Caching and TTFB

Target: **TTFB under 400ms at p75**.

- **Version assets with `filemtime()`.** See `maulik_portfolio_asset_version()` in
  `inc/assets.php` for the reference implementation. It checks `is_readable()`,
  checks that `filemtime()` did not return `false`, and falls back to
  `MAULIK_PORTFOLIO_VERSION` when the file is missing, which is the common case in a
  distributed ZIP.

  The reason for `filemtime()` rather than the theme version alone: bumping a version
  string by hand means every asset re-downloads for every visitor when one file changed,
  and forgetting to bump it means the change never reaches anyone. `filemtime()` gives
  a correct cache key per file with no human involvement.

- **Never generate cache keys from `time()`, `microtime()`, `rand()`, or
  `uniqid()`.** That defeats the cache entirely and is a common way to discover that
  "the page cache is not working" when in fact nothing is cacheable.
- `MAULIK_PORTFOLIO_VERSION` is defined once in `functions.php` and used as the fallback, not
  recomputed per call.

---

## 8. Cache correctness

- **Never `define()` anything inside a function that runs late in the request.** Use
  `defined()` guards, which `functions.php` does for all three constants.
- Object caching is keyed by URL, not by user. See section 4.3. This is the trap that
  leaks private content, so treat any user-varying branch in a template as a blocking
  finding, not a warning.

---

## 9. WordPress-specific rules with performance consequences

- **No custom post types, no taxonomies, no shortcodes, no form handling.** Project
  data lives in block attributes inside post content, which needs no database schema and
  no meta queries. Flag any proposal to add them.
- **No `register_block_type()` in PHP.** Block registration is automatic from
  `blocks/*/block.json`. A manual registration for a block that already has a
  `block.json` produces a duplicate registration warning.
- **Never create `block-templates/` or `block-template-parts/`.** Core checks
  `file_exists()` on the stylesheet directory and switches to legacy handling if either
  exists. It is silent and it breaks `templates/` entirely.
- `style.css` and `templates/index.html` must both exist or WordPress does not
  recognise this as a block theme.
- **`render.php` returns `''`, never echoes.** Emitted whitespace becomes visible DOM
  output between block wrappers and pushes CLS above budget. A single trailing newline
  is enough. This is covered in detail in `.audit-prompts/error-handling-resilience.md`.
- **`block.json` `"type": "string"` is a type assertion, not sanitization.** Attribute
  values are fully untrusted at render time and must be escaped per output context.

---

## 10. No em dashes, no en dashes

Anywhere: code, comments, copy, docs, commit messages. CI greps the whole repository for
U+2014 and U+2013 and fails on any hit. This applies to your own review output too.

Also: **no skill percentage bars, no progress meters, no star ratings** in any
suggested markup, and **no raw email addresses or `mailto` links**. Contact is a form
with an intent dropdown.

---

## 11. What good output looks like

Blocking first, then warning, then suggestion. For each finding: file and line, the rule
broken by name, the concrete cost in bytes or queries or milliseconds, which budget
line it threatens, and replacement code that passes `phpcs.xml.dist` cleanly.

Prefer a measured claim to an adjective. "This adds one query per loop iteration and
threatens TTFB" is a finding. "This could be slower" is not.

If the diff is clean, say so in one line and stop.