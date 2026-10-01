<?php
/**
 * Title: Page Not Found
 * Slug: maulik-portfolio/page-not-found
 * Categories: maulik-portfolio-error
 * Description: The 404 view, with the eyebrow taken from the design.
 * Inserter: no
 *
 * The eyebrow "404 / ROUTE NOT RESOLVED" came from the design's inline fallback
 * and it is good, so it is preserved verbatim rather than softened.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"tagName":"section","className":"error-404 dark-grid","layout":{"type":"constrained"}} -->
<section class="wp-block-group error-404 dark-grid">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '404 / ROUTE NOT RESOLVED', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading"><?php echo esc_html__( 'Page not found.', 'maulik-portfolio' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php echo esc_html__( 'That URL does not exist on this site. It may have moved, or the address may be mistyped.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( '/' ) ); ?>"><?php echo esc_html__( 'Back to home', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( '/projects/' ) ); ?>"><?php echo esc_html__( 'Browse projects', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</section>
<!-- /wp:group -->