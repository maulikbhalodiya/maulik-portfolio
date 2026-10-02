<?php
/**
 * Title: Home Contact Section
 * Slug: maulik-portfolio/home-contact
 * Categories: maulik-portfolio-home
 * Description: Homepage section 08, the hiring call to action on an inner card over pure black, with resume, LinkedIn and contact actions.
 * Inserter: yes
 *
 * Section 08 of 08, and the only section with no texture on it. It is the closing
 * band and it is deliberately flat: pure black behind a #0A0A0B card with a 1px
 * #262626 border at p-8 sm:p-12 md:p-16.
 *
 * THE INNER CARD IS NEW AND IT IS THE POINT OF THE SECTION. The previous version
 * put the eyebrow, the heading, the lede and two buttons straight onto black with
 * no card around them, and capped the measure at 576px. The design wraps
 * everything in a bordered card and caps the content at 768px. Without the card
 * the band is a black rectangle with text on it.
 *
 * THE HEADING IS JOB SEEKING LANGUAGE AND IT IS CORRECT. "Looking for a WordPress
 * or PHP developer?" is a recruiter speaking. It is the opposite of the pattern
 * this theme bans, and it is framed correctly. Do not soften it.
 *
 * THE THIRD ACTION IS THE CONTACT PAGE, NOT A MAIL LINK. The design's third
 * button is "Email Me" behind a mail icon. This theme publishes no email address
 * and offers no mail affordance anywhere: the prototype's address was published
 * and spam listed inside 48 hours. A mail icon promises a mail client the site
 * deliberately does not offer, so the third action is a link to the contact page
 * form. The icon changes with it.
 *
 * THERE IS NO EMAIL ADDRESS IN THIS FILE AND THERE IS NO MAILTO.
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

/**
 * The two published external channels, which must match channel-links.php.
 */
$maulik_portfolio_cta_channels = array(
	array(
		'label' => __( 'Connect on LinkedIn', 'maulik-portfolio' ),
		'url'   => 'https://www.linkedin.com/in/maulikbhalodiya/',
	),
);

?>
<!-- wp:group {"anchor":"hiring-cta","ariaLabelledby":"heading-hiring-cta","backgroundColor":"black","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section section-cta hz-hiring has-black-background-color has-background" id="hiring-cta" aria-labelledby="heading-hiring-cta">
	<!-- wp:group {"className":"hz-hiring__card","layout":{"type":"default"}} -->
	<div class="wp-block-group hz-hiring__card">
		<!-- wp:group {"className":"hz-stack-6","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-stack-6">
			<!-- wp:paragraph {"className":"hz-eyebrow"} -->
			<p class="hz-eyebrow"><?php echo esc_html__( '08 · Employment Opportunities and Technical Evaluation', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2,"anchor":"heading-hiring-cta","className":"hz-section__title"} -->
			<h2 class="wp-block-heading hz-section__title" id="heading-hiring-cta"><?php echo esc_html__( 'Looking for a WordPress or PHP developer?', 'maulik-portfolio' ); ?></h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"className":"hz-hiring__lede"} -->
			<p class="hz-hiring__lede"><?php echo esc_html__( 'I am interested in software engineering roles involving custom WordPress plugin development, PHP backend architecture, API integrations, payment workflows and technically challenging systems. Review my resume or connect directly to discuss how I can contribute to your engineering team.', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:group {"className":"hz-hiring__actions","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-hiring__actions">
				<!-- wp:buttons {"className":"hz-actions","layout":{"type":"flex","flexWrap":"wrap"}} -->
				<div class="wp-block-buttons hz-actions">
					<!-- wp:button {"className":"hz-btn-amber hz-btn-lg"} -->
					<div class="wp-block-button hz-btn-amber hz-btn-lg">
						<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( null, '/resume/' ) ); ?>"><?php echo esc_html__( 'View Resume', 'maulik-portfolio' ); ?></a>
					</div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
				<?php foreach ( $maulik_portfolio_cta_channels as $maulik_portfolio_cta_channel ) : ?>
					<a class="hz-link hz-btn-dark hz-btn-lg" href="<?php echo esc_url( $maulik_portfolio_cta_channel['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $maulik_portfolio_cta_channel['label'] ); ?></a>
				<?php endforeach; ?>
				<a class="hz-link hz-btn-dark hz-btn-lg" href="<?php echo esc_url( get_home_url( null, '/contact/' ) ); ?>"><?php echo esc_html__( 'Start a conversation', 'maulik-portfolio' ); ?></a>
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->