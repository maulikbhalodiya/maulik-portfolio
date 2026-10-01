# PHASE 1 · Design foundation

**Status: NOT STARTED** · this is the next phase

Everything downstream reads the tokens set here, so a palette correction after
Phase 3 means rechecking every page. This phase is small in volume and large in
leverage.

---

## 1.1 `theme.json` verification pass

The palette and typography were set in Phase 0. What remains is the structural
work that turns correct values into a correct file.

- [ ] **`contentSize` 48rem** for prose, **`wideSize` 90rem** for the container
- [ ] `defaultFontSizes: false`, `defaultSpacingSizes: false`,
      `defaultPalette: false`, `defaultGradients: false` all explicit, because
      `version: 3` flips the first two to `true`
- [ ] `useRootPaddingAwareAlignments: true` **paired with** an explicit
      `styles.spacing.padding` object, or it achieves nothing
- [ ] `border.radius: false`, `shadow.defaultPresets: false`. The design has
      zero rounded corners and zero shadows. This is its spine.
- [ ] `spacing.units` excludes `vh`, which is not in the design scale
- [ ] **The two accents, both present:**
      - `#FACC15` for dark surfaces
      - `#8A6A00` for light surfaces
- [ ] **Append palette entries at the end. Never reorder.** Translation uses
      positional JSON key paths as gettext context, so reordering silently
      repoints existing translations and nobody notices for months
- [ ] Record the four grid sizes as `settings.custom`, already done: 48, 32, 96,
      64px

### The two-yellow fix, the one non-negotiable item

`#FACC15` on `#F8F7F0` is roughly 1.5:1. On `#FFFDF4` roughly 1.9:1. Both fail
WCAG AA outright.

The design already handles this correctly: on light sections the focus ring
switches to `#0A0A0B` and text switches to `#0A0A0B`. Amber is never text on a
light surface.

So `theme.json` must declare **two** accent entries. The darkened value is
**`#8A6A00`**, already specified in the earlier prototype, which is why it did
not appear in the reverse-engineered palette of the current design. Use it. Do
not invent a new one, and do not assume the brand amber is safe on light
surfaces because it is the brand colour.

---

## 1.2 SCSS architecture

- [ ] `main.scss` is the **only** file with `@use` statements
- [ ] Four directories, each with an `_index.scss` that forwards its partials:
      `abstracts/`, `base/`, `components/`, `utilities/`
- [ ] `@use` and `@forward` only. **Never `@import`**, deprecated since Dart
      Sass 1.80.0
- [ ] Dart Sass CLI, not `wp-scripts build`. wp-scripts provides no
      `includePaths`, will not compile a standalone `style.scss`, and does not
      expose `silenceDeprecations`, so a Sass deprecation cannot be suppressed
      without overriding the loader
- [ ] **Colours consumed as `--wp--preset--*` at runtime.** Never hand-duplicate
      a hex value as a SCSS variable
- [ ] Only compile-time-only values as SCSS: breakpoints, content width, mixin
      parameters, anything inside a Sass function

### The four grid textures

These carry different meaning and **must not be unified**:

| Class | Size | Colour | Orientation | Use |
|---|---|---|---|---|
| `dark-grid` | 48px | cream 3.5% | both axes | default page surface, heroes |
| `dark-subgrid` | 32px | amber 14% | dots | behind the skills ecosystem |
| `cream-editorial` | 96px | near-black 4% | vertical only | light rhythm section |
| `warm-editorial` | 64px | near-black 3.5% | horizontal only | second light section |

Two dark, two light. The different sizes and the different line orientations are
what make them distinguishable at a glance. `dark-subgrid` declares no
background colour and assumes a dark parent.

---

## 1.3 Fonts

The single biggest performance liability in the approved design. The prototype
loads three Google Fonts families, eleven weights, from a third party, with no
`font-display` control, no preload and no subset. That is a render-blocking
third-party stylesheet on the critical path of every page, and it will blow the
CLS budget on its own.

- [ ] Source woff2 for the three families: **Syne** (display, 600/700/800),
      **Plus Jakarta Sans** (body, 400 to 700), **IBM Plex Mono** (metadata,
      400/500/600 plus 400 italic)
- [ ] **Latin subset only.** Nothing else is rendered
- [ ] **Maximum two weights per family.** Eleven weights is the problem
- [ ] Self-hosted under `assets/fonts/`
- [ ] `font-display: swap`
- [ ] `document.fonts.ready` before the hero canvas's first paint

### Metric-matched fallbacks, the actual CLS fix

Declared on the **fallback** face, using the **web font's** metrics:

