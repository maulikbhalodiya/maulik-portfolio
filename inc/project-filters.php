<?php
/**
 * Registration and server side render for the Projects archive filter tablist.
 *
 * WHY THIS IS A DYNAMIC BLOCK AND NOT SCOPED FRONT END SCRIPTING.
 *
 * Three approaches were available and the choice was made on one constraint
 * above all: a core block cannot carry an arbitrary data attribute.
 * WP_Block_Type::prepare_attributes_for_render() drops every attribute that is
 * not in the block's own schema, so a `data-pj-filter` written into the comment
 * of a core group block never reaches the HTML. The filter row therefore cannot
 * be built from core group and core paragraph blocks if the tabs need role,
 * aria-selected and data attributes, which they do. The options were a dynamic
 * block, a core/html block holding the row, or the Interactivity API.
 *
 * A dynamic block was chosen over core/html because core/html can only be
 * edited in the code editor, whereas a registered block appears in the inserter
 * with a title, a description and keywords, and because the view script attaches
 * through view_script_handles, which means WordPress only downloads the
 * behaviour on the pages where the block actually rendered. That is the same
 * conditional loading inc/hero-ecosystem.php already established, and it is
 * better than an is_page() slug test because it cannot drift from the content.
 *
 * The Interactivity API was considered and rejected. Core does ship
 * core/interactivity and this page does carry data-wp-interactive on some
 * elements, but the directives would have to live in the block markup as HTML
 * attributes, which puts the filter state in the same place as the labels and
 * makes a ten state tablist far harder to read, review and translate than a
 * twenty line keydown handler. It is the right tool for state that must survive
 * a round trip to the server; a client side visibility filter does not.
 *
 * WHY THE CARDS ARE TAGGED WITH CLASSES AND NOT WITH A JSON PAYLOAD.
 *
 * A JSON payload would have to be maintained beside the cards it describes, and
 * a card added in the editor would silently fail to appear under its own
 * filters. The category is instead carried on the card as pj-cat-* classes in
 * the block's className, which is the one list of custom classes a core block
 * genuinely supports and which an author sets in the editor's own CSS classes
 * field. The tag lives on the card it describes, so the two cannot drift.
 *
 * THE FILTERS THEMSELVES ARE NOT EDITABLE DATA.
 *
 * The set is fixed by the design reference as FILTER_OPTIONS in
 * src/pages/ProjectsArchivePage.tsx, and it is declared once below rather than
 * repeated in ten places. The labels are translatable so the row can be
 * localised even though the set is not.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'MAULIK_PORTFOLIO_PROJECT_FILTERS_BLOCK' ) ) {
	/**
	 * Block name. Kept as a constant so the render callback, the asset handle
	 * and the tab id prefix can never drift apart.
	 */
	define( 'MAULIK_PORTFOLIO_PROJECT_FILTERS_BLOCK', 'maulik-portfolio/project-filters' );

	/**
	 * View script handle for the tab behaviour.
	 */
	define( 'MAULIK_PORTFOLIO_PROJECT_FILTERS_HANDLE', 'maulik-portfolio-project-filters' );
}

if ( ! function_exists( 'maulik_portfolio_project_filters_tabs' ) ) {
	/**
	 * The filter tabs, in the order the design states them.
	 *
	 * `id` is the category slug the view script matches against the pj-cat-*
	 * classes on the cards. `show` is the space separated list of page sections
	 * that remain visible under that tab. The pair reproduces
	 * ProjectsArchivePage.tsx lines 33 to 58 exactly, including the two cases the
	 * reference special cases: `all` is not a category and constrains nothing,
	 * and `integration` matches a card that carries either the api or the
	 * automation tag.
	 *
	 * @since 0.4.0
	 *
	 * @return array<int, array<string, string>> Ordered list of tab definitions.
	 */
	function maulik_portfolio_project_filters_tabs() {
		return array(
			array(
				'id'    => 'all',
				'show'  => 'independent professional',
				'label' => __( 'All', 'maulik-portfolio' ),
			),
			array(
				'id'    => 'independent',
				'show'  => 'independent professional',
				'label' => __( 'Independent', 'maulik-portfolio' ),
			),
			array(
				'id'    => 'professional',
				'show'  => 'professional',
				'label' => __( 'Professional', 'maulik-portfolio' ),
			),
			array(
				'id'    => 'wordpress',
				'show'  => 'independent professional',
				'label' => __( 'WordPress', 'maulik-portfolio' ),
			),
			array(
				'id'    => 'php',
				'show'  => 'independent professional',
				'label' => __( 'PHP', 'maulik-portfolio' ),
			),
			array(
				'id'    => 'api',
				'show'  => 'independent professional',
				'label' => __( 'API', 'maulik-portfolio' ),
			),
			array(
				'id'    => 'payment',
				'show'  => 'professional',
				'label' => __( 'Payment', 'maulik-portfolio' ),
			),
			array(
				'id'    => 'security',
				'show'  => 'professional',
				'label' => __( 'Security', 'maulik-portfolio' ),
			),
			array(
				'id'    => 'integration',
				'show'  => 'professional',
				'label' => __( 'Integration', 'maulik-portfolio' ),
			),
			array(
				'id'    => 'woocommerce',
				'show'  => 'professional',
				'label' => __( 'WooCommerce', 'maulik-portfolio' ),
			),
		);
	}
}

