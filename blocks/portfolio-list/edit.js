/**
 * Editor UI for the portfolio list block.
 *
 * WHY THIS FILE USES GLOBALS AND createElement RATHER THAN IMPORTS AND JSX.
 *
 * The theme has no JavaScript build step. package.json declares no bundler
 * configuration and this block adds none, so an editor script here is loaded by
 * WordPress as a plain classic script with no transpiler in front of it. That
 * means two things are unavailable: import statements, and JSX. So the block
 * editor packages are read off the wp global, which the block editor screen
 * always publishes, and elements are built with wp.element.createElement.
 *
 * Adding a build step later is a strictly additive change. The alternative,
 * shipping a build, would mean committing compiled output and adding a bundler
 * entry point for one block, which is a larger change than this issue is.
 *
 * WHY NO ServerSideRender.
 *
 * A live server render in the editor would be the ideal preview, but
 * ServerSideRender arrives as a separate script package that this block's
 * editorScript does not declare a dependency on, because there is no built
 * asset file to declare one in. Guessing that a sibling script happens to be
 * enqueued is the kind of dependency that breaks on the next Core change with
 * no error here. So the editor shows what the block is set to rather than
 * pretending to render projects. The front end render is the real one, in
 * render.php, and it is what a visitor gets.
 */

( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;

	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var RangeControl = wp.components.RangeControl;
	var SelectControl = wp.components.SelectControl;
	var ToggleControl = wp.components.ToggleControl;

	/*
	 * The order options are declared once, here, rather than inline in the
	 * control. The render file maps the same keys through its own allow list,
	 * because an attribute value arrives from the block comment and is therefore
	 * author input rather than trusted configuration. A key this list does not
	 * offer falls back to menu-order server side, so an old or hand edited block
	 * degrades to a defined order instead of an undefined query shape.
	 *
	 * menu-order is first and is the default because it is the order the case
	 * studies were authored in. It sorts on the Page Attributes Order field and
	 * falls back to newest first for two projects left at the same position.
	 */
	var ORDER_OPTIONS = [
		{ label: __( 'Authored order', 'maulik-portfolio' ), value: 'menu-order' },
		{ label: __( 'Newest first', 'maulik-portfolio' ), value: 'date' },
		{ label: __( 'Oldest first', 'maulik-portfolio' ), value: 'date-asc' },
		{ label: __( 'Title, A to Z', 'maulik-portfolio' ), value: 'title' },
		{ label: __( 'Recently updated', 'maulik-portfolio' ), value: 'modified' },
		{ label: __( 'Random', 'maulik-portfolio' ), value: 'rand' },
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
				{ title: __( 'Projects', 'maulik-portfolio' ), initialOpen: true },
				el( RangeControl, {
					label: __( 'How many projects', 'maulik-portfolio' ),
					value: attributes.count,
					min: 1,
					max: 24,
					step: 1,
					onChange: function ( count ) {
						setAttributes( { count: count } );
					},
					help: __(
						'Caps at 24 so the query behind the grid stays bounded. Longer lists are reached with the pagination control the block adds below the grid.',
						'maulik-portfolio'
					),
				} ),
				el( SelectControl, {
					label: __( 'Order', 'maulik-portfolio' ),
					value: attributes.order,
					options: ORDER_OPTIONS,
					onChange: function ( order ) {
						setAttributes( { order: order } );
					},
					help: __(
						'Random is re-evaluated on every request, so a visitor who reloads sees a different set.',
						'maulik-portfolio'
					),
				} ),
				el( ToggleControl, {
					label: __( 'Show the excerpt', 'maulik-portfolio' ),
					checked: attributes.showExcerpt,
					onChange: function ( showExcerpt ) {
						setAttributes( { showExcerpt: showExcerpt } );
					},
					help: __(
						'Each card already carries its title and cover. The excerpt adds the summary paragraph inside the card disclosure.',
						'maulik-portfolio'
					),
				} )
			)
		);

		var summary = el(
			'p',
			{ className: 'portfolio-list__editor-summary' },
			__( 'Portfolio List renders on the front end.', 'maulik-portfolio' )
		);

		var settings = el(
			'ul',
			{ className: 'portfolio-list__editor-settings' },
			el(
				'li',
				{},
				el( 'strong', {}, __( 'Projects', 'maulik-portfolio' ) ),
				' ' + attributes.count
			),
			el(
				'li',
				{},
				el( 'strong', {}, __( 'Order', 'maulik-portfolio' ) ),
				' ' + attributes.order
			),
			el(
				'li',
				{},
				el( 'strong', {}, __( 'Excerpt', 'maulik-portfolio' ) ),
				' ' + ( attributes.showExcerpt ? __( 'shown', 'maulik-portfolio' ) : __( 'hidden', 'maulik-portfolio' ) )
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
	wp.maulikPortfolio.portfolioListEdit = Edit;
} )( window.wp );