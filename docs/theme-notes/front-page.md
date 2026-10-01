# Archived comments from `templates/front-page.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/front-page.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 3 (originally at templates/front-page.html line 1)

```text
	THE HOMEPAGE.

	CONDITION: front page. WordPress uses this file whenever a static page is set
	as the front page in Reading settings. It takes precedence over home.html and
	over index.html, and index.html carries the generic listing instead.

	STRUCTURE IS A GROUP BLOCK, NOT A TEMPLATE.

	A block theme has no "section" block, so there is nothing in the inserter
	that says "put a surface here". Two custom block types would solve it and
	custom blocks are forbidden in a theme, so each surface below is a
	core/group carrying a registered block style variation slug in its
	className:

		{"className":"section surface-dark-grid"}

	"section" is the default vertical rhythm, py-16 rising to md:py-24.
	"section-tight" is the tighter rhythm, used where a section directly
	follows its predecessor without a surface change, and on the inner wrapper
	groups inside a section.

	surface-dark-grid        hero and most dark sections
	surface-dark-subgrid     the denser dotted field, section 03 ONLY, because
                         that is the skills ecosystem
	surface-cream-editorial  the first light section
	surface-warm-editorial   the second light section

	THE SURFACE RHYTHM IS LOAD BEARING. In order:

	  01 dark-grid    02 dark-subgrid  03 dark-grid   04 dark-grid
	  05 cream        06 warm          07 dark-grid   08 pure black

	Do not flatten it, do not reorder the sections, do not make 05 or 06 dark,
	and do not use surface-dark-subgrid anywhere except section 03, which is
	the only place the denser field appears. Section 08
	is pure black, applied with backgroundColor rather than a variation, because
	black is a palette colour and not a surface.

	THE AMBER SQUARE. Every section opens with a mono eyebrow preceded by an
	8x8 pixel amber square. That motif appears in all eight sections and it is
	what makes the page read as one designed surface rather than eight stacked
	boxes. It is a span inside the eyebrow paragraph rather than a spacer,
	because core/spacer supports no background and the span carries
	aria-hidden="true" so a decorative shape never reaches a screen reader.

	HEADINGS. One h1 in section 01. h2 in every later section. No skipped
	levels anywhere. Every section carries aria-labelledby pointing at its own
	heading id.

	A template is block markup. It is parsed, not executed, so it cannot call
	esc_html__() and its literal text is NOT translatable. The translatable
	versions of these strings live in patterns/, which is PHP. Nothing here is
	invented: no metrics, no percentages, no testimonials, no client names, no
	client domains, no skill ratings. Where the record is thin the line says so.

	NO HIRING OR SERVICE LANGUAGE. Section 08 asks to be contacted, it does not
	advertise. This site seeks employment.
```
## Comment 2 of 3 (originally at templates/front-page.html line 219)

```text
			SECTION 04 SITS DIRECTLY AFTER ANOTHER DARK SURFACE, so it takes
			section-tight rather than the full section rhythm. Adjacency is the
			only reason to choose the tighter spacing.

			NINE PROJECTS, ALL COMPLETED AS PROFESSIONAL WORK. Titles and
			categories are the verified record. There is no client field, no live
			URL, no repository link, no date and no metric on any of them, because
			none of those exist in the record. Nothing is substituted for them.
```
## Comment 3 of 3 (originally at templates/front-page.html line 401)

```text
			SECTION 08 IS THE EMPLOYMENT SECTION AND IT IS CORRECT AS WRITTEN.

			"Looking for a WordPress or PHP developer?" is a recruiter speaking.
			That is seeking employment, which is the opposite of the pattern this
			theme bans. Do not soften the heading and do not add recruitment
			agency, commission, service, availability or rate language here or
			anywhere in this file.

			PURE BLACK, applied with backgroundColor rather than a variation
			because black is a palette colour and not one of the four surfaces.
			It is the only #000000 section in the theme and that is intentional.
			There is no mail link and no published address. Contact runs through
			the contact page.
```
