# Archived comments from `templates/home.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/home.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 1 (originally at templates/home.html line 1)

```text
	CONDITION: the blog posts index. WordPress picks this over index.html as soon
	as the Reading settings show posts on the front page, so it is not a stub for
	the homepage. It is also the archive-style listing for any custom post type
	that has not been given its own template.

	THE RULE THAT SHAPED THIS FILE: the theme is architected to support a future
	blog, but the site does not advertise one. There is deliberately no Writing or
	Blog link in any template, part or pattern. That is why no navigation block
	appears here and why the footer in patterns/footer.php omits it. The
	capability exists without being advertised.

	A query loop is used rather than do_blocks() so the listing stays native. The
	no-results block is intentionally left empty: an empty query renders no
	copy, and any placeholder string here would be untranslatable literal text.
```
