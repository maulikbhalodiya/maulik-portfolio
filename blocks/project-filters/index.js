/**
 * Registration entry point for the project filters block.
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
 * WHY THE REGISTERING IS DEFERRED TO wp.domReady.
 *
 * edit.js publishes its edit component at the end of its own run, as
 * wp.maulikPortfolio.projectFiltersEdit, and index.js reads it from there. This
 * theme has no build step, so there is no generated .asset.php to declare
 * edit.js as a dependency of index.js, and block.json's editorScript array order
 * is not honoured by the editor. Registering at script evaluation time
 * therefore dereferenced wp.maulikPortfolio before edit.js had created it,
 * which threw a TypeError and left the block unregistered in the editor.
 * wp.domReady fires after every document script has run, so by then the edit
 * component exists no matter which of the two files the browser fetched first.
 *
 * The other fix would be to declare edit.js as a dependency of index.js in the
 * enqueue code in inc/, which is the more correct dependency graph. That is out
 * of this file's scope, so it is noted rather than done here. Doing both is fine.
 *
 * WHY THE SETTINGS ARE NOT SPREAD IN HERE.
 *
 * The title, the category, the supports, the empty attribute schema and
 * everything else live in block.json, and WordPress already publishes that
 * file's contents to the editor before this script runs. Repeating them here
 * would be a second source of truth that silently disagrees with the first
 * whenever either is edited. So only the two things JavaScript owns are passed:
 * edit, and save.
 *
 * save returns null because the block is dynamic. The front end markup comes
 * from maulik_portfolio_project_filters_render() in inc/project-filters.php,
 * and any markup returned here would be stored in post content and then thrown
 * away at render time.
 *
 * @package Maulik_Portfolio
 */

( function ( wp ) {
	'use strict';

	wp.domReady( function () {
		var namespace = wp.maulikPortfolio || {};
		var edit = namespace.projectFiltersEdit;

		if ( typeof edit !== 'function' ) {
			throw new Error(
				'maulik-portfolio/project-filters: edit.js did not publish ' +
					'wp.maulikPortfolio.projectFiltersEdit.'
			);
		}

		wp.blocks.registerBlockType( 'maulik-portfolio/project-filters', {
			edit: edit,
			save: function () {
				return null;
			},
		} );
	} );
} )( window.wp );
