<?php
/**
 * Server side render for the portfolio list block.
 *
 * WHY THIS FILE ECHOES INSTEAD OF RETURNING. THIS IS NOT A STYLE CHOICE.
 *
 * WordPress does not call a file rendered block the way it calls a
 * render_callback. When block.json says "render": "file:./render.php", Core
 * wraps the file in ob_start(), requires it, and returns ob_get_clean(). A
 * return statement inside that file returns from the require, and its value is
 * discarded, because the closure returns the buffer and never assigns what the
 * require produced. So a render.php that returns a string renders as an empty
 * block with no error, no warning and no clue where the markup went.
 *
 * A bare return is a different matter and is not the bug. Early return after a
 * printf() is how this file skips the rest of the work, and the buffer Core
 * already holds is still returned, so nothing is lost. Only return with a
 * value is destructive here, and there is none in this file.
 *
 * Every value printed below therefore goes out through printf with an esc_*
 * on the argument, so the output escaping sniff has something real to check and
 * so a future edit cannot accidentally print an unescaped attribute.
 *
 * WHY THE CARD MARKUP IS HAND WRITTEN INSTEAD OF NESTED BLOCKS.
 *
 * The markup is the markup _page-projects.scss and _sections.scss already
 * select on: hz-card, hz-card__body, hz-card__meta, hz-card__lead,
 * hz-card__title, hz-card__desc, hz-card__foot and hz-link. That stylesheet
 * cancels Core's is-layout-flow block gap with two class selectors, so the
 * class names are load bearing and not decoration. A previous version of this
 * file emitted portfolio-list__card and friends, which appear nowhere in the
 * compiled stylesheet, so the grid rendered with every spacing and colour rule
 * the design depends on missing. The names below are the ones that exist.
 *
 * The grid is a real list without being a ul. Each card is an article, which is
 * what the static markup used and what the filter script moves with
 * appendChild(), so role="list" on the grid and role="listitem" on each card
 * carry the list semantics. A ul here would be invalid, because the grid also
 * holds the Pagination block when there is more than one page of results.
 *
 * WHY NOTHING IS DECLARED HERE.
 *
 * Core includes this file once per block instance on the request. A function or
 * class declared in it would be declared again for the second block on the
 * page, which is a fatal redeclaration error. The project role reader lives in
 * inc/portfolio-cpt.php, which functions.php loads once.
 *
 * WHY EVERY LOCAL IS PREFIXED.
 *
 * This file is included into the global scope of the request, not into a
 * function, so a plain $count here is a genuine global variable and collides
 * with anything else on the request that happens to use the same name. The
 * theme's PHPCS ruleset enforces that with PrefixAllGlobals, which is correct for
 * an included file even though it reads like ceremony in a procedural file.
 *
 * WHY A WP_Query AND NOT REST.
 *
 * The REST route exists so the block editor can read and save projects, not so
 * the front end has to fetch them over the network and wait for JavaScript. A
 * server side WP_Query means the grid is in the initial HTML, so it is readable
 * with scripting disabled and it costs one bounded indexed query rather than a
 * round trip plus a client side render.
 *
 * WHY NO FEATURED IMAGE IS PRINTED.
 *
 * get_the_post_thumbnail() is deliberately absent. The stylesheet has no rule
 * for a cover image inside hz-card, so a thumbnail would land in the card as an
 * unstyled img and push the body and foot down, which is the design moving for
 * a field that has no design yet. It belongs in with a matching rule, not here.
 *
 * PAGINATION IS THE CORE BLOCK, NOT LOCAL MARKUP.
 *
 * When the result set is larger than the requested count, a core Pagination
 * block is rendered through render_block() on a block array. That is the core
 * block producing its own markup, its own accessibility and its own screen
 * reader text, so none of that is reimplemented here.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/*
 * The attribute values arrive from the block comment, so they are author input
 * rather than trusted server configuration. count is clamped into a range that
 * costs one bounded query, and order is matched against an explicit allow list
 * rather than handed to WP_Query, because orderby and order are two separate
 * arguments and a lookup that resolves one while inferring the other is how an
 * ascending request ends up descending.
 *
 * The default order is menu_order ascending then date descending. The page this
 * block replaced listed its case studies in a deliberate editorial sequence
 * rather than by publication date, so position in the list is the first sort
 * key and recency only breaks ties between two posts left at the same position.
 */
