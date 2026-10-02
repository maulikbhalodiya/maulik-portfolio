<?php
/**
 * Registration and server side render for the RankKernel subsystem status filter.
 *
 * WHY THIS IS A DYNAMIC BLOCK.
 *
 * docs/ARCHITECTURE.md section 2.4 states that there is no core/html wrapper, no
 * shortcode and no pasted markup in page content. The filter row used to be a
 * core/html block on content/pages/rankkernel.html, which broke that rule, and
 * inc/project-filters.php had already solved the identical problem for the
 * Projects archive filter. This file is that file's answer applied here: a
 * registered dynamic block whose markup is emitted by a render callback and
 * whose behaviour is a view script attached through view_script_handles.
 *
 * A core block cannot carry the tablist contract. WP_Block_Type::
 * prepare_attributes_for_render() drops every attribute that is not in the
 * block's own schema, so role, aria-selected, aria-controls and the data
 * attributes the script reads would never reach the HTML if the row were built
 * from core group and core paragraph blocks. That is the same reason
 * inc/project-filters.php is a dynamic block rather than a core group.
 *
 * The view script is the same file that used to be enqueued from
 * maulik_portfolio_enqueue_rankkernel_filter_script(), registered here under the
 * same handle so there is still exactly one script tag on the page and the
 * render_block hook in functions.php, which looks for data-rk-filter in the
 * rendered content, keeps working unchanged.
 *
 * THE TABS ARE NOT EDITABLE DATA.
 *
 * The set is fixed by the design reference as the statusFilter state in
 * src/components/RankKernelArchitecture.tsx, which is All, Implemented, In
 * Progress and Planned. It is declared once below rather than repeated, and the
 * labels are translatable so the row can be localised even though the set
 * cannot.
 *
 * The selected state is applied by the view script on init rather than trusted
 * from the markup, so aria-selected is correct even if a filter is printed twice
 * on one page.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'MAULIK_PORTFOLIO_RANKKERNEL_FILTERS_BLOCK' ) ) {
	/**
	 * Block name. Kept as a constant so the render callback, the asset handle
	 * and the tab id prefix can never drift apart.
	 */
	define( 'MAULIK_PORTFOLIO_RANKKERNEL_FILTERS_BLOCK', 'maulik-portfolio/rankkernel-filters' );
}

if ( ! defined( 'MAULIK_PORTFOLIO_RANKKERNEL_FILTERS_HANDLE' ) ) {
	/**
	 * View script handle for the tab behaviour.
	 *
	 * The same handle functions.php registers. Registering the same handle with
	 * the same arguments twice is a no-op in WordPress, so the block owns the
	 * view script and the existing hook owns the conditional enqueue without
	 * either of them being able to load the file twice.
	 */
	define( 'MAULIK_PORTFOLIO_RANKKERNEL_FILTERS_HANDLE', 'maulik-portfolio-rankkernel-filter' );
}

if ( ! function_exists( 'maulik_portfolio_rankkernel_filters_tabs' ) ) {
	/**
	 * The four filter tabs, in the order the design states them.
	 *
	 * `value` is the status token the view script compares against the status
	 * label on each directory entry and each diagram node. `icon` is the status
	 * mark the design draws on an unselected status tab and hides on the
	 * selected one. The set reproduces RankKernelArchitecture.tsx lines 96 to
	 * 114, including the two details that are easy to lose: All is labelled with
	 * the subsystem count rather than the word All, and All carries no mark.
	 *
	 * @since 0.1.0
	 *
	 * @return array<int, array<string, string>> Ordered list of tab definitions.
	 */
	function maulik_portfolio_rankkernel_filters_tabs() {
		return array(
			array(
				'value' => 'ALL',
				'label' => __( 'All States (9)', 'maulik-portfolio' ),
				'icon'  => '',
			),
			array(
				'value' => 'IMPLEMENTED',
				'label' => __( 'IMPLEMENTED', 'maulik-portfolio' ),
				'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>',
			),
			array(
				'value' => 'IN PROGRESS',
				'label' => __( 'IN PROGRESS', 'maulik-portfolio' ),
				'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 6v6l4 2"></path><circle cx="12" cy="12" r="10"></circle></svg>',
			),
			array(
				'value' => 'PLANNED',
				'label' => __( 'PLANNED', 'maulik-portfolio' ),
				'icon'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16.24 7.76-1.804 5.411a2 2 0 0 1-1.265 1.265L7.76 16.24l1.804-5.411a2 2 0 0 1 1.265-1.265z"></path><circle cx="12" cy="12" r="10"></circle></svg>',
			),
		);
	}
}

