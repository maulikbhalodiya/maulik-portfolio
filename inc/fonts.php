<?php
/**
 * Web font loading and above the fold preloading.
 *
 * The @font-face rules themselves are NOT declared here. They live in the
 * compiled stylesheet assets/css/theme.css, which assets.php already enqueues,
 * via assets/styles/base/_fonts.scss. Declaring a second copy of those rules
 * from PHP would mean the same src URL is registered twice, which makes the
 * browser download the file twice and can produce two competing font metrics
 * for the same family.
 *
 * What this file owns is the part PHP can do better than CSS: telling the
 * browser to start the font transfer before it discovers the @font-face rule.
 *
 * The @font-face rules are inside a render blocking stylesheet, so without a
 * preload the browser cannot know a font exists until it has already parsed and
 * executed theme.css. By the time it starts the font request the text may
 * already be painted in a fallback face, causing a visible swap.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'maulik_portfolio_fonts' ) ) {
	/**
	 * Register the font preloads.
	 *
	 * Hooked on after_setup_theme so the filter this exposes is in place long
	 * before wp_head runs and long before a template is rendered. Registering
	 * it from a template would emit the markup at the wrong point in the
	 * document and would be invisible to any caching layer.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function maulik_portfolio_fonts() {
		add_action( 'wp_head', 'maulik_portfolio_print_font_preloads', 1 );
	}
	add_action( 'after_setup_theme', 'maulik_portfolio_fonts' );
}

if ( ! function_exists( 'maulik_portfolio_preload_fonts' ) ) {
	/**
	 * List the font files worth preloading.
	 *
	 * At most two faces, and only the ones that paint above the fold on the
	 * first view: the Syne display face used by the h1, and the Plus Jakarta
	 * Sans body face. Preloading the full set is worse than not preloading at
	 * all, because every preloaded byte competes with the LCP element for the
	 * same connection and the same bandwidth. A font that turns out to be
	 * below the fold has spent the budget that the h1 needed.
	 *
	 * The mono face is deliberately absent. It is metadata, and it renders
	 * well after the headline.
	 *
	 * Paths are relative to the theme root so that a child theme can replace a
	 * face by shipping its own file at the same path, which is why the URL is
	 * built with get_theme_file_uri() rather than get_template_directory_uri().
	 *
	 * @since 0.1.0
	 *
	 * @return string[] Relative paths of the font files to preload, keyed by
	 *                 a short label used only for readability.
	 */
	function maulik_portfolio_preload_fonts() {
		$preloads = array(
			// Display face for the h1, above the fold on every template.
			'syne-display'           => 'assets/fonts/syne-latin-700-800.woff2',
			// Body face, needed for the first paragraph of paint.
			'plus-jakarta-sans-body' => 'assets/fonts/plus-jakarta-sans-latin-400-600.woff2',
		);

		/**
		 * Filters the list of font files preloaded in the document head.
		 *
		 * Removing an entry here does not remove the font, only the preload
		 * hint for it. The face is still declared by the stylesheet and will
		 * still load when the browser reaches the text that needs it.
		 *
		 * @since 0.1.0
		 *
		 * @param string[] $preloads Relative paths of the font files to preload,
		 *                          keyed by a short label.
		 */
		$preloads = apply_filters( 'maulik_portfolio_preload_fonts', $preloads );

		/*
		 * Only keep entries that resolve to a real file. In a checkout where
		 * the font binaries have not been committed, or where a child theme
		 * removed them, this yields an empty set and the page simply loads its
		 * fonts through the stylesheet as usual. A missing file must never be
		 * a fatal, and must never become a 404 in the critical path.
		 */
		return array_filter(
			$preloads,
			static function ( $relative_path ) {
				return is_string( $relative_path )
					&& '' !== $relative_path
					&& file_exists( get_theme_file_path( $relative_path ) );
			}
		);
	}
}

if ( ! function_exists( 'maulik_portfolio_print_font_preloads' ) ) {
	/**
	 * Print the preload link elements for the above the fold font faces.
	 *
	 * Every link is emitted with the crossorigin attribute. Font requests are
	 * made in CORS mode by the CSS Fonts specification, so a preload without
	 * that attribute would be a separate, uncached request for the same file
	 * rather than a match against the one the stylesheet later asks for.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function maulik_portfolio_print_font_preloads() {
		$preloads = maulik_portfolio_preload_fonts();

		if ( empty( $preloads ) ) {
			return;
		}

		foreach ( $preloads as $relative_path ) {
			$url = get_theme_file_uri( $relative_path );
			$ver = maulik_portfolio_asset_version( $relative_path, true );

			if ( '' !== $ver ) {
				$url = add_query_arg( 'ver', rawurlencode( $ver ), $url );
			}

			printf(
				'<link rel="preload" href="%1$s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( $url )
			);
		}
	}
}
