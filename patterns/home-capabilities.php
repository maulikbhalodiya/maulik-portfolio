<?php
/**
 * Title: Home Capabilities Section
 * Slug: maulik-portfolio/home-capabilities
 * Categories: maulik-portfolio-home
 * Description: Homepage section 02, the five capability selector cards beside the selected capability panel, on the flat base surface.
 * Inserter: yes
 *
 * Section 02 of 08. Flat #0A0A0B with a 1px #262626 rule under it, which is what
 * the approved design states. It previously wore the 48px dark grid variation,
 * which put a texture on a section the design leaves flat and made the run of
 * textured bands on the page three sections long instead of two.
 *
 * THE SHAPE CHANGED, AND THE SHAPE WAS WRONG. The previous version was an
 * eyebrow, an h2, a lede and a five item bulleted list. The design has no list
 * here at all: it has five interactive selector cards on the left at p-6, split
 * by 1px #262626 hairlines, and a detail panel on the right on #111113 with a
 * 1px #262626 border at p-8 md:p-10. Both panels are built.
 *
 * THE SELECTED CARD IS RENDERED SERVER SIDE. The design swaps the right hand
 * panel in the browser as the reader clicks a card. A static page cannot do
 * that, so the first capability is marked selected and its panel is rendered,
 * and the other four are marked INSPECT. Nothing is a dead button: the state
 * labels are text, not controls.
 *
 * NO LEVELS AND NO PERCENTAGES. Each entry says what the area has to survive
 * rather than how proficient someone claims to be. A proficiency number would be
 * a claim, and a claim has to be defended forever.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The five capability areas, as the design's data set states them.
 *
 * Held as data and iterated, because the point of the section is that there are
 * exactly five of them and the markup should make changing the count a one line
 * edit rather than a find and replace across two block delimiters.
 */
$maulik_portfolio_capabilities = array(
	array(
		'number'       => '01',
		'title'        => __( 'WordPress Engineering', 'maulik-portfolio' ),
		'summary'      => __( 'Custom plugins, hooks, REST APIs, Gutenberg, WooCommerce and WordPress backend functionality.', 'maulik-portfolio' ),
		'focus'        => __( 'Designing extensible WordPress systems that separate business logic from presentation templates while respecting core lifecycle hooks and permissions.', 'maulik-portfolio' ),
		'mechanisms'   => array(
			__( 'Custom plugin architecture with isolated service classes', 'maulik-portfolio' ),
			__( 'Action and filter hook orchestration across core and third party plugins', 'maulik-portfolio' ),
			__( 'Custom Gutenberg blocks and structured editorial interfaces', 'maulik-portfolio' ),
			__( 'WooCommerce and Gravity Forms lifecycle customization', 'maulik-portfolio' ),
		),
		'technologies' => __( 'WordPress · Custom Plugins · Hooks · REST API · Gutenberg · WooCommerce · WP CLI', 'maulik-portfolio' ),
	),
	array(
		'number'       => '02',
		'title'        => __( 'PHP Backend', 'maulik-portfolio' ),
		'summary'      => __( 'Object oriented PHP, Composer, database operations, reusable architecture and server side processing.', 'maulik-portfolio' ),
		'focus'        => __( 'Building structured PHP backend services with PSR 4 autoloading, custom MySQL table abstractions and predictable data transformation pipelines.', 'maulik-portfolio' ),
		'mechanisms'   => array(
			__( 'Object oriented PHP class hierarchies and dependency organization', 'maulik-portfolio' ),
			__( 'Composer autoloading and PSR 4 namespace modularity', 'maulik-portfolio' ),
			__( 'Custom database table operations and prepared SQL statements', 'maulik-portfolio' ),
			__( 'Asynchronous background tasks and AJAX processing handlers', 'maulik-portfolio' ),
		),
		'technologies' => __( 'PHP · OOP · Composer · PSR 4 · MySQL · Custom Tables · AJAX', 'maulik-portfolio' ),
	),
	array(
		'number'       => '03',
		'title'        => __( 'API and Integration Engineering', 'maulik-portfolio' ),
		'summary'      => __( 'REST APIs, authentication, webhooks, third party services and event driven workflows.', 'maulik-portfolio' ),
		'focus'        => __( 'Connecting WordPress environments to external cloud services, OCR engines and marketing automation platforms with resilient error handling.', 'maulik-portfolio' ),
		'mechanisms'   => array(
			__( 'Custom WordPress REST API endpoint registration and permission callbacks', 'maulik-portfolio' ),
			__( 'Asynchronous polling loops for external document analysis services', 'maulik-portfolio' ),
			__( 'Authenticated cURL and HTTP client communication with JSON payloads', 'maulik-portfolio' ),
			__( 'Event driven webhook listeners for external state synchronization', 'maulik-portfolio' ),
		),
		'technologies' => __( 'REST API · Webhooks · API Authentication · JSON · cURL · Azure Form Recognizer · Brevo API', 'maulik-portfolio' ),
	),
	array(
		'number'       => '04',
		'title'        => __( 'Payment Engineering', 'maulik-portfolio' ),
		'summary'      => __( 'Payment gateways, payment workflows, webhooks, secure communication and transaction processing.', 'maulik-portfolio' ),
		'focus'        => __( 'Architecting cross domain payment bridges and multi gateway workflows that isolate sensitive checkout operations while keeping transaction states synchronized.', 'maulik-portfolio' ),
		'mechanisms'   => array(
			__( 'Cross domain payment communication between origin sites and approved payment environments', 'maulik-portfolio' ),
			__( 'Iframe based payment interface integration with signed state payloads', 'maulik-portfolio' ),
			__( 'Webhook verification for asynchronous transaction settlement updates', 'maulik-portfolio' ),
			__( 'Multi gateway routing and Gravity Forms payment workflow integration', 'maulik-portfolio' ),
		),
		'technologies' => __( 'Payment Gateways · Webhooks · REST APIs · Gravity Forms · HMAC SHA 256', 'maulik-portfolio' ),
	),
	array(
		'number'       => '05',
		'title'        => __( 'Security', 'maulik-portfolio' ),
		'summary'      => __( 'Request verification, HMAC SHA 256, encryption, secure API communication and protected data workflows.', 'maulik-portfolio' ),
		'focus'        => __( 'Protecting data in transit and at rest using cryptographic verification, PHP Sodium encryption, strict capability checks and defensive input handling.', 'maulik-portfolio' ),
		'mechanisms'   => array(
			__( 'HMAC SHA 256 payload signing and shared secret authentication across domains', 'maulik-portfolio' ),
			__( 'Field level encryption and decryption using PHP Sodium and external keys', 'maulik-portfolio' ),
			__( 'WordPress nonce verification and granular user capability checks', 'maulik-portfolio' ),
			__( 'Strict input validation, sanitization and context aware output escaping', 'maulik-portfolio' ),
		),
		'technologies' => __( 'HMAC SHA 256 · PHP Sodium · Encryption · Nonce Verification · Input Validation · Output Escaping · Capability Checks', 'maulik-portfolio' ),
	),
);

