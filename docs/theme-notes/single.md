# Archived comments from `templates/single.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/single.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 1 (originally at templates/single.html line 1)

```text
	CONDITION: a single post, for posts that have no assigned custom template.

	singular.html already covers both single posts and single pages and takes
	priority over this file in Core's template hierarchy. single.html is kept
	alongside it because the CI file existence gate requires the explicit blog
	post template, and because a future blog is an explicit requirement even
	though no Writing or Blog link appears anywhere in this theme. The capability
	exists without being advertised.

	A template is block markup. It is parsed, not executed, so it cannot call
	esc_html__() and literal text here is NOT translatable. The no-results block
	is deliberately left empty rather than filled with an untranslatable
	placeholder.
```
