# Archived comments from `templates/index.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/index.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 1 (originally at templates/index.html line 1)

```text
	The presence of templates/index.html is what tells WordPress this is a block
	theme. Together with style.css at the theme root it is the required pair.
	If either is missing Core falls back to treating the theme as a classic theme
	and every other file in templates/ stops being used.

	CONDITION: the fallback. WordPress uses this template when nothing more
	specific matches, so it has to be the most generic reasonable layout in the
	theme rather than the homepage. The homepage lives in front-page.html, which
	takes precedence over this file whenever a static front page is assigned.

	NOTE ON LANGUAGE: a template is block markup. It is parsed, not executed, so
	it cannot call esc_html__() and its literal text is NOT translatable. The
	headings and eyebrows here are the theme's own structural fallback copy. The
	translatable versions of every string in this theme live in patterns/, where
	esc_html__() is actually available.
```

---

## Correction note, 2026-10-01

The archived text above is verbatim and is not rewritten. The claim inside it that "the
translatable versions of every string in this theme live in patterns/" is now too
narrow, because there are three translatable homes rather than one.

The technical claim stands and must not be lost: a template is block markup that is
parsed rather than executed, so it cannot call `esc_html__()` and any literal string in
one is permanently untranslated. That is why the headings and eyebrows in this fallback
template are the theme's own structural copy.

What has changed is the destination. The translatable version of an authored string now
lives in one of:

- `patterns/*.php`, for reusable compositions and for chrome copy
- `content/pages/*.html`, loaded into the `post_content` of the corresponding WordPress
  Page, for authored page copy
- a language file, for anything derived from a token in `theme.json`

See `docs/ARCHITECTURE.md`. This note also already argued for content derived blocks:
`post-title` and `post-content` appear here precisely so that an untranslatable
placeholder string never ships, and the same instinct is what the editor first
architecture applies to whole pages.
