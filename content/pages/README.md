# Canonical page content

`content/pages/*.html` holds the WordPress block markup for each page's
`post_content`. These files are the repository's source of truth for page copy.
The live database is not version controlled, so without them an edit in the
WordPress editor leaves no trace in git.

The filename without its extension is the page slug. `content/pages/about.html`
is the `post_content` for the page whose slug is `about`. Nothing else maps the
files to pages: no post ID appears anywhere in this directory or in
`tools/migrate-content.php`, so pages can be recreated, reimported or moved to a
new database without the script breaking.

## Deploy note

`content/` is source material for a migration, not a runtime asset. Nothing in
the theme reads it, and WordPress never loads it. It belongs in the same
category as `docs/` and `tools/` in `.github/workflows/live-deploy.yml`, which
already excludes both with `--exclude`. As of this commit the workflow does not
yet exclude `content/`, so it would be rsynced into the live theme directory for
no reason. Add `--exclude='content/'` to both the dry run and the real rsync in
that workflow. This is deliberately left to whoever owns the workflow, because
`.github/` is outside the write scope of the change that created this directory.

## Copy is verbatim

Every sentence came from the corresponding template, which is the approved live
rendering:

| File | Source template |
|---|---|
| `home.html` | `templates/front-page.html` |
| `about.html` | `templates/page-about.html` |
| `projects.html` | `templates/page-projects.html` |
| `rankkernel.html` | `templates/page-rankkernel.html` |
| `resume.html` | `templates/page-resume.html` plus the live `post_content` |
| `contact.html` | `templates/page-contact.html` |

Nothing is rewritten, shortened, expanded or improved. No metric, project count,
client name, testimonial or award has been added. Where the template and a
content file ever disagree, the template wins and the content file is wrong.

Two cases need explaining rather than defending.

**`core/post-title` becomes a level 1 heading.** Five templates render their page
heading with `core/post-title`. A page's own `post_content` cannot contain
`core/post-title`, because `post-title` inside `post_content` would try to render
the post content as its own title. Each of those pages uses a single
`core/heading` at level 1 carrying the page title instead, which is what
`post-title` emits. The Home template already used `core/heading` at level 1, so
Home keeps its literal "Maulik Bhalodiya".

**The Resume body is three paragraphs, not five sections.** The resume template
delegates its body to `core/post-content`, so the approved resume prose was never
in a template to begin with. It was migrated verbatim from the `post_content`
this page already had in the database. That content is a summary paragraph, an
education line and a pointer to the case studies. The outline section above it
names five parts, of which the approved record supplies two. No skills list, no
per role experience block and no selected work list has been written to close
the gap, because doing so would mean inventing resume claims. `templates/page-portfolio.html`
is placeholder prose for a case study that has not been written; nothing has been
invented for it and there is no content file for it.

## Compositions

Each file is assembled from the same sections the eight `patterns/home-*.php`
files expose to the inserter, in the same order and on the same surfaces, so a
reviewer opening a content file should recognise each section as its pattern,
rendered into real blocks rather than printed.

| Page | Sections |
|---|---|
| Home | hero, capabilities, technical stack, selected work, experience, approach, about, hiring CTA |
| About | hero, who, journey, technical focus, education, next step |
| Projects | hero, case study archive, elsewhere |
| RankKernel | hero, subsystems, dependencies, boundaries |
| Resume | document hero, what this page holds, the document |
| Contact | hero, channels, message, before you write |

## Surfaces and texture

The four texture sizes are read from `theme.json` under
`settings.custom.grid` and confirmed against the `background-size` declarations
in `styles/surface-*.json`. They were not guessed.

| Style variation | Texture | Source |
|---|---|---|
| `is-style-surface-dark-grid` | 48px grid | `styles/surface-dark-grid.json`, `theme.json` `grid.dark` |
| `is-style-surface-dark-subgrid` | 32px sub grid | `styles/surface-dark-subgrid.json`, `theme.json` `grid.sub` |
| `is-style-surface-cream-editorial` | 96px editorial | `styles/surface-cream-editorial.json`, `theme.json` `grid.cream` |
| `is-style-surface-warm-editorial` | 64px editorial | `styles/surface-warm-editorial.json`, `theme.json` `grid.warm` |
| `is-style-section` | section padding | `styles/section.json` |

There is no fourth section variation. The design states three section paddings and
only three, and a fourth tight variation was removed rather than kept with no
design behind it. Runs of blocks that sit inside a section carry the plain
`section-inner` class from assets/styles/components/_rhythm.scss, which sets the
inner stack gap and no page padding.

Surface names are written as `is-style-` because that is the class WordPress emits
when a variation registered from `styles/` is applied. Writing the bare name
produces a class no stylesheet targets, which is why the grid textures used to
render as flat colour. The legacy bare names `dark-grid`, `cream-editorial`,
`warm-editorial` and `dark-subgrid` appear nowhere in this directory, and no
aliases exist for them.

Pure black CTA sections use the native `backgroundColor` attribute with the
`has-black-background-color` class, because there is no registered variation for
the pure black surface and inventing one would be a design change.

Section rhythm per page, matching the templates exactly:

- Home: dark grid, dark grid, sub grid, dark grid tight, cream editorial, warm editorial, dark grid, black
- About: dark grid, dark grid, cream editorial, warm editorial, dark grid, black
- Projects: dark grid, cream editorial, black
- RankKernel: dark grid, dark grid, dark grid, cream editorial
- Resume: dark grid, cream editorial tight, cream editorial
- Contact: dark grid, dark grid, cream editorial, black

## Block vocabulary

Native core blocks only:

- `core/group`
- `core/heading`
- `core/paragraph`
- `core/list` with `core/list-item`
- `core/buttons` with `core/button`

No `core/html`, no shortcodes, no pasted markup wrappers. The inline spans inside
the eyebrow paragraphs (`w-2 h-2 bg-accent` and `eyebrow-text`) are rich text
inside a real `core/paragraph`, which is what the templates and the patterns both
have, not raw HTML blocks.

`templates/page-projects.html` renders its archive with `core/query` and
`core/post-template`. Those are deliberately not in `projects.html`. They read
the posts table at render time rather than storing copy, neither is in the
approved vocabulary above, and freezing the loop into `post_content` would turn a
live archive into a static list that goes stale the moment a case study is
published. The archive section carries its heading and the approved honest
sentence; the loop stays in the template.

## Whitespace between block delimiters

There is none. Not by preference: WordPress's own `serialize_blocks()` joins
top level blocks with an empty string, so any blank line between two top level
block delimiters parses back as a top level freeform block. A reviewer checking
these files with `parse_blocks()` should expect zero top level freeform blocks,
and should get zero. `tools/migrate-content.php` fails the run if it sees one.

## Migrating

```sh
# Review. Prints what would change per page and touches nothing.
wp eval-file tools/migrate-content.php -- --path=/var/www/maulik-dev

# Write. Backs up existing post_content first, outside the web root.
wp eval-file tools/migrate-content.php -- --apply --path=/var/www/maulik-dev

# Undo.
wp eval-file tools/migrate-content.php -- --rollback --path=/var/www/maulik-dev
```

Dry run is the default. See the script's docblock for the full contract,
including the conflict policy and the backup layout.
