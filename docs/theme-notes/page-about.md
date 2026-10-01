# Archived comments from `templates/page-about.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/page-about.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 2 (originally at templates/page-about.html line 1)

```text
	CONDITION: a page using the "about" custom template. Registered in
	theme.json customTemplates under the name "about", so an editor picks it
	from the Page attributes panel. Served at /about/.

	STRUCTURE IS GROUP BLOCKS WITH STYLE VARIATIONS, exactly as on the
	homepage. Each section is a core/group whose className carries the surface
	slug and the rhythm slug, so an editor can change either from the Styles
	panel without touching this file.

	SECTION ORDER IS SEQUENTIAL AND IS NOT TO BE REPRODUCED FROM THE PROTOTYPE.
	The prototype concatenates two eyebrows into one reading "06 ... and 08 ..."
	and renders 07 after 08. Number 01 to 05 in order here.

	FACTS ARE THE VERIFIED RECORD ONLY. WordPress Developer at Qrolic
	Technologies from July 2025, PHP Developer Intern January to June 2025,
	Rajkot, Gujarat, B.Tech Computer Engineering at Ganpat University 2021 to
	2025. No phone number belongs on this site. No metric, no percentage, no
	client name.

	A template is block markup. It is parsed, not executed, so it cannot call
	esc_html__() and its literal text is NOT translatable.
```
## Comment 2 of 2 (originally at templates/page-about.html line 201)

```text
			ANYTHING THE EDITOR WRITES ON THE PAGE RENDERS HERE, below the
			structured sections. It is deliberately last so page content can never
			push the designed structure around.
```
