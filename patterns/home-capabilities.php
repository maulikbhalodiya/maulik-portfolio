<?php
/**
 * Title: Home Capabilities Section
 * Slug: maulik-portfolio/home-capabilities
 * Categories: maulik-portfolio-home
 * Description: Homepage section 02, on the solid base surface.
 * Inserter: yes
 *
 * Section 02 of 08. Solid base, so the rhythm alternates against the hero.
 *
 * PATTERNS RUN ON init. The conditional tags, the queried post accessor, the
 * loop and the title accessors are not available here and are not used.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"tagName":"section","className":"section section-capabilities has-base-background-color has-background","anchor":"what-i-build","ariaLabelledby":"heading-what-i-build","backgroundColor":"base","layout":{"type":"constrained"}} -->
<section class="wp-block-group section section-capabilities has-base-background-color has-background" id="what-i-build" aria-labelledby="heading-what-i-build">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '02 · Core Engineering Capabilities', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-what-i-build"} -->
	<h2 class="wp-block-heading" id="heading-what-i-build"><?php echo esc_html__( 'I build systems, not just websites.', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php echo esc_html__( 'The five capability areas and their inspection panel are added in a later phase.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->
</section>
<!-- /wp:group -->