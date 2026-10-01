# ARCHITECTURE

### `maulik-portfolio` editor first block architecture, and where each piece of the page lives

### Last updated 2026-10-01. Supersedes nothing: this narrows and explains decisions already taken.

---

## 1. THE ONE SENTENCE

**The page lives in `post_content`, and the WordPress editor is the interface.** Everything
else in the theme exists to frame it, style it, or compose it for reuse.

Concretely, and this is the test that matters: opening `Pages`, then `About`, then `Edit`
must show the real About page. Before this architecture it did not. The Home page had 92
blocks in its template and 1 block in its `post_content`, so the editor showed a single
empty paragraph and the real content was invisible to anyone who wanted to change it.

**A `core/post-content` block existing in a template does not prove a page is editor
first.** That block was already present and the page was still effectively template
driven. The test is where the substantial composition lives.

---

## 2. THE SEVEN PLACES, AND WHAT EACH IS FOR

| Place                  | Owns                                                         | Never owns                                    |
| ---------------------- | ------------------------------------------------------------ | --------------------------------------------- |
| `templates/*.html`     | page level structural rendering context, WordPress hierarchy | page copy                                     |
| `parts/*.html`         | global chrome: header, footer                                | page copy of any kind                         |
| `post_content`         | the authored page body                                       | structural decisions the editor cannot change |
| core blocks            | the foundation: every block used is `core/*` where one fits  | bespoke behaviour                             |
| `patterns/*.php`       | reusable compositions that insert real editable blocks       | the page itself                               |
| block style variations | surfaces and vertical rhythm, via `is-style-*`               | copy, layout data                             |
| custom blocks          | only where genuinely justified                               | anything that reads a data model              |

### 2.1 Templates

A template is the structural frame for a WordPress hierarchy match: front page, single
page, a specific slug, 404, archive. It decides where `main` is, which blocks wrap the
content, and which parts are pulled in.

A template is `.html` block markup. **It is parsed, not executed.** That single fact
drives a hard rule: a template cannot call `esc_html__()`, so any literal string in a
template is permanently untranslated. Templates therefore carry structure and
content-derived blocks, never copy.

### 2.2 Template parts

`parts/` is global chrome: `header.html` and `footer.html`, plus anything shared across
the whole site. A part renders identically on every page, so it can never hold page
copy. Parts are `.html` and are parsed, not executed, so they cannot translate either.

### 2.3 Page content

`post_content` is where the authored page lives, and the editor is how it is authored.
This is the third and final home for translatable strings, and it is fully translatable
because it runs PHP.

### 2.4 Core blocks

Every block in this theme is a `core/*` block wherever a core block fits: `core/group`,
`core/heading`, `core/paragraph`, `core/list`, `core/buttons`, `core/post-title`,
`core/post-content`, `core/query`, `core/form`. Core blocks are the foundation precisely
because an editor can already use them and the inserter already offers them. There is no
`core/html` wrapper, no shortcode, and no pasted markup in page content.

### 2.5 Patterns

A pattern is a reusable composition that inserts **real, editable blocks** into the
editor. It is not a rendering escape hatch and it is not where the page lives. It is the
right home for a composition that appears on more than one page, and for chrome copy that
needs translation.

Patterns execute during `init`, not at render. `is_single()`, `get_post()`,
`the_title()` and the loop are unavailable inside one. Only `esc_html_e()`, `esc_html()`,
`get_theme_file_uri()` and iteration over static data work.

### 2.6 Block style variations

Variations own **surfaces and vertical rhythm**, not copy. Two vocabularies exist:

- `section` and `section-tight`: default vertical rhythm
- `surface-dark-grid`, `surface-dark-subgrid`, `surface-cream-editorial`,
  `surface-warm-editorial`: the four canvas textures

#### Why the CSS is theme owned, and why `is-style-*` is canonical

**Verified finding, 2026-10-01:** `wp_get_global_stylesheet()` returns 6,344 bytes
containing **zero** `is-style-surface-*` rules, even though the variations register
successfully and their `css` survives registration. In this setup WordPress does not emit
the variation CSS to the frontend.

