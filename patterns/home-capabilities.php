<?php
/**
 * Title: Home Capabilities Section
 * Slug: maulik-portfolio/home-capabilities
 * Categories: maulik-portfolio-home
 * Description: Homepage section 02, the five interactive capability selector buttons beside the selected capability panel, on the flat base surface.
 * Inserter: yes
 *
 * Section 02 of 08. Flat #0A0A0B with a 1px #262626 rule under it, which is what
 * the approved design states. It previously wore the 48px dark grid variation,
 * which put a texture on a section the design leaves flat and made the run of
 * textured bands on the page three sections long instead of two.
 *
 * THE SELECTOR IS FIVE REAL BUTTONS AND THE DESIGN SAYS SO. Measured on the
 * reference at 1440: five button elements inside this section, zero anchors and
 * zero links among the selector cards, and the design component sets
 * onClick on each one. The static version of this section rendered the same five
 * entries as five article elements with a text state label and nothing
 * clickable, which is what this version replaces.
 *
 * THE WHOLE CARD IS THE CONTROL, WHICH IS WHY THE CARD IS RAW HTML. The design
 * puts the number, the title, the summary and the ACTIVE or INSPECT state label
 * inside one button, and it wraps the title in an h3 so the section keeps its
 * heading outline. A button's content model is phrasing content, so a WordPress
 * heading block cannot legally go inside one, and no core block combination
 * produces that nesting. core/html does, which is the same block this page
 * already uses for the section 03 node map, the section 03 tab strip and the
 * section 06 stage chevrons. The detail panel on the right is left as ordinary
 * core blocks, because that is the editorial content an editor needs to edit and
 * none of it has to be clickable.
 *
 * ALL FIVE PANELS ARE IN THE POST AND FOUR ARE COLLAPSED. The design swaps the
 * right hand panel in the browser, which a static page cannot do, so all five
 * panels are rendered and the four that are not selected carry
 * hz-switch-panel--idle. That class is display: none, which contributes no
 * height, so the section is exactly as tall as the selected panel alone and the
 * page height budget is unchanged. Rendering one panel and swapping its text
 * out of a data attribute was the alternative and it was rejected: it would put
 * every capability's copy into the markup twice and make none of it editable as
 * blocks.
 *
 * WITH JAVASCRIPT OFF THE SECTION STILL READS. The first panel is the one
 * without the idle class and the first card carries aria-pressed, so the saved
 * markup alone already describes the state the design starts in.
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
		'id'           => 'wordpress',
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
		'id'           => 'php',
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
		'id'           => 'apis',
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
		'id'           => 'payments',
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
		'id'           => 'security',
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
 * The panel element id for a capability.
 *
 * The capability buttons address their panels by id through data-hz-target, and
 * assets/js/section-switcher.js resolves that with getElementById, so the two
 * have to be generated from one function rather than written out twice.
 *
 * @param string $capability_id The capability id.
 * @return string The panel element id.
 */
function maulik_portfolio_capability_panel_id( $capability_id ) {
	return 'hz-capability-panel-' . $capability_id;
}

/**
 * The five capability selector buttons, as one HTML string.
 *
 * Emitted by a helper rather than inline in the template so the button markup
 * exists once and the section body stays readable.
 *
 * The strip carries data-hz-switcher, which is what assets/js/section-switcher.js
 * looks for, plus the class the buttons carry and the class the selected button
 * carries. Nothing else on the page uses those names.
 *
 * @param array $capabilities The five capability areas.
 * @return string The selector markup, without a surrounding element.
 */
function maulik_portfolio_capability_buttons( $capabilities ) {
	$markup = '';

	foreach ( $capabilities as $index => $capability ) {
		$is_selected = 0 === $index;

		$markup .= sprintf(
			'<button type="button" class="hz-capability" data-hz-target="%1$s" aria-pressed="%2$s"><span class="hz-capability__body"><span class="hz-capability__number">%3$s · %4$s</span><h3 class="hz-capability__title" id="capability-%5$s">%6$s</h3><span class="hz-capability__summary">%7$s</span></span><span class="hz-capability__state" data-hz-state>%8$s</span></button>',
			esc_attr( maulik_portfolio_capability_panel_id( $capability['id'] ) ),
			$is_selected ? 'true' : 'false',
			esc_html( $capability['number'] ),
			esc_html__( 'Capability Area', 'maulik-portfolio' ),
			esc_html( $capability['number'] ),
			esc_html( $capability['title'] ),
			esc_html( $capability['summary'] ),
			$is_selected
				? esc_html__( 'ACTIVE', 'maulik-portfolio' )
				: esc_html__( 'INSPECT', 'maulik-portfolio' )
		);
	}

	return $markup;
}

