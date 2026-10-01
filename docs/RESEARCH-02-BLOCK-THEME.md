# RESEARCH 02: BLOCK THEME ARCHITECTURE
### Source: lib-3 lane · WP Theme Handbook + Block Editor Handbook + `wordpress-develop` trunk source

---

## 1. THE FINDING THAT CHANGES THE PLAN

**Themes get NO automatic block registration.** The lane checked core source
directly: `wp_register_block_types_from_metadata_collection()` exists in
`wp-includes/blocks.php`, but **nothing in core ever calls it for a theme
directory**. No scanner exists in `wp-settings.php`, `default-filters.php`,
`script-loader.php`, or `blocks.php`. Core's own test fixture registers manually:

```php
// core: tests/phpunit/data/themedir1/block-theme/functions.php
add_action( 'init', 'register_theme_blocks' );
function register_theme_blocks() {
    register_block_type( __DIR__ . 'blocks/example-block' );
}
```

Auto-registration of a `blocks/` directory is a `@wordpress/scripts` **plugin**
convention. Widely repeated in blog posts, and wrong for themes.

Auto vs manual, exhaustively:

| Item | Auto? |
|---|---|
| `templates/*.html` | yes |
| `parts/*.html` | yes |
| `patterns/*.php` | yes |
| `styles/*.json` | yes |
| `theme.json` | yes |
| `blocks/*/block.json` | **no, `functions.php` required** |
| Pattern categories | no |
| Block styles | no |
| Block stylesheets | no |

## 2. THE OTHER FINDING: `style.css` IS NOT AUTO-ENQUEUED

Counterintuitive. Block themes get these supports free from
`_add_default_theme_supports()` (runs only `if wp_is_block_theme()`):

- `post-thumbnails`, `responsive-embeds`, `editor-styles`
- `html5` for comment-form, comment-list, search-form, gallery, caption, style, script
- `automatic-feed-links`

But the main stylesheet still needs an explicit enqueue. Twenty Twenty-Five's
pattern, and the `SCRIPT_DEBUG` `.min` suffix trick are worth copying:

```php
add_action( 'wp_enqueue_scripts', 'maulik_portfolio_enqueue_styles' );
function maulik_portfolio_enqueue_styles() {
    $suffix = SCRIPT_DEBUG ? '' : '.min';
    $src    = 'style' . $suffix . '.css';

    wp_enqueue_style(
        'maulik-portfolio-style',
        get_parent_theme_file_uri( $src ),
        array(),
        wp_get_theme()->get( 'Version' )
    );
    wp_style_add_data( 'maulik-portfolio-style', 'path', get_parent_theme_file_path( $src ) );
}
```

`wp_style_add_data( ..., 'path', ... )` is what enables on-demand conditional
loading. Without it the stylesheet loads on every page unconditionally.

**Two filters make per-block asset loading work, and they are the single highest
leverage performance lever available to a block theme:**

```php
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
add_filter( 'should_load_block_assets_on_demand', '__return_true' );
```

## 3. Block theme is detected by exactly two files

```php
// core: WP_Theme::is_block_theme()
$paths_to_index_block_template = array(
    $this->get_file_path( '/templates/index.html' ),
    $this->get_file_path( '/block-templates/index.html' ),
);
```

`style.css` + `templates/index.html`. Nothing else is checked.

**Legacy folder detection is all-or-nothing and keyed on `file_exists()`:**

```php
// core: WP_Theme::get_block_template_folders()
if ( file_exists( $stylesheet_directory . '/block-templates' )
  || file_exists( $stylesheet_directory . '/block-template-parts' ) ) {
    $this->block_template_folders = array(
        'wp_template'      => 'block-templates',
        'wp_template_part' => 'block-template-parts',
    );
}
```

If **either** legacy folder exists, **both** switch. A stray empty
`block-templates/` directory silently breaks `/templates/`. Never create one.

## 4. Template parts cannot be nested