$maulik_portfolio_list_count = isset( $attributes['count'] ) ? (int) $attributes['count'] : 9;
$maulik_portfolio_list_count = min( 24, max( 1, $maulik_portfolio_list_count ) );

$maulik_portfolio_list_order_map = array(
	'menu-order' => array(
		'orderby' => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
		'order'   => array(
			'ASC',
			'DESC',
		),
	),
	'date'       => array(
		'orderby' => 'date',
		'order'   => 'DESC',
	),
	'date-asc'   => array(
		'orderby' => 'date',
		'order'   => 'ASC',
	),
	'title'      => array(
		'orderby' => 'title',
		'order'   => 'ASC',
	),
	'modified'   => array(
		'orderby' => 'modified',
		'order'   => 'DESC',
	),
	'rand'       => array(
		'orderby' => 'rand',
		'order'   => 'ASC',
	),
);

$maulik_portfolio_list_order_key = isset( $attributes['order'] ) && is_string( $attributes['order'] )
	? $attributes['order']
	: 'menu-order';

$maulik_portfolio_list_order = isset( $maulik_portfolio_list_order_map[ $maulik_portfolio_list_order_key ] )
	? $maulik_portfolio_list_order_map[ $maulik_portfolio_list_order_key ]
	: $maulik_portfolio_list_order_map['menu-order'];

$maulik_portfolio_list_args = array(
	'post_type'              => 'project',
	'post_status'            => 'publish',
	'posts_per_page'         => $maulik_portfolio_list_count,
	'orderby'                => $maulik_portfolio_list_order['orderby'],
	'order'                  => $maulik_portfolio_list_order['order'],
	'ignore_sticky_posts'    => true,
	'no_found_rows'          => false,
	'update_post_meta_cache' => true,
	'update_post_term_cache' => false,
);

$maulik_portfolio_list_query = new WP_Query( $maulik_portfolio_list_args );

/*
 * get_block_wrapper_attributes() is Core's own attribute builder: it escapes
 * every value it prints and returns a ready made attribute string, so there is
 * nothing left here for esc_attr() to do and applying one would double encode
 * it. The text argument is escaped by the same statement.
 *
 * The empty branch deliberately prints no grid. An empty ul inside a two column
 * grid is a broken looking box rather than an honest absence, and the line
 * below says only that there is nothing published yet, so the page makes no
 * claim about how many projects the owner has.
 */
if ( ! $maulik_portfolio_list_query->have_posts() ) {
	wp_reset_postdata();

	$maulik_portfolio_list_empty_attributes = get_block_wrapper_attributes(
		array( 'class' => 'pj-work__intro' )
	);

	printf(
		'<div %1$s><p class="pj-work__lede">%2$s</p></div>',
		$maulik_portfolio_list_empty_attributes, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core builds and escapes the attribute string itself.
		esc_html__( 'No projects have been published yet.', 'maulik-portfolio' )
	);

	unset( $maulik_portfolio_list_empty_attributes );

	return;
}

$maulik_portfolio_list_cards = '';
$maulik_portfolio_list_index = 0;

