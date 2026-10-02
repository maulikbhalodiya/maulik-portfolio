<?php
/**
 * Maulik Portfolio functions and definitions.
 *
 * This file is intentionally a thin loader and nothing else. Every piece of
 * behaviour lives in a file under inc/ so that a child theme can replace a
 * single concern without having to fork this file, and so that the load order
 * stays obvious at a glance.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme version.
 *
 * Used to bust asset caches. Kept in sync with the Version header in
 * style.css by hand, because Core does not read style.css at runtime for this.
 */
define( 'MAULIK_PORTFOLIO_VERSION', '0.1.0' );

/**
 * Absolute path to the theme directory, with a trailing slash.
 */
define( 'MAULIK_PORTFOLIO_DIR', trailingslashit( get_parent_theme_file_path() ) );

/**
 * URL of the theme directory, with a trailing slash.
 */
define( 'MAULIK_PORTFOLIO_URI', trailingslashit( get_parent_theme_file_uri() ) );

require_once MAULIK_PORTFOLIO_DIR . 'inc/helpers.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/setup.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/assets.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/fonts.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/performance.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/patterns-category.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/hero-ecosystem.php';

/**
 * Enqueue the header scroll state script.
 *
 * The design header is fully transparent until the visitor scrolls past 24
 * pixels, at which point it takes the near black band, the hairline border and
 * the backdrop blur. A scroll position cannot be read in CSS, so it is read in
 * one small file that adds and removes a class on the header element.
 *
 * wp_enqueue_scripts fires on the front end only, which is the whole of the
 * requirement: the site editor, the REST API and wp-admin never load it, and
 * there is nothing to switch off for them.
 *
 * The handle is registered and enqueued separately rather than only enqueued,
 * so a child theme can declare it as a dependency of its own script without
 * having to know the path.
 *
 * The dependency list is empty on purpose. The file is a dependency free IIFE
 * with no jQuery, so there is nothing to wait for.
 *
 * @since 0.1.0
 *
 * @return void
 */
function maulik_portfolio_enqueue_site_header_script() {
	$rel = 'assets/js/site-header.js';

	wp_register_script(
		'maulik-portfolio-site-header',
		get_parent_theme_file_uri( $rel ),
		array(),
		maulik_portfolio_asset_version( $rel ),
		true
	);

	wp_enqueue_script( 'maulik-portfolio-site-header' );
}
add_action( 'wp_enqueue_scripts', 'maulik_portfolio_enqueue_site_header_script' );
