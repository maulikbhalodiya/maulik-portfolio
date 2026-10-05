<?php
/**
 * Server side render for the portfolio list block.
 *
 * WHY THIS FILE DECLARES NOTHING.
 *
 * WordPress includes render.php once for every instance of the block type on a
 * request. A function or class declared here would therefore be declared again
 * for the second block on the page, which is a fatal redeclaration error. Every
 * shared helper this block needs, including the project role reader, lives in
 * inc/portfolio-cpt.php, which functions.php loads once.
 *
 * WHY EVERY LOCAL IS PREFIXED.
 *
 * A render file is included into the global scope of the request, not into a
 * function, so a plain $count here is a genuine global variable and collides
 * with anything else on the request that happens to use the same name. The
 * theme's PHPCS ruleset enforces that with PrefixAllGlobals, which is correct
 * for an included file even though it reads like ceremony inside a file that
 * appears to be procedural.
 *
 * WHY A WP_Query AND NOT REST.
 *
 * The REST route exists so the block editor can read and save projects, not so
 * the front end has to fetch them over the network and wait for JavaScript. A
 * server side WP_Query means the grid is in the initial HTML, so it is readable
 * with scripting disabled and it costs one bounded indexed query rather than a
 * round trip plus a client side render.
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
 * arguments and an unmapped string here would be a query shape nobody intended.
 */
$maulik_portfolio_list_count = isset( $attributes['count'] ) ? (int) $attributes['count'] : 6;
$maulik_portfolio_list_count = min( 24, max( 1, $maulik_portfolio_list_count ) );

$maulik_portfolio_list_show_excerpt = isset( $attributes['showExcerpt'] ) ? (bool) $attributes['showExcerpt'] : true;

/*
 * One map, holding both arguments, because orderby and order are separate and a
 * lookup that resolves one and infers the other is how an ascending request
 * ends up descending. rand is excluded: WP_Query ignores order for it anyway.
 */
$maulik_portfolio_list_order_map = array(
	'date'     => array(
		'orderby' => 'date',
		'order'   => 'DESC',
	),
	'date-asc' => array(
		'orderby' => 'date',
		'order'   => 'ASC',
	),
	'title'    => array(
		'orderby' => 'title',
		'order'   => 'ASC',
	),
	'modified' => array(
		'orderby' => 'modified',
		'order'   => 'DESC',
	),
	'rand'     => array(
		'orderby' => 'rand',
		'order'   => 'ASC',
	),
);

$maulik_portfolio_list_order_key = isset( $attributes['order'] ) && is_string( $attributes['order'] )
	? $attributes['order']
	: 'date';

$maulik_portfolio_list_order = isset( $maulik_portfolio_list_order_map[ $maulik_portfolio_list_order_key ] )
	? $maulik_portfolio_list_order_map[ $maulik_portfolio_list_order_key ]
	: $maulik_portfolio_list_order_map['date'];

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

if ( ! $maulik_portfolio_list_query->have_posts() ) {
	wp_reset_postdata();

	$maulik_portfolio_list_empty = sprintf(
		'<p %1$s>%2$s</p>',
		get_block_wrapper_attributes( array( 'class' => 'portfolio-list__empty' ) ),
		esc_html__( 'No projects have been published yet.', 'maulik-portfolio' )
	);

	return $maulik_portfolio_list_empty;
}

$maulik_portfolio_list_cards = '';

