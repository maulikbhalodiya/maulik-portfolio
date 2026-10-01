# Maulik Portfolio

A dark, performance-obsessed WordPress **block theme** for the personal developer
portfolio of Maulik Bhalodiya, a WordPress and PHP developer.

Text domain: `maulik-portfolio`  
Function prefix: `maulik_portfolio`  
Constant prefix: `MAULIK_PORTFOLIO`  
Licence: GPL-2.0-or-later

---

## What this theme is

This is a block theme. That is a specific architectural commitment, not a label:

- Templates are `.html` files containing block markup. They are parsed, not executed.
- Theme supports, filters and enqueues live in PHP under `inc/`.
- Design tokens live in `theme.json` and reach the browser as `--wp--preset--*` custom
  properties.
- Project data lives in block attributes inside post content. There are no custom post
  types, no taxonomies and no custom database schema.

We hold ourselves to WordPress.org review standards as a discipline, even though this
theme is not being submitted to the directory. The discipline is the point: it forces
the decisions that a personal site would otherwise postpone indefinitely, and it keeps
the theme portable to any host that runs WordPress.

---

## Requirements

| | |
|---|---|
| WordPress | 6.7 or later (tested to 6.9) |
| PHP | 7.4 or later |
| Licence | GPL-2.0-or-later |

Block themes require WordPress 6.1 or later at the absolute minimum. This theme
requires 6.7 because it depends on `theme.json` version 3 semantics and on block
template behaviour that settled there.

---

## Layout

```
maulik-portfolio/
├── assets/
│   └── styles/
│       ├── main.scss               # single Sass entry point
│       ├── abstracts/              # compile-time-only values and mixins
│       ├── base/                   # reset and typography
│       ├── components/             # block level components
│       ├── utilities/              # spacing helpers
│       └── generated/              # generated from theme.json, never edit
├── inc/
│   ├── assets.php                  # style enqueue
│   ├── helpers.php                 # Maulik_Portfolio namespace convention
│   ├── seo.php                     # development noindex gate
│   └── setup.php                   # theme support and global filters
├── languages/
│   ├── maulik-portfolio.pot
│   └── .gitkeep
├── parts/
│   ├── footer.html                 # template parts, block markup only
│   └── header.html
├── patterns/
│   ├── footer.php                  # patterns ARE PHP, run on init
│   └── hello.php
├── scripts/
│   └── generate-scss-tokens.mjs    # theme.json to SCSS token generator
├── templates/
│   ├── 404.html
│   ├── index.html                  # REQUIRED for block theme detection
│   └── singular.html
├── composer.json
├── package.json
├── phpcs.xml.dist
├── phpstan.neon
├── style.css                       # theme headers only, NOT auto-enqueued
├── theme.json
├── functions.php                   # thin loader
└── ...
```

There is deliberately **no `index.php`**. Block themes do not need it. There are also no
`header.php`, `footer.php`, `sidebar.php`, `single.php`, `page.php`, `archive.php`,
`search.php`, `comments.php` or `404.php`. Those are classic theme files, and CI fails
if any of them appear.

---

## Build

Node 22.22.2 or 24.15.0 or 26 or later. Use `.nvmrc`:

```bash
nvm use
npm install
```

```bash
npm run tokens      # regenerate SCSS tokens from theme.json
npm run build:css   # compile assets/styles/main.scss to style.css
npm run build:css:min   # compile to style.min.css
npm start           # tokens then build
```

Compiled `style.css` is committed. `/build/` and `/assets/css/` are deliberately **not**
in `.gitignore`, so that any fresh checkout can produce the release ZIP and every change
to compiled output shows up as a reviewable diff. The trade is diff noise in exchange for
a build that is reproducible from the repository alone.

---

## Lint

```bash
# PHP
composer install
composer lint          # phpcs, must report zero errors AND zero warnings
composer lint:fix      # phpcbf, see the caveat below
composer stan          # PHPStan level 5

# CSS and JS
npm run lint:css       # stylelint
npm run lint:js        # eslint
npm run lint:md        # markdown format check
npm run format         # prettier via wp-scripts
```

