/**
 * Registration entry point for the project filters block.
 *
 * WHY THERE IS AN index.js AND NOT A BUILD OUTPUT.
 *
 * The conventional layout is index.js at the block root importing edit.js, and
 * a bundler producing a build directory that block.json points at instead. This
 * theme has no bundler step, so the build half of that convention does not
 * exist and pretending it does would mean committing compiled output. Both
 * files are therefore plain classic scripts, declared in order in block.json's
 * editorScript array, and index.js is the one that calls registerBlockType.
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

	var edit = wp.maulikPortfolio.projectFiltersEdit;

	wp.blocks.registerBlockType( 'maulik-portfolio/project-filters', {
		edit: edit,
		save: function () {
			return null;
		},
	} );
} )( window.wp );
