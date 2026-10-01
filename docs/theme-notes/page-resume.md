# Archived comments from `templates/page-resume.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/page-resume.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 3 (originally at templates/page-resume.html line 1)

```text
	CONDITION: a page using the "resume" custom template. Registered in
	theme.json customTemplates under the name "resume". Served at /resume/.

	PRINT IS A PRIMARY USE CASE FOR THIS PAGE, not an afterthought. The header
	and footer already carry no-print, and the hero carries it below, so the
	printed page is the document alone. The hero is the chrome that wraps the
	document, not part of it.

	ONE h1 ONLY. The prototype renders two, one from the hero and one from the
	document body. post-title below emits the single h1. Do not add a heading
	above it.

	FIVE DOCUMENT SECTIONS, in order: Professional summary, Technical skills,
	Professional experience, Selected development work, Education.

	THE BODY IS A SINGLE POST-CONTENT BLOCK ON PURPOSE. A resume is edited as a
	document. Five template-locked sections would force the editor to unlock
	the template before they could change a job title, which is the opposite of
	what a site owner should have to do. The section headings below are the
	verified structure and the words inside them are ordinary editable blocks.

	A template is block markup. It is parsed, not executed, so it cannot call
	esc_html__() and its literal text is NOT translatable.
```
## Comment 2 of 3 (originally at templates/page-resume.html line 59)

```text
			THE RESUME STRUCTURE REFERENCE.

			Five sections, in order. This is the document's shape, kept as plain
			monospace text in the page editor so an editor can see what the page
			is expected to hold without it reading as body copy. The headings
			themselves live in the page content below.
```
## Comment 3 of 3 (originally at templates/page-resume.html line 109)

```text
				THE EDITABLE DOCUMENT.

				One post-content block. The editor owns every heading and every
				line in here, and edits them in the normal page editor with no
				template unlock step.

				WHEN THE CONTENT IS WRITTEN, these headings are h3, not h2, so
				they sit under the h2 above and no heading level is skipped. The
				page h1 comes from post-title in the hero above and appears only
				once.
```

---

## Correction note, 2026-10-01

The archived text above is verbatim and is not rewritten. This note is added because it
is evidence for a decision, not because it is wrong.

**The claim "the body is a single post-content block on purpose" is the editor first
architecture, already argued for in the original file.** The reasoning in that paragraph
is that a resume is edited as a document, and that five template locked sections would
force the editor to unlock the template before changing a job title. That is precisely
the argument for moving page copy into `post_content`, and this repository had already
made it three times, in `page-resume.md`, in `page-portfolio.md` and in `page.md`.

What changed on 2026-10-01 is that the rule is now applied consistently. It used to hold
for the resume while the rest of the site carried its copy in templates, which meant
opening the About page in the editor showed almost nothing. The architecture is now
uniform: `templates/page-resume.html` supplies structure and the WordPress hierarchy,
while the five document sections and their words live in `content/pages/resume.html`
loaded into `post_content`.

The translatability paragraph above remains correct: a template is parsed, not executed,
so it cannot call `esc_html__()`. The page body is translatable because it lives in
`post_content`. See `docs/ARCHITECTURE.md`.