/**
 * The capability whose detail panel is rendered, which is the first one.
 */
$maulik_portfolio_active_capability = $maulik_portfolio_capabilities[0];

?>
<!-- wp:group {"anchor":"capabilities","ariaLabelledby":"heading-capabilities","className":"is-style-section hz-section hz-capabilities","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section hz-section hz-capabilities" id="capabilities" aria-labelledby="heading-capabilities">
	<!-- wp:group {"className":"hz-section__head","layout":{"type":"default"}} -->
	<div class="wp-block-group hz-section__head">
		<!-- wp:group {"className":"hz-section__head-main","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-section__head-main">
			<!-- wp:paragraph {"className":"hz-eyebrow"} -->
			<p class="hz-eyebrow"><?php echo esc_html__( '02 · Core Engineering Capabilities', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2,"anchor":"heading-capabilities","className":"hz-section__title"} -->
			<h2 class="wp-block-heading hz-section__title" id="heading-capabilities"><?php echo esc_html__( 'I build systems, not just websites.', 'maulik-portfolio' ); ?></h2>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->
		<!-- wp:paragraph {"className":"hz-section__head-lede"} -->
		<p class="hz-section__head-lede"><?php echo esc_html__( 'I specialize in custom WordPress functionality, object oriented PHP backend engineering, third party API integrations, payment architectures and security focused technical problem solving.', 'maulik-portfolio' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<!-- wp:group {"className":"hz-stack-8","layout":{"type":"default"}} -->
	<div class="wp-block-group hz-stack-8">
		<!-- wp:group {"className":"hz-body","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-body">
			<!-- wp:group {"className":"hz-body__lead","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-body__lead">
				<!-- wp:group {"className":"hz-capabilities__list","layout":{"type":"default"}} -->
				<div class="wp-block-group hz-capabilities__list">
					<?php foreach ( $maulik_portfolio_capabilities as $maulik_portfolio_capability_index => $maulik_portfolio_capability ) : ?>
						<?php
						$maulik_portfolio_capability_is_selected = 0 === $maulik_portfolio_capability_index;
						$maulik_portfolio_capability_class       = $maulik_portfolio_capability_is_selected ? 'hz-capability hz-capability-selected' : 'hz-capability';
						$maulik_portfolio_capability_label       = $maulik_portfolio_capability_is_selected ? 'hz-label' : 'hz-label-faint';
						$maulik_portfolio_capability_state       = $maulik_portfolio_capability_is_selected ? 'ACTIVE' : 'INSPECT';
						?>
						<!-- wp:group {"className":<?php echo wp_json_encode( $maulik_portfolio_capability_class ); ?>,"layout":{"type":"default"}} -->
						<article class="wp-block-group <?php echo esc_html( $maulik_portfolio_capability_class ); ?>">
							<!-- wp:group {"className":"hz-stack-2","layout":{"type":"default"}} -->
							<div class="wp-block-group hz-stack-2">
								<!-- wp:paragraph {"className":<?php echo wp_json_encode( $maulik_portfolio_capability_label ); ?>} -->
								<p class="<?php echo esc_html( $maulik_portfolio_capability_label ); ?>"><?php echo esc_html( $maulik_portfolio_capability['number'] ); ?> · <?php echo esc_html__( 'Capability Area', 'maulik-portfolio' ); ?></p>
								<!-- /wp:paragraph -->
								<!-- wp:heading {"level":3,"anchor":<?php echo wp_json_encode( 'capability-' . $maulik_portfolio_capability['number'] ); ?>,"className":"hz-capability__title"} -->
								<h3 class="wp-block-heading hz-capability__title" id="capability-<?php echo esc_html( $maulik_portfolio_capability['number'] ); ?>"><?php echo esc_html( $maulik_portfolio_capability['title'] ); ?></h3>
								<!-- /wp:heading -->
								<!-- wp:paragraph {"className":"hz-capability__summary"} -->
								<p class="hz-capability__summary"><?php echo esc_html( $maulik_portfolio_capability['summary'] ); ?></p>
								<!-- /wp:paragraph -->
							</div>
							<!-- /wp:group -->
							<!-- wp:paragraph {"className":<?php echo wp_json_encode( $maulik_portfolio_capability_label . ' hz-capability__state' ); ?>} -->
							<p class="<?php echo esc_html( $maulik_portfolio_capability_label ); ?> hz-capability__state"><?php echo esc_html( $maulik_portfolio_capability_state ); ?></p>
							<!-- /wp:paragraph -->
						</article>
						<!-- /wp:group -->
					<?php endforeach; ?>
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
			<!-- wp:group {"className":"hz-body__main","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-body__main">
				<!-- wp:group {"className":"hz-panel hz-panel-rail","layout":{"type":"default"}} -->
				<div class="wp-block-group hz-panel hz-panel-rail">
					<!-- wp:group {"className":"hz-stack-6","layout":{"type":"default"}} -->
					<div class="wp-block-group hz-stack-6">
						<!-- wp:group {"className":"hz-row hz-capabilities__detail-head","layout":{"type":"default"}} -->
						<div class="wp-block-group hz-row hz-capabilities__detail-head">
							<!-- wp:paragraph {"className":"hz-label"} -->
							<p class="hz-label"><?php echo esc_html__( 'Capability Specification', 'maulik-portfolio' ); ?> · <?php echo esc_html( $maulik_portfolio_active_capability['number'] ); ?></p>
							<!-- /wp:paragraph -->
							<!-- wp:heading {"level":3,"className":"hz-capabilities__detail-title"} -->
							<h3 class="wp-block-heading hz-capabilities__detail-title"><?php echo esc_html( $maulik_portfolio_active_capability['title'] ); ?></h3>
							<!-- /wp:heading -->
						</div>
						<!-- /wp:group -->
						<!-- wp:group {"className":"hz-stack-2","layout":{"type":"default"}} -->
						<div class="wp-block-group hz-stack-2">
							<!-- wp:paragraph {"className":"hz-label-faint"} -->
							<p class="hz-label-faint"><?php echo esc_html__( 'Engineering Mandate', 'maulik-portfolio' ); ?></p>
							<!-- /wp:paragraph -->
							<!-- wp:paragraph {"className":"hz-body-copy"} -->
							<p class="hz-body-copy"><?php echo esc_html( $maulik_portfolio_active_capability['focus'] ); ?></p>
							<!-- /wp:paragraph -->
						</div>
						<!-- /wp:group -->
						<!-- wp:group {"className":"hz-stack-2","layout":{"type":"default"}} -->
						<div class="wp-block-group hz-stack-2">
							<!-- wp:paragraph {"className":"hz-label"} -->
							<p class="hz-label"><?php echo esc_html__( 'Key Technical Mechanisms', 'maulik-portfolio' ); ?></p>
							<!-- /wp:paragraph -->
							<!-- wp:group {"className":"hz-grid-2","layout":{"type":"default"}} -->
							<div class="wp-block-group hz-grid-2">
								<?php foreach ( $maulik_portfolio_active_capability['mechanisms'] as $maulik_portfolio_mechanism_index => $maulik_portfolio_mechanism ) : ?>
									<!-- wp:group {"className":"hz-tile","layout":{"type":"default"}} -->
									<div class="wp-block-group hz-tile">
										<!-- wp:paragraph {"className":"hz-tile__index"} -->
										<p class="hz-tile__index"><?php echo esc_html( sprintf( '%02d', $maulik_portfolio_mechanism_index + 1 ) ); ?></p>
										<!-- /wp:paragraph -->
										<!-- wp:paragraph {"className":"hz-tile__text"} -->
										<p class="hz-tile__text"><?php echo esc_html( $maulik_portfolio_mechanism ); ?></p>
										<!-- /wp:paragraph -->
									</div>
									<!-- /wp:group -->
								<?php endforeach; ?>
							</div>
							<!-- /wp:group -->
						</div>
						<!-- /wp:group -->
					</div>
					<!-- /wp:group -->
					<!-- wp:paragraph {"className":"hz-meta hz-rule-top"} -->
					<p class="hz-meta hz-rule-top"><?php echo esc_html( $maulik_portfolio_active_capability['technologies'] ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->