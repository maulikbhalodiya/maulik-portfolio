<?php
/**
 * Title: Home Contact Section
 * Slug: maulik-portfolio/home-contact
 * Categories: maulik-portfolio-home
 * Description: Homepage section 08, on the pure black surface.
 * Inserter: yes
 *
 * Section 08 of 08, and the only pure black section in the theme.
 *
 * The heading is job-seeking language and it is correct. "Looking for a WordPress
 * or PHP developer?" is a recruiter speaking, which is the opposite of the
 * pattern this theme bans. Do not soften it.
 *
 * The prototype's Email Me CTA became a link to the contact page, because there
 * is no mailto and no plaintext address anywhere in this theme.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"tagName":"section","className":"section section-contact has-black-background-color has-background","anchor":"hiring-cta","ariaLabelledby":"heading-hiring-cta","backgroundColor":"black","layout":{"type":"constrained"}} -->
<section class="wp-block-group section section-contact has-black-background-color has-background" id="hiring-cta" aria-labelledby="heading-hiring-cta">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '08 · Employment Opportunities and Technical Evaluation', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-hiring-cta"} -->
	<h2 class="wp-block-heading" id="heading-hiring-cta"><?php echo esc_html__( 'Looking for a WordPress or PHP developer?', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php echo esc_html__( 'Technical evaluation details are added in a later phase.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( '/contact/' ) ); ?>"><?php echo esc_html__( 'Start a conversation', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</section>
<!-- /wp:group -->