# Archived comments from `templates/page-projects.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/page-projects.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 2 (originally at templates/page-projects.html line 1)

```text
	CONDITION: any published page. There is no custom template: theme.json
	has no customTemplates key and templates/ registers no page-specific
	template. The page is served by templates/page.html, which renders
	wp:post-content for whatever slug this page has. Served at /projects/.

	THE ARCHIVE. A heading and a core/query that is ready for content. The
	query is non-inheriting so it does not silently adopt the page query.

	NO CLIENT-SIDE FILTERING HERE, AND DELIBERATELY SO. The prototype filter is
	client-side useState with no URL parameters and no links, which means it
	cannot be shared, cannot be crawled and does not work without JavaScript.
	It also has five real defects that must not be ported:

	  · the button "Payment" matches nothing, the data says "Payments"
	  · "Integration" matches nothing directly
	  · "Independent" always returns an empty professional grid
	  · "Automation" and "Payments" are real tags with no button to reach them
	  · the button vocabulary and the tag vocabulary are different word sets

	When filtering arrives it is server side, as links with query parameters,
	and the button vocabulary and the tag vocabulary are reconciled first.

	The post type is post because the custom post type does not exist yet, and
	creating one inside a theme is forbidden. The CPT belongs in a plugin.

	A template is block markup. It is parsed, not executed, so it cannot call
	esc_html__() and its literal text is NOT translatable.
```
## Comment 2 of 2 (originally at templates/page-projects.html line 115)

```text
			ANYTHING THE EDITOR WRITES ON THE PAGE RENDERS HERE, below the
			structured sections.
```

---

## Design mapping note, 2026-10-06

The archived comment above lists `"Independent" always returns an empty professional grid`
among five defects of the prototype filter that must not be ported. That list describes the
prototype's filter logic in isolation. It does not describe the delivered page, because the
delivered page reproduces the empty grid and adds the piece the prototype paired with it.

This note records a mapping that is not written down anywhere else in the repository and that
has already produced a wrong conclusion twice, once from an agent and once from the owner.

### `independent` is a section visibility switch, not a category filter

Every `file:line` below was read before being cited.

**1. The design special cases the tab to an empty professional grid.**
`/home/ubuntu/design-reference/src/pages/ProjectsArchivePage.tsx` lines 40 to 43:

```js
  const filteredProfessionalProjects =
    selectedFilter === 'Independent'
      ? []
      : PROJECTS_DATA.filter((project) => {
```

The tab is in `FILTER_OPTIONS` at line 15 of the same file, so the branch is reachable. The
empty array is deliberate, not a fallthrough.

**2. No project carries an `Independent` filter category.**
`/home/ubuntu/design-reference/src/data/portfolioData.ts` declares nine `filterCategories`
arrays, at lines 582, 716, 830, 952, 1066, 1155, 1228, 1301 and 1374. The complete set of
distinct values across all nine is:

```text
Professional, WordPress, PHP, API, Payments, Security, Automation, WooCommerce
```

There is no `Independent` value. Line 582, the first of the nine, reads in full:

```js
    filterCategories: ['Professional', 'WordPress', 'PHP', 'API', 'Payments', 'Security'],
```

The string `Independent` does occur in that file, at line 217 as
`category: 'Independent Open Source'`, but that is an orbit entry for the homepage skills
graph, not a `filterCategories` value. A card carrying an `Independent` tag could therefore
never be selected by the grid filter even if the `? []` branch were removed.

**3. The design renders RankKernel as its own section, not as a grid card.**
Same file, lines 138 to 140:

```jsx
      {/* First Section: Independent Development (RankKernel) */}
      {showIndependent && (
        <section
```

The section closes at line 219. The professional grid section opens separately at line 223,
under the comment `Second Section: Professional Engineering Work`. The two are siblings. The
gate is `showIndependent`, defined at lines 33 to 38 as true for `All`, `Independent`,
`WordPress`, `PHP` and `API`.

**4. Our implementation already matches the design.**

| Location | Line | Content |
| --- | --- | --- |
| `content/pages/projects.html` | 56 | `<!-- /wp:group --><!-- wp:group {"tagName":"section","className":"pj-indep","anchor":"independent-development",...} -->` |
| `content/pages/projects.html` | 57 | `<section class="wp-block-group pj-indep" id="independent-development" ...>` |
| `content/pages/projects.html` | 120 | the `pj-work` section opens, so `pj-indep` and the grid are siblings here too |
| `assets/js/project-filters.js` | 62 | `const INDEPENDENT_SELECTOR = '.pj-indep';` |
| `assets/js/project-filters.js` | 150 | `this.independent = document.querySelector( INDEPENDENT_SELECTOR );` |
| `assets/js/project-filters.js` | 198 to 203 | the section is replaced by `document.createComment( 'pj-indep' )` on init |
| `assets/js/project-filters.js` | 306 to 309 | `if ( show.independent ) { restore } else { detach }` |

