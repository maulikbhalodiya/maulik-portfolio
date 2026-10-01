<?php
/**
 * Theme setup and global filters.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'maulik_portfolio_setup' ) ) {
	/**
	 * Register theme support and the global filters.
	 *
	 * The two conditional block asset filters that used to live here now live
	 * in inc/performance.php, where the reasoning behind them can be recorded
	 * properly. They are not duplicated here, because a filter registered
	 * twice in two files makes it impossible to tell which copy a future
	 * change is meant to edit.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function maulik_portfolio_setup() {
		// This theme makes no outbound HTTP requests, and remote patterns would
		// block Core from rendering on a slow or filtered network.
		add_filter( 'should_load_remote_block_patterns', '__return_false' );

		add_action( 'after_setup_theme', 'maulik_portfolio_content_width' );
		add_action( 'wp_enqueue_scripts', 'maulik_portfolio_enqueue_styles' );

		/*
		 * Deliberately NOT registering a wp_head callback here.
		 *
		 * RankKernel owns the document head on this site: titles, meta
		 * descriptions, canonical URLs, Open Graph, Twitter cards, JSON-LD and
		 * robots directives, including the staging noindex. A theme callback
		 * competing with the active SEO plugin emits duplicate tags, and a
		 * duplicate canonical is actively harmful.
		 *
		 * A previous version of this file hooked maulik_portfolio_noindex() to
		 * wp_head from inc/seo.php. Deleting that file without removing the hook
		 * left wp_head calling a function that no longer existed, which is a
		 * fatal error part way through render_head(). The visible symptom was a
		 * page with a complete head and no body at all, returning HTTP 200.
		 *
		 * If a head callback is ever needed here again, check first that the
		 * function actually exists, and prefer a filter over an action so it
		 * participates in the existing output rather than appending to it.
		 */
	}
	add_action( 'after_setup_theme', 'maulik_portfolio_setup' );
}

if ( ! function_exists( 'maulik_portfolio_content_width' ) ) {
	/**
	 * Set the content width used by oEmbed and large images.
	 *
	 * This matches the layout.contentSize value in theme.json. WordPress cannot
	 * read that value on its own for this purpose.
	 *
	 * @since 0.1.0
	 *
	 * @global int $content_width
	 *
	 * @return void
	 */
	function maulik_portfolio_content_width() {
		/**
		 * Filters the theme content width.
		 *
		 * @since 0.1.0
		 *
		 * @param int $content_width Content width in pixels.
		 */
		$GLOBALS['content_width'] = (int) apply_filters( 'maulik_portfolio_content_width', 768 );
	}
}
