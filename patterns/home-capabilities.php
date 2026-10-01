<?php
/**
 * Title: Home Capabilities Section
 * Slug: maulik-portfolio/home-capabilities
 * Categories: maulik-portfolio-home
 * Description: Homepage section 02, the five core engineering capabilities as a bolded list, on the 48px dark grid surface.
 * Inserter: yes
 *
 * Section 02 of 08. The dark grid surface again, so the rhythm alternates
 * against the hero rather than repeating one texture eight times.
 *
 * FIVE AREAS, AND FIVE IS THE VERIFIED COUNT. The list is not padded to reach a
 * number and the number is not rounded up from the list. The list is the fact.
 *
 * NO LEVELS AND NO PERCENTAGES. Each entry says what the area has to survive
 * rather than how proficient someone claims to be. A proficiency number would be
 * a claim, and a claim has to be defended forever.
 *
 * THE SHAPE CHANGED. The previous version of this pattern was five
 * "capability-card" articles, each with its own heading, summary and a link to a
 * case study route. Three of those five routes were real project slugs and one
 * was a RankKernel link, which meant the pattern asserted relationships between
 * capability areas and case studies that the approved homepage never asserts.
 * The template renders the same five areas as five bolded list items and nothing
 * else, so that is what this is.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The five capability areas, as label and description pairs.
 *
 * Held as data and iterated, because the point of the section is that there are
 * exactly five of them and the markup should make changing the count a one line
 * edit rather than a find and replace across two block delimiters.
 */
$maulik_portfolio_capability_areas = array(
	array(
		'label' => __( 'WordPress Engineering.', 'maulik-portfolio' ),
		'text'  => __( 'Custom plugin architecture, role and capability design, form driven workflows and admin interfaces built on hooks rather than template overrides.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'PHP Backend.', 'maulik-portfolio' ),
		'text'  => __( 'Object oriented backend services, REST endpoints, scheduled work, data modelling in WordPress tables and the encryption and hashing primitives around them.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'API and Integration Engineering.', 'maulik-portfolio' ),
		'text'  => __( 'Signed REST communication, asynchronous polling for slow third party services, credential isolation and webhook reconciliation.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'Payment Engineering.', 'maulik-portfolio' ),
		'text'  => __( 'Checkout flows, gateway abstraction, iframe hosted payment interfaces and server authoritative transaction state.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'Security.', 'maulik-portfolio' ),
		'text'  => __( 'HMAC SHA 256 request signing, nonce verification, context specific output escaping and capability gated access to sensitive records.', 'maulik-portfolio' ),
	),
);

?>
<!-- wp:group {"tagName":"section","className":"is-style-section is-style-surface-dark-grid","anchor":"capabilities","ariaLabelledby":"heading-capabilities","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section is-style-surface-dark-grid" id="capabilities" aria-labelledby="heading-capabilities">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '02 · Core Engineering Capabilities', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-capabilities"} -->
	<h2 class="wp-block-heading" id="heading-capabilities"><?php echo esc_html__( 'I build systems, not just websites.', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php echo esc_html__( 'Five areas account for most of the work. Each one is described by what it has to survive, not by a level or a score.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:list -->
	<ul class="wp-block-list">
		<?php foreach ( $maulik_portfolio_capability_areas as $maulik_portfolio_capability_area ) : ?>
			<!-- wp:list-item -->
			<li><strong><?php echo esc_html( $maulik_portfolio_capability_area['label'] ); ?></strong> <?php echo esc_html( $maulik_portfolio_capability_area['text'] ); ?></li>
			<!-- /wp:list-item -->
		<?php endforeach; ?>
	</ul>
	<!-- /wp:list -->
</section>
<!-- /wp:group -->