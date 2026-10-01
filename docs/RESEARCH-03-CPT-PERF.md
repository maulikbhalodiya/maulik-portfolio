# RESEARCH 03: CPT, TAXONOMY, META, PERFORMANCE
### Source: lib-5 lane · official WP developer docs + web.dev · flags uncertainty explicitly

Full detail in the lane output. Condensed here to what changes the build.

---

## 1. THE DECISIVE FINDING FOR US

> **"`custom-fields` support is a hard prerequisite for registered meta to appear in REST."**
> Source: REST API handbook, confirmed in core Trac #47866

Two consequences:

1. `'custom-fields'` in `supports` is **not optional** for a portfolio. Without it, registered meta
   doesn't appear in REST **and won't save** through the REST route.
2. Cost: it re-enables the legacy Custom Fields metabox in the editor, which most people omit
   deliberately. We accept that, or we hide it with `remove_post_type_support()` in the admin only.

## 2. `show_in_rest => true` is non-negotiable

> "Must be true to enable the Gutenberg editor."

Without it the CPT has no editor at all. Same for `register_taxonomy()`: `show_in_rest` is
**required for the block editor's taxonomy panel**. Both default to `false`. Easy to miss.

## 3. The meta `show_in_rest => false` failure mode

This is the classic "my dynamic block renders blank" bug, and it fails **silently**:

1. Key doesn't appear in the `meta` object of `/wp/v2/projects/<id>`: `WP_REST_Meta_Fields`
   skips it entirely.
2. `apiFetch` returns `undefined`. **No error. Blank fields.**
3. Even bypassing REST, the editor's save won't persist it, because
   `WP_REST_Posts_Controller` → `WP_REST_Post_Meta_Fields` only handles REST-exposed fields.

**Our mitigation:** read data in `render.php` and emit markup. No client-side data at all. This
also sidesteps the preloading research gap flagged below.

## 4. `register_taxonomy()` has NO `template` / `template_lock`

I verified the full `WP_Taxonomy` property list from the class reference. Those two arguments
exist **only on `register_post_type()`** (since 5.0). Lots of blog posts claim otherwise.

Term page templates are done with files: `/templates/taxonomy-project_technology.html`.

## 5. Registration order matters for rewrite hierarchy

Register the **taxonomy before the CPT** (taxonomy at priority 0, CPT default 10), otherwise a
nested rewrite slug won't match.

## 6. Double-registration hazard

> Callbacks are attached via `add_filter()` and are **never removed** on re-registration.

Registering the same meta key twice means both sanitize callbacks run chained and both auth
callbacks run, making effective permissions load-order dependent. Guard every call:

```php
if ( ! registered_meta_key_exists( 'post', $key, 'project' ) ) {
    register_post_meta( 'project', $key, $args );
}
```

## 7. `render.php` must be side-effect free

Verbatim from the docs:

> "This file loads for every instance of the block type when rendering the page HTML on the
> server... The simplest way to avoid the risk of errors is to consume that shared logic from
> another file."

**Never declare a function or class inside `render.php`.** Put shared logic in an included file.
This is a real footgun in a theme, because we register blocks from `functions.php` which is loaded
once, but `render.php` is included per block instance.

## 8. Performance targets (web.dev, 2026)

Core Web Vitals, p75, segmented by device:

| Metric | Good | Needs improvement | Poor |
|---|---|---|---|
| LCP | ≤ 2.5s | 2.5 to 4.0s | > 4.0s |
| INP | ≤ 200ms | 200 to 500ms | > 500ms |
| CLS | ≤ 0.1 | 0.1 to 0.25 | > 0.25 |
| TTFB | ≤ 0.8s | 0.8 to 1.8s | > 1.8s |

**Our targets, tighter than the requirement:**
LCP < 1.8s · INP < 150ms · **CLS ≤ 0.05** (half budget) · TTFB < 400ms

Lighthouse 10 weights: TBT 30%, LCP 25%, CLS 25%, FCP 10%, SI 10%. **TBT is the heaviest metric**,
which is why jQuery removal and Interactivity-over-React matters more than micro-optimising.

## 9. Highest-leverage performance lever for us

```php
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
```

Default is `false`: core ships `wp-block-library` (100+ KB uncompressed) as one
render-blocking sheet for every page, including pages using 15 of ~90 blocks. Flipping to `true`
loads per-block CSS only when rendered. **Measure it**: on a two-block page the request overhead
can outweigh the saving.

## 10. Fonts: the metric-override technique (eliminates font CLS)

`size-adjust` + `ascent-override` + `descent-override` + `line-gap-override` declared **on the
fallback** `@font-face`, using the **web font's** metrics. Chromium 87+, Firefox 89+.

`size-adjust = avgCharWidth(webfont) / avgCharWidth(fallback)`

This is the only technique that stops text *reflowing* (different line wrapping), not just
vertical jitter. Chrome's own write-up says it "can effectively eliminate font-related
layout-shifts."

**Multiple fallback faces are required**: `Arial` doesn't exist on Android, so Roboto needs its
own override set.