if ( ! function_exists( 'maulik_portfolio_rankkernel_filters_render' ) ) {
	/**
	 * Render the filter tablist.
	 *
	 * Returns a string rather than echoing. Echoing here would put the markup
	 * through the escape output sniff for no benefit, and returning is what the
	 * block render callback contract expects.
	 *
	 * The markup is complete before any script runs. Every tab is a real button
	 * with an accessible name taken from its own text, in the tab order, and the
	 * selected tab carries aria-selected. The row is a tablist and the element
	 * the four tabs narrow, the status directory grid, carries role tabpanel and
	 * is the target of every aria-controls. A visitor whose JavaScript never
	 * arrives therefore gets four labelled controls a keyboard can reach and a
	 * tabpanel that says what is in it, and the first tab already states the set
	 * that is on screen. The page does not depend on the script to be readable,
	 * only to be filterable.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $attributes Block attributes. Unused.
	 * @param string               $content    Inner block content. Unused, the
	 *                                         block has no inner blocks.
	 * @return string Rendered block HTML, or an empty string when there are no
	 *                tabs to show.
	 */
	function maulik_portfolio_rankkernel_filters_render( $attributes = array(), $content = '' ) {
		unset( $attributes, $content );

		$tabs = maulik_portfolio_rankkernel_filters_tabs();

		if ( array() === $tabs ) {
			return '';
		}

		$buttons = '';

		foreach ( $tabs as $index => $tab ) {
			$is_selected = ( 0 === $index );
			$id          = sanitize_html_class( strtolower( str_replace( ' ', '-', $tab['value'] ) ) );
			$mark        = '';

			if ( '' !== $tab['icon'] ) {
				$mark = sprintf(
					'<span class="rk-filter__icon rk-filter__icon--%1$s" aria-hidden="true">%2$s</span>',
					esc_attr( $id ),
					$tab['icon']
				);
			}

			/*
			 * EVERY TAB IS IN THE TAB ORDER. No tabindex is written here, on
			 * purpose, so all four tabs are reachable with the Tab key.
			 *
			 * The roving tabindex is the ARIA Authoring Practices pattern for a
			 * tablist whose tabs are switched by the arrow keys, and it was
			 * written here before. It is wrong for this row. These four tabs
			 * narrow a directory rather than switch a panel, they sit in a flex
			 * row where all four are visible at once, and the audited home page
			 * defect was exactly this: six tabindex="-1" attributes that took
			 * their controls out of the keyboard order entirely, so a keyboard
			 * user could not reach them at all. A visitor who never presses an
			 * arrow key, which is most of them, now has a working path through
			 * all four.
			 *
			 * The design reference carries no tabindex on these tabs at all, and
			 * measures zero of them, which is the same answer.
			 */
			$buttons .= sprintf(
				'<button type="button" class="rk-filter__tab" role="tab" id="%1$s" aria-controls="rk-directory-grid" aria-selected="%2$s" data-rk-filter-value="%3$s">%4$s<span>%5$s</span></button>',
				esc_attr( 'rk-filter-tab-' . $id ),
				$is_selected ? 'true' : 'false',
				esc_attr( $tab['value'] ),
				$mark,
				esc_html( $tab['label'] )
			);
		}

		$label = __( 'Filter RankKernel subsystems by development status', 'maulik-portfolio' );

		return sprintf(
			'<div %1$s>%2$s</div>',
			get_block_wrapper_attributes(
				array(
					'class'          => 'rk-filter',
					'role'           => 'tablist',
					'aria-label'     => $label,
					'data-rk-filter' => 'yes',
				)
			),
			$buttons
		);
	}
}

if ( ! function_exists( 'maulik_portfolio_rankkernel_filters_register' ) ) {
	/**
	 * Register the filter tablist block and its view script.
	 *
	 * The script is registered as a block view script rather than enqueued on
	 * wp_enqueue_scripts, so WordPress only prints it on the pages where the
	 * block actually rendered. Every other page on the site never downloads it.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function maulik_portfolio_rankkernel_filters_register() {
		$script_relative = 'assets/js/rankkernel-filter.js';

		if ( ! wp_script_is( MAULIK_PORTFOLIO_RANKKERNEL_FILTERS_HANDLE, 'registered' ) ) {
			$version = defined( 'MAULIK_PORTFOLIO_VERSION' ) ? (string) MAULIK_PORTFOLIO_VERSION : '0.0.0';

			if ( function_exists( 'maulik_portfolio_asset_version' ) ) {
				$version = maulik_portfolio_asset_version( $script_relative );
			}

			wp_register_script(
				MAULIK_PORTFOLIO_RANKKERNEL_FILTERS_HANDLE,
				get_parent_theme_file_uri( $script_relative ),
				array(),
				$version,
				true
			);
		}

		register_block_type(
			MAULIK_PORTFOLIO_RANKKERNEL_FILTERS_BLOCK,
			array(
				'api_version'         => 3,
				'title'               => __( 'RankKernel Filters', 'maulik-portfolio' ),
				'category'            => 'design',
				'icon'                => 'filter',
				'description'         => __( 'A tablist of RankKernel subsystem states that narrows the status directory and dims the architecture diagram to one state at a time.', 'maulik-portfolio' ),
				'keywords'            => array( 'rankkernel', 'filter', 'tablist', 'subsystem', 'status' ),
				'textdomain'          => 'maulik-portfolio',
				'attributes'          => array(),
				'supports'            => array(
					'html'      => false,
					'className' => true,
				),
				'view_script_handles' => array( MAULIK_PORTFOLIO_RANKKERNEL_FILTERS_HANDLE ),
				'render_callback'     => 'maulik_portfolio_rankkernel_filters_render',
			)
		);
	}
}
add_action( 'init', 'maulik_portfolio_rankkernel_filters_register' );
