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
 * WHY THIS IS A PATTERN AT ALL WHEN IT IS A WORD FOR WORD COPY OF
 * templates/404.html. A 404 is a rendering context rather than an editable page.
 * The template part it needs cannot hold translatable strings, and the visitor
 * facing copy still has to be translatable, so the strings live here. The two
 * copies have to be kept in step by hand, which is a real cost: the pattern
 * exists because translating the template is not possible and deleting the
 * pattern would make the strings unreachable, not because it renders anything
 * the template does not.
 *
 * ONE CLASS CHANGED AND NOTHING ELSE. The section carried the bare name
 * "dark-grid", which no stylesheet targets, so the 48px grid texture was not
 * rendering on the 404 page at all. It is now is-style-surface-dark-grid, which is
 * the class Core emits for the style variation in styles/surface-dark-grid.json.
 * The legacy name is not kept alongside it as an alias; two names for one
 * surface is how a stylesheet ends up with six rules for four textures.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"tagName":"section","className":"error-404 is-style-surface-dark-grid","layout":{"type":"constrained"}} -->
<section class="wp-block-group error-404 is-style-surface-dark-grid">
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
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( null, '/' ) ); ?>"><?php echo esc_html__( 'Back to home', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( null, '/projects/' ) ); ?>"><?php echo esc_html__( 'Browse projects', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</section>
<!-- /wp:group -->