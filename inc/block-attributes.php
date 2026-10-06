<?php
/**
 * Declares the block attributes this theme puts on core blocks by hand.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'maulik_portfolio_declare_block_attributes' ) ) {
	/**
	 * Add theme owned attributes to the schema core registered for a block type.
	 *
	 * WHY THIS FILTER AND NOT A block.json.
	 *
	 * Core ships wp-includes/blocks/group/block.json, but it lives inside
	 * wp-includes and is not extensible by a theme. Calling register_block_type()
	 * for core/group does not amend the core block type either: WP_Block_Type_Registry
	 * rejects the second registration outright with _doing_it_wrong( 'already
	 * registered' ) and returns false. The `block_type_metadata` filter is applied
	 * inside register_block_type_from_metadata() before the registry call, so an
	 * entry added to its attributes array does land in the registered schema.
	 * Measured: the attribute appears in
	 * WP_Block_Type_Registry::get_instance()->get_registered( 'core/group' )->attributes
	 * once this filter runs, and is absent when the filter is not hooked.
	 *
	 * WHAT THIS DOES NOT DO.
	 *
	 * It does not stop the block editor flagging a group whose saved wrapper
	 * markup carries data-rk-panel by hand. That warning comes from the editor
	 * comparing the stored markup against what the client side save() function
	 * regenerates, and the client schema for core/group comes from the bundled
	 * block-library JavaScript, not from PHP. The post editor bootstraps
	 * get_block_editor_server_block_settings() through
	 * unstable__bootstrapServerSideBlockDefinitions(), but the client
	 * blockSettings.attributes object wins that merge outright, so a server side
	 * declaration for an already registered core block never reaches the
	 * editor's copy of the schema. Measured in the editor with the real
	 * bootstrap call: the attribute is still absent from
	 * wp.blocks.getBlockType( 'core/group' ).attributes afterwards, and a group
	 * with data-rk-panel in its markup is still invalid.
	 *
	 * NO DEFAULT, DELIBERATELY.
	 *
	 * The attribute is a plain string with no default. A default does populate
	 * the in memory attribute value for every group, but it does not reach the
	 * markup: measured with default "rk-default", neither the server render
	 * (WP_Block::render()) nor the editor serializer emitted data-rk-panel.
	 * So a default buys nothing and is left off.
	 *
	 * The rendered page is unchanged. core/group has no render callback, so
	 * WP_Block::render() replays the saved wrapper and then runs the block
	 * support filters over it, which rewrite the class attribute (a layout
	 * support adds is-layout-flow and wp-block-group-is-layout-flow to markup
	 * that was saved without them). The hand written data attribute survives
	 * that pass, which is why assets/js/rankkernel-filter.js still finds all
	 * nine panels on the frontend.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $metadata Metadata core is about to register.
	 * @return array<string, mixed> Metadata with the theme attributes added.
	 */
	function maulik_portfolio_declare_block_attributes( $metadata ) {
		if ( ! isset( $metadata['name'] ) || 'core/group' !== $metadata['name'] ) {
			return $metadata;
		}

		if ( ! isset( $metadata['attributes'] ) || ! is_array( $metadata['attributes'] ) ) {
			$metadata['attributes'] = array();
		}

		/*
		 * Lower case, because that is the spelling the frontend script reads.
		 * assets/js/rankkernel-filter.js:86 selects on `[data-rk-panel]` and
		 * line 308 reads `panels[ i ].getAttribute( 'data-rk-panel' )`. HTML
		 * attribute names are case insensitive in the parser and lower cased in
		 * the DOM, so a different spelling here would name a different attribute
		 * in the block comment while the script kept looking for this one.
		 *
		 * The isset guard means a future core release that declares the
		 * attribute itself wins over the theme.
		 */
		if ( ! isset( $metadata['attributes']['data-rk-panel'] ) ) {
			$metadata['attributes']['data-rk-panel'] = array(
				'type' => 'string',
			);
		}

		return $metadata;
	}
}
add_filter( 'block_type_metadata', 'maulik_portfolio_declare_block_attributes' );
