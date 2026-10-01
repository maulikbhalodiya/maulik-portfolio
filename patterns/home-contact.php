<?php
/**
 * Title: Home Contact Section
 * Slug: maulik-portfolio/home-contact
 * Categories: maulik-portfolio-home
 * Description: Homepage section 08, the hiring call to action with a contact and a resume button, on the solid black surface.
 * Inserter: yes
 *
 * Section 08 of 08, and the only section with no texture on it. It is the closing
 * band and it is deliberately flat.
 *
 * THE HEADING IS JOB SEEKING LANGUAGE AND IT IS CORRECT. "Looking for a WordPress
 * or PHP developer?" is a recruiter speaking. It is the opposite of the pattern
 * this theme bans, and it is framed correctly. Do not soften it.
 *
 * THE LEDE IS THE CASE STUDIES SENTENCE, NOT AN INTAKE SENTENCE. The previous
 * version of this pattern invited a reader to "Send the role, the codebase and the
 * problem" and carried a second line stating that no email address is published.
 * The approved homepage leads with the case studies as the way to evaluate the
 * work. That is the sentence here. The no email statement is a standing project
 * rule enforced in CI, not visitor copy, and repeating it on the page is
 * apologising for something that does not happen.
 *
 * THERE IS NO EMAIL ADDRESS IN THIS FILE AND THERE IS NO MAILTO. Contact runs
 * through the contact page form or it does not happen. The prototype's Email Me
 * call to action became a link to the contact page, because a mail icon promises a
 * mail client. That address was published and spam listed inside 48 hours.
 *
 * WHY backgroundColor SURVIVES THE VOCABULARY CHANGE. has-black-background-color
 * is not a legacy name. It is the class WordPress emits for the backgroundColor
 * attribute, so removing it while keeping the attribute would produce markup that
 * disagrees with what Core renders. is-style-section is a style variation and
 * does get the prefix.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"tagName":"section","className":"is-style-section","anchor":"hiring-cta","ariaLabelledby":"heading-hiring-cta","backgroundColor":"black","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section has-black-background-color has-background" id="hiring-cta" aria-labelledby="heading-hiring-cta">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '08 · Employment Opportunities and Technical Evaluation', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-hiring-cta"} -->
	<h2 class="wp-block-heading" id="heading-hiring-cta"><?php echo esc_html__( 'Looking for a WordPress or PHP developer?', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php echo esc_html__( 'If you are hiring for this kind of work, the case studies are the fastest way to evaluate the work. Each one shows the problem, the architecture, the decisions and the verification steps, with client identities removed.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( null, '/contact/' ) ); ?>"><?php echo esc_html__( 'Start a conversation', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( null, '/resume/' ) ); ?>"><?php echo esc_html__( 'Read the resume', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</section>
<!-- /wp:group -->