The grid is emptied by a separate mechanism. Line 59 sets
`const GRID_SELECTOR = '.pj-work__grid';`, and line 122 decides each card with
`return card.classList.contains( CARD_CLASS_PREFIX + slug );`. For the slug `independent`
that is a test for `pj-cat-independent`, which no card carries, so the matching set is empty
and lines 335 to 342 move every card into the stash fragment.

**5. Consequence.** Clicking `independent` empties the professional grid and reveals the
RankKernel section. This is correct and is not a defect. It is the design's intent, and the
shipped page matches it.

### The reasoning error, recorded so it is not repeated

* **The `pj-cat-*` vocabulary describes cards inside the professional grid.**
  `inc/project-filters.php` line 36 states the rule: "The category is instead carried on the
  card as pj-cat-* classes in the block's className". Those classes are the per-card filter
  vocabulary, and they are emitted by `blocks/portfolio-list/render.php` line 340, not by the
  section markup. So the absence of `pj-cat-independent` is a true observation and it is
  irrelevant to this tab. It is evidence about the grid, not about the page.
* **A probe that counts the wrong container will report this tab as broken.**
  A probe counting only `.pj-work__grid` children, or counting visible `article.hz-card`
  elements, returns zero for `independent`. That is the probe measuring the wrong container,
  not the page failing. The correct check is that `.pj-indep` is present and the grid is
  empty. Both halves are required; either alone is ambiguous, because `WordPress` and `PHP`
  also set `show.independent` while keeping matching cards.
* **Do not "fix" this by making `independent` a real category.**
  Adding a taxonomy term named `independent`, or adding a filterable RankKernel card to the
  grid, would contradict the design's `? []` branch and duplicate the section that
  `content/pages/projects.html` line 57 already renders. It would also break the design's own
  Section 1 and Section 2 split. The current implementation is the correct one.

### Open question: RankKernel Schema and REST status disagrees between sources

Two sources describe the same two RankKernel subsystems and they do not agree. This note
records the disagreement and does not attempt to reconcile it.

| Source | Schema | REST |
| --- | --- | --- |
| `design-reference/src/data/portfolioData.ts` lines 508 and 519 | `status: 'IN PROGRESS' as SubsystemStatus` | `status: 'IN PROGRESS' as SubsystemStatus` |
| `rk-verify/docs/RANKKERNEL-CURRENT-STATE.md` lines 61 and 67 | `Schema` marked `COMPLETE` | `REST / Headless` marked `PARTIAL` |
| live `/rankkernel/` | row `Schema and JSON-LD` | no REST module row |

Three details are worth stating precisely, because the loose version of this claim is wrong
in each case.

* The design does **not** use the word `COMPLETED`. Line 28 of `portfolioData.ts` declares
  the vocabulary: `export type SubsystemStatus = 'IMPLEMENTED' | 'IN PROGRESS' | 'PLANNED';`.
  The string `COMPLETED` does not appear anywhere in that file.
* The design marks Schema `IN PROGRESS` at line 508, not `COMPLETED`. The three modules it
  marks `IMPLEMENTED` are Modules, Settings and Metadata, at lines 475, 486 and 497.
* The owner's document does not use the word `Shipped` for Schema. Line 61 reads
  `| Schema | COMPLETE | SchemaModule, src/Modules/Schema/Pieces/, REST write boundary |`.
  It also does list REST, at line 67, as `| REST / Headless | **PARTIAL** |`. What it does
  not list is a REST *module*: line 77 gives the registry entries without an implementation
  as `importer`, `image-seo`, `gutenberg`, `ai` and `headless`, and REST is not among them.
  Line 67 describes REST routes, not a module directory.

The live page was read to confirm the third row. `https://maulik-dev.duckdns.org/rankkernel/`
carries nine subsystem rows: Breadcrumbs, Robots and llms.txt, Metadata Engine, Schema and
JSON-LD, Instant Indexing, XML Sitemaps, Content Analysis, Redirects and 404 Monitor. There
is no REST row. A REST reference does appear on that page, but as a technology chip in the
stack list, not as a subsystem.

**Which source governs.** `rk-verify/docs/RANKKERNEL-CURRENT-STATE.md` line 3 declares itself
the single source of truth: "This file is the single source of truth for where RankKernel is
right now." Its stated precedence rule is that when another document disagrees, the repository
is authoritative and the disagreement must be verified rather than resolved from memory. Under
that rule the owner's document governs the module inventory and this project follows it, which
is consistent with the live page showing nine modules and no REST. The design's
`portfolioData.ts` is a design-time snapshot with its own three-word vocabulary and is not the
authority for shipped module status.

Nothing in this change alters either source or the rendered page. A later reader who needs to
reconcile the design snapshot against the shipped module list should do it as its own change,
with its own verification.