```css
/* Web font */
@font-face {
  font-family: "Syne";
  src: url("/wp-content/themes/maulik-portfolio/assets/fonts/syne-latin.woff2") format("woff2");
  font-weight: 700;
  font-display: swap;
}

/* Fallback with adjusted metrics */
@font-face {
  font-family: "Syne Fallback";
  src: local("Arial");
  size-adjust: 87.4%;
  ascent-override: 101.2%;
  descent-override: 24.8%;
  line-gap-override: 0%;
}
```

- [ ] `size-adjust`, `ascent-override`, `descent-override`, `line-gap-override`
      for each family
- [ ] **Multiple fallbacks per family, mandatory.** Arial does not exist on
      Android, so Roboto needs its own override set
- [ ] Compute from real font metadata, do not guess. Guessed values are worse
      than no values because they look deliberate

This is the only technique that stops text *reflowing*, not just vertical jitter.
Chrome's own documentation says metric overrides including `size-adjust` can
eliminate font-related layout shift.

### WordPress traps specific to fonts

- **Never** put absolute font URLs in `add_editor_style()`.
  `get_block_editor_theme_styles()` fetches with `wp_remote_get()`, which fails
  silently against a self-signed dev certificate.
- `@font-face` with relative paths breaks in `add_editor_style()` because the CSS
  is inlined without a base URL.
- Block themes already get `editor-styles` support from core, so
  `add_theme_support( 'editor-styles' )` is unnecessary.

---

## 1.4 Typography details that must survive

Easy to lose, and each changes how the design reads:

- [ ] **`text-wrap: balance` on all headings.** Prevents orphans at display sizes
- [ ] **`font-variant-numeric: tabular-nums` on all mono.** Makes timestamps and
      numeric metadata align in a column. The mono usage depends on this
- [ ] **`line-height: 1.08` on display headings**, much tighter than the 1.6 body
      base. This single value is most of the display type's character
- [ ] The mono voice carries **every** piece of machine-readable information:
      section identifiers, breadcrumbs, metadata lists, the footer legal line.
      Removing it makes the site look like a generic dark portfolio

---

## 1.5 Block theme markers

`style.css` and `templates/index.html` are the exact pair WordPress checks. If
either is missing, Core falls back to classic theme handling and **every other
file in `templates/` silently stops being used**, with no error anywhere.

- [ ] `templates/index.html` with real content, not a placeholder
- [ ] `parts/header.html`, `parts/footer.html`, **flat and block markup only**
- [ ] **No PHP in any part.** Template parts are `.html`; translatable content
      goes in `patterns/*.php`
- [ ] `functions.php` still a thin loader, unchanged
- [ ] **Never create `block-templates/` or `block-template-parts/`.** Core checks
      `file_exists()` on the stylesheet directory and if *either* legacy folder
      exists it switches *both*, silently breaking `/templates/`
- [ ] `home.html` and `single.html` stubbed for future blog posts, **no nav link
      and no menu item**

---

## 1.6 Two lines that decide front-end performance

```php
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
add_filter( 'should_load_block_assets_on_demand', '__return_true' );
```

- [ ] Both added to `inc/setup.php`
- [ ] Verified active, not just present

Without these, WordPress ships `wp-block-library`, a 100KB+ stylesheet, on every
page including pages that use fifteen of ninety blocks, and it is render
blocking, so it dominates the critical path regardless of what else is
optimised.

Note `style.css` is **not** auto-enqueued in a block theme. It needs an explicit
enqueue via `get_parent_theme_file_uri()` with
`wp_style_add_data( ..., 'path', ... )` so on-demand loading works. Without the
path data the stylesheet loads unconditionally on every page.

---

## 1.7 Asset versioning

- [ ] `filemtime()` or the theme version as `$ver` on every enqueue, so cached
      HTML points at immutable URLs
- [ ] `style.min.css` under `SCRIPT_DEBUG`, `style.css` otherwise, following the
      Twenty Twenty-Five pattern

---

## Definition of done

- [ ] `theme.json` validates and every v3 trap is handled explicitly
- [ ] Both accents declared, `#FACC15` and `#8A6A00`
- [ ] `border.radius` false, no shadow presets
- [ ] SCSS compiles with `@use` only, zero deprecation warnings
- [ ] Four grid textures present at four distinct sizes
- [ ] Three font families self-hosted, Latin subset, max two weights each
- [ ] Metric-matched fallbacks for every family, multiple fallbacks each
- [ ] No third-party font request anywhere
- [ ] Both block theme markers present with real content
- [ ] Zero PHP in template parts
- [ ] Both asset filters active
- [ ] `style.css` enqueued with path data
- [ ] All CI gates green on a push

**Commit:** `feat(theme): design foundation, self-hosted fonts, block markers`