**phpcbf cannot fix what matters.** It fixes whitespace, indentation, brace and
parenthesis placement, and some naming and spacing sniffs. It does **not** fix
escaping, sanitizing, missing nonces, i18n problems, or unprepared SQL. It also does
not fix Yoda conditions, because mechanically swapping operands can change evaluation
order. Never accept a phpcbf run as evidence that code is safe. Read the diff.

---

## Translation

```bash
wp i18n make-pot . languages/maulik-portfolio.pot --domain=maulik-portfolio
```

**Palette entries in `theme.json` must be appended to, never reordered.** WordPress
translates theme.json names using the JSON key path as the gettext context, so
reordering or renaming an entry silently detaches every translation attached to it. This
is recorded in `languages/maulik-portfolio.pot` as well as here.

---

## Architecture notes

These are the decisions that are easy to undo by accident and expensive to debug later.

### Why the palette is provisional

The palette in `theme.json` is a placeholder, not the approved design. It is a dark
near-black base with a warm accent, chosen so the theme is legible and neutral until
the real design arrives. It is intentionally minimal. Do not build on top of it as if
it were settled, and do not duplicate its values into Sass, because they will change.

### Why `defaultPalette` and `defaultFontSizes` are explicitly false

`theme.json` version 3 flips several defaults to `true`. That matters here:

- `settings.color.defaultPalette` defaults to `true`, which would put Core's **light
  mode** default palette alongside ours and break a dark theme.
- `settings.typography.defaultFontSizes` defaults to `true`, which would put Core's
  default font sizes back beside ours.
- `settings.spacing.defaultSpacingSizes` defaults to `true`, same problem.

All three are set to `false` explicitly. They are not redundant with the fact that a
custom palette exists.

### Why `useRootPaddingAwareAlignments` is paired with explicit padding

`useRootPaddingAwareAlignments: true` tells the editor that root padding handles the
page gutter. It is only meaningful if `styles.spacing.padding` actually sets left and
right padding. Enabling it alone gives an editor preview that does not match the front
end. Both are set.

### Two things that are NOT in `theme.json`

Recorded here because both are easy to add by mistake:

- **`settings.renzh` does not exist.** There is no such theme.json setting.
- **`"layout": {"type": "constrained"}` is not valid inside `settings.layout`.**
  `settings.layout` takes `contentSize` and `wideSize` only. The `type: "constrained"`
  form belongs in a template or block comment attribute, where it is correctly written in
  `templates/index.html`.

`theme.json` is parsed with strict `json_decode`, so it cannot carry `//` comments
either. Any note about its contents lives here, not inline.

### Why `style.css` must be enqueued explicitly

WordPress stopped auto-enqueueing the parent stylesheet when it introduced block
themes. `style.css` is not loaded unless something asks for it. `inc/assets.php`
enqueues the `maulik-portfolio-style` handle and calls `wp_style_add_data( ..., 'path', ... )`
so that Core can build per-block style dependencies, which is what makes
`should_load_separate_core_block_assets` work for this theme.

`style.css` therefore carries theme headers and a comment block, and nothing else.

### Why `block-templates/` and `block-template-parts/` must never exist

Core checks `file_exists()` on the stylesheet directory and switches to legacy block
theme handling if **either** legacy directory exists. The failure is silent: `templates/`
stops resolving, template parts go missing, and you get blank headers and footers with
no error anywhere. Block themes use `templates/` and `parts/`. CI asserts both legacy
directories are absent.

### Why patterns cannot use the loop

A pattern is included during `init`, long before there is a request context.
`is_single()`, `get_post()`, `the_title()` and the loop are all unavailable. Only static
data, `esc_html_e()` and `get_theme_file_uri()` work. Anything in a template part that
needs translation or a dynamic value belongs in a pattern instead, which is why
`parts/header.html` contains only a Site Title block that handles its own i18n.

### Why the two asset filters are the first thing in `inc/setup.php`

`should_load_separate_core_block_assets` and `should_load_block_assets_on_demand` are
the single biggest performance lever a block theme has. Together they let Core enqueue
per-block assets only for the blocks a page actually renders. A typical portfolio page
renders about a dozen distinct core blocks out of the ninety or so registered. Without
these filters a visitor downloads the entire catalogue on every page.

### Why the development noindex is double gated

