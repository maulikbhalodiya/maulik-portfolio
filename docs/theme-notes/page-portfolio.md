# Archived comments from `templates/page-portfolio.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/page-portfolio.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 2 (originally at templates/page-portfolio.html line 1)

```text
	CONDITION: a page using the "portfolio" custom template. Registered in
	theme.json customTemplates under the name "portfolio". Served at
	/projects/{slug}/ for case study pages. The page that lists them is
	page-projects.html.

	TWELVE NUMBERED SECTIONS, AND THE ORDER IS THE FRAMEWORK. The design does
	NOT use a case / approach / solution / impact structure. This is the
	structure the design actually has:

	  01 Overview            07 Implementation
	  02 Problem             08 Security
	  03 My Role             09 Verification
	  04 Technical Approach  10 Technologies
	  05 Architecture        11 Confidentiality
	  06 Engineering         12 Related Work
	      Decisions

	THERE IS NO IMPACT SECTION AND NO METRICS ANYWHERE, AND NO PLACEHOLDER FOR
	EITHER. There is no impact field and no metrics field on any project in the
	source data, because outcomes would require numbers that have not been
	verified. Do not add a thirteenth section, do not add a percentage, and do
	not leave an empty slot that reads as an omission. An honest case study
	without outcomes is correct here.

	Section 08 Security has no source field of its own in the design. It is
	derived from the security or validation note on each architecture flow, so
	the security claims cannot drift from the flow they describe.

	NO CLIENT FIELD, NO CLIENT NAME, NO DOMAIN, NO LIVE URL, NO REPO URL, NO
	DATE, NO TESTIMONIAL. None of those exist on any project. Nothing is
	substituted for them.

	WHAT EACH PARAGRAPH BELOW IS. The twelve sections carry the structure and
	the headings, which are fixed by the framework. The body of each is one
	honest line recording where that section's prose comes from. The case study
	prose itself is written per project from the verified record, into the page
	content block at the foot of this template. Each line is meant to be
	replaced by real content, not read as copy.

	A template is block markup. It is parsed, not executed, so it cannot call
	esc_html__() and its literal text is NOT translatable.
```
## Comment 2 of 2 (originally at templates/page-portfolio.html line 265)

```text
			THE CASE STUDY PROSE. Written per project from the verified record,
			into the page content in the normal page editor. The twelve headings
			above are the framework and do not change from project to project.
```
