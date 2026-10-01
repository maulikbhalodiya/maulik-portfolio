<?php
/**
 * Front end performance decisions.
 *
 * These two filters were previously registered from inc/setup.php alongside
 * unrelated theme support calls. They live here instead so that the reason for
 * them, which is the whole point, stays attached to the code.
 *
 * The filter names and their values are not obvious and the default looks like
 * an oversight when read without context, so the reasoning is recorded in full
 * below rather than left to a reader to reconstruct.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'maulik_portfolio_performance' ) ) {
	/**
	 * Register the conditional block asset loading filters.
	 *
	 * Registered on after_setup_theme. Core consults both of these while
	 * resolving the block style and script queues for a request, so they have
	 * to be in place before the first template is loaded. Registering them from
	 * a template render would be too late for the page being rendered and would
	 * also mean a page cached without the filter stays wrong forever.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function maulik_portfolio_performance() {
		/*
		 * Without this, Core enqueues the bundled wp-block-library stylesheet, a
		 * single sheet over 100KB, on every page. That sheet is render
		 * blocking, so it sits directly in the critical path and the browser
		 * cannot paint anything until it has arrived and parsed. With it, Core
		 * registers each core block style as a separate handle and only queues
		 * the handles the blocks on the current page actually need. A page that
		 * renders fifteen of the ninety core blocks stops paying for the other
		 * seventy five.
		 */
		add_filter( 'should_load_separate_core_block_assets', '__return_true' );

		/*
		 * The companion to the filter above, and it is what makes the split
		 * useful rather than merely different. Left at its default of false,
		 * Core still prints the individual block handles it collected, because
		 * it assumes plugins and themes may have made the shared sheet
		 * necessary. Turning it on means the enqueued set is exactly the set
		 * the rendered blocks need.
		 */
		add_filter( 'should_load_block_assets_on_demand', '__return_true' );
	}
	add_action( 'after_setup_theme', 'maulik_portfolio_performance' );
}

if ( ! function_exists( 'maulik_portfolio_performance_rules' ) ) {
	/**
	 * Record the constraints that keep the front end cacheable.
	 *
	 * This function intentionally registers no filter. It exists so the rules
	 * below are stated in code that is loaded on every request, next to the
	 * filters they justify, instead of living only in a wiki page that is not
	 * read by the person about to violate them.
	 *
	 * Rule: templates and parts must contain ZERO is_user_logged_in() branches.
	 *
	 * Full page caching is keyed on the response to an anonymous request. A
	 * logged in response differs from the cached one, and a cache will either
	 * serve the anonymous markup to a logged in visitor, or evict the entry on
	 * every hit and cache nothing at all. Either outcome is bad and the first
	 * one leaks private markup to public visitors.
	 *
	 * A portfolio has no reason to produce per user front end output. There is
	 * no account area, no saved state, no personalised content. The only
	 * logged in difference worth having is the admin bar, and Core renders that
	 * through its own template hook with a non zero template offset, which
	 * already prevents the body of the page from being served from the full
	 * page cache. So the constraint costs nothing here.
	 *
	 * The correct way to vary output for a logged in user is the read more
	 * template part, not a branch inside a template. If a template ever needs
	 * to know, it can read a post meta value that a filter varies, which keeps
	 * the response itself identical and the cache entry valid.
	 *
	 * Rule: the filters in this file are registered on after_setup_theme, never
	 * from a template, for the reason documented on
	 * maulik_portfolio_performance().
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function maulik_portfolio_performance_rules() {
		// Intentionally empty. The rules are documentation attached to the
		// filters above, not runtime checks. A runtime check here would cost a
		// function call on every request to assert something a linter or a code
		// review can assert instead, and it would still be bypassable from a
		// child theme, which is the case that matters.
	}
}
