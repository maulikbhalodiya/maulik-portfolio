/**
 * Preserve hand authored wrapper attributes on core blocks in the editor.
 *
 * WHY THIS FILE EXISTS.
 *
 * Several wrappers in this theme's content carry attributes that were written
 * into the saved markup by hand and have no attribute schema behind them:
 * role="tabpanel", aria-labelledby, data-rk-panel, data-hz-arch-status and the
 * valueless hidden attribute. They are load bearing. assets/js/rankkernel-filter.js
 * selects on [data-rk-panel] and reads the value, so the attribute decides which
 * inspector panel belongs to which architecture node, and role/aria-labelledby
 * are the accessible names for the switcher and section tabs.
 *
 * core/group's save() renders a bare wrapper from a fixed attribute set, so on
 * the next save it regenerates markup without those attributes. The editor
 * compares the stored markup against what save() produces, reports the block as
 * invalid, and the attributes are dropped from the content. Adding the
 * attribute to the server side schema (inc/block-attributes.php) does not help:
 * the client schema comes from the bundled block-library definition, so the
 * server side declaration never reaches the editor.
 *
 * THIS IS ADDITIVE ONLY.
 *
 * Nothing here ever removes an attribute. Each attribute is emitted only when
 * the block already carries a value for it, so a group that never had
 * data-rk-panel keeps being saved without one. No stored markup is rewritten.
 *
 * THE THREE HOOKS USED, AND WHY ALL THREE ARE NEEDED.
 *
 * 1. blocks.registerBlockType amends core/group's client attribute schema.
 *    This is the only way the values can exist on the block object at all:
 *    createBlock() runs sanitizeBlockAttributes(), which rebuilds the
 *    attribute set from the block type's schema and drops every key the
 *    schema does not declare. A filter that only added attributes later would
 *    be discarded here. Confirmed in WP 7.1.2,
 *    wp-includes/js/dist/blocks.js at the "blocks.registerBlockType"
 *    applyFilters call inside registerBlockType(), and
 *    sanitizeBlockAttributes() in the same file.
 *
 *    The filter alone is not sufficient, because it only runs for types that
 *    register after it is added, and this theme's editor scripts are enqueued
 *    after the block-library bundle has already registered the core blocks. So
 *    the same merge is also applied directly to an already registered core
 *    block type once wp.domReady fires. Both paths are idempotent and additive:
 *    an attribute core already declares is never touched.
 *
 * 2. blocks.getBlockAttributes recovers values the schema did not parse.
 *    It runs after the schema driven parse and receives the block's innerHTML,
 *    so the wrapper element can be read directly. It is used for two things:
 *    reading the theme's attributes off the wrapper, and recovering tagName
 *    for the groups whose stored wrapper is a section or an article while the
 *    block comment omits tagName. core/group declares tagName with no source,
 *    so the parser takes it from the comment, and a comment that does not
 *    mention tagName yields the default "div" while the markup says otherwise.
 *    Reading the stored element name is what makes core render the same tag
 *    instead of silently rewriting it. Confirmed in WP 7.1.2,
 *    wp-includes/js/dist/blocks.js getBlockAttributes(), which applies
 *    "blocks.getBlockAttributes" with ( blockAttributes, blockType, innerHTML,
 *    attributes ).
 *
 * 3. blocks.getSaveContent.extraProps puts the attributes back on the wrapper.
 *    core/group's save() renders useInnerBlocksProps.save( useBlockProps.save() ),
 *    and useBlockProps.save() is the getBlockProps() exported from
 *    wp-includes/js/dist/blocks.js, which applies "blocks.getSaveContent.extraProps"
 *    to the props it builds. That path has no apiVersion guard, so it runs for
 *    core/group. (The other application of the same filter, inside
 *    getSaveElement(), is skipped for apiVersion above 1, so the getBlockProps()
 *    path is the one that matters here.) Confirmed in WP 7.1.2,
 *    wp-includes/js/dist/blocks.js getBlockProps() and the
 *    useBlockProps.save assignment in wp-includes/js/dist/block-editor.js.
 *
 * THE ATTRIBUTE LIST IS MEASURED, NOT GUESSED.
 *
 * Every attribute below was counted by parsing the stored post_content of the
 * three pages this site ships (posts 11, 10 and 9) and reading the attributes
 * off each core/group wrapper element, keeping only what core's own save()
 * would not have produced:
 *
 *   post 10 (rankkernel)  data-rk-panel 9, hidden 8, aria-labelledby 7, role 1
 *   post 11 (home)        role 12, aria-labelledby 8, data-hz-arch-status 1
 *   post 9  (projects)    aria-labelledby 3
 *
 * role and aria-labelledby are declared for every core/group because the same
 * handful of values appear on all three pages and a group is not the only block
 * type that can carry them. data-rk-panel and data-hz-arch-status are declared
 * globally too rather than filtered by value, so a future panel or figure
 * saved with a new value is still preserved.
 *
 * aria-label is deliberately absent: core/group declares supports.ariaLabel,
 * so that attribute is already schema owned and needs no help here.
 *
 * WHAT THIS DOES NOT DO.
 *
 * It does not fix a core/group whose stored children are raw HTML with no wp:
 * delimiters. The parser drops those children, so the wrapper regenerates with
 * a different child list no matter what the wrapper attributes are. That is a
 * content migration, not a schema problem. It does not touch core/button class
 * names either.
 */

( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.hooks || ! wp.blocks ) {
		return;
	}

	/*
	 * Attributes that this theme wrote into saved markup by hand.
	 *
	 * 'attribute' as the type keeps the value a string, which is what the
	 * markup holds. `hidden` is special cased further down: it is stored
	 * without a value, and it has to be read as a boolean.
	 */
	var SCHEMA = {
		role: { type: 'string' },
		'aria-labelledby': { type: 'string' },
		'data-rk-panel': { type: 'string' },
		'data-hz-arch-status': { type: 'string' },
		hidden: { type: 'boolean' },
	};

	var BLOCK_NAME = 'core/group';

	/* Attributes that are stored without a value and read back as present. */
	var VALUELESS = [ 'hidden' ];

	/*
	 * core's own validation treats a boolean attribute as equal whenever it
	 * appears on both sides, so the value is never compared for these.
	 * Matched against BOOLEAN_ATTRIBUTES in
	 * wp-includes/js/dist/blocks.js.
	 */
	var BOOLEAN_ATTRS = [ 'hidden' ];

	/**
	 * Read the attributes off the wrapper element of a block's inner HTML.
	 *
	 * Uses a detached element rather than a regular expression so the
	 * attribute list is read the same way a browser reads it, including
	 * valueless attributes such as hidden.
	 *
	 * @param {string} innerHTML Serialized inner content of the block.
	 * @return {Object} The wrapper element's tag name and attributes.
	 */
	function readWrapper( innerHTML ) {
		var result = { tagName: '', attributes: {} };

		if ( typeof innerHTML !== 'string' || ! innerHTML ) {
			return result;
		}

		var host = document.createElement( 'div' );
		host.innerHTML = innerHTML.trim();

		var wrapper = host.firstElementChild;
		if ( ! wrapper ) {
			return result;
		}

		result.tagName = wrapper.tagName.toLowerCase();

		for ( var i = 0; i < wrapper.attributes.length; i++ ) {
			var attr = wrapper.attributes[ i ];
			var name = attr.name.toLowerCase();

			if ( BOOLEAN_ATTRS.indexOf( name ) !== -1 ) {
				result.attributes[ name ] = true;
			} else if ( attr.value !== '' ) {
				result.attributes[ name ] = attr.value;
			}
		}

		return result;
	}

	/**
	 * The value to store for a wrapper attribute.
	 *
	 * Returns undefined for an attribute that is not on the wrapper at all, so
	 * that the attribute stays absent rather than being saved as empty.
	 *
	 * @param {Object}  attributes Attributes read off the wrapper element.
	 * @param {string}  name       Attribute name.
	 * @return {string|boolean|undefined} Value to store, if any.
	 */
	function storedValue( attributes, name ) {
		if ( ! Object.prototype.hasOwnProperty.call( attributes, name ) ) {
			return undefined;
		}

		if ( VALUELESS.indexOf( name ) !== -1 ) {
			return true;
		}

		return attributes[ name ];
	}

	/**
	 * Add the theme's attributes to a block type's schema, in place.
	 *
	 * Mutates the attributes object rather than replacing it, because the
	 * registry holds this exact object and getBlockType() hands it back out.
	 * An attribute core already declares is left exactly as core declared it.
	 *
	 * @param {Object} blockType Block type whose schema is to be amended.
	 * @return {boolean} True when at least one attribute was added.
	 */
	function declareAttributes( blockType ) {
		if ( ! blockType || ! blockType.attributes || typeof blockType.attributes !== 'object' ) {
			return false;
		}

		var added = false;

		Object.keys( SCHEMA ).forEach( function ( key ) {
			if ( Object.prototype.hasOwnProperty.call( blockType.attributes, key ) ) {
				return;
			}

			blockType.attributes[ key ] = Object.assign( {}, SCHEMA[ key ] );
			added = true;
		} );

		return added;
	}

	/*
	 * 1. Declare the attributes on the client schema.
	 *
	 * Applied to every block type rather than core/group alone, because the
	 * wrappers that carry these attributes are not all groups.
	 */
	wp.hooks.addFilter(
		'blocks.registerBlockType',
		'maulik-portfolio/hand-authored-attributes',
		function ( settings ) {
			if ( ! settings ) {
				return settings;
			}

			var attributes = settings.attributes;

			/*
			 * Copy first. This filter runs before the block type reaches the
			 * registry, and mutating the object core handed in would edit
			 * core's shared definition.
			 */
			var next = Object.assign( {}, attributes || {} );
			var changed = false;

			Object.keys( SCHEMA ).forEach( function ( key ) {
				if ( Object.prototype.hasOwnProperty.call( next, key ) ) {
					return;
				}

				next[ key ] = Object.assign( {}, SCHEMA[ key ] );
				changed = true;
			} );

			return changed ? Object.assign( {}, settings, { attributes: next } ) : settings;
		}
	);

	/*
	 * The core blocks are registered by the block-library bundle, which
	 * evaluates before a theme editor script does, so the filter above never
	 * sees them. Patch the types that are already registered.
	 *
	 * unregisterBlockType() is deliberately not used: core keeps references to
	 * its own registered settings and re-registering a core type is the kind of
	 * thing that ends with a duplicate warning.
	 */
	if ( wp.domReady && 'function' === typeof wp.domReady ) {
		wp.domReady( function () {
			var registered = wp.blocks.getBlockTypes();

			for ( var i = 0; i < registered.length; i++ ) {
				declareAttributes( registered[ i ] );
			}
		} );
	}

	/*
	 * 2. Recover the attributes the schema parse dropped.
	 *
	 * getBlockAttribute() takes an attribute with no source from the block
	 * comment, so an attribute that only ever appears in the markup resolves
	 * to undefined and is then dropped by sanitizeBlockAttributes(). Reading
	 * the wrapper here is what puts the value on the block.
	 *
	 * The same pass recovers tagName. The stored element name is used, so a
	 * section stays a section and an article stays an article. No tag is ever
	 * assumed: when the markup has no element, tagName is left alone.
	 */
	wp.hooks.addFilter(
		'blocks.getBlockAttributes',
		'maulik-portfolio/hand-authored-attributes',
		function ( blockAttributes, blockType, innerHTML, commentAttributes ) {
			if ( ! blockType ) {
				return blockAttributes;
			}

			var wrapper = readWrapper( innerHTML );

			/*
			 * tagName recovery is limited to core/group.
			 *
			 * core/button also declares a tagName attribute, but there it
			 * names the inner link element rather than the block wrapper. A
			 * stored <a class="wp-block-button__link"> would therefore have
			 * had its link tag rewritten to the wrapper's <div>, which turns
			 * every button on the page invalid. Measured: with tagName
			 * recovery applied to all block types, core/button went from 0
			 * invalid to 6 on the home page and from 0 to 4 on rankkernel.
			 * Only core/group's tagName names its wrapper.
			 */
			if (
				BLOCK_NAME === blockType.name &&
				wrapper.tagName &&
				blockType.attributes &&
				blockType.attributes.tagName
			) {
				var current = ( commentAttributes || {} ).tagName;

				/*
				 * Only override when the comment did not state a tag. A
				 * comment that names a tag is authoritative and is left as it
				 * is, so this can never introduce a tag core did not have.
				 */
				if ( current === undefined || current === null || current === '' ) {
					blockAttributes.tagName = wrapper.tagName;
				}
			}

			Object.keys( SCHEMA ).forEach( function ( key ) {
				if ( ! Object.prototype.hasOwnProperty.call( blockType.attributes || {}, key ) ) {
					return;
				}

				/*
				 * An existing non undefined value came from the comment and
				 * wins. This only fills in what the parse left empty.
				 */
				if ( blockAttributes[ key ] !== undefined && blockAttributes[ key ] !== null ) {
					return;
				}

				var value = storedValue( wrapper.attributes, key );
				if ( value !== undefined ) {
					blockAttributes[ key ] = value;
				}
			} );

			return blockAttributes;
		}
	);

	/*
	 * 3. Put the attributes back on the wrapper save() renders.
	 *
	 * Reached through useBlockProps.save(), which core/group's save() calls.
	 * Only attributes the block already carries are emitted, so this adds and
	 * never removes.
	 */
	wp.hooks.addFilter(
		'blocks.getSaveContent.extraProps',
		'maulik-portfolio/hand-authored-attributes',
		function ( props, blockType, attributes ) {
			if ( ! blockType || ! attributes ) {
				return props;
			}

			var extra = {};
			var found = false;

			Object.keys( SCHEMA ).forEach( function ( key ) {
				var value = attributes[ key ];

				if ( value === undefined || value === null || value === '' || value === false ) {
					return;
				}

				extra[ key ] = value;
				found = true;
			} );

			return found ? Object.assign( {}, props, extra ) : props;
		}
	);
} )( window.wp );