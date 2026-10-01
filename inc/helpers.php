<?php
/**
 * Shared helper functions.
 *
 * @package Maulik_Portfolio
 */

namespace Maulik_Portfolio;

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'Maulik_Portfolio\theme_version' ) ) {
	/**
	 * Return the theme version.
	 *
	 * This file exists mainly to establish the namespacing convention for the
	 * theme. Functions that are hooked to WordPress actions keep the
	 * maulik_portfolio_ prefix so that phpcs PrefixAllGlobals recognises them as
	 * theme globals. Internal helpers that are never hooked move into the
	 * Maulik_Portfolio namespace so that they cannot collide with another theme.
	 *
	 * Note the ordering constraint that this file demonstrates. The namespace
	 * declaration has to come before any other statement in the file, including
	 * the ABSPATH guard, because PHP requires it to be the very first
	 * statement. This is the only shape a namespaced theme file can take.
	 *
	 * The single function below is deliberately trivial. It is here to prove
	 * the convention compiles and lints, not because the theme needs it.
	 *
	 * @since 0.1.0
	 *
	 * @return string Theme version string.
	 */
	function theme_version() {
		return defined( 'MAULIK_PORTFOLIO_VERSION' ) ? (string) MAULIK_PORTFOLIO_VERSION : '0.0.0';
	}
}