So the variation rules are compiled into the theme stylesheet (`assets/css/theme.css`)
and keyed on `is-style-*`, for example:

```css
.wp-block-group.is-style-section {
  /* vertical rhythm */
}
.wp-block-group.is-style-surface-dark-grid {
  /* canvas texture */
}
```

**`is-style-*` is the canonical vocabulary.** The bare slugs (`section`, `surface-dark-grid`)
are what the JSON declares; `is-style-` is what WordPress puts on the rendered element.
Styling must therefore be written against `is-style-*`. Writing it against the bare slug
silently matches nothing, and the failure looks identical to "the variation CSS never
loaded", which is how the original investigation began.

The variations themselves stay registered in `styles/*.json` so the editor can offer them
from the Styles panel. Registration for the editor and CSS delivery for the frontend are
two separate concerns, and only the second one is handled by the theme stylesheet here.

### 2.7 Custom blocks

A custom block is allowed here **only where it is genuinely justified**, and the test is
one question:

> **Does the block's `render.php` read a data model, meaning posts, meta or terms?**

| Block kind                                              | Belongs in    | Why                                                   |
| ------------------------------------------------------- | ------------- | ----------------------------------------------------- |
| Presentational: card, badge, flow, canvas visual, modal | **the theme** | design and presentation, which is what a theme is for |
| Reads project data via `render.php`                     | **a plugin**  | functionality unrelated to design and presentation    |

The WordPress.org dividing line is exactly this: "functionality that is not related to
design and presentation". Project data lives in block attributes inside `post_content`,
which needs no schema and no meta queries, so the motivating use case for a data reading
block does not arise in this theme.

#### Registration is manual, and mandatory

**A theme gets no automatic block registration.** This is verified against core source,
not inferred. `wp_register_block_types_from_metadata_collection()` exists in
`wp-includes/blocks.php`, but **nothing in core ever calls it for a theme directory**.
There is no scanner in `wp-settings.php`, `default-filters.php`, `script-loader.php` or
`blocks.php`. Core's own test fixture registers by hand:

```php
// core: tests/phpunit/data/themedir1/block-theme/functions.php
add_action( 'init', 'register_theme_blocks' );
function register_theme_blocks() {
	register_block_type( __DIR__ . 'blocks/example-block' );
}
```

Auto registration of a `blocks/` directory is a `@wordpress/scripts` **plugin**
convention. It is widely repeated and it is wrong for themes. An agent that creates a
`blocks/` directory and waits for Core to notice will produce code that never registers
and never renders.

And registration is not an optimisation either. Server side registration is **mandatory**
for `theme.json` styling to apply: "if you want a block to be styled via `theme.json`, it
must be registered on the server. Otherwise, the block won't recognize or apply any
styles assigned to it in `theme.json`."

Current registration state: **no custom blocks exist.** The four canvas visuals and the
résumé modal are the candidates, and they are presentation rather than reusable
functionality, so if they are built they belong in the theme.

---

## 3. THE CONCRETE EXAMPLE: `/about/`

This is the example the architecture is judged against.

### 3.1 The WordPress Page's `post_content` holds

| Section         | Content                                |
| --------------- | -------------------------------------- |
| Hero            | page heading and the opening statement |
| Who I am        | the verified record                    |
| Journey         | the timeline                           |
| Technical focus | the working areas                      |
| Education       | degree and institution                 |
| CTA             | the next step                          |

Source of that content: `content/pages/about.html`. All core blocks: `core/group`,
`core/heading`, `core/paragraph`, `core/list` with `core/list-item`, `core/buttons` with
`core/button`. Surfaces carried as `is-style-*` class names.

### 3.2 `templates/page-about.html` contributes only

```
header  →  main  →  post-content  →  footer
```

Four things. No copy. That template would render the same on every page using it, and if
it carried the About body it would be lying about what it is.

### 3.3 The wrong version, and how it fails

The previous template carried hero, who, journey, focus, education and CTA inline and left
`post_content` holding one empty paragraph. The site rendered correctly and looked
perfect. It was still broken: opening the About page in the editor showed an empty
paragraph, so the only way to change a word was to edit a template file, which is not
editing.