/**
 * The block class string for one capability detail panel.
 *
 * The idle class is what collapses the four panels that are not selected, so
 * this is the one place that decision is expressed.
 *
 * @param bool $is_selected Whether this capability is the selected one.
 * @return string The class list.
 */
function maulik_portfolio_capability_panel_classes( $is_selected ) {
	return $is_selected
		? 'hz-panel hz-panel-rail hz-switch-panel'
		: 'hz-panel hz-panel-rail hz-switch-panel hz-switch-panel--idle';
}

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
				<!-- wp:html -->
				<?php
				/*
				 * The trailing newline is load bearing. PHP swallows the single
				 * newline that immediately follows a closing ?> tag, so without
				 * it the emitted markup and the block delimiter that closes it
				 * land on one line, the delimiter stops being first on its line,
				 * and the block parser stops seeing the block at all.
				 */
				echo '<div class="hz-capabilities__list" data-hz-switcher data-hz-tab="hz-capability" data-hz-state-selected="ACTIVE" data-hz-state-idle="INSPECT">' . maulik_portfolio_capability_buttons( $maulik_portfolio_capabilities ) . "</div>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
				<!-- /wp:html -->
			</div>
			<!-- /wp:group -->
			<!-- wp:group {"className":"hz-body__main hz-switch-panels","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-body__main hz-switch-panels">
				<?php foreach ( $maulik_portfolio_capabilities as $maulik_portfolio_capability_index => $maulik_portfolio_capability ) : ?>
					<?php
					$maulik_portfolio_capability_selected = 0 === $maulik_portfolio_capability_index;
					$maulik_portfolio_panel_id            = maulik_portfolio_capability_panel_id( $maulik_portfolio_capability['id'] );
					$maulik_portfolio_panel_classes       = maulik_portfolio_capability_panel_classes( $maulik_portfolio_capability_selected );
					?>
					<!-- wp:group {"anchor":<?php echo wp_json_encode( $maulik_portfolio_panel_id ); ?>,"className":<?php echo wp_json_encode( $maulik_portfolio_panel_classes ); ?>,"layout":{"type":"default"}} -->
					<div class="wp-block-group <?php echo esc_html( $maulik_portfolio_panel_classes ); ?>" id="<?php echo esc_attr( $maulik_portfolio_panel_id ); ?>">
						<!-- wp:group {"className":"hz-stack-6","layout":{"type":"default"}} -->
						<div class="wp-block-group hz-stack-6">
							<!-- wp:group {"className":"hz-row hz-capabilities__detail-head","layout":{"type":"default"}} -->
							<div class="wp-block-group hz-row hz-capabilities__detail-head">
								<!-- wp:paragraph {"className":"hz-label"} -->
								<p class="hz-label"><?php echo esc_html__( 'Capability Specification', 'maulik-portfolio' ); ?> · <?php echo esc_html( $maulik_portfolio_capability['number'] ); ?></p>
								<!-- /wp:paragraph -->
								<!-- wp:heading {"level":3,"className":"hz-capabilities__detail-title"} -->
								<h3 class="wp-block-heading hz-capabilities__detail-title"><?php echo esc_html( $maulik_portfolio_capability['title'] ); ?></h3>
								<!-- /wp:heading -->
							</div>
							<!-- /wp:group -->
							<!-- wp:group {"className":"hz-stack-2","layout":{"type":"default"}} -->
							<div class="wp-block-group hz-stack-2">
								<!-- wp:paragraph {"className":"hz-label-faint"} -->
								<p class="hz-label-faint"><?php echo esc_html__( 'Engineering Mandate', 'maulik-portfolio' ); ?></p>
								<!-- /wp:paragraph -->
								<!-- wp:paragraph {"className":"hz-body-copy"} -->
								<p class="hz-body-copy"><?php echo esc_html( $maulik_portfolio_capability['focus'] ); ?></p>
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
									<?php foreach ( $maulik_portfolio_capability['mechanisms'] as $maulik_portfolio_mechanism_index => $maulik_portfolio_mechanism ) : ?>
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
						<p class="hz-meta hz-rule-top"><?php echo esc_html( $maulik_portfolio_capability['technologies'] ); ?></p>
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
</section>
<!-- /wp:group -->