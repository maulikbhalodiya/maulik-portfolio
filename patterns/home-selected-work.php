<?php
/**
 * Title: Home Selected Work Section
 * Slug: maulik-portfolio/home-selected-work
 * Categories: maulik-portfolio-home
 * Description: Homepage section 04, the RankKernel block and the featured project cards, on a split surface.
 * Inserter: yes
 *
 * Section 04 of 08. Split internally, as the design specifies: the RankKernel block
 * sits on the solid base surface and the project cards sit on dark-grid. That
 * internal change of surface is the reason this section reads as two bands rather
 * than one list.
 *
 * FOUR CARDS, NOT NINE. Only four of the nine projects are fully written in the
 * source content. The fifth featured entry carries thirteen literal placeholder
 * strings and is reduced to a single honest line rather than shown as a card that
 * would render as a wall of brackets. Showing five cards where the fifth is empty
 * is worse than showing four and saying why.
 *
 * NOTHING HERE CLAIMS AN OUTCOME. No percentages, no revenue, no client names, no
 * domains, no speedups. The source data has no impact field at all, which is a
 * deliberate strength of the design rather than a gap to fill.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The four fully written featured projects.
 */
$maulik_portfolio_featured_projects = array(
	array(
		'title'        => __( 'Cross Domain Payment Architecture', 'maulik-portfolio' ),
		'summary'      => __( 'A reusable cross domain payment system. Multiple origin WordPress sites collect submissions and process transactions through one approved payment environment, over an iframe interface, with signed REST requests and asynchronous webhooks.', 'maulik-portfolio' ),
		'category'     => __( 'Payments and Security Architecture', 'maulik-portfolio' ),
		'technologies' => array( 'WordPress', 'PHP', 'REST APIs', 'Gravity Forms', 'HMAC SHA 256' ),
		'path'         => '/projects/cross-domain-payment-architecture/',
	),
	array(
		'title'        => __( 'Employee Application Management System', 'maulik-portfolio' ),
		'summary'      => __( 'An internal application workflow built on WordPress, with custom roles and capability checks governing access at the server rather than in the interface.', 'maulik-portfolio' ),
		'category'     => __( 'Workflow and Access Control', 'maulik-portfolio' ),
		'technologies' => array( 'WordPress', 'PHP', 'Gravity Forms', 'AJAX', 'Custom Roles' ),
		'path'         => '/projects/employee-application-management/',
	),
	array(
		'title'        => __( 'Identity Document OCR Integration', 'maulik-portfolio' ),
		'summary'      => __( 'An OCR pipeline that turns uploaded identity documents into validated, structured records inside WordPress, with the processing boundary and its validation rules made explicit.', 'maulik-portfolio' ),
		'category'     => __( 'API and Document Processing', 'maulik-portfolio' ),
		'technologies' => array( 'WordPress', 'PHP', 'REST APIs', 'OCR Integration' ),
		'path'         => '/projects/identity-document-ocr/',
	),
	array(
		'title'        => __( 'Secure WordPress Data Encryption', 'maulik-portfolio' ),
		'summary'      => __( 'Encryption for sensitive records held in WordPress, keyed by an external secret, with capability gated decryption so plaintext is only released to users who are authorised to read it.', 'maulik-portfolio' ),
		'category'     => __( 'Data Protection', 'maulik-portfolio' ),
		'technologies' => array( 'WordPress', 'PHP', 'Sodium', 'HMAC SHA 256' ),
		'path'         => '/projects/wordpress-data-encryption/',
	),
);

?>
<!-- wp:group {"tagName":"section","className":"section section-work","anchor":"selected-work","ariaLabelledby":"heading-selected-work","layout":{"type":"constrained"}} -->
<section class="wp-block-group section section-work" id="selected-work" aria-labelledby="heading-selected-work">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '04 · Selected Development Work', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-selected-work"} -->
	<h2 class="wp-block-heading" id="heading-selected-work"><?php echo esc_html__( 'Selected Development Work', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:group {"tagName":"div","className":"section-work__rankkernel has-base-background-color has-background","backgroundColor":"base","layout":{"type":"constrained"}} -->
	<div class="wp-block-group section-work__rankkernel has-base-background-color has-background">
		<!-- wp:heading {"level":3,"className":"section-work__rankkernel-title"} -->
		<h3 class="wp-block-heading section-work__rankkernel-title"><?php echo esc_html__( 'RankKernel', 'maulik-portfolio' ); ?></h3>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p><?php echo esc_html__( 'An independent open source SEO and schema engine for WordPress. It is also the engine running this site, which is why the portfolio is its own case study.', 'maulik-portfolio' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"className":"section-work__rankkernel-notice"} -->
		<p class="section-work__rankkernel-notice"><?php echo esc_html__( 'RankKernel is an independent personal open source software project built by Maulik Bhalodiya. It is not affiliated with Qrolic Technologies and is not client work.', 'maulik-portfolio' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"className":"section-work__rankkernel-status","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
		<p class="section-work__rankkernel-status"><?php echo esc_html__( 'Subsystems: 3 implemented · 3 in progress · 3 planned', 'maulik-portfolio' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"className":"section-work__link","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
		<p class="section-work__link"><a href="<?php echo esc_url( get_home_url( null, '/rankkernel/' ) ); ?>"><?php echo esc_html__( 'RankKernel architecture', 'maulik-portfolio' ); ?></a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"tagName":"div","className":"section-work__grid dark-grid","layout":{"type":"constrained"}} -->
	<div class="wp-block-group section-work__grid dark-grid">
		<?php foreach ( $maulik_portfolio_featured_projects as $maulik_portfolio_featured_project ) : ?>
			<!-- wp:group {"tagName":"article","className":"project-card","layout":{"type":"constrained"}} -->
			<article class="wp-block-group project-card">
				<!-- wp:heading {"level":3,"className":"project-card__title"} -->
				<h3 class="wp-block-heading project-card__title"><a href="<?php echo esc_url( get_home_url( null, $maulik_portfolio_featured_project['path'] ) ); ?>"><?php echo esc_html( $maulik_portfolio_featured_project['title'] ); ?></a></h3>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"project-card__category","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
				<p class="project-card__category"><?php echo esc_html( $maulik_portfolio_featured_project['category'] ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"project-card__summary"} -->
				<p class="project-card__summary"><?php echo esc_html( $maulik_portfolio_featured_project['summary'] ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"project-card__technologies","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
				<p class="project-card__technologies"><?php echo esc_html( implode( ' · ', $maulik_portfolio_featured_project['technologies'] ) ); ?></p>
				<!-- /wp:paragraph -->
			</article>
			<!-- /wp:group -->
		<?php endforeach; ?>

		<!-- wp:paragraph {"className":"section-work__more","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
		<p class="section-work__more"><a href="<?php echo esc_url( get_home_url( null, '/projects/' ) ); ?>"><?php echo esc_html__( 'All projects', 'maulik-portfolio' ); ?></a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
