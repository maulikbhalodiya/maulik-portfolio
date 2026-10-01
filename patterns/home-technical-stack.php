<?php
/**
 * Title: Home Technical Stack Section
 * Slug: maulik-portfolio/home-technical-stack
 * Categories: maulik-portfolio-home
 * Description: Homepage section 03, the seven verified stack systems as label and paragraph pairs, on the 32px dark subgrid surface.
 * Inserter: yes
 *
 * Section 03 of 08. The dark subgrid surface, a 32px dotted field rather than the
 * 48px grid of sections 01 and 02, so the page visibly changes texture before the
 * content changes shape.
 *
 * SEVEN SYSTEMS, TAKEN FROM THE SYS.01 TO SYS.07 VOCABULARY. The design also
 * enumerates forty one individual skills, but it attaches no level, no weight and
 * no percentage to any of them, and inventing a relevance number for each would
 * put a figure on the page that nothing in the source data supports.
 *
 * THE SHAPE CHANGED. The previous version was seven "stack-category" cards, each
 * with a heading and a bullet list of concrete mechanisms such as
 * "hash_hmac signing with hash_equals comparison". Those sentences are accurate
 * and they are good engineering prose, but the approved homepage does not render
 * them and this pattern is supposed to be the same section the approved homepage
 * renders. One paragraph per system is what the template carries.
 *
 * THE INNER GROUP IS A RHYTHM CLASS, NOT A SURFACE. is-style-section-tight comes
 * from styles/section-tight.json and only sets padding and block gap, so it can
 * be combined with the surface class on the outer group without either one
 * fighting the other.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The seven verified stack systems.
 */
$maulik_portfolio_stack_systems = array(
	array(
		'label' => __( 'SYS.01 · WordPress', 'maulik-portfolio' ),
		'text'  => __( 'Custom plugins, block and shortcode free structure, custom roles and capabilities, form submission workflows, admin screens and query level work against WordPress data.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'SYS.02 · PHP', 'maulik-portfolio' ),
		'text'  => __( 'Backend service layers, typed error handling, the Sodium cryptography extension, HMAC verification with timing safe comparison, and code that a second maintainer can read.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'SYS.03 · APIs', 'maulik-portfolio' ),
		'text'  => __( 'REST routes registered in code, signed payloads, asynchronous job polling, webhook receivers and retry safe delivery for third party services.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'SYS.04 · Databases', 'maulik-portfolio' ),
		'text'  => __( 'Schema design for custom tables, form entry metadata, transactional references, audit trails and queries that stay scoped to the current user\'s role.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'SYS.05 · Security', 'maulik-portfolio' ),
		'text'  => __( 'Payload signing, nonce verification, context specific escaping before render, capability checks on sensitive views and decryption, and key material held outside the database.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'SYS.06 · Integrations', 'maulik-portfolio' ),
		'text'  => __( 'Payment gateways, cloud OCR services, object storage, booking and form systems, marketing automation and marketing APIs, each behind one adapter the rest of the code can call.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'SYS.07 · Tools', 'maulik-portfolio' ),
		'text'  => __( 'Git for version control, staging environments for verification before anything ships, and browser and server side inspection of the requests that carry the behaviour.', 'maulik-portfolio' ),
	),
);

?>
<!-- wp:group {"tagName":"section","className":"is-style-section is-style-surface-dark-subgrid","anchor":"technical-stack","ariaLabelledby":"heading-technical-stack","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section is-style-surface-dark-subgrid" id="technical-stack" aria-labelledby="heading-technical-stack">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '03 · Technical Stack Ecosystem', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-technical-stack"} -->
	<h2 class="wp-block-heading" id="heading-technical-stack"><?php echo esc_html__( 'Verified Technical Stack', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php echo esc_html__( 'Seven systems, grouped by what they do rather than by how popular they are. Every entry describes a concrete use, so nothing here has to be defended with a proficiency number.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:group {"tagName":"div","className":"is-style-section-tight","layout":{"type":"constrained"}} -->
	<div class="wp-block-group is-style-section-tight">
		<?php foreach ( $maulik_portfolio_stack_systems as $maulik_portfolio_stack_system ) : ?>
			<!-- wp:paragraph {"typography":{"fontFamily":"var:preset|font-family|mono"}} -->
			<p><?php echo esc_html( $maulik_portfolio_stack_system['label'] ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html( $maulik_portfolio_stack_system['text'] ); ?></p>
			<!-- /wp:paragraph -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->