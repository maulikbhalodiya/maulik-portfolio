<?php
/**
 * Registers the block pattern categories used by this theme's patterns.
 *
 * Patterns in the patterns/ directory declare category slugs in their file
 * headers. Core only surfaces a pattern in the inserter when its category has
 * been registered, so an unregistered category is a pattern that parses
 * cleanly, registers without error, and is then invisible to the person
 * building the site. There is no failure to notice.
 *
 * Core's own categories, which are `header`, `footer` and `featured`, are
 * registered by WordPress itself and must not be registered here.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the pattern categories this theme owns.
 *
 * Kept as data rather than as three separate calls so that adding a category
 * is a single array entry, and so the set of categories a pattern can declare
 * is readable in one place.
 *
 * @since 0.2.0
 *
 * @return array<string, array<string, string>> Category slug mapped to label and description.
 */
function maulik_portfolio_pattern_categories() {
	return array(
		'maulik-portfolio-shell' => array(
			'label'       => __( 'Portfolio Shell', 'maulik-portfolio' ),
			'description' => __( 'Header and footer sections built from core blocks.', 'maulik-portfolio' ),
		),
		'maulik-portfolio-home'  => array(
			'label'       => __( 'Home Sections', 'maulik-portfolio' ),
			'description' => __( 'The eight sections of the homepage, one per pattern.', 'maulik-portfolio' ),
		),
		'maulik-portfolio-error' => array(
			'label'       => __( 'Not Found', 'maulik-portfolio' ),
			'description' => __( 'Content for the not found template.', 'maulik-portfolio' ),
		),
	);
}

/**
 * Registers every category this theme owns.
 *
 * Hooked to init at the default priority, which is what the block editor
 * expects. Registering later than init is too late for the inserter, and
 * registering earlier than init is too early because the function
 * register_block_pattern_category() does not exist yet.
 *
 * @since 0.2.0
 *
 * @return void
 */
function maulik_portfolio_register_pattern_categories() {
	foreach ( maulik_portfolio_pattern_categories() as $slug => $args ) {
		register_block_pattern_category( $slug, $args );
	}
}
add_action( 'init', 'maulik_portfolio_register_pattern_categories' );
