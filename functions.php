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
 * Enqueue the section switcher script.
 *
 * Sections 02, 03 and 06 are core blocks whose content stays in post_content,
 * so there is no dynamic block to hang a view script off the way
 * inc/hero-ecosystem.php does. That makes the conditional load explicit rather
 * than automatic, and it is worth being precise about why it is still cheap.
 *
 * The check is has_block() on the queried post, not a guess from the page slug.
 * has_block() is a string test against the already loaded post content, so it
 * costs nothing measurable, and it means the script is loaded on a singular view
 * whose post_content actually carries a core/html block, which is where every
 * one of the three control strips lives. A page with no interactive section
 * never downloads it.
 *
 * The script is registered unconditionally and enqueued conditionally, so a
 * child theme can declare it as a dependency without knowing the path.
 *
 * The dependency list is empty on purpose. The file is a dependency free IIFE
 * with no jQuery and no globals, so there is nothing to wait for.
 *
 * @since 0.1.0
 *
 * @return void
 */
function maulik_portfolio_enqueue_section_switcher_script() {
	$rel = 'assets/js/section-switcher.js';

	wp_register_script(
		'maulik-portfolio-section-switcher',
		get_parent_theme_file_uri( $rel ),
		array(),
		maulik_portfolio_asset_version( $rel ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	if ( ! is_singular() ) {
		return;
	}

	if ( ! has_block( 'core/html', get_queried_object_id() ) ) {
		return;
	}

	wp_enqueue_script( 'maulik-portfolio-section-switcher' );
}
add_action( 'wp_enqueue_scripts', 'maulik_portfolio_enqueue_section_switcher_script' );
require_once MAULIK_PORTFOLIO_DIR . 'inc/project-filters.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/rankkernel-filters.php';

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

	// The Resume page hero carries a Print View control. It is a real
	// <button data-rs-print> rendered by a core button block, and this scoped
	// IIFE is what makes it call window.print(). It follows the header script
	// precedent above: dependency free, enqueued on the front end only, and it
	// no-ops when the control is absent from the page.
	$resume_rel = 'assets/js/resume-actions.js';

	wp_register_script(
		'maulik-portfolio-resume-actions',
		get_parent_theme_file_uri( $resume_rel ),
		array(),
		maulik_portfolio_asset_version( $resume_rel ),
		true
	);

	wp_enqueue_script( 'maulik-portfolio-resume-actions' );
}
add_action( 'wp_enqueue_scripts', 'maulik_portfolio_enqueue_site_header_script' );

/**
 * Enqueue the RankKernel subsystem status filter script.
 *
 * The filter is one scoped IIFE with no jQuery and no dependency, following
 * assets/js/site-header.js, and it is the only thing that adds behaviour to the
 * RankKernel status directory.
 *
 * It is enqueued from render_block rather than from wp_enqueue_scripts, so the
 * browser only downloads it on a response that actually rendered the filter.
 * That is the same mechanism WordPress itself uses for per-block view scripts
 * under should_load_separate_core_block_assets, applied here explicitly so it
 * does not depend on that setting being on. A response without the filter never
 * prints the file at all.
 *
 * Registration happens on init rather than on wp_enqueue_scripts because
 * render_block fires while the content is being rendered, which for a classic
 * block theme is after wp_head and before wp_footer. A script enqueued at that
 * point is still printed, because it is registered in the footer.
 *
 * The front end is the only place the script belongs. The site editor renders
 * blocks too, but its own preview already carries its interactivity, and the
 * filter is inert in the editor because no view script runs there.
 *
 * @since 0.1.0
 *
 * @param string $block_content Rendered block content.
 * @return string Rendered block content, unchanged.
 */
function maulik_portfolio_enqueue_rankkernel_filter_script( $block_content ) {
	if ( is_admin() || ! is_string( $block_content ) ) {
		return $block_content;
	}

	if ( false !== strpos( $block_content, 'data-rk-filter' ) ) {
		wp_enqueue_script( 'maulik-portfolio-rankkernel-filter' );
	}

	return $block_content;
}
add_filter( 'render_block', 'maulik_portfolio_enqueue_rankkernel_filter_script', 10, 1 );

/**
 * Register the RankKernel subsystem status filter script.
 *
 * Registered separately from the enqueue above so the handle exists by the time
 * the first block renders, and so a child theme can declare it as a dependency.
 *
 * @since 0.1.0
 *
 * @return void
 */
function maulik_portfolio_register_rankkernel_filter_script() {
	$rel = 'assets/js/rankkernel-filter.js';

	wp_register_script(
		'maulik-portfolio-rankkernel-filter',
		get_parent_theme_file_uri( $rel ),
		array(),
		maulik_portfolio_asset_version( $rel ),
		true
	);
}
add_action( 'init', 'maulik_portfolio_register_rankkernel_filter_script' );