---

## 4. CONTENT SOURCE AND MIGRATION

`content/pages/*.html` is the **source of truth** for page content. The database is
**generated** from it.

|                             |                                     |
| --------------------------- | ----------------------------------- |
| Authored in                 | `content/pages/*.html`              |
| Loaded into                 | the WordPress Page's `post_content` |
| Rendered by                 | `core/post-content` in a template   |
| Edited by an owner, through | `Pages`, then the page, then `Edit` |

Direction of flow matters: repository to database to render. Editing in the database
directly is not lost work, because the next generation run overwrites it, so changes must
go into `content/pages/`. That is the tradeoff, and it is taken deliberately: a portfolio
whose content cannot be version reviewed and diffed is a worse artifact.

Copy is verbatim on migration. Sentences lifted from the previous template rendering were
not rewritten, shortened, expanded or improved. If the file and the approved rendering
ever disagree, the rendering is authoritative and the file is what is wrong.

---

## 5. TRANSLATABLE STRINGS: THREE HOMES, NOT ONE

The earlier claim was that every translatable string in this theme lives in `patterns/`.
That is now incomplete, and the corrected statement is:

| Home             | For                                | Why it works                             |
| ---------------- | ---------------------------------- | ---------------------------------------- |
| `patterns/*.php` | reusable compositions, chrome copy | runs PHP, so `esc_html__()` is available |
| `post_content`   | authored page copy                 | runs PHP, fully translatable             |
| language files   | `theme.json` token names           | gettext context is the JSON key path     |

**And the claim that is still exactly right:** templates and template parts are `.html`
and are parsed rather than executed, so they cannot translate anything. A literal string
in a template is permanently wrong for every non-English reader. That is why the heading
in `templates/page.html` is empty rather than filled with a placeholder.

Palette entries in `theme.json` must be appended to, never reordered. WordPress uses the
JSON key path as the gettext context, so reordering silently detaches every translation
attached to a name.

---

## 6. WHAT THIS COMPLETED

The repository had already argued for editor owned content three times, in
`docs/theme-notes/page-resume.md`, `docs/theme-notes/page-portfolio.md` and
`docs/theme-notes/page.md`. Each note reached the conclusion from a different direction:
the resume note refused template locked sections because they would force an editor to
unlock a template to change a job title; the portfolio note said the prose belongs in the
normal page editor; the page note left its heading empty because a placeholder string in a
template is permanently wrong.

Those three notes were right and they were not applied. The change completes a decision
this repository had already made, rather than introducing a new one. The original
reasoning is preserved verbatim in `docs/theme-notes/` with dated correction notes
appended, never rewritten.

---

## 7. RULES THAT STILL BIND, UNCHANGED

These are decisions the owner has not revisited, and this architecture changes none of
them:

- No custom post types. No taxonomies. No shortcodes. No form handling handler.
- No em dashes (U+2014) and no en dashes (U+2013), anywhere. CI greps repo wide.
- No raw email addresses and no `mailto` links. Contact is a form with an intent dropdown.
- No skill percentage bars, no progress meters, no star ratings.
- Never create `block-templates/` or `block-template-parts/`.
- Never create `index.php`.
- `style.css` and `templates/index.html` must both exist.
- `should_load_separate_core_block_assets` and
  `should_load_block_assets_on_demand` both `true`. `should_load_remote_block_patterns`
  filtered to `false`.

---

## 8. RELATED

| Document                                                   | Covers                                                            |
| ---------------------------------------------------------- | ----------------------------------------------------------------- |
| [`PLAN.md`](PLAN.md)                                       | why the site is built this way                                    |
| [`RESEARCH-02-BLOCK-THEME.md`](RESEARCH-02-BLOCK-THEME.md) | block registration, verified against core source                  |
| [`RESEARCH-01-WPORG.md`](RESEARCH-01-WPORG.md)             | the theme versus plugin dividing line                             |
| [`theme-notes/`](theme-notes/)                             | verbatim archived architectural reasoning, with dated corrections |
