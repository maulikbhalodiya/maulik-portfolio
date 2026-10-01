<?php
/**
 * Front end asset registration.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'maulik_portfolio_enqueue_styles' ) ) {
	/**
	 * Enqueue the theme stylesheet.
	 *
	 * A block theme must enqueue its own stylesheet. Core does not enqueue
	 * style.css for block themes, so relying on the old automatic behaviour
	 * leaves you with an unstyled front end and no obvious clue why.
	 *
	 * wp_style_add_data( ..., 'path', ... ) is what allows
	 * should_load_separate_core_block_assets to work correctly for a block
	 * theme. Core uses the path to build per-block style dependencies, so
	 * without it the on demand loading heuristics cannot see this handle.
	 *
	 * SCRIPT_DEBUG asks for the minified build, but the minified build is a
	 * build artefact. A checkout that has not been built, or one distributed
	 * as a ZIP without build output, would otherwise 404 on every page load.
	 * When the .min file is not actually there the unminified one is used and
	 * the page renders, slowly, rather than not at all.
	 *
	 * The compiled stylesheet lives at assets/css/theme.css and is NOT style.css.
	 * style.css is the hand authored theme header that WordPress reads to learn
	 * the theme name, version and text domain, and it is one of the two files
	 * that mark this directory as a block theme. The Sass build deliberately
	 * targets a different path so the header survives every build.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function maulik_portfolio_enqueue_styles() {
		$rel = 'assets/css/theme.css';

		// Prefer the minified build, but only if it is really there.
		if ( SCRIPT_DEBUG ) {
			$min = 'assets/css/theme.min.css';

			if ( file_exists( get_parent_theme_file_path( $min ) ) ) {
				$rel = $min;
			}
		}

		/*
		 * The parent theme file URI is used rather than
		 * get_template_directory_uri() so a child theme can override the
		 * stylesheet and have that override take effect.
		 */
		wp_enqueue_style(
			'maulik-portfolio-style',
			get_parent_theme_file_uri( $rel ),
			array(),
			maulik_portfolio_asset_version( $rel )
		);

		wp_style_add_data( 'maulik-portfolio-style', 'path', get_parent_theme_file_path( $rel ) );
	}
}

if ( ! function_exists( 'maulik_portfolio_asset_version' ) ) {
	/**
	 * Build a cache busting version string for an asset.
	 *
	 * Uses the file modification time when the file is readable, so editing a
	 * stylesheet does not require remembering to bump a version string. Falls
	 * back to the theme version when the file is missing or unreadable, which
	 * is the common case for a theme distributed as a ZIP without vendor
	 * artefacts or an unbuilt stylesheet.
	 *
	 * @since 0.1.0
	 *
	 * @param string $relative_path    Path relative to the theme root.
	 * @param bool   $child_overridable Whether to resolve through the child
	 *                                  theme first. True for assets a child
	 *                                  theme is expected to replace, such as
	 *                                  fonts, so the mtime comes from the file
	 *                                  that will actually be served.
	 * @return string Version string.
	 */
	function maulik_portfolio_asset_version( $relative_path, $child_overridable = false ) {
		$path = $child_overridable
			? get_theme_file_path( $relative_path )
			: get_parent_theme_file_path( $relative_path );

		if ( is_readable( $path ) ) {
			$mtime = filemtime( $path );

			if ( false !== $mtime ) {
				return MAULIK_PORTFOLIO_VERSION . '.' . (string) $mtime;
			}
		}

		return MAULIK_PORTFOLIO_VERSION;
	}
}
