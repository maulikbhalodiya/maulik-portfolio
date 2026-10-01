<?php
/**
 * Title: Home Experience Section
 * Slug: maulik-portfolio/home-experience
 * Categories: maulik-portfolio-home
 * Description: Homepage section 05, the professional experience timeline, on the solid base surface.
 * Inserter: yes
 *
 * Section 05 of 08. Solid base, continuing the alternating rhythm.
 *
 * TWO ENTRIES. The verified timeline is PHP Developer Intern from January to June
 * 2025, then WordPress Developer full time from July 2025 to present. Those dates
 * come from the resume, so they are used. A recruiter facing a timeline with no
 * dates reads it as an omission rather than as modesty.
 *
 * The period is a translatable string built with a translator comment, because the
 * word "present" is a word and it has to be translatable like any other. The dates
 * themselves are numbers and are not translated.
 *
 * Qrolic Technologies. One c after Qro. The resume says "Qrologic" in one place and
 * that is a typo, and it is spelled correctly everywhere here.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Verified professional experience.
 */
$maulik_portfolio_experience = array(
	array(
		'role'    => __( 'WordPress Developer', 'maulik-portfolio' ),
		'company' => __( 'Qrolic Technologies', 'maulik-portfolio' ),
		'period'  => sprintf(
			/* translators: 1: start month and year, 2: end month and year or the word present. */
			__( '%1$s to present', 'maulik-portfolio' ),
			__( 'July 2025', 'maulik-portfolio' )
		),
		'detail'  => __( 'Custom plugin and WordPress engineering. REST API layers, payment architecture, document processing and role scoped access control.', 'maulik-portfolio' ),
	),
	array(
		'role'    => __( 'PHP Developer Intern', 'maulik-portfolio' ),
		'company' => __( 'Qrolic Technologies', 'maulik-portfolio' ),
		'period'  => sprintf(
			/* translators: 1: start month and year, 2: end month and year. */
			__( '%1$s to %2$s', 'maulik-portfolio' ),
			__( 'January 2025', 'maulik-portfolio' ),
			__( 'June 2025', 'maulik-portfolio' )
		),
		'detail'  => __( 'Backend development, database work and WordPress integration during an initial internship placement.', 'maulik-portfolio' ),
	),
);

?>
<!-- wp:group {"tagName":"section","className":"section section-experience has-base-background-color has-background","anchor":"experience","ariaLabelledby":"heading-experience","backgroundColor":"base","layout":{"type":"constrained"}} -->
<section class="wp-block-group section section-experience has-base-background-color has-background" id="experience" aria-labelledby="heading-experience">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '05 · Career Progression and Responsibilities', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-experience"} -->
	<h2 class="wp-block-heading" id="heading-experience"><?php echo esc_html__( 'Professional Experience', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:list {"className":"experience-list"} -->
	<ul class="wp-block-list experience-list">
		<?php foreach ( $maulik_portfolio_experience as $maulik_portfolio_experience_entry ) : ?>
			<!-- wp:list-item {"className":"experience-item"} -->
			<li class="experience-item">
				<!-- wp:heading {"level":3,"className":"experience-item__role"} -->
				<h3 class="wp-block-heading experience-item__role"><?php echo esc_html( $maulik_portfolio_experience_entry['role'] ); ?></h3>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"experience-item__meta","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
				<p class="experience-item__meta"><?php echo esc_html( $maulik_portfolio_experience_entry['company'] ); ?> <span class="experience-item__separator" aria-hidden="true">·</span> <?php echo esc_html( $maulik_portfolio_experience_entry['period'] ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"experience-item__detail"} -->
				<p class="experience-item__detail"><?php echo esc_html( $maulik_portfolio_experience_entry['detail'] ); ?></p>
				<!-- /wp:paragraph -->
			</li>
			<!-- /wp:list-item -->
		<?php endforeach; ?>
	</ul>
	<!-- /wp:list -->
</section>
<!-- /wp:group -->
