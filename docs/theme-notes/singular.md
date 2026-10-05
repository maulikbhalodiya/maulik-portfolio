# Archived comments from `templates/singular.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/singular.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 1 (originally at templates/singular.html line 1)

```text
	CONDITION: the single-item fallback for both is_singular() cases, posts and
	pages alike. WordPress checks singular.html before single.html and page.html,
	so anything that does not resolve to a more specific file lands here.

	Kept deliberately generic. The specific shapes live in single.html, page.html
	and the page-{slug}.html family.

	A template is block markup. It is parsed, not executed, so it cannot call
	esc_html__() and literal text here is NOT translatable. The blocks below are
	therefore content-derived (post-title, post-content) rather than a hardcoded
	heading, because an untranslatable placeholder string shipped in a template is
	permanently wrong for every non-English reader.
```

---

## Correction note, 2026-10-01

The archived text above is verbatim and is not rewritten. Added because this note already
argued for editor owned content.

**The choice of `post-title` and `post-content` over a hardcoded heading is the editor
first decision, already made in the original file.** The stated reason is that "an
untranslatable placeholder string shipped in a template is permanently wrong for every
non-English reader", and the remedy chosen was content derived blocks. That is the same
reasoning that later moved whole page compositions into `post_content`: a template that
carries copy hides the page from the editor.

This fallback is now structure and hierarchy only. It is also worth recording that a
`core/post-content` block sitting in this template never proved a page was editor first.
The real test is whether the substantial composition lives in `post_content`, and before
2026-10-01 the Home page had 92 blocks in its template and 1 in its `post_content`. See
`docs/ARCHITECTURE.md`.
