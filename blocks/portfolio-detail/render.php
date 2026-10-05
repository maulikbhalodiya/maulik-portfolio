<?php
/**
 * Server side render for the portfolio detail block.
 *
 * WHY THIS FILE ECHOES INSTEAD OF RETURNING. THIS IS NOT A STYLE CHOICE.
 *
 * WordPress does not call a file rendered block the way it calls a
 * render_callback. When block.json says "render": "file:./render.php", Core
 * wraps the file in ob_start(), require s it, and returns ob_get_clean(). A
 * return statement inside that file returns from the require, and its value is
 * discarded, because the closure returns the buffer and never assigns what
 * require produced. So a render.php that returns a string renders as an empty
 * block with no error, no warning and no clue where the markup went. This was
 * measured on this theme before the file was rewritten: the block registered,
 * reported a callable render callback, and printed nothing.
 *
 * Every value printed below therefore goes out through printf with an esc_*
 * on the argument, so the output escaping sniff has something real to check and
 * so a future edit cannot accidentally print an unescaped attribute.
 *
 * WHY NOTHING IS DECLARED HERE.
 *
 * Core includes this file once per block instance on the request. A function or
 * class declared in it would be declared again for the second block on the page,
 * which is a fatal redeclaration error. The project role reader lives in
 * inc/portfolio-cpt.php, which functions.php loads once.
 *
 * WHY EVERY LOCAL IS PREFIXED.
 *
 * This file is included into the global scope of the request, not into a
 * function, so a plain $show_meta here is a genuine global variable and
 * collides with anything else on the request that uses the same name. The theme's
 * PHPCS ruleset enforces that with PrefixAllGlobals, which is correct for an
 * included file even though it reads like ceremony in a procedural file.
 *
 * WHICH PROJECT IS "CURRENT".
 *
 * The block reads the post the template is already rendering, from the main
 * query, and it refuses to render a project at all unless that post is a
 * published project. That check is the reason this block can be dropped into any
 * template safely: on a page, a post, an archive or a 404 it prints its empty
 * state rather than borrowing the wrong post.
 *
 * It deliberately does not fall back to any project. A detail view that silently
 * substituted the most recent project would publish one project's content under
 * another project's heading, which is worse than showing nothing.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$maulik_portfolio_detail_show_meta = isset( $attributes['showMeta'] ) ? (bool) $attributes['showMeta'] : true;

/*
 * The main query is read rather than a fresh WP_Query, so the block shows the
 * project the visitor actually requested and never runs a second query to find
 * it. get_queried_object_id() is the template context, which is what a template
 * block means by current.
 */
$maulik_portfolio_detail_post_id = (int) get_queried_object_id();
$maulik_portfolio_detail_post    = $maulik_portfolio_detail_post_id > 0
	? get_post( $maulik_portfolio_detail_post_id )
	: null;

$maulik_portfolio_detail_is_project = $maulik_portfolio_detail_post instanceof WP_Post
	&& 'project' === $maulik_portfolio_detail_post->post_type
	&& 'publish' === $maulik_portfolio_detail_post->post_status;

if ( ! $maulik_portfolio_detail_is_project ) {
	$maulik_portfolio_detail_empty_attributes = get_block_wrapper_attributes(
		array( 'class' => 'portfolio-detail portfolio-detail--empty' )
	);

	printf(
		'<div %1$s><p class="portfolio-detail__empty">%2$s</p></div>',
		$maulik_portfolio_detail_empty_attributes, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core builds and escapes the attribute string itself.
		esc_html__( 'This block renders one project and needs a project template around it.', 'maulik-portfolio' )
	);

	unset( $maulik_portfolio_detail_empty_attributes );

	return;
}

$maulik_portfolio_detail_id      = (int) $maulik_portfolio_detail_post->ID;
$maulik_portfolio_detail_title   = get_the_title( $maulik_portfolio_detail_id );
$maulik_portfolio_detail_role    = maulik_portfolio_get_project_role( $maulik_portfolio_detail_id );
$maulik_portfolio_detail_excerpt = get_the_excerpt( $maulik_portfolio_detail_id );

$maulik_portfolio_detail_cover = '';

