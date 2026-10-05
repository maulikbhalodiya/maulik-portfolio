/**
 * Editor UI for the RankKernel filters block.
 *
 * WHY THIS FILE USES GLOBALS AND createElement RATHER THAN IMPORTS AND JSX.
 *
 * The theme has no JavaScript build step. package.json declares no bundler
 * entry point for block scripts and this block adds none, so this file is
 * loaded by WordPress as a plain classic script with no transpiler in front of
 * it. That means two things are unavailable: import statements, and JSX. So
 * the block editor packages are read off the wp global, which the block editor
 * screen always publishes, and elements are built with wp.element.createElement.
 *
 * This is the same arrangement blocks/portfolio-list/edit.js already uses.
 * Adding a build step later is a strictly additive change.
 *
 * WHY THERE ARE NO CONTROLS TO SET.
 *
 * block.json declares an empty attributes object for this block, and that is
 * the honest state of it: the subsystem states are fixed by the design
 * reference and are declared once in the tab list in inc/rankkernel-filters.php.
 * There is no attribute for an author to set, so the inspector says so rather
 * than showing controls that would write nothing into the block comment.
 *
 * @package Maulik_Portfolio
 */

( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;

	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;

	/**
	 * The block editor UI.
	 *
	 * @param {Object} props Block props.
	 * @return {Object} Element.
	 */
	function Edit( props ) {
		var blockProps = useBlockProps();

		var inspector = el(
			InspectorControls,
			{},
			el(
				PanelBody,
				{ title: __( 'Subsystems', 'maulik-portfolio' ), initialOpen: true },
				el(
					'p',
					{},
					__(
						'This block has no settings. The subsystem states are fixed and are declared once in the theme, so the tablist an author sees here is the tablist a visitor gets.',
						'maulik-portfolio'
					)
				)
			)
		);

		var summary = el(
			'p',
			{ className: 'rankkernel-filters__editor-summary' },
			__( 'RankKernel Filters renders on the front end.', 'maulik-portfolio' )
		);

		return el(
			Fragment,
			{},
			inspector,
			el( 'div', blockProps, summary )
		);
	}

	/*
	 * Attached to the wp global rather than exported, because this file is a
	 * classic script and there is nothing to export to. index.js picks it up
	 * and calls registerBlockType, which is where the block is registered.
	 */
	wp.maulikPortfolio = wp.maulikPortfolio || {};
	wp.maulikPortfolio.rankkernelFiltersEdit = Edit;
} )( window.wp );
