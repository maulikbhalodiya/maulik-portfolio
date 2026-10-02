<?php
/**
 * Title: Home Approach Section
 * Slug: maulik-portfolio/home-approach
 * Categories: maulik-portfolio-home
 * Description: Homepage section 06, the five stage selector rows beside the selected stage deep dive, on the 48px dark grid surface.
 * Inserter: yes
 *
 * Section 06 of 08. The 48px dark grid surface, which is what the design states.
 * It previously wore the 64px warm editorial variation, a light surface with a
 * texture the design does not use here.
 *
 * THE PREVIOUS VERSION WAS A FLAT LIST OF FIVE SENTENCES. An eyebrow, an h2, a
 * paragraph and a five item list with bold labels in sentence case. The design
 * has none of that structure: it has five stage selector rows on the left at p-5
 * and a deep dive panel on the right carrying a heading, a description, a
 * question list, an output line and a Next Stage action.
 *
 * THE STAGE ROWS ARE ANCHORS TO THIS SECTION, NOT DEAD BUTTONS. The design
 * swaps the right hand panel in the browser. A static page cannot, so the first
 * stage is marked SELECTED and its deep dive is rendered, and the other four
 * carry VIEW and point at the section anchor. Nothing on the page is a control
 * that does nothing.
 *
 * THE THREE QUESTIONS PER STAGE ARE THE DESIGN'S, NOT NEW ONES. The previous
 * version asked thirteen verification questions across the five stages, which is
 * more than the approved homepage states and more than the section needs.
 *
 * There is no role="tablist" here and there never was. The prototype used it
 * without a matching role="tabpanel", which is invalid, and it is not carried
 * over.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The five engineering stages.
 */
$maulik_portfolio_stages = array(
	array(
		'number'      => '01',
		'stage'       => __( 'Understand', 'maulik-portfolio' ),
		'headline'    => __( 'Understand the requirement, constraints and system.', 'maulik-portfolio' ),
		'description' => __( 'Before writing plugin code, I map out the existing WordPress environment, data ownership boundaries, third party API limits and security constraints.', 'maulik-portfolio' ),
		'questions'   => array(
			__( 'Where does authoritative state live between WordPress and external services?', 'maulik-portfolio' ),
			__( 'Which user roles and capabilities should be permitted to read or mutate this data?', 'maulik-portfolio' ),
			__( 'What happens when an external API times out or returns partial data?', 'maulik-portfolio' ),
		),
		'artifacts'   => __( 'Requirement Boundary Map · Role and Capability Matrix · API Contract Specification', 'maulik-portfolio' ),
	),
	array(
		'number'      => '02',
		'stage'       => __( 'Design', 'maulik-portfolio' ),
		'headline'    => __( 'Choose the architecture, data flow and integration approach.', 'maulik-portfolio' ),
		'description' => __( 'I select the appropriate WordPress storage strategy, hook lifecycle points, authentication mechanism and modular class structure.', 'maulik-portfolio' ),
		'questions'   => array(
			__( 'Does this data belong in postmeta, options or a dedicated custom MySQL table?', 'maulik-portfolio' ),
			__( 'Should this external operation run synchronously or via asynchronous AJAX polling and webhooks?', 'maulik-portfolio' ),
			__( 'How are requests authenticated and signed across domain boundaries?', 'maulik-portfolio' ),
		),
		'artifacts'   => __( 'PSR 4 Namespace Structure · REST and Webhook Flow Diagram · Cryptographic Signing Contract', 'maulik-portfolio' ),
	),
	array(
		'number'      => '03',
		'stage'       => __( 'Build', 'maulik-portfolio' ),
		'headline'    => __( 'Implement maintainable functionality.', 'maulik-portfolio' ),
		'description' => __( 'I write modular, object oriented PHP with clear separation between hook registration, business logic, database access and presentation templates.', 'maulik-portfolio' ),
		'questions'   => array(
			__( 'Can this plugin module be tested or updated without breaking unrelated site features?', 'maulik-portfolio' ),
			__( 'Are all inputs sanitized and all SQL queries parameterized?', 'maulik-portfolio' ),
			__( 'Is every rendered output escaped for its exact HTML, attribute or JSON context?', 'maulik-portfolio' ),
		),
		'artifacts'   => __( 'Modular WordPress Plugin Codebase · Authenticated REST Endpoints · Accessible Frontend and Admin UI', 'maulik-portfolio' ),
	),
	array(
		'number'      => '04',
		'stage'       => __( 'Verify', 'maulik-portfolio' ),
		'headline'    => __( 'Test workflows, security, edge cases and integration behavior.', 'maulik-portfolio' ),
		'description' => __( 'I verify signature validation, capability restrictions, duplicate webhook delivery, malformed uploads and unexpected API responses.', 'maulik-portfolio' ),
		'questions'   => array(
			__( 'Does HMAC SHA 256 verification reject tampered payloads immediately?', 'maulik-portfolio' ),
			__( 'Can an unprivileged user bypass the interface and call the AJAX or REST endpoint directly?', 'maulik-portfolio' ),
			__( 'Does decryption fail safely if ciphertext or key material is invalid?', 'maulik-portfolio' ),
		),
		'artifacts'   => __( 'Security Boundary Verification · Edge Case Payload Checks · Role Permission Audit', 'maulik-portfolio' ),
	),
	array(
		'number'      => '05',
		'stage'       => __( 'Improve', 'maulik-portfolio' ),
		'headline'    => __( 'Refine the system based on findings and future requirements.', 'maulik-portfolio' ),
		'description' => __( 'I simplify complex conditional paths, document architectural decisions and prepare reusable patterns for future engineering requirements.', 'maulik-portfolio' ),
		'questions'   => array(
			__( 'Can common integration patterns be extracted into reusable service classes?', 'maulik-portfolio' ),
			__( 'Are error states clear enough to diagnose production integration issues quickly?', 'maulik-portfolio' ),
			__( 'How does the architecture support upcoming modules without accumulating technical debt?', 'maulik-portfolio' ),
		),
		'artifacts'   => __( 'Refactored Service Modules · Developer Architecture Documentation · Extensible Hook Interfaces', 'maulik-portfolio' ),
	),
);

