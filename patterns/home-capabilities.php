<?php
/**
 * Title: Home Capabilities Section
 * Slug: maulik-portfolio/home-capabilities
 * Categories: maulik-portfolio-home
 * Description: Homepage section 02, the five capability areas, on the solid base surface.
 * Inserter: yes
 *
 * Section 02 of 08. Solid base, so the rhythm alternates against the hero.
 *
 * FIVE CAPABILITY AREAS, and five is the verified count. The design data file has
 * CapabilityItem with five entries and the design spec repeats five. The brief for
 * this file said "six" and then listed five, so the list wins and no sixth area is
 * invented to make a number agree. Inventing one would be exactly the failure this
 * whole content model exists to prevent.
 *
 * NO LEVELS AND NO PERCENTAGES. Each area carries a summary and a related case
 * study, which is what the design carries. A proficiency number would be a claim,
 * and a claim has to be defended forever.
 *
 * PATTERNS RUN ON init. The conditional tags, the queried post accessor, the loop
 * and the title accessors are not available here and are not used.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The five verified capability areas.
 *
 * The path is the related case study route. Three of the five are real projects.
 * The PHP Backend area relates to RankKernel, which is not a project slug and has
 * its own route at /rankkernel/. That is the design's own routing decision, kept.
 */
$maulik_portfolio_capability_areas = array(
	array(
		'title'      => __( 'WordPress Engineering', 'maulik-portfolio' ),
		'summary'    => __( 'Custom plugin architecture, roles and capability checks, form and entry handling, and template level work inside WordPress.', 'maulik-portfolio' ),
		'path'       => '/projects/employee-application-management/',
		'link_label' => __( 'Employee Application Management System', 'maulik-portfolio' ),
	),
	array(
		'title'      => __( 'PHP Backend', 'maulik-portfolio' ),
		'summary'    => __( 'Server side logic, custom REST routes, database access and scheduled work, written to be read by the next maintainer.', 'maulik-portfolio' ),
		'path'       => '/rankkernel/',
		'link_label' => __( 'RankKernel', 'maulik-portfolio' ),
	),
	array(
		'title'      => __( 'API and Integration Engineering', 'maulik-portfolio' ),
		'summary'    => __( 'Third party service integration, document processing pipelines, and signed request and webhook contracts between separate systems.', 'maulik-portfolio' ),
		'path'       => '/projects/identity-document-ocr/',
		'link_label' => __( 'Identity Document OCR Integration', 'maulik-portfolio' ),
	),
	array(
		'title'      => __( 'Payment Engineering', 'maulik-portfolio' ),
		'summary'    => __( 'Checkout flows across origin and processing domains, gateway abstraction, webhook reconciliation and auditable transaction state.', 'maulik-portfolio' ),
		'path'       => '/projects/cross-domain-payment-architecture/',
		'link_label' => __( 'Cross Domain Payment Architecture', 'maulik-portfolio' ),
	),
	array(
		'title'      => __( 'Security', 'maulik-portfolio' ),
		'summary'    => __( 'Request signing, capability gated authorization, nonce protection on state changing actions, and encryption for data at rest.', 'maulik-portfolio' ),
		'path'       => '/projects/wordpress-data-encryption/',
		'link_label' => __( 'Secure WordPress Data Encryption', 'maulik-portfolio' ),
	),
);

?>
<!-- wp:group {"tagName":"section","className":"section section-capabilities has-base-background-color has-background","anchor":"what-i-build","ariaLabelledby":"heading-what-i-build","backgroundColor":"base","layout":{"type":"constrained"}} -->
<section class="wp-block-group section section-capabilities has-base-background-color has-background" id="what-i-build" aria-labelledby="heading-what-i-build">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '02 · Core Engineering Capabilities', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-what-i-build"} -->
	<h2 class="wp-block-heading" id="heading-what-i-build"><?php echo esc_html__( 'I build systems, not just websites.', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"className":"section-lede"} -->
	<p class="section-lede"><?php echo esc_html__( 'Five areas I work in. Each one links to the case study that shows the work.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<?php foreach ( $maulik_portfolio_capability_areas as $maulik_portfolio_capability_area ) : ?>
		<!-- wp:group {"tagName":"article","className":"capability-card","layout":{"type":"constrained"}} -->
		<article class="wp-block-group capability-card">
			<!-- wp:heading {"level":3,"className":"capability-card__title"} -->
			<h3 class="wp-block-heading capability-card__title"><?php echo esc_html( $maulik_portfolio_capability_area['title'] ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"capability-card__summary"} -->
			<p class="capability-card__summary"><?php echo esc_html( $maulik_portfolio_capability_area['summary'] ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"className":"capability-card__link","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
			<p class="capability-card__link"><a href="<?php echo esc_url( get_home_url( null, $maulik_portfolio_capability_area['path'] ) ); ?>"><?php echo esc_html( $maulik_portfolio_capability_area['link_label'] ); ?></a></p>
			<!-- /wp:paragraph -->
		</article>
		<!-- /wp:group -->
	<?php endforeach; ?>
</section>
<!-- /wp:group -->
