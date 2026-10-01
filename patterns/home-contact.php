<?php
/**
 * Title: Home Contact Section
 * Slug: maulik-portfolio/home-contact
 * Categories: maulik-portfolio-home
 * Description: Homepage section 08, the contact call to action, on the pure black surface.
 * Inserter: yes
 *
 * Section 08 of 08, and the only pure black section in the theme.
 *
 * THE HEADING IS JOB SEEKING LANGUAGE AND IT IS CORRECT. "Looking for a WordPress
 * or PHP developer?" is a recruiter speaking. It is the opposite of the pattern
 * this theme bans, and it is framed correctly. Do not soften it.
 *
 * The prototype's Email Me CTA became a link to the contact page, because there is
 * no mail link, no plaintext address and no mail icon anywhere in this theme. The
 * address was published and spam listed inside 48 hours. Contact runs through the
 * form or it does not happen.
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

	<!-- wp:paragraph {"className":"section-lede"} -->
	<p class="section-lede"><?php echo esc_html__( 'Send the role, the codebase and the problem. I will tell you directly whether I am the right fit.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph {"className":"section-contact__meta","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="section-contact__meta"><?php echo esc_html__( 'Contact runs through the form on the contact page. No email address is published on this site.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"className":"section-contact__actions","layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-buttons section-contact__actions">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( null, '/contact/' ) ); ?>"><?php echo esc_html__( 'Start a conversation', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( null, '/resume/' ) ); ?>"><?php echo esc_html__( 'View Resume', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</section>
<!-- /wp:group -->
