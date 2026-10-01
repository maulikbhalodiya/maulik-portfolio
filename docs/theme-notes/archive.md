# Archived comments from `templates/archive.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/archive.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 1 (originally at templates/archive.html line 1)

```text
	CONDITION: any archive that has no more specific template: category, tag,
	taxonomy, author and date archives, and post type archives that have not been
	given their own file.

	The theme's real project archive is /projects/, which is a page and is handled
	by page-projects.html. This file is the fallback listing, so it uses a query
	loop rather than a hardcoded grid.

	A template is block markup. It is parsed, not executed, so it cannot call
	esc_html__(). The query-no-results block is deliberately left empty: literal
	placeholder text here would ship untranslatable.
```
