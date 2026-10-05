/**
 * Editor UI for the hero interactive ecosystem block.
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
 * WHY NO ServerSideRender.
 *
 * A live server render would be the ideal preview, but ServerSideRender
 * arrives as a separate script package that this block's editorScript does not
 * declare a dependency on, because there is no built asset file to declare one
 * in. The editor shows what the block is set to. The front end render is the
 * real one, in inc/hero-ecosystem.php, and it is what a visitor gets.
 *
 * WHY THE NODE IDS ARE LISTED HERE AND NOT FETCHED.
 *
 * The authoritative list is the PHP array in
 * maulik_portfolio_hero_ecosystem_nodes(). The render callback falls back to
 * the first node whenever selectedNode does not match an id it knows, so a
 * stale or hand edited value degrades to a defined node rather than an empty
 * panel. That fallback is what makes this mirror safe to state here without a
 * round trip to the server, and the list is kept in the same order the PHP
 * declares it so the two read side by side.
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
	var SelectControl = wp.components.SelectControl;

	/* Mirrors maulik_portfolio_hero_ecosystem_nodes() in inc/hero-ecosystem.php. */
	var NODE_OPTIONS = [
		{ label: __( 'WordPress', 'maulik-portfolio' ), value: 'wordpress' },
		{ label: __( 'PHP', 'maulik-portfolio' ), value: 'php' },
		{ label: __( 'Plugin', 'maulik-portfolio' ), value: 'plugin' },
		{ label: __( 'API', 'maulik-portfolio' ), value: 'api' },
		{ label: __( 'Database', 'maulik-portfolio' ), value: 'database' },
		{ label: __( 'Security', 'maulik-portfolio' ), value: 'security' },
		{ label: __( 'Git', 'maulik-portfolio' ), value: 'git' },
		{ label: __( 'RankKernel', 'maulik-portfolio' ), value: 'rankkernel' },
	];

	/**
	 * The block editor UI.
	 *
	 * @param {Object}   props               Block props.
	 * @param {Object}   props.attributes    Current attribute values.
	 * @param {Function} props.setAttributes Setter for the block attributes.
	 * @return {Object} Element.
	 */
	function Edit( props ) {
		var attributes = props.attributes;
		var setAttributes = props.setAttributes;
		var blockProps = useBlockProps();

		var inspector = el(
			InspectorControls,
			{},
			el(
				PanelBody,
				{ title: __( 'Ecosystem', 'maulik-portfolio' ), initialOpen: true },
				el( SelectControl, {
					label: __( 'Node selected on load', 'maulik-portfolio' ),
					value: attributes.selectedNode,
					options: NODE_OPTIONS,
					onChange: function ( selectedNode ) {
						setAttributes( { selectedNode: selectedNode } );
					},
					help: __(
						'The node a visitor lands on. Hovering and clicking still work on every node, and the canvas keeps rotating either way.',
						'maulik-portfolio'
					),
				} )
			)
		);

		var summary = el(
			'p',
			{ className: 'hero-ecosystem__editor-summary' },
			__( 'Hero Interactive Ecosystem renders on the front end.', 'maulik-portfolio' )
		);

		var settings = el(
			'ul',
			{ className: 'hero-ecosystem__editor-settings' },
			el(
				'li',
				{},
				el( 'strong', {}, __( 'Selected node', 'maulik-portfolio' ) ),
				' ' + attributes.selectedNode
			),
			el(
				'li',
				{},
				el( 'strong', {}, __( 'Nodes drawn', 'maulik-portfolio' ) ),
				' ' + String( NODE_OPTIONS.length )
			)
		);

		return el(
			Fragment,
			{},
			inspector,
			el( 'div', blockProps, summary, settings )
		);
	}

	/*
	 * Attached to the wp global rather than exported, because this file is a
	 * classic script and there is nothing to export to. index.js picks it up
	 * and calls registerBlockType, which is where the block is registered.
	 */
	wp.maulikPortfolio = wp.maulikPortfolio || {};
	wp.maulikPortfolio.heroEcosystemEdit = Edit;
} )( window.wp );
