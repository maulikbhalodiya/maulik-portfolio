# ⚠️ CRITICAL FINDING: read this before the rest of the research lands

**Source: WordPress.org Theme Directory handbook, "Required" page (last modified 9 June 2026), §1 and §5, plus Theme Check plugin (current build `20260901`).**

---

## The headline finding

You asked me to research "custom block theme with dynamic post types and pages and terms and blocks."
The WordPress.org Theme Directory explicitly classifies **all four of those as "plugin territory"
and forbids them in a theme.**

> "Do not: Include zip files or plugins in the theme folder, or download plugins automatically."

Plugin territory includes:
- `register_post_type`: custom post types
- `register_taxonomy`: custom taxonomies
- custom blocks that are not presentation
- custom user contact methods
- shortcodes
- "Functionality that is not related to design and presentation"
- and explicitly: "Analytics or tracking support, SEO options, contact forms, non-design related
  meta boxes, resource caching, social media 'like', 'follow' and 'share' buttons, session tampering"

Theme Check flags `register_post_type` and `register_taxonomy` under a check category literally
named **"Plugin Territory."**

**A theme that registers a `project` CPT will be closed, not merely warned.**

---

## This forces a decision that changes what I build

There are exactly two honest paths. There is no third one.

### Path A: Split architecture (theme stays submittable)

```
maulik-portfolio/          <- THEME. Presentation only.
  theme.json                   design tokens, palette, typography, layout
  templates/*.html             how things look
  patterns/*.php               reusable layout patterns
  parts/*.html                 header, footer
  blocks/                      presentational blocks only
    (no custom post types, no data access)

mk-portfolio-core/             <- PLUGIN. Owns the data model.
  register_post_type('project')
  register_taxonomy(...)
  register_post_meta(...)
  dynamic blocks that query data
```

Both are GPL. Both are independently installable. The plugin holds the content model; the theme
holds the design. This is exactly how WordPress core itself is structured (`wp-includes` vs
`wp-admin` vs default themes).

**Cost:** two codebases, two releases, two review queues. The plugin also has to go through
plugin directory review if you ever want it listed.

**Benefit:** the theme is genuinely submittable, and the plugin is reusable. Nothing is coupled
to RankKernel, satisfying spec §58 cleanly.

### Path B: Single theme, no directory submission

Put the CPT, taxonomy, meta, and dynamic blocks directly in `functions.php` or a `lib/` folder.
One codebase. You own it. Deploy it to your own site.

**Cost:** you give up the wp.org listing. Nobody can install your theme from the directory.

**Benefit:** dramatically simpler. One repo, one deploy, one thing to maintain. For a personal
portfolio that only you will ever run, this is often the correct engineering call.

---

## What this means for each thing you asked about

| You asked for | Path A | Path B |
|---|---|---|
| `project` custom post type | plugin | `functions.php` |
| `technology` taxonomy | plugin | `functions.php` |
| `register_post_meta` structured fields | plugin | `functions.php` |
| Dynamic blocks reading project data | plugin | `blocks/*/render.php` |
| theme.json design tokens | theme | theme |
| templates, patterns, parts | theme | theme |
| Presentational blocks (card, badge, flow) | theme | theme |
| Submittable to wp.org | ✅ theme yes | ❌ |
| Single repo | ❌ | ✅ |

---

## Other hard findings from lane 1

**`index.php` and `functions.php` are NOT required for a block theme.** This is the single biggest
difference from a classic theme. The complete required set is:

```
style.css          required, root only
readme.txt         required
theme.json         required
templates/index.html   required
screenshot.png     required, 1200x900, 4:3
```

`functions.php` is optional and only exists if you need PHP. If you take Path B, `functions.php`
is where the "no CPTs in themes" rule will bite.

**`theme.json` version 3 is current** (shipped WP 6.6, still latest in 2026). The `$schema` URL
should be **pinned to your minimum supported WP version**, not `trunk`:

```json
"$schema": "https://schemas.wp.org/wp/6.6/theme.json"
```

Pinning to trunk floats and will offer editor autocomplete for settings your declared minimum
WordPress version does not actually support.

**v3 breaking change:** if you define *any* `fontSizes` or `spacingSizes`, you must now set
`defaultFontSizes: false` / `defaultSpacingSizes: false` to override core's defaults. Previously
it was always override.

**Theme Check must pass with zero errors.** Warnings and notices do not block. The plugin's version
number is the date of the guidelines it was built from, currently `20260901`. Run it with
`WP_DEBUG` enabled, because a theme emitting a PHP notice is an automatic rejection.

**No remote resources, at all, except Google Fonts.** No CDN, no self-hosted-from-CDN fonts, no
external API calls. This is a GDPR/privacy requirement. It is why the live prototype fetches the
GitHub API for stars, that must become a static value or a user-consented request in the shipped
theme.

**Bundled jQuery is forbidden.** Use the version core ships. Minified files are fine provided the
unminified original also ships.

**Themes may add only ONE database option**, which must be an array, prefixed with the theme slug.

**Everything must be prefixed** with 4+ characters in the public namespace: functions, globals,
constants, post meta keys, script handles, block pattern category slugs.

**Text domain must equal the theme slug exactly** (lowercase, hyphenated, same as folder name).
Theme Check enforces this.

**Skip links are automatic in block themes**: core adds them to `<main>`. Do not hand-build one.
Same for `wp_head`, `wp_footer`, `body_class`, `language_attributes`. The handbook page listing
those as requirements is from 2020 and is classic-theme only.

**Do not use the `accessibility-ready` tag** on a first submission. It carries an extended bar.

**One theme in the review queue at a time.** Violating this closes all your tickets and can
trigger a permanent upload ban across accounts.

**No favicon or site icon in the theme**, that's a Site Setting since WP 4.3.

---

## Still to land

Lane 3 (block theme architecture), lane 5 (CPT/taxonomy/meta/performance), lane 7 (WPCS/SCSS
tooling) are still running. I will fold their findings in, but none of them change the decision
above: the plugin-territory rule is settled and authoritative.