/**
 * The stage whose deep dive is rendered, which is the first one.
 */
$maulik_portfolio_active_stage = $maulik_portfolio_stages[0];

/**
 * The chevron that connects two stage rows, taken from the design's icon set.
 *
 * @return string The SVG element.
 */
function maulik_portfolio_chevron_down() {
	return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 5v14"></path><path d="m19 12-7 7-7-7"></path></svg>';
}

?>
<!-- wp:group {"anchor":"engineering-approach","ariaLabelledby":"heading-engineering-approach","className":"is-style-section hz-section hz-approach is-style-surface-dark-grid","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section hz-section hz-approach is-style-surface-dark-grid" id="engineering-approach" aria-labelledby="heading-engineering-approach">
	<!-- wp:group {"className":"hz-section__head","layout":{"type":"default"}} -->
	<div class="wp-block-group hz-section__head">
		<!-- wp:group {"className":"hz-section__head-main","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-section__head-main">
			<!-- wp:paragraph {"className":"hz-eyebrow"} -->
			<p class="hz-eyebrow"><?php echo esc_html__( '06 · Methodology and System Discipline', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2,"anchor":"heading-engineering-approach","className":"hz-section__title"} -->
			<h2 class="wp-block-heading hz-section__title" id="heading-engineering-approach"><?php echo esc_html__( 'How I approach engineering', 'maulik-portfolio' ); ?></h2>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->
		<!-- wp:paragraph {"className":"hz-section__head-lede"} -->
		<p class="hz-section__head-lede"><?php echo esc_html__( 'Every plugin, integration and security workflow follows a structured five stage engineering progression.', 'maulik-portfolio' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<!-- wp:group {"className":"hz-stack-8","layout":{"type":"default"}} -->
	<div class="wp-block-group hz-stack-8">
		<!-- wp:group {"className":"hz-body","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-body">
			<!-- wp:group {"className":"hz-body__lead","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-body__lead">
				<!-- wp:group {"className":"hz-approach__stages","layout":{"type":"default"}} -->
				<div class="wp-block-group hz-approach__stages">
					<?php foreach ( $maulik_portfolio_stages as $maulik_portfolio_stage_index => $maulik_portfolio_stage ) : ?>
						<?php
						$maulik_portfolio_stage_is_selected = 0 === $maulik_portfolio_stage_index;
						$maulik_portfolio_stage_class       = $maulik_portfolio_stage_is_selected ? 'hz-stage' : 'hz-stage hz-stage-idle';
						$maulik_portfolio_stage_state       = $maulik_portfolio_stage_is_selected ? 'SELECTED' : 'VIEW';
						$maulik_portfolio_stage_current     = $maulik_portfolio_stage_is_selected ? ' aria-current="true"' : '';
						?>
						<!-- wp:paragraph {"className":<?php echo wp_json_encode( $maulik_portfolio_stage_class ); ?>} -->
						<p class="<?php echo esc_html( $maulik_portfolio_stage_class ); ?>"><a class="hz-stage__link" href="#stage-<?php echo $maulik_portfolio_stage['number']; ?>"<?php echo $maulik_portfolio_stage_current; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><span class="hz-label hz-stage__number"><?php echo esc_html__( 'Stage', 'maulik-portfolio' ); ?> <?php echo esc_html( $maulik_portfolio_stage['number'] ); ?></span><span class="hz-stage__name"><?php echo esc_html( $maulik_portfolio_stage['stage'] ); ?></span><span class="hz-stage__headline"><?php echo esc_html( $maulik_portfolio_stage['headline'] ); ?></span></a><span class="hz-stage__state"><?php echo esc_html( $maulik_portfolio_stage_state ); ?></span></p>
						<!-- /wp:paragraph -->
						<?php if ( $maulik_portfolio_stage_index < count( $maulik_portfolio_stages ) - 1 ) : ?>
							<!-- wp:html -->
							<div class="hz-arrow"><?php echo maulik_portfolio_chevron_down(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
							<!-- /wp:html -->
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
			<!-- wp:group {"className":"hz-body__main hz-panel hz-panel-rail","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-body__main hz-panel hz-panel-rail">
				<!-- wp:group {"className":"hz-row","layout":{"type":"default"}} -->
				<div class="wp-block-group hz-row">
					<!-- wp:paragraph {"className":"hz-label"} -->
					<p class="hz-label"><?php echo esc_html__( 'Stage', 'maulik-portfolio' ); ?> <?php echo esc_html( $maulik_portfolio_active_stage['number'] ); ?> <?php echo esc_html__( 'of 05 · Engineering Execution', 'maulik-portfolio' ); ?></p>
					<!-- /wp:paragraph -->
					<!-- wp:heading {"level":3,"anchor":<?php echo wp_json_encode( 'stage-' . $maulik_portfolio_active_stage['number'] ); ?>,"className":"hz-domain__title"} -->
					<h3 class="wp-block-heading hz-domain__title" id="stage-<?php echo esc_html( $maulik_portfolio_active_stage['number'] ); ?>"><?php echo esc_html( $maulik_portfolio_active_stage['stage'] ); ?>: <?php echo esc_html( $maulik_portfolio_active_stage['headline'] ); ?></h3>
					<!-- /wp:heading -->
				</div>
				<!-- /wp:group -->
				<!-- wp:paragraph {"className":"hz-body-copy"} -->
				<p class="hz-body-copy"><?php echo esc_html( $maulik_portfolio_active_stage['description'] ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:group {"className":"hz-stack-3","layout":{"type":"default"}} -->
				<div class="wp-block-group hz-stack-3">
					<!-- wp:paragraph {"className":"hz-label"} -->
					<p class="hz-label"><?php echo esc_html__( 'Key Architectural Questions Evaluated', 'maulik-portfolio' ); ?></p>
					<!-- /wp:paragraph -->
					<!-- wp:list {"className":"hz-questions","layout":{"type":"default"}} -->
					<ul class="wp-block-list hz-questions">
						<?php foreach ( $maulik_portfolio_active_stage['questions'] as $maulik_portfolio_question ) : ?>
							<!-- wp:list-item -->
							<li class="hz-question"><?php echo esc_html( $maulik_portfolio_question ); ?></li>
							<!-- /wp:list-item -->
						<?php endforeach; ?>
					</ul>
					<!-- /wp:list -->
				</div>
				<!-- /wp:group -->
				<!-- wp:group {"className":"hz-approach__foot","layout":{"type":"default"}} -->
				<div class="wp-block-group hz-approach__foot">
					<span class="hz-label-faint"><?php echo esc_html__( 'Stage Outputs:', 'maulik-portfolio' ); ?> <?php echo esc_html( $maulik_portfolio_active_stage['artifacts'] ); ?></span>
					<a class="hz-link hz-approach__next" href="#engineering-approach"><?php echo esc_html__( 'Next Stage', 'maulik-portfolio' ); ?></a>
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