# Archived comments from `parts/footer.html`

These notes were originally plain HTML comments written directly inside
the block theme file `parts/footer.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 2 (originally at parts/footer.html line 1)

```text
	FOOTER TEMPLATE PART.

	WHY THERE ARE NO WORDS IN THIS FILE. Same rule as the header part, and it is
	worth restating because the footer is where the temptation is strongest: a
	copyright line, four column headings and eight links all feel like markup.

	A block theme template part is .html and is PARSED, not executed. It cannot
	contain PHP. esc_html_e() cannot be called here, the year cannot be generated
	here, and get_bloginfo() cannot be called here. A literal "Copyright 2026
	Maulik Bhalodiya" written here would ship to every visitor of every language,
	untranslatable, and would also be wrong next year.

	THE SPLIT, THEREFORE:

	  parts/    STRUCTURE ONLY. Pure block markup. Zero PHP. Zero user-facing
	            words. The 5/2/3/2 grid exists here as columns.
	  patterns/ WORDS. Every translatable string, wrapped in
	            esc_html__( 'Text', 'maulik-portfolio' ). patterns/footer.php
	            carries the four group headings, the links and the copyright row
	            with wp_date( 'Y' ) for the year, generated rather than hardcoded.

	            patterns/footer.php must open with Title:, Slug: and Categories:.
	            Without the Slug line Core silently does not register the pattern
	            and the translatable footer never reaches the site editor. No error
	            appears anywhere obvious. Verified by counting what
	            get_block_patterns() returns.

	FLAT DIRECTORY. /parts cannot be nested, for the same reason the header is a
	single file: Core resolves part slugs in one flat namespace and there is no
	nested part lookup.

	NO EMAIL LINK, NO MAILTO, NO PLAINTEXT ADDRESS. Anywhere. The prototype had
	nine mailto links and seven plaintext occurrences and all of them were
	removed deliberately. Contact runs through the contact page.

	NO WRITING OR BLOG LINK. Posts are supported, the blog is not advertised.
```

### Correction, 2026-10-02: the NO EMAIL LINK paragraph above is superseded

The archived reasoning above is kept verbatim, as this directory requires, and its
`NO EMAIL LINK, NO MAILTO, NO PLAINTEXT ADDRESS` rule is no longer in force.

The owner's standing rule for this project is that design or colour related work uses
exactly what the demo design uses, with no substitutions. The design's footer has an
`Email` link in the Connect column, and this theme had substituted a second `/contact/`
link in its place, which is itself the substitution the rule forbids. The owner was told
the address had been spam-listed in production once and ruled that it ships anyway. That
decision is settled and is not to be reopened.

What holds now: the design's single address appears in the footer Connect column and in
the section 08 `Email Me` button. No plaintext address appears anywhere, and no other
`mailto` exists. Contact is still a form with an intent dropdown everywhere else. The CI
gates that enforced the blanket ban were narrowed to exempt exactly this one address
rather than removed, so an address nobody decided to publish still fails the build.

One detail worth keeping from the original paragraph, because it was right: the reason a
`/contact/` substitution was a bad idea is structural, not just a policy breach. Explore
already carries Contact pointing at `/contact/`, so the same label at the same URL in two
columns of one footer reads as a duplicate. The fix for that was to publish the design's
real third link, not to drop the entry.
## Comment 2 of 2 (originally at parts/footer.html line 39)

Note on this one: in the shipped file this second block was never actually opened
with `<!--`. It was therefore not a comment at all but raw freeform text followed
by a stray `-->`, which is why it reached visitors even after the other comments
were addressed. The text is archived verbatim here.

```text
	THE BRIDGE, SAME REASON AS THE HEADER. This part references
	maulik-portfolio/footer, which is where every word lives. Without that
	reference the footer rendered its column headings and copyright as empty
	paragraphs, because structure alone carries no text and Core has no fallback
	to supply it. An empty footer reads like a styling bug rather than a missing
	reference, which is why it survived until the rendered markup was inspected
	rather than photographed.

	The chain is: template -> wp:template-part -> wp:pattern -> words.
```
