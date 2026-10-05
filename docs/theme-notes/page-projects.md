# Archived comments from `templates/page-projects.html`

These notes were originally plain HTML comments written directly inside
the block theme file `templates/page-projects.html`. WordPress runs that file through `do_blocks()`,
and `WP_Block_Parser` keeps any `<!-- ... -->` that is not a block delimiter
(`<!-- wp:` or `<!-- /wp:`) as freeform HTML and renders it verbatim. The
comment text was therefore being printed as body copy in front of real
visitors on every page.

They have been moved here, word for word and with their original tab
indentation, so the architectural rationale is preserved without being shown
to visitors. Nothing below is paraphrased or summarised. Each fenced block is
the inner text of one original comment, in the order it appeared in the file.

## Comment 1 of 2 (originally at templates/page-projects.html line 1)

```text
	CONDITION: any published page. There is no custom template: theme.json
	has no customTemplates key and templates/ registers no page-specific
	template. The page is served by templates/page.html, which renders
	wp:post-content for whatever slug this page has. Served at /projects/.

	THE ARCHIVE. A heading and a core/query that is ready for content. The
	query is non-inheriting so it does not silently adopt the page query.

	NO CLIENT-SIDE FILTERING HERE, AND DELIBERATELY SO. The prototype filter is
	client-side useState with no URL parameters and no links, which means it
	cannot be shared, cannot be crawled and does not work without JavaScript.
	It also has five real defects that must not be ported:

	  · the button "Payment" matches nothing, the data says "Payments"
	  · "Integration" matches nothing directly
	  · "Independent" always returns an empty professional grid
	  · "Automation" and "Payments" are real tags with no button to reach them
	  · the button vocabulary and the tag vocabulary are different word sets

	When filtering arrives it is server side, as links with query parameters,
	and the button vocabulary and the tag vocabulary are reconciled first.

	The post type is post because the custom post type does not exist yet, and
	creating one inside a theme is forbidden. The CPT belongs in a plugin.

	A template is block markup. It is parsed, not executed, so it cannot call
	esc_html__() and its literal text is NOT translatable.
```
## Comment 2 of 2 (originally at templates/page-projects.html line 115)

```text
			ANYTHING THE EDITOR WRITES ON THE PAGE RENDERS HERE, below the
			structured sections.
```
