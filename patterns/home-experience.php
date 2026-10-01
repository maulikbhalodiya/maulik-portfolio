<?php
/**
 * Title: Home Experience Section
 * Slug: maulik-portfolio/home-experience
 * Categories: maulik-portfolio-home
 * Description: Homepage section 05, the two professional experience entries with verified dates, on the 96px cream editorial surface.
 * Inserter: yes
 *
 * Section 05 of 08. The cream editorial surface at 96px, which is the first light
 * surface on the page and the reason the first five sections read as a dark run.
 *
 * TWO ENTRIES, AND BOTH DATES ARE VERIFIED. WordPress Developer full time from
 * July 2025 to present, PHP Developer Intern from January to June 2025. A recruiter
 * facing a timeline with no dates reads it as an omission rather than as modesty,
 * so the dates are carried.
 *
 * THE DETAIL SENTENCES ARE NOT INVENTED. The previous version of this pattern
 * summarised each role in its own words. The approved homepage uses specific
 * sentences, and those are the ones here. The previous version also built the
 * period with a translators comment and a sprintf, which is defensible for an
 * assembled string. The approved copy is a fixed line of prose, so it is a fixed
 * line of prose.
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
$maulik_portfolio_experience_entries = array(
	array(
		'label' => __( 'WordPress Developer · Qrolic Technologies · July 2025 to present', 'maulik-portfolio' ),
		'text'  => __( 'Custom plugin development and backend work inside WordPress, including payment architectures, form driven internal systems, third party API integrations and field level encryption. Responsibilities run from design through implementation to verification.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'PHP Developer Intern · Qrolic Technologies · January to June 2025', 'maulik-portfolio' ),
		'text'  => __( 'PHP and WordPress fundamentals in a production codebase: structure, form handling, data access and debugging real problems rather than exercises.', 'maulik-portfolio' ),
	),
);

?>
<!-- wp:group {"tagName":"section","className":"is-style-section is-style-surface-cream-editorial","anchor":"experience","ariaLabelledby":"heading-experience","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section is-style-surface-cream-editorial" id="experience" aria-labelledby="heading-experience">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '05 · Career Progression and Responsibilities', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-experience"} -->
	<h2 class="wp-block-heading" id="heading-experience"><?php echo esc_html__( 'Professional Experience', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:group {"tagName":"div","className":"is-style-section-tight","layout":{"type":"constrained"}} -->
	<div class="wp-block-group is-style-section-tight">
		<?php foreach ( $maulik_portfolio_experience_entries as $maulik_portfolio_experience_entry ) : ?>
			<!-- wp:paragraph {"typography":{"fontFamily":"var:preset|font-family|mono"}} -->
			<p><?php echo esc_html( $maulik_portfolio_experience_entry['label'] ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html( $maulik_portfolio_experience_entry['text'] ); ?></p>
			<!-- /wp:paragraph -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->