Staging installs are usually publicly reachable and frequently unprotected. So
`inc/seo.php` emits `noindex, nofollow`, but only when `WP_DEBUG` **and** an explicit
opt-in are both true. No single constant or option can enable it on its own, which
means a leftover `define()` in a `wp-config.php` cannot deindex a production site.

### Why `namespace` comes before the ABSPATH guard in `inc/helpers.php`

PHP requires a `namespace` declaration to be the very first statement in a file, ahead
of everything including the guard. This is the only shape a namespaced theme file can
take, and `inc/helpers.php` demonstrates it. Hooked functions keep the `maulik_portfolio`
prefix so phpcs `PrefixAllGlobals` recognises them as theme globals; internal helpers
that are never hooked move into `namespace Maulik_Portfolio;`.

### Why the stylelint config is shipped in the repository

`wp-scripts` resolves stylelint configuration from ancestor directories and from
`~/.config`, which means a contributor's machine can silently change which rules apply.
Shipping `.stylelintrc.json` in the repository is what makes the lint result
reproducible. Same reasoning for `.editorconfig`, `phpcs.xml.dist` and `phpstan.neon`.

### Why SCSS is compiled with the Dart Sass CLI and not wp-scripts

Stock `@wordpress/scripts` provides no `includePaths`, will not compile a standalone
`style.scss`, and does not expose `silenceDeprecations`, so Sass deprecations cannot be
suppressed without overriding the loader. So `@wordpress/scripts` is used for
stylelint, eslint and formatting only, and `sass` is invoked directly with
`--load-path=assets/styles`.

**Write `@use` and `@forward` only. Never `@import`.** It was deprecated in Dart Sass
1.80.0 and is removed in 3.0.0.

### Why tokens are generated rather than hand written

`assets/styles/generated/_theme-json-tokens.scss` is produced by
`scripts/generate-scss-tokens.mjs` from `theme.json` and is committed so a fresh
checkout can compile CSS with no Node toolchain. CI regenerates it and fails on drift.

Only compile-time-only values are emitted: content width, wide width, breakpoints.
Colours, fonts and spacing sizes are **not** emitted as SCSS variables, because
WordPress already serves them at runtime as `--wp--preset--*`. Duplicating them in Sass
creates two sources of truth that drift the first time anyone tweaks the palette in the
site editor.

---

## Performance budget

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
`descent-override` and `line-gap-override`, and **multiple fallbacks are required**. The
reason is concrete: Arial, the usual second entry in a fallback stack, does not exist on
Android, so a stack that relies on it silently drops to the Android system default on
Android, every line re-flows when the web font arrives, and CLS goes through the roof.
A fallback stack that looks reasonable on a laptop is a CLS bug on the device most
visitors actually use.

---

## Content rules

These are enforced in CI, not just documented.

- **No em dashes and no en dashes.** Anywhere: code, comments, copy, docs, commit
  messages. Use a comma, a colon, a semicolon, a full stop, or parentheses.
- **No raw email addresses and no `mailto` links.** Contact is a form with an intent
  dropdown. Addresses are scraped by automated bots within hours of a site going live.
- **No skill percentage bars, no progress meters, no star ratings.** There is no data
  source for them, so they are claims the site cannot substantiate.
- **No third party requests of any kind.** No analytics, no CDN, no Google Fonts. Fonts
  are self-hosted. `should_load_remote_block_patterns` is filtered to `false`.

---

## Known gaps

- **`screenshot.png` does not exist yet.** WordPress requires it at **1200x900** for the
  theme browser and it is a hard release blocker. It must be a real screenshot of the
  theme as it appears, with no fake browser chrome around it. Commit it before release.
- **No PHPUnit suite.** Deliberate. See the `_comment` key in `composer.json` for the
  reasoning. Revisit the moment the theme has a `render.php` callback or any PHP with
  observable behaviour.
- **No `build/` output.** That directory is marked `linguist-generated` in
  `.gitattributes` and is populated by whatever produces the release ZIP. It does not
  exist yet.
- **The palette is provisional.** See the architecture notes above.
- **No navigation menu.** The Navigation block is absent from `parts/header.html`
  because an unassigned navigation block renders an empty container that costs layout
  shift. It goes in once a menu exists.

---

## Licence

GPL-2.0-or-later. See [LICENSE.md](LICENSE.md).