WordPress-specific trap: **never** put absolute font URLs in `add_editor_style()`.
`get_block_editor_theme_styles()` fetches with `wp_remote_get()`, which silently fails against a
self-signed dev certificate. And relative `@font-face` paths break because the CSS is inlined
without a base URL.

## 11. LCP image trap

Core lazy-loads `img` by default. A lazy LCP image won't load until near-viewport, which alone
can push LCP past 2.5s. Core doesn't know which image is your theme's hero, so we force it:

```php
$attr['loading'] = 'eager';
$attr['fetchpriority'] = 'high';
$attr['decoding'] = 'sync';
```

And always emit intrinsic `width`/`height`, that's where most portfolio CLS actually comes from,
more than fonts.

## 12. `srcset` needs at least two registered sizes

`wp_calculate_image_srcset()` returns `false` when fewer than two sources exist. So we need a
deliberate `add_image_size()` ladder sized to real card dimensions, then `wp media regenerate`
once (existing thumbnails won't have the new sub-sizes).

Sources wider than **2048px are excluded by default** (`max_srcset_image_width` filter). Raise it
if we serve wider heroes.

## 13. `no_found_rows`: the biggest WP_Query win

Skips the `COUNT(*)` entirely. But it conflicts with pagination: set `true` when you don't render
a paginator, `false` when you do. Can't have both.

Related N+1 trap: `update_post_term_cache => false` also forces
`lazy_load_term_meta => false`, which disables term-meta priming and makes **every**
`get_term_meta()` a database hit. Don't set it false unless we read no term meta.

## 14. Filters are server-side, not client-side

The research is explicit that a filterable grid should be **server-side links** (`?tech=php`) with
`<link rel="prefetch">`. That's strictly better for Core Web Vitals than a client-side filter,
needs no JS, and works without JavaScript, which also satisfies our spec §68 requirement.

## 15. Object cache is non-persistent by default

WordPress ships only a request-scoped array implementation. Without a persistent drop-in
(Redis/Memcached/APCu), `wp_cache_*` gives you nothing across requests. The distinction that
matters:

- **Object cache** makes WordPress *do less work*
- **Page cache** makes WordPress *not run at all*

Full page caching **cannot work for logged-in users**: auth cookies plus per-user output
(admin bar, nonces, capabilities) make a URL-only cache key incorrect. Design consequence for us:
**zero logged-in-only front-end behaviour.** A portfolio has no reason to have any.

## 16. Cache-friendly theme rules

1. No `is_user_logged_in()` branches in templates
2. Deterministic output: no stray nonces, random IDs, or per-request timestamps
3. Stable asset versioning so cached HTML points at immutable URLs:
   ```php
   $css = get_theme_file_path( 'assets/css/main.css' );
   wp_enqueue_style( 'maulik-dev-main', get_theme_file_uri( 'assets/css/main.css' ),
       array(), filemtime( $css ) );
   ```
4. No cookies set on front-end GETs
5. Correct `Cache-Control` on HTML

## 17. `viewScript` vs `viewScriptModule`, not interchangeable

> "WordPress scripts and WordPress script modules are not compatible at the moment."

| | `viewScript` | `viewScriptModule` |
|---|---|---|
| Since | 5.9 | 6.5 |
| Emitted as | classic `<script>` | `<script type="module">` |
| Deps on | WP scripts | Script modules |
| Preload | serialised ordering | import map + `modulepreload`, **parallel, no waterfall** |
| Interactivity API | ❌ | ✅ |

Interactivity API scripts **must** go in `viewScriptModule`, never `viewScript`.

For four canvas visuals we need neither: they're self-contained 2D canvas with no framework
dependency. Plain `viewScript` is correct and cheapest.

## 18. Critical CSS

No core API. Build step extracts above-the-fold rules into an inline `<style>`; rest loads async.
Budget **≤ 14KB compressed**. Above that, fix the page rather than the extraction.

Note the interaction: leaving `should_load_separate_core_block_assets` at `false` makes
`wp-block-library` a large render-blocking sheet that dominates the critical path regardless of
what you inline. Fix that first.

---

## 19. Flagged uncertainties (do not treat as verified)

The lane was explicit about these. Most relevant to us:

- **`register_taxonomy()` `template`/`template_lock`**: verified *absent*. High confidence.
- **Front-end `apiFetch` preloading wiring**: `rest_preload_api_request()` is verified but the
  current recommended hook/callback was not retrievable. **We avoid this entirely by rendering
  server-side.**
- **`block.json` `data` field deprecation**, not in the current schema or docs, and **no official
  deprecation notice naming it was found**. Don't cite one.
- **WebP/AVIF output format defaults** and whether `default_quality` exists in the current
  release, not verified. We force formats via the `image_editor_output_format` filter ourselves,
  which is verified.
- **`wp_script_add_data( $handle, 'strategy', 'defer' )`**: exact argument name and accepted
  values not verified. Check before using.
- **Object caching handbook guidance**: all performance handbook URLs 404'd this pass. Claims
  derive from verified `wp_cache_*` references.
- **Lighthouse weights**: "Lighthouse 10" weights returned; current may differ. Indicative.
- **`$url` / `$query` render props**, not documented. Read off `$block` if ever needed.