while ( $maulik_portfolio_list_query->have_posts() ) {
	$maulik_portfolio_list_query->the_post();

	++$maulik_portfolio_list_index;

	$maulik_portfolio_list_id           = (int) get_the_ID();
	$maulik_portfolio_list_title        = get_the_title( $maulik_portfolio_list_id );
	$maulik_portfolio_list_role         = maulik_portfolio_get_project_role( $maulik_portfolio_list_id );
	$maulik_portfolio_list_excerpt      = get_the_excerpt( $maulik_portfolio_list_id );
	$maulik_portfolio_list_show_excerpt = isset( $attributes['showExcerpt'] ) ? (bool) $attributes['showExcerpt'] : true;
	$maulik_portfolio_list_slug         = (string) get_post_field( 'post_name', $maulik_portfolio_list_id );

	/*
	 * The card keeps its own id so it can be targeted, and the link points at
	 * it. The post type is registered with publicly_queryable false, so
	 * get_permalink() has no addressable single project route to return and a
	 * link built from it would resolve to a 404. While that is the case the card
	 * links to its own anchor, which is what the page did before this block
	 * read the CPT. Flipping publicly_queryable is enough to make these links
	 * resolve, with no change here.
	 */
	$maulik_portfolio_list_post_type_object = get_post_type_object( 'project' );
	$maulik_portfolio_list_permalink        = '';

	if ( $maulik_portfolio_list_post_type_object instanceof WP_Post_Type
		&& $maulik_portfolio_list_post_type_object->publicly_queryable
	) {
		$maulik_portfolio_list_permalink = (string) get_permalink( $maulik_portfolio_list_id );
	}

	$maulik_portfolio_list_anchor = '' !== $maulik_portfolio_list_slug ? $maulik_portfolio_list_slug : 'project-' . $maulik_portfolio_list_id;

	$maulik_portfolio_list_href = '' !== $maulik_portfolio_list_permalink
		? $maulik_portfolio_list_permalink
		: '#' . $maulik_portfolio_list_anchor;

	/*
	 * The meta row is two spans: the position in the list with the kind of work
	 * it is, and the role line. The position is derived from the query result
	 * order, so it renumbers itself when the order attribute changes instead of
	 * being a number someone has to keep correct by hand.
	 */
	$maulik_portfolio_list_kind = sprintf(
		/* translators: %1$d: the position of this project in the grid, zero padded to two digits. */
		esc_html__( '%1$02d · Professional Project', 'maulik-portfolio' ),
		$maulik_portfolio_list_index
	);

	$maulik_portfolio_list_meta_spans = sprintf(
		'<span class="hz-meta">%s</span>',
		$maulik_portfolio_list_kind
	);

	if ( '' !== $maulik_portfolio_list_role ) {
		$maulik_portfolio_list_meta_spans .= sprintf(
			'<span class="hz-meta">%s</span>',
			esc_html( $maulik_portfolio_list_role )
		);
	}

	$maulik_portfolio_list_meta = sprintf(
		'<div class="wp-block-group hz-card__meta">%s</div>',
		$maulik_portfolio_list_meta_spans // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Both spans were escaped when this fragment was built.
	);

	$maulik_portfolio_list_desc = '';

	if ( $maulik_portfolio_list_show_excerpt && '' !== $maulik_portfolio_list_excerpt ) {
		$maulik_portfolio_list_desc = sprintf(
			'<p class="hz-card__desc">%s</p>',
			esc_html( wp_strip_all_tags( $maulik_portfolio_list_excerpt, true ) )
		);
	}

	/*
	 * The link is a real anchor with a real href, so it is in the tab order and
	 * its text is its accessible name. An unstyled div with a click handler
	 * would be reachable only with a mouse and would have no name at all.
	 */
	$maulik_portfolio_list_foot = sprintf(
		'<div class="wp-block-group hz-card__foot"><a class="hz-link" href="%1$s"><span>%2$s</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hz-icon" aria-hidden="true" focusable="false"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a></div>',
		esc_url( $maulik_portfolio_list_href ),
		esc_html_x( 'View Case Study', 'link on a project card', 'maulik-portfolio' )
	);

	$maulik_portfolio_list_cards .= sprintf(
		'<article class="wp-block-group hz-card pj-card" role="listitem" id="%1$s"><div class="wp-block-group hz-card__body">%2$s<div class="wp-block-group hz-card__lead"><h3 class="wp-block-heading hz-card__title">%3$s</h3>%4$s</div></div>%5$s</article>',
		esc_attr( $maulik_portfolio_list_anchor ),
		$maulik_portfolio_list_meta, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Both spans were escaped when this fragment was built.
		esc_html( $maulik_portfolio_list_title ),
		$maulik_portfolio_list_desc, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- It was escaped when this fragment was built.
		$maulik_portfolio_list_foot // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The href and the link text were escaped when this fragment was built.
	);
}

