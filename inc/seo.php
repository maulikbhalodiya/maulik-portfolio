<?php
/**
 * Search engine exposure controls.
 *
 * @package Maulik_Dev
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'maulik_dev_is_development' ) ) {
	/**
	 * Determine whether the theme considers itself to be in development.
	 *
	 * Read this before you copy the logic anywhere else. The guard below is
	 * deliberately hard to leave enabled by accident. The flag is on only when
	 * BOTH of the following are true:
	 *
	 * 1. WP_DEBUG is true. WordPress sets this to true only on development
	 *    installs. A production install running a cached config file will have
	 *    it false.
	 * 2. Either MAULIK_DEV_NOINDEX is explicitly defined and true, or the
	 *    maulik_dev_noindex option has been set to a truthy value.
	 *
	 * The trap this guards against is leaving a single boolean flag in wp-config
	 * and shipping it. Because condition 1 is WP_DEBUG, defining only
	 * MAULIK_DEV_NOINDEX on a production site does nothing at all. There is no
	 * single constant or option that turns this on by itself, so forgetting to
	 * clean up cannot de index a production site.
	 *
	 * If you genuinely need noindex on a non debug production install, set both
	 * WP_DEBUG and MAULIK_DEV_NOINDEX and read this function's documentation
	 * again afterwards.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True when noindex output is enabled.
	 */
	function maulik_dev_is_development() {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return false;
		}

		if ( defined( 'MAULIK_DEV_NOINDEX' ) && MAULIK_DEV_NOINDEX ) {
			return true;
		}

		/**
		 * Filters whether the development noindex tag is emitted.
		 *
		 * This filter only has any effect while WP_DEBUG is true, which is why
		 * no filter at all can enable noindex on a production install.
		 *
		 * @since 0.1.0
		 *
		 * @param bool $enabled Whether noindex is enabled.
		 */
		return (bool) apply_filters( 'maulik_dev_noindex', (bool) get_option( 'maulik_dev_noindex', false ) );
	}
}

if ( ! function_exists( 'maulik_dev_noindex' ) ) {
	/**
	 * Emit a noindex, nofollow robots meta tag while in development.
	 *
	 * Silently indexing a development or staging copy of a portfolio is a real
	 * and common problem, because staging is usually publicly reachable and
	 * frequently has no password in front of it.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function maulik_dev_noindex() {
		if ( ! maulik_dev_is_development() ) {
			return;
		}

		echo '<meta name="robots" content="noindex, nofollow" />' . "\n";
	}
}
