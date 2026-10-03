# Archived comments from `templates/page-contact.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/page-contact.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 3 (originally at templates/page-contact.html line 1)

```text
	CONDITION: a page using the "contact" custom template. Registered in
	theme.json customTemplates under the name "contact". Served at /contact/.

	THERE IS NO EMAIL ADDRESS ON THIS SITE. No mail link, no plaintext
	address, no obfuscated address, in this template, in any pattern, or
	anywhere else in the theme. The prototype carried seven plaintext
	occurrences and nine mail link targets and all of them were removed on
	purpose.
	Publishing an address scrapes it off the site the day it ships. LinkedIn
	and GitHub are the entire contact surface.

	THE FORM IS A LATER PHASE. The section heading below is already in place
	and correctly labelled, and it is deliberately empty. An empty heading in
	the site editor is a clear instruction to the editor. A disabled form
	wired to nowhere is a broken promise to a visitor. Leave it empty.

	DO NOT ADD HIRING, SERVICE, AVAILABILITY OR RATE LANGUAGE. The site seeks
	employment and does not advertise.

	A template is block markup. It is parsed, not executed, so it cannot call
	esc_html__() and its literal text is NOT translatable.
```

### Correction, 2026-10-02: the `THERE IS NO EMAIL ADDRESS ON THIS SITE` paragraph above is superseded

The archived comment is kept verbatim, as this directory requires, but its
`THERE IS NO EMAIL ADDRESS ON THIS SITE. No mail link, no plaintext address, no
obfuscated address, in this template, in any pattern, or anywhere else in the theme`
rule is no longer in force. See the same dated correction in
`docs/theme-notes/footer.md`, which carries the identical rule and the identical
correction; the two notes agree from here on.

What holds now: the design's single address, `maulikbhalodiya9999@gmail.com`, is
published in four places, the footer Connect column, the section 08 `Email Me` call
to action, the hero strip and the mobile menu channel row. No other address exists
and no other `mailto` exists. The contact page's own channels are still this site's
contact page, LinkedIn, GitHub and the resume, and the form is still the form with
an intent dropdown, not a mail link, and it still cannot deliver a submission.

The reason for the reversal is the owner's standing rule that design or colour
related work uses exactly what the demo design uses, with no substitutions. The
owner was told the address had been spam-listed in production and ruled that it
ships anyway. That decision is settled and is not to be reopened.

## Comment 2 of 3 (originally at templates/page-contact.html line 89)

```text
			THE FORM SECTION. Heading in place, nothing inside it.

			A form arrives in a later phase. When it does it must post to the
			site's own endpoint, must not be a mail link, and must state what it
			does with the data before the visitor submits anything.
```
## Comment 3 of 3 (originally at templates/page-contact.html line 144)

```text
			ANYTHING THE EDITOR WRITES ON THE PAGE RENDERS HERE, below the
			structured sections, so page content can never displace the designed
			structure.
```