while ( $maulik_portfolio_list_query->have_posts() ) {
	$maulik_portfolio_list_query->the_post();

	$maulik_portfolio_list_id      = (int) get_the_ID();
	$maulik_portfolio_list_title   = get_the_title( $maulik_portfolio_list_id );
	$maulik_portfolio_list_role    = maulik_portfolio_get_project_role( $maulik_portfolio_list_id );
	$maulik_portfolio_list_excerpt = get_the_excerpt( $maulik_portfolio_list_id );

	$maulik_portfolio_list_card_classes = array( 'portfolio-list__card' );

	if ( '' !== $maulik_portfolio_list_role ) {
		$maulik_portfolio_list_card_classes[] = 'portfolio-list__card-has-role';
	}

	$maulik_portfolio_list_cover = '';

	if ( has_post_thumbnail( $maulik_portfolio_list_id ) ) {
		$maulik_portfolio_list_cover = get_the_post_thumbnail(
			$maulik_portfolio_list_id,
			'large',
			array(
				'class'    => 'portfolio-list__cover',
				'loading'  => 'lazy',
				'decoding' => 'async',
			)
		);
	}

	$maulik_portfolio_list_secondary = '';

	if ( '' !== $maulik_portfolio_list_role ) {
		$maulik_portfolio_list_secondary .= sprintf(
			'<p class="portfolio-list__role">%s</p>',
			esc_html( $maulik_portfolio_list_role )
		);
	}

	if ( $maulik_portfolio_list_show_excerpt && '' !== $maulik_portfolio_list_excerpt ) {
		$maulik_portfolio_list_secondary .= sprintf(
			'<p class="portfolio-list__excerpt">%s</p>',
			esc_html( wp_strip_all_tags( $maulik_portfolio_list_excerpt, true ) )
		);
	}

	/*
	 * A details element rather than a div with a hidden attribute, so the
	 * disclosure is operable and readable before any script arrives.
	 *
	 * The id in data-wp-context is what the view script compares against the
	 * store's openId, so each card decides for itself whether it is the open one
	 * rather than the script keeping a list of open cards in step with the DOM.
	 */
	if ( '' !== $maulik_portfolio_list_secondary ) {
		$maulik_portfolio_list_secondary = sprintf(
			'<details class="portfolio-list__more" data-wp-context="%1$s" data-wp-on--click="actions.toggleCard" data-wp-bind--open="state.isOpen"><summary class="portfolio-list__more-summary">%2$s</summary><div class="portfolio-list__more-body">%3$s</div></details>',
			esc_attr( (string) $maulik_portfolio_list_id ),
			esc_html__( 'More about this project', 'maulik-portfolio' ),
			$maulik_portfolio_list_secondary
		);
	}

	$maulik_portfolio_list_cards .= sprintf(
		'<li class="%1$s">%2$s<h3 class="portfolio-list__title">%3$s</h3>%4$s</li>',
		esc_attr( implode( ' ', $maulik_portfolio_list_card_classes ) ),
		$maulik_portfolio_list_cover,
		esc_html( $maulik_portfolio_list_title ),
		$maulik_portfolio_list_secondary
	);
}

wp_reset_postdata();

$maulik_portfolio_list_markup = sprintf(
	'<ul class="portfolio-list__grid">%s</ul>',
	$maulik_portfolio_list_cards
);

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

$maulik_portfolio_list_wrapper = sprintf(
	'<div %1$s>%2$s</div>',
	get_block_wrapper_attributes(
		array(
			'class'               => 'portfolio-list',
			'data-wp-interactive' => 'maulik-portfolio/portfolio-list',
		)
	),
	$maulik_portfolio_list_markup
);

unset(
	$maulik_portfolio_list_args,
	$maulik_portfolio_list_card_classes,
	$maulik_portfolio_list_cards,
	$maulik_portfolio_list_cover,
	$maulik_portfolio_list_excerpt,
	$maulik_portfolio_list_id,
	$maulik_portfolio_list_markup,
	$maulik_portfolio_list_maxpage,
	$maulik_portfolio_list_order,
	$maulik_portfolio_list_order_key,
	$maulik_portfolio_list_order_map,
	$maulik_portfolio_list_query,
	$maulik_portfolio_list_role,
	$maulik_portfolio_list_secondary,
	$maulik_portfolio_list_show_excerpt,
	$maulik_portfolio_list_title,
	$maulik_portfolio_list_wrapper
);

return $maulik_portfolio_list_wrapper;
