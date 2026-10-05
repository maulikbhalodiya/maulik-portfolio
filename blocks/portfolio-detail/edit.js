/**
 * Editor UI for the portfolio detail block.
 *
 * WHY THIS FILE USES GLOBALS AND createElement RATHER THAN IMPORTS AND JSX.
 *
 * The theme has no JavaScript build step, and this block adds none, so an editor
 * script here is loaded by WordPress as a plain classic script with no
 * transpiler in front of it. Import statements and JSX are therefore both
 * unavailable. The block editor packages are read off the wp global, which the
 * block editor screen always publishes, and elements are built with
 * wp.element.createElement.
 *
 * WHY THERE IS NO LIVE PROJECT PREVIEW HERE.
 *
 * A preview would mean either ServerSideRender, which arrives as a separate
 * script package this block does not declare a dependency on because there is no
 * built asset file to declare one in, or a REST read of the projects endpoint
 * inside the editor. The first guesses at a sibling script's presence, which is
 * the kind of dependency that breaks on the next Core change without an error
 * here. The second is real work that duplicates what the front end already does
 * properly, for a block whose whole purpose is to render one post that the
 * template already has in hand. So the editor states what the block is set to
 * and leaves the render to render.php.
 */

( function ( wp ) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;

	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var ToggleControl = wp.components.ToggleControl;

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
				{ title: __( 'Project', 'maulik-portfolio' ), initialOpen: true },
				el( ToggleControl, {
					label: __( 'Show the details line', 'maulik-portfolio' ),
					checked: attributes.showMeta,
					onChange: function ( showMeta ) {
						setAttributes( { showMeta: showMeta } );
					},
					help: __(
						'The details line carries the role and the publication date. With it off, the block renders the cover, the title, the excerpt and the content only.',
						'maulik-portfolio'
					),
				} )
			)
		);

		var summary = el(
			'p',
			{ className: 'portfolio-detail__editor-summary' },
			__(
				'Portfolio Detail renders on the front end. Place it in a template, not in a page, because it reads whichever project the template is already showing.',
				'maulik-portfolio'
			)
		);

		var note = el(
			'p',
			{ className: 'portfolio-detail__editor-note' },
			attributes.showMeta
				? __( 'Details line: on.', 'maulik-portfolio' )
				: __( 'Details line: off.', 'maulik-portfolio' )
		);

		return el(
			Fragment,
			{},
			inspector,
			el( 'div', blockProps, summary, note )
		);
	}

	/*
	 * Attached to the wp global rather than exported, because this file is a
	 * classic script and there is nothing to export to. index.js picks it up and
	 * calls registerBlockType.
	 */
	wp.maulikPortfolio = wp.maulikPortfolio || {};
	wp.maulikPortfolio.portfolioDetailEdit = Edit;
} )( window.wp );