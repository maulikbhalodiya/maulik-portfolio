# Archived comments from `parts/header.html`

These notes were originally plain HTML comments written directly inside
the block theme file `parts/header.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 2 (originally at parts/header.html line 1)

```text
	HEADER TEMPLATE PART.

	WHY THERE ARE NO WORDS IN THIS FILE.

	Block theme template parts are .html files and they are PARSED, not executed.
	They cannot contain PHP. There is no get_template_part() in a block theme in
	the sense a classic theme developer means it, so esc_html__() cannot be
	called here, esc_html_e() cannot be called here, and no option can be read
	here. Anything written as a literal string in this file ships to every
	visitor of every language, untranslatable, forever.

	That failure is invisible in a screenshot. The header looks correct, it reads
	correctly in English, and nothing anywhere reports an error while it is wrong.
	It is the single most common block theme porting mistake precisely because it
	looks fine.

	THE SPLIT, THEREFORE:

	  parts/    STRUCTURE ONLY. Pure block markup. Zero PHP. Zero user-facing
	            words. Which blocks appear and where.
	  patterns/ WORDS. Every translatable string in the theme, each one wrapped
	            in esc_html__( 'Text', 'maulik-portfolio' ). Because patterns are
	            PHP, translation is available there and only there.

	patterns/header.php carries the six navigation labels. It is the
	translatable source of this structure and it is what an editor sees in the
	site editor.

	ONE THING THAT FAILS COMPLETELY SILENTLY. Every file in patterns/ must open
	with a Title:, Slug: and Categories: header in its docblock. Core reads those
	with get_file_data() and if the Slug line is missing it does not register the
	pattern and does not raise an error in the place anyone would look. The
	pattern simply never appears. Verified against Core 7.1.2 by counting what
	get_block_patterns() returns, which is the only way to notice.

	FLAT DIRECTORY. /parts cannot be nested. Core looks up parts by slug in a
	single flat namespace, so parts/header/mobile.html is unreachable: there is no
	get_template_part( 'header/mobile' ) in a block theme. A second header region
	is a separate slug, such as header-minimal.html, at the top level of /parts.

	NAVIGATION IS EMPTY HERE AND THAT IS DELIBERATE. An unassigned navigation
	block renders an empty container and costs layout shift. It is added when
	there is a menu to assign.

	NO WRITING OR BLOG LINK. The theme supports posts, but the site does not
	advertise a blog. See patterns/header.php for the six items that do exist:
	Home, RankKernel, Projects, About, Resume, Contact.
```
## Comment 2 of 2 (originally at parts/header.html line 50)

Note on this one: in the shipped file this second block was never actually opened
with `<!--`. It was therefore not a comment at all but raw freeform text followed
by a stray `-->`, which is why it reached visitors even after the other comments
were addressed. The text is archived verbatim here.

```text
	THE BRIDGE. This part references maulik-portfolio/header, and that pattern
	carries every word. An earlier version put an UNASSIGNED core/navigation block
	here instead, on the reasoning that an empty navigation block costs layout
	shift. That was wrong in a way that looked right: an unassigned navigation
	block does not render empty, Core falls back to listing every published page,
	so the live header silently became an alphabetical auto-list that included
	the default Sample Page. The nav was not translatable, not ordered as the
	design specifies, and not limited to six items. A screenshot of it looks like
	a perfectly reasonable navigation, which is why it survived review.

	The correct chain is:

	  template  ->  wp:template-part  ->  wp:pattern  ->  words

	Each hop is explicit, and the words stay in PHP where they can be translated.

	NO WRITING OR BLOG LINK. The theme supports posts, but the site does not
	advertise a blog. See patterns/header.php for the six items that do exist:
	Home, RankKernel, Projects, About, Resume, Contact.
```
