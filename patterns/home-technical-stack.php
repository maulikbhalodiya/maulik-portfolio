<?php
/**
 * Title: Home Technical Stack Section
 * Slug: maulik-portfolio/home-technical-stack
 * Categories: maulik-portfolio-home
 * Description: Homepage section 03, the seven category technical stack, on the dark-grid surface.
 * Inserter: yes
 *
 * Section 03 of 08. Dark-grid again, which is the point of the rhythm.
 *
 * SEVEN CATEGORIES, taken from the design's SYS.01 to SYS.07 vocabulary. The
 * design also enumerates forty one individual skills, but it attaches no level, no
 * weight and no percentage to any of them, and inventing a relevance number for
 * each would put a figure on the page that nothing in the source data supports.
 *
 * WHAT IS HERE INSTEAD: the verified mechanism behind the categories. Every line
 * below is a concrete WordPress or PHP mechanism named in the verified case study
 * content. That is the pattern the design itself uses, where a skill explains how
 * it is used rather than asserting a level, and it is preserved deliberately.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The seven stack categories with the mechanisms verified against the case studies.
 */
$maulik_portfolio_stack_categories = array(
	array(
		'id'         => 'SYS.01',
		'title'      => __( 'WordPress', 'maulik-portfolio' ),
		'mechanisms' => array(
			__( 'Custom plugin architecture', 'maulik-portfolio' ),
			__( 'Hook driven extension', 'maulik-portfolio' ),
			__( 'Custom roles and capability checks', 'maulik-portfolio' ),
			__( 'Gutenberg block development', 'maulik-portfolio' ),
			__( 'Form and entry meta persistence', 'maulik-portfolio' ),
		),
	),
	array(
		'id'         => 'SYS.02',
		'title'      => __( 'PHP', 'maulik-portfolio' ),
		'mechanisms' => array(
			__( 'Custom REST endpoints with register_rest_route', 'maulik-portfolio' ),
			__( 'hash_hmac signing with hash_equals comparison', 'maulik-portfolio' ),
			__( 'Sodium encryption keyed by an external secret', 'maulik-portfolio' ),
			__( 'Capability gated AJAX handlers', 'maulik-portfolio' ),
		),
	),
	array(
		'id'         => 'SYS.03',
		'title'      => __( 'APIs', 'maulik-portfolio' ),
		'mechanisms' => array(
			__( 'Server to server REST communication', 'maulik-portfolio' ),
			__( 'Asynchronous webhook delivery and reconciliation', 'maulik-portfolio' ),
			__( 'Retry idempotency and duplicate callback protection', 'maulik-portfolio' ),
			__( 'Third party service automation', 'maulik-portfolio' ),
		),
	),
	array(
		'id'         => 'SYS.04',
		'title'      => __( 'Databases', 'maulik-portfolio' ),
		'mechanisms' => array(
			__( 'Server enforced scoping on every query', 'maulik-portfolio' ),
			__( 'Auditable transaction and state records', 'maulik-portfolio' ),
			__( 'MySQL for reporting and reconciliation reads', 'maulik-portfolio' ),
		),
	),
	array(
		'id'         => 'SYS.05',
		'title'      => __( 'Security', 'maulik-portfolio' ),
		'mechanisms' => array(
			__( 'HMAC SHA 256 payload signing', 'maulik-portfolio' ),
			__( 'Nonce verification on submissions and AJAX actions', 'maulik-portfolio' ),
			__( 'Context specific output escaping', 'maulik-portfolio' ),
			__( 'Role based authorization on sensitive views', 'maulik-portfolio' ),
		),
	),
	array(
		'id'         => 'SYS.06',
		'title'      => __( 'Integrations', 'maulik-portfolio' ),
		'mechanisms' => array(
			__( 'Payment gateway abstraction behind one REST contract', 'maulik-portfolio' ),
			__( 'Iframe checkout session coordination', 'maulik-portfolio' ),
			__( 'Document OCR processing pipelines', 'maulik-portfolio' ),
			__( 'Object storage automation', 'maulik-portfolio' ),
		),
	),
	array(
		'id'         => 'SYS.07',
		'title'      => __( 'Tools', 'maulik-portfolio' ),
		'mechanisms' => array(
			__( 'Git for versioned, reviewable change', 'maulik-portfolio' ),
			__( 'Composer for dependency management', 'maulik-portfolio' ),
			__( 'WordPress Coding Standards and static analysis on every change', 'maulik-portfolio' ),
		),
	),
);

?>
<!-- wp:group {"tagName":"section","className":"section section-stack dark-grid","anchor":"technical-skills","ariaLabelledby":"heading-technical-skills","layout":{"type":"constrained"}} -->
<section class="wp-block-group section section-stack dark-grid" id="technical-skills" aria-labelledby="heading-technical-skills">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '03 · Interactive Technical Ecosystem', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-technical-skills"} -->
	<h2 class="wp-block-heading" id="heading-technical-skills"><?php echo esc_html__( 'Verified Technical Stack', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"className":"section-lede"} -->
	<p class="section-lede"><?php echo esc_html__( 'Seven categories. Each entry names a mechanism and how it is used, not a level. A number here would be a claim, and a claim has to be defended.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<?php foreach ( $maulik_portfolio_stack_categories as $maulik_portfolio_stack_category ) : ?>
		<!-- wp:group {"tagName":"article","className":"stack-category","layout":{"type":"constrained"}} -->
		<article class="wp-block-group stack-category">
			<!-- wp:heading {"level":3,"className":"stack-category__title"} -->
			<h3 class="wp-block-heading stack-category__title"><span class="stack-category__id" aria-hidden="true"><?php echo esc_html( $maulik_portfolio_stack_category['id'] ); ?></span> <?php echo esc_html( $maulik_portfolio_stack_category['title'] ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:list {"className":"stack-category__mechanisms"} -->
			<ul class="wp-block-list stack-category__mechanisms">
				<?php foreach ( $maulik_portfolio_stack_category['mechanisms'] as $maulik_portfolio_stack_mechanism ) : ?>
					<!-- wp:list-item -->
					<li><?php echo esc_html( $maulik_portfolio_stack_mechanism ); ?></li>
					<!-- /wp:list-item -->
				<?php endforeach; ?>
			</ul>
			<!-- /wp:list -->
		</article>
		<!-- /wp:group -->
	<?php endforeach; ?>
</section>
<!-- /wp:group -->
