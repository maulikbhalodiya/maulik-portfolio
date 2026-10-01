<?php
/**
 * Front end asset registration.
 *
 * @package Maulik_Dev
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'maulik_dev_enqueue_styles' ) ) {
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
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function maulik_dev_enqueue_styles() {
		$suffix = SCRIPT_DEBUG ? '.min' : '';

		/*
		 * The parent theme file URI is used rather than
		 * get_template_directory_uri() so a child theme can override style.css
		 * and have that override take effect.
		 */
		wp_enqueue_style(
			'maulik-dev-style',
			get_parent_theme_file_uri( 'style' . $suffix . '.css' ),
			array(),
			maulik_dev_asset_version( 'style' . $suffix . '.css' )
		);

		wp_style_add_data( 'maulik-dev-style', 'path', get_parent_theme_file_path( 'style' . $suffix . '.css' ) );
	}
}

if ( ! function_exists( 'maulik_dev_asset_version' ) ) {
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
	 * @param string $relative_path Path relative to the theme root.
	 * @return string Version string.
	 */
	function maulik_dev_asset_version( $relative_path ) {
		$path = get_parent_theme_file_path( $relative_path );

		if ( is_readable( $path ) ) {
			$mtime = filemtime( $path );

			if ( false !== $mtime ) {
				return MAULIK_DEV_VERSION . '.' . (string) $mtime;
			}
		}

		return MAULIK_DEV_VERSION;
	}
}
