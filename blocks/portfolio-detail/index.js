/**
 * Registration entry point for the portfolio detail block.
 *
 * WHY THERE IS AN index.js AND NOT A BUILD OUTPUT.
 *
 * The conventional layout is index.js at the block root importing edit.js, and
 * a bundler producing a build directory that block.json points at instead. This
 * theme has no bundler step, so the build half of that convention does not
 * exist and pretending it does would mean committing compiled output. Both
 * files are therefore plain classic scripts, declared in the editorScript array
 * in block.json, and index.js is the one that calls registerBlockType.
 *
 * WHY REGISTRATION IS DEFERRED, AND HOW.
 *
 * edit.js publishes its edit component at the end of its own run, as
 * wp.maulikPortfolio.portfolioDetailEdit, and index.js reads it from there.
 * This theme has no build step, so there is no generated .asset.php to declare
 * edit.js as a dependency of index.js, and WordPress registers every file:./
 * entry in block.json's editorScript array as a standalone script with no
 * dependencies, so that array is a list, not a load order guarantee. Registering
 * at script evaluation time therefore dereferenced wp.maulikPortfolio before
 * edit.js had created it, which threw a TypeError and left the block
 * unregistered in the editor.
 *
 * Deferring through wp.domReady was tried and did not work. wp.domReady comes
 * from the wp-dom-ready script handle, and nothing in this theme declares that
 * handle as a dependency of any block script, so the global does not exist when
 * this file evaluates. The blocks stayed unregistered, only with a different
 * message.
 *
 * So the ready mechanism is chosen at run time rather than assumed:
 *   1. wp.domReady, when the editor happens to provide it. Preferred, and it
 *      fires after every document script has run.
 *   2. DOMContentLoaded, when that handle is missing and the document is still
 *      parsing.
 *   3. Immediately, when that handle is missing and the document is already
 *      parsed, because there is nothing left to wait for.
 *
 * All three paths run the same function exactly once, and that function still
 * throws the named Error below if edit.js never published its edit component. A
 * silent no-op would be worse than a loud failure.
 *
 * The other fix, and the more correct dependency graph, is to declare the script
 * handles in the enqueue code in inc/. That is out of this file's scope, so it
 * is noted rather than done here. Doing both is fine.
 *
 * WHY THE SETTINGS ARE NOT SPREAD IN HERE.
 *
 * The title, the category, the supports and everything else live in block.json,
 * and WordPress already publishes that file's contents to the editor before
 * this script runs. Repeating them here would be a second source of truth that
 * silently disagrees with the first whenever either is edited.
 *
 * save returns null because the block is dynamic. The front end markup comes
 * from render.php, and any markup returned here would be stored in post content
 * and then thrown away at render time.
 */

( function ( wp ) {
	'use strict';

	var registerBlock = function () {
		var namespace = wp.maulikPortfolio || {};
		var edit = namespace.portfolioDetailEdit;

		if ( typeof edit !== 'function' ) {
			throw new Error(
				'maulik-portfolio/portfolio-detail: edit.js did not publish ' +
					'wp.maulikPortfolio.portfolioDetailEdit.'
			);
		}

		wp.blocks.registerBlockType( 'maulik-portfolio/portfolio-detail', {
			edit: edit,
			save: function () {
				return null;
			},
		} );
	};

	if ( typeof wp.domReady === 'function' ) {
		wp.domReady( registerBlock );
	} else if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', registerBlock );
	} else {
		registerBlock();
	}
} )( window.wp );