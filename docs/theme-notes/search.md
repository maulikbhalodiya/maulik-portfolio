# Archived comments from `templates/search.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/search.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 1 (originally at templates/search.html line 1)

```text
	CONDITION: search results, for both posts and pages, and for any post type
	that has not opted out of search.

	search.html is the only template where a core/search block belongs. It is
	deliberately absent from every other template in this theme: the design has
	no site-wide search UI, and shipping an unassigned search box on every page
	would add a layout shift for a feature nobody asked for.

	A template is block markup. It is parsed, not executed, so it cannot call
	esc_html__() and literal text here is NOT translatable. The core/search block
	carries its own placeholder and button strings, which the block manages and
	which are translatable through WordPress core. The query-no-results block is
	left empty for the same reason index.html leaves it empty.
```
