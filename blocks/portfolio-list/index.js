/**
 * Registration entry point for the portfolio list block.
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
 * The attribute schema, the title, the supports and everything else live in
 * block.json, and WordPress already publishes that file's contents to the
 * editor before this script runs. Repeating them here would be a second source
 * of truth that silently disagrees with the first whenever either is edited.
 * So only the two things JavaScript owns are passed: edit, and save.
 *
 * save returns null because the block is dynamic. The front end markup comes
 * from render.php, and any markup returned here would be stored in post content
 * and then thrown away at render time.
 */

( function ( wp ) {
	'use strict';

	var edit = wp.maulikPortfolio.portfolioListEdit;

	wp.blocks.registerBlockType( 'maulik-portfolio/portfolio-list', {
		edit: edit,
		save: function () {
			return null;
		},
	} );
} )( window.wp );