wp_reset_postdata();

/*
 * No count line is emitted here on purpose. The "Showing N of M" paragraph is a
 * sibling of the grid inside pj-work__head in the page, not a child of the grid,
 * and emitting one from inside the grid wrapper would both duplicate that
 * paragraph and make it a grid item, which is a layout change rather than a
 * rendering of the same thing.
 */
$maulik_portfolio_list_markup = $maulik_portfolio_list_cards;

$maulik_portfolio_list_maxpage = (int) $maulik_portfolio_list_query->max_num_pages;

if ( $maulik_portfolio_list_maxpage > 1 ) {
	/*
	 * The rendered argument is a block array, not a comment string, and that is
	 * the whole reason this call is safe. Serialising it as
	 * '<!-- wp:pagination {...} /-->' would mean running esc_attr() over JSON,
	 * and esc_attr() turns every double quote into &quot;, which Core's parser
	 * does not decode, so the block is dropped and the grid renders with no
	 * pager and no error anywhere. An array carries no quotes to escape.
	 */
	$maulik_portfolio_list_markup .= render_block(
		array(
			'core/pagination',
			array(
				'paginationArrow' => 'arrow',
				'layout'          => array(
					'type'           => 'flex',
					'justifyContent' => 'space-between',
				),
			),
			array(),
		)
	);
}

/*
 * The wrapper IS the grid, not a container around the grid, and that is a hard
 * requirement rather than a style choice.
 *
 * assets/js/project-filters.js reads this.work.querySelector( '.pj-work__grid' )
 * and then takes Array.prototype.slice.call( this.grid.children ) as the list of
 * cards. It also appendChild()s a matching card back onto the grid to reorder
 * it. If the cards sat one level deeper inside this block's own element, every
 * child the filter saw would be that single wrapper, so no card would ever match
 * a filter and the tablist would silently do nothing. The class list below is
 * therefore the grid's, on the element whose children are the cards.
 *
 * hz-work__grid is the same class the static grid carried, and it is what
 * supplies display: grid and the 32px gap. pj-work__grid is the page scoped
 * class that zeroes Core's is-layout-flow block gap on each card.
 *
 * The element is a ul and the cards are li, so the number of items a screen
 * reader announces matches the number of cards, and it stays a grid container
 * for the filter script.
 */
$maulik_portfolio_list_wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'wp-block-group pj-work__grid hz-work__grid',
		'role'  => 'list',
	)
);

printf(
	'<div %1$s>%2$s</div>',
	$maulik_portfolio_list_wrapper_attributes, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core builds and escapes the attribute string itself.
	$maulik_portfolio_list_markup // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Every fragment in it was escaped when it was built.
);

unset( $maulik_portfolio_list_wrapper_attributes );

unset(
	$maulik_portfolio_list_anchor,
	$maulik_portfolio_list_args,
	$maulik_portfolio_list_cards,
	$maulik_portfolio_list_count,
	$maulik_portfolio_list_desc,
	$maulik_portfolio_list_excerpt,
	$maulik_portfolio_list_foot,
	$maulik_portfolio_list_href,
	$maulik_portfolio_list_id,
	$maulik_portfolio_list_index,
	$maulik_portfolio_list_markup,
	$maulik_portfolio_list_maxpage,
	$maulik_portfolio_list_meta,
	$maulik_portfolio_list_order,
	$maulik_portfolio_list_order_key,
	$maulik_portfolio_list_order_map,
	$maulik_portfolio_list_permalink,
	$maulik_portfolio_list_post_type_object,
	$maulik_portfolio_list_query,
	$maulik_portfolio_list_role,
	$maulik_portfolio_list_show_excerpt,
	$maulik_portfolio_list_slug,
	$maulik_portfolio_list_title
);