if ( has_post_thumbnail( $maulik_portfolio_detail_id ) ) {
	$maulik_portfolio_detail_cover = get_the_post_thumbnail(
		$maulik_portfolio_detail_id,
		'full',
		array(
			'class'    => 'portfolio-detail__cover',
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);
}

$maulik_portfolio_detail_excerpt_markup = '';

if ( '' !== $maulik_portfolio_detail_excerpt ) {
	$maulik_portfolio_detail_excerpt_markup = sprintf(
		'<p class="portfolio-detail__excerpt">%1$s</p>',
		esc_html( wp_strip_all_tags( $maulik_portfolio_detail_excerpt, true ) )
	);
}

/*
 * The meta line sits inside a details element, so it is operable and readable
 * before any script arrives. Only the publication date is unconditional: role is
 * author input through a registered meta key and may legitimately be empty.
 */
$maulik_portfolio_detail_meta_parts = array();

if ( '' !== $maulik_portfolio_detail_role ) {
	$maulik_portfolio_detail_meta_parts[] = sprintf(
		'<span class="portfolio-detail__role">%s</span>',
		esc_html( $maulik_portfolio_detail_role )
	);
}

$maulik_portfolio_detail_meta_parts[] = sprintf(
	'<time class="portfolio-detail__date" datetime="%1$s">%2$s</time>',
	esc_attr( (string) get_the_date( 'c', $maulik_portfolio_detail_id ) ),
	esc_html( (string) get_the_date( '', $maulik_portfolio_detail_id ) )
);

$maulik_portfolio_detail_meta_markup = '';

if ( $maulik_portfolio_detail_show_meta ) {
	$maulik_portfolio_detail_meta_markup = sprintf(
		'<details class="portfolio-detail__meta" data-wp-on--click="actions.onToggle" data-wp-bind--open="state.isOpen"><summary class="portfolio-detail__meta-summary">%1$s</summary><div class="portfolio-detail__meta-body">%2$s</div></details>',
		esc_html__( 'Project details', 'maulik-portfolio' ),
		implode( '', $maulik_portfolio_detail_meta_parts )
	);
}

/*
 * the_content is applied rather than post_content printed raw. The filter is
 * what gives inner blocks their own render and their own escaping, so bypassing
 * it would print unrendered block comments. The filter escapes its own output, so
 * re-escaping it here would double encode it.
 *
 * the_content is a core hook and is deliberately not renamed or wrapped. A theme
 * that renamed it would break every plugin extending post content, which is the
 * opposite of what a theme should do.
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core hook, a theme must not rename it.
$maulik_portfolio_detail_content = apply_filters( 'the_content', $maulik_portfolio_detail_post->post_content );

$maulik_portfolio_detail_wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class'               => 'portfolio-detail',
		'data-wp-interactive' => 'maulik-portfolio/portfolio-detail',
	)
);

printf(
	'<article %1$s>%2$s<h2 class="portfolio-detail__title">%3$s</h2>%4$s%5$s</article>',
	$maulik_portfolio_detail_wrapper_attributes, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core builds and escapes the attribute string itself.
	$maulik_portfolio_detail_cover, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_the_post_thumbnail() escapes the src, alt, class and every other attribute it prints.
	esc_html( $maulik_portfolio_detail_title ),
	$maulik_portfolio_detail_excerpt_markup . $maulik_portfolio_detail_meta_markup, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Every fragment in both was escaped when it was built.
	$maulik_portfolio_detail_content // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content escapes its own output, and re-escaping it here would double encode it.
);

unset( $maulik_portfolio_detail_wrapper_attributes );

unset(
	$maulik_portfolio_detail_content,
	$maulik_portfolio_detail_cover,
	$maulik_portfolio_detail_excerpt,
	$maulik_portfolio_detail_excerpt_markup,
	$maulik_portfolio_detail_id,
	$maulik_portfolio_detail_is_project,
	$maulik_portfolio_detail_meta_markup,
	$maulik_portfolio_detail_meta_parts,
	$maulik_portfolio_detail_post,
	$maulik_portfolio_detail_post_id,
	$maulik_portfolio_detail_role,
	$maulik_portfolio_detail_show_meta,
	$maulik_portfolio_detail_title
);