`/parts` must be flat. No `parts/header/mobile.html`
(gutenberg#54279).

Priority order, highest first: user-saved template in DB, child theme
`/templates/`, parent theme `/templates/`, `index`.

**There is no `singular-{post_type}.html` step.** CPT singles go straight from
`single-{post_type}.html` to `singular.html`.

## 5. THE TRAP THAT BREAKS EVERY BLOCK THEME PORT

**Block theme template parts are `.html` and cannot contain PHP. There is no
`get_template_part()` in a block theme.**

| | Classic | Block |
|---|---|---|
| Mechanism | `get_template_part( 'header' )` in `header.php` | `<!-- wp:template-part {"slug":"header"} /-->` |
| Resolution | PHP `include` | `render_block_core_template_part()` resolves the `wp_template_part` post, DB first then theme file, runs `do_blocks()` |
| PHP allowed | yes | **no** |
| i18n | `esc_html_e()` inline | **impossible** |

**So any header or footer content that needs i18n or dynamic PHP must live in a
`patterns/*.php` file, not a part.** This is the most common block theme porting
mistake and it is not detectable from a screenshot.

## 6. Patterns execute at `init`, not at render

Verbatim from the handbook:

> "Your patterns cannot access things like the global query or post… WordPress
> functions like `is_home()`, `is_single()`, `get_post()`, and others are simply
> not ready yet."

Works in a pattern: `esc_html_e()`, `get_theme_file_uri()`,
`get_stylesheet_directory_uri()`, `foreach` over our own data array.

Does not work: `the_title()`, `is_page()`, `get_post()`, the loop.

For anything query-dependent, use the Query Loop block or Block Bindings.

**Pattern registration must be a file on disk** or core emits `_doing_it_wrong`.
Child theme slugs win on collision via an `is_registered()` short-circuit.

`register_block_pattern_category()` on `init` priority 10, unregister on
priority 999.

## 7. theme.json v3 traps

`version: 3` requires WP 6.6+. Setting it **flips two defaults to `true`** that
were `false` in v2:

- `settings.typography.defaultFontSizes`
- `settings.spacing.defaultSpacingSizes`

So defining our own `fontSizes` and `spacingSizes` **without** explicitly setting
those two to `false` makes core's defaults reappear alongside ours. Every
portfolio palette ends up with a stray "Medium" grey. Must be explicit.

Other v3 specifics we will use:

- `useRootPaddingAwareAlignments: true` moves padding off `<body>` onto container
  blocks. **Must be paired with an explicit `styles.spacing.padding` object** or
  it does nothing useful.
- `spacingSizes` and `spacingScale` can coexist; explicit `spacingSizes` win on
  slug collision.
- `settings.layout` accepts **only** `contentSize`, `wideSize`, `allowEditing`,
  `allowCustomContentAndWideSize`. `"layout": {"type": "constrained"}` is
  **invalid there**. Layout type belongs in a block's `layout` attribute or
  `styles.blocks.core.group`.
- `color.defaultPalette: false` is essential. Core's default palette is light
  mode and would break a dark theme.
- `appearanceTools: true` expands into ~15 sub-settings at once. Re-disable
  individually where unwanted, e.g. `{"appearanceTools":true,"position":{"sticky":false}}`.
- A block must declare a support **and** the theme must enable the corresponding
  `theme.json` setting before the UI control appears.

**`settings.renzh` does not exist** in any theme.json version or in the schema.
Treat as an error.

**You cannot customise your own theme's registered block styles via
`theme.json`.** `styles.blocks.<block>.variations.*` only works for
core-registered styles (gutenberg#49630).

## 8. Full-Site Editing: the three things that will bite

**1. DB overrides theme files, permanently.** Any Site Editor save is stored in
`wp_template` / `wp_template_part` and overrides our files. Theme updates
deliver nothing to a user who has edited. To change a shipped template we have to
tell the user to reset it, or ship a version bump that invalidates their copy.

**2. Style variations are a one-way snapshot.** Verbatim: "When you update a
style variation in a future theme release, the user will not receive those
changes if they have already saved the style variation." Design each variation
as a complete, self-contained design system that never needs a theme update.

**3. `templateParts` in `theme.json` is metadata only.** Registering a part there
does not include it anywhere. Every template still needs its own
`<!-- wp:template-part -->`.

Development mode clears the pattern cache:
`define( 'WP_DEVELOPMENT_MODE', 'theme' );` in `wp-config.php`.

Block it from phoning home:
```php
add_filter( 'should_load_remote_block_patterns', '__return_false' );
```

## 9. Custom block registration for a theme

Since themes get no scanner, `functions.php` does it. Preferred, WP 6.8+:

```php
add_action( 'init', 'maulik_portfolio_register_blocks' );
function maulik_portfolio_register_blocks() {
    if ( function_exists( 'wp_register_block_types_from_metadata_collection' )
        && file_exists( MAULIK_PORTFOLIO_DIR . '/build/blocks-manifest.php' ) ) {
        wp_register_block_types_from_metadata_collection(
            MAULIK_PORTFOLIO_DIR . '/build',
            MAULIK_PORTFOLIO_DIR . '/build/blocks-manifest.php'
        );
        return;
    }

    foreach ( glob( MAULIK_PORTFOLIO_DIR . '/build/*', GLOB_ONLYDIR ) as $block_dir ) {
        if ( file_exists( $block_dir . '/block.json' ) ) {
            register_block_type( $block_dir );
        }
    }
}
```

Version matrix: `wp_register_block_metadata_collection()` 6.7,
`wp_register_block_types_from_metadata_collection()` 6.8 and recommended.

**Server-side registration is mandatory**, not an optimisation. Verbatim: "if you
want a block to be styled via `theme.json`, it must be registered on the server.
Otherwise, the block won't recognize or apply any styles assigned to it in
`theme.json`." Without it there is no dynamic rendering, no supports, no block
hooks, no style variations, no `theme.json` styles, no REST block-types entry.

**PHP-only block, zero JS, zero build step** (WP 7.0+, `supports.autoRegister`):

```php
register_block_type( 'maulik-portfolio/server-note', array(
    'render_callback' => function ( $attributes ) {
        return sprintf(
            '<div %1$s>%2$s</div>',
            get_block_wrapper_attributes(),
            esc_html( $attributes['text'] ?? '' )
        );
    },
    'attributes' => array( 'text' => array( 'type' => 'string', 'default' => '' ) ),
    'supports'   => array( 'autoRegister' => true ),
) );
```

`render_callback` is required. Core injects
`window.__unstableAutoRegisterBlocks` before `wp-block-library` and the editor
registers it client-side at API version 3.

**This is the right tool for our four canvas visuals and the résumé modal.** They
are presentation, not reusable functionality, so they belong in the theme, and
they need no build step.

## 10. `render.php` rules

```php
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'maulik-portfolio-card' ) );
```

- Returns an attribute **string**, not an element.
- Output is **already escaped**. Do not run it through `esc_attr()`.
- Return `''` to render nothing. Do not emit whitespace or an HTML comment, or
  the wrapper does not vanish and the parent grid gets a stray node.
- **Never declare a function or class in `render.php`.** Verbatim: "This file
  loads for every instance of the block type when rendering the page HTML on the
  server. Accounting for that is essential when declaring functions or classes in
  the file." `require_once` from `inc/` instead.
- `$attributes` are never sanitized for you. `"type": "string"` in the schema is
  a type assertion, not sanitization.

Asset fields accept a `file:` path relative to `block.json`, a registered handle,
or an array mixing both. Dependencies and version come from a sibling
`*.asset.php`. `script`, `viewScript`, `style`, `viewStyle` load only when the
block actually renders.

## 11. Per-block CSS

```php
add_action( 'init', 'maulik_portfolio_enqueue_block_styles' );
function maulik_portfolio_enqueue_block_styles() {
    wp_enqueue_block_style( 'core/image', array(
        'handle' => 'maulik-portfolio-block-image',
        'src'    => get_theme_file_uri( 'assets/blocks/core-image.css' ),
        'path'   => get_theme_file_path( 'assets/blocks/core-image.css' ),
    ) );
}
```

Naming `{namespace}-{slug}.css`. Target `.wp-block-{namespace}-{slug}`, except
core blocks drop the namespace, so `.wp-block-image`. This **inlines into
`<head>`**, which is why it beats adding to `style.css`.

## 12. Deprecations to avoid writing new code against

| Deprecated | Replacement |
|---|---|
| `block-templates/`, `block-template-parts/` | `templates/`, `parts/` |
| `template-parts/` as a parts dir | `parts/` |
| `add_theme_support( 'block-templates' )` | automatic |
| `add_theme_support( 'wp-block-styles' )` in a block theme | `styles` in `theme.json` |
| `add_theme_support( 'editor-color-palette' )` etc. | `settings.color.palette` |
| `color.__experimentalDuotone` | `filter.duotone` |
| `setCascadingProperties` on navigation | rewritten flex props |

**Not deprecated:** the `styles/` folder. Style variations are a current,
first-class feature. Anything claiming otherwise is wrong.

## 13. Template hierarchy for our routes

| Request | Filenames tried in order |
|---|---|
| Front page | `front-page.html`, `home.html`, `page.html`, `index.html` |
| Static page | custom template, `page-{slug}.html`, `page-{id}.html`, `page.html`, `singular.html`, `index.html` |
| Blog index | `home.html`, `index.html` |
| Single post | `single.html`, `singular.html`, `index.html` |
| Archive | `archive.html`, `index.html` |
| Search | `search.html`, `index.html` |
| 404 | `404.html`, `index.html` |

`has_archive` + `rewrite.slug` gotcha: the template filename always uses the
**post type key**, never the rewrite slug.

Custom templates declared in `theme.json` `customTemplates`, `name` being the
filename without extension, `postTypes` defaults to `["page"]`. **Custom
templates only apply to single views, never archives.**

## 14. Flags from the lane

- `'template' => array( array( 'core/pattern', … ) )` on `register_post_type` was
  not verified against a dev note. We use `single-{post_type}.html` instead.
- Absence of a theme `blocks/` scanner is verified by reading core source and
  core's own test fixture. High confidence, but it contradicts most blog posts, so
  it is worth one manual smoke test after first build.
- Several documentation paths 301 or 404. Canonical forms are in the lane output.
- `supports.listView` is marked "Since WordPress 7.0" in current docs.