if ( ! function_exists( 'maulik_portfolio_project_filters_render' ) ) {
	/**
	 * Render the filter tablist.
	 *
	 * Returns a string rather than echoing. Echoing here would put the markup
	 * through the escape output sniff for no benefit, and returning is what the
	 * block render callback contract expects.
	 *
	 * The markup is complete before any script runs. Every tab is a real button
	 * with an accessible name taken from its own text, the selected tab carries
	 * aria-selected, and the whole row is a tablist. No tabindex is written on
	 * any tab: the buttons are natively focusable, so all ten stay in the tab
	 * order and a roving tabindex would only take nine of them out of it. A
	 * visitor whose JavaScript never arrives therefore gets ten labelled
	 * controls that a keyboard can reach, and the first tab already states the
	 * set that is on screen. The page does not depend on the script to be
	 * readable, only to be filterable.
	 *
	 * aria-controls is deliberately absent. The design has no tabpanel: the tabs
	 * narrow one list that also contains the independent section, so there is no
	 * single panel for a tab to own, and pointing the attribute at a partial
	 * region would describe a relationship that does not exist.
	 *
	 * @since 0.4.0
	 *
	 * @param array<string, mixed> $attributes Block attributes. Unused.
	 * @param string               $content    Inner block content. Unused, the
	 *                                         block has no inner blocks.
	 * @return string Rendered block HTML, or an empty string when the theme has
	 *                no tabs to show.
	 */
	function maulik_portfolio_project_filters_render( $attributes = array(), $content = '' ) {
		unset( $attributes, $content );

		$tabs = maulik_portfolio_project_filters_tabs();

		if ( array() === $tabs ) {
			return '';
		}

		$buttons = '';

		foreach ( $tabs as $index => $tab ) {
			$is_selected = ( 0 === $index );
			$classes     = $is_selected ? 'pj-tab pj-tab-selected' : 'pj-tab';

			$buttons .= sprintf(
				'<button type="button" role="tab" id="%1$s" class="%2$s" aria-selected="%3$s" data-pj-filter="%4$s" data-pj-show="%5$s">%6$s</button>',
				esc_attr( 'pj-tab-' . $tab['id'] ),
				esc_attr( $classes ),
				$is_selected ? 'true' : 'false',
				esc_attr( $tab['id'] ),
				esc_attr( $tab['show'] ),
				esc_html( $tab['label'] )
			);
		}

		return sprintf(
			'<div %1$s role="tablist" aria-label="%2$s">%3$s</div>',
			get_block_wrapper_attributes( array( 'class' => 'pj-filters__list' ) ),
			esc_attr__( 'Filter Development Work', 'maulik-portfolio' ),
			$buttons
		);
	}
}

if ( ! function_exists( 'maulik_portfolio_project_filters_register' ) ) {
	/**
	 * Register the filter tablist block and its view script.
	 *
	 * The script is registered as a block view script rather than enqueued on
	 * wp_enqueue_scripts, so WordPress only prints it on the pages where the
	 * block actually rendered. Every other page on the site never downloads it.
	 *
	 * @since 0.4.0
	 *
	 * @return void
	 */
	function maulik_portfolio_project_filters_register() {
		$script_relative = 'assets/js/project-filters.js';

		$version = defined( 'MAULIK_PORTFOLIO_VERSION' ) ? (string) MAULIK_PORTFOLIO_VERSION : '0.0.0';

		if ( function_exists( 'maulik_portfolio_asset_version' ) ) {
			$version = maulik_portfolio_asset_version( $script_relative );
		}

		wp_register_script(
			MAULIK_PORTFOLIO_PROJECT_FILTERS_HANDLE,
			get_parent_theme_file_uri( $script_relative ),
			array(),
			$version,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);

		register_block_type(
			MAULIK_PORTFOLIO_PROJECT_FILTERS_BLOCK,
			array(
				'api_version'         => 3,
				'title'               => __( 'Project Filters', 'maulik-portfolio' ),
				'category'            => 'design',
				'icon'                => 'filter',
				'description'         => __( 'A tablist of archive filters that narrows the case study grid to one classification or technical domain at a time.', 'maulik-portfolio' ),
				'keywords'            => array( 'filter', 'tablist', 'projects', 'archive' ),
				'textdomain'          => 'maulik-portfolio',
				'attributes'          => array(),
				'supports'            => array(
					'html'      => false,
					'className' => true,
				),
				'view_script_handles' => array( MAULIK_PORTFOLIO_PROJECT_FILTERS_HANDLE ),
				'render_callback'     => 'maulik_portfolio_project_filters_render',
			)
		);
	}
}
add_action( 'init', 'maulik_portfolio_project_filters_register' );
