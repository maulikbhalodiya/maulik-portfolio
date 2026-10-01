<?php
/**
 * Theme setup and global filters.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'maulik_portfolio_setup' ) ) {
	/**
	 * Register theme support and the global performance filters.
	 *
	 * The two asset filters below are the single biggest performance lever a
	 * block theme has. Without them Core ships every core block stylesheet and
	 * every core block script to every page, including the ones that never use
	 * the block. Together they let Core queue only what the current template
	 * actually renders.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function maulik_portfolio_setup() {
		add_filter( 'should_load_separate_core_block_assets', '__return_true' );
		add_filter( 'should_load_block_assets_on_demand', '__return_true' );

		// This theme makes no outbound HTTP requests, and remote patterns would
		// block Core from rendering on a slow or filtered network.
		add_filter( 'should_load_remote_block_patterns', '__return_false' );

		add_action( 'after_setup_theme', 'maulik_portfolio_content_width' );
		add_action( 'wp_enqueue_scripts', 'maulik_portfolio_enqueue_styles' );
		add_action( 'wp_head', 'maulik_portfolio_noindex' );
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
