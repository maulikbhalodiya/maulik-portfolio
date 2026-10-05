# Archived comments from `templates/page-rankkernel.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/page-rankkernel.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 4 (originally at templates/page-rankkernel.html line 1)

```text
	CONDITION: any published page. There is no custom template: theme.json
	has no customTemplates key and templates/ registers no page-specific
	template. The page is served by templates/page.html, which renders
	wp:post-content for whatever slug this page has. Served at /rankkernel/.

	THIS PAGE SHIPS. RankKernel is the site's own open source SEO and schema
	engine and it is the strongest story the portfolio has.

	THE THEME MUST NOT DEPEND ON RANKKERNEL IN CODE. No class_exists checks, no
	conditional rendering, no graceful degradation branch, no PHP in this file
	whatsoever. If the plugin were removed tomorrow the site would lose its SEO
	features and nothing else. The reference to it here is data in a template,
	nothing more. This is why there is no dynamic block.

	TWO STATEMENTS ARE COMPLIANCE CRITICAL AND APPEAR BELOW.

	  · RankKernel was created by Maulik Bhalodiya.
	  · Maulik Bhalodiya is not affiliated with Qrolic Technologies. RankKernel
	    is not employer work and not client work.

	Dropping the second statement is the single worst thing that could happen
	to this page, because a reader who has just seen professional WordPress work
	listed under an employer would otherwise assume the project belongs to it.

	NINE SUBSYSTEMS, HONEST STATUS: 3 IMPLEMENTED, 3 IN PROGRESS, 3 PLANNED.
	Status values are uppercase and space separated, as written here. The tally
	is computed from the blocks below, not written into a heading as
	"All States (9)", because a count that lives in one place and a set of rows
	that lives in another will disagree the first time a row is added.

	A template is block markup. It is parsed, not executed, so it cannot call
	esc_html__() and its literal text is NOT translatable.
```
## Comment 2 of 4 (originally at templates/page-rankkernel.html line 59)

```text
			THE SUBSYSTEM REGISTER.

			Nine subsystems, three per status. Each row is a group carrying its
			status as mono metadata, so the status is data the editor can change
			rather than a word baked into a heading.

			THE TALLY IS NOT WRITTEN INTO THE VISIBLE COPY. It is read off these
			nine rows. Do not add a hardcoded count to a heading.
```
## Comment 3 of 4 (originally at templates/page-rankkernel.html line 161)

```text
			THE DEPENDENCY GRAPH.

			Fourteen real edges between the subsystems above. This is the
			strongest asset the page has and it is an actual dependency
			relationship, not decoration.
```
## Comment 4 of 4 (originally at templates/page-rankkernel.html line 276)

```text
			ANYTHING THE EDITOR WRITES ON THE PAGE RENDERS HERE, below the
			structured sections.
```
