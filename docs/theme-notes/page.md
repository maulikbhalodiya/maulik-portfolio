# Archived comments from `templates/page.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/page.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 1 (originally at templates/page.html line 1)

```text
	CONDITION: any single page that has no more specific template. This is the
	generic page shape: hero-ish heading, then the content the editor wrote. It
	is also the fallback that page-{slug}.html and page-{id}.html override.

	A template is block markup. It is parsed, not executed, so it cannot call
	esc_html__() and literal text here is NOT translatable. The heading below is
	empty for that reason: an untranslated placeholder string shipped in a
	template is permanently wrong, whereas an empty heading renders nothing and
	the editor supplies the real title. Same reasoning for the no-results block
	below.
```
