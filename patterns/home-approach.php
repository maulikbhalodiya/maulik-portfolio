<?php
/**
 * Title: Home Approach Section
 * Slug: maulik-portfolio/home-approach
 * Categories: maulik-portfolio-home
 * Description: Homepage section 06, the five interactive stage selector buttons beside the selected stage deep dive, on the 48px dark grid surface.
 * Inserter: no
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
 * THE STAGE ROWS ARE REAL BUTTONS AND THE DESIGN SAYS SO. Measured on the
 * reference at 1440: five stage buttons at about 110 pixels tall, each carrying
 * the stage number, the stage name, the headline and a SELECTED or VIEW state
 * label, and the design component calls setActiveStageIndex from each one. The
 * static version of this section rendered them as anchor links pointing at this
 * section's own anchor, which is a control that navigates rather than a control
 * that switches, and is what this version replaces.
 *
 * THE ROW IS RAW HTML FOR THE SAME REASON THE CAPABILITY CARD IS. A button's
 * content model is phrasing content, and the row's children are spans, which is
 * already what the design renders, but the row itself was a paragraph block
 * wrapping an anchor, and no core block combination produces a button element
 * with that content. core/html carries the rows, which is the same block this
 * page already uses for the stage chevrons between them.
 *
 * ALL FIVE PANELS ARE IN THE POST AND FOUR ARE COLLAPSED. The design swaps the
 * deep dive in the browser, so the five deep dives are rendered and the four
 * that are not selected carry hz-switch-panel--idle. That class is display: none,
 * which contributes no height, so the section is exactly as tall as the selected
 * panel alone and the page height budget is unchanged.
 *
 * NEXT STAGE IS A BUTTON THAT SELECTS, NOT A LINK. The design has a Next Stage
 * control in the panel foot and its handler advances the selected stage index,
 * wrapping from the fifth back to the first. The previous version of this
 * pattern pointed an anchor at this section's own anchor, which navigated to
 * the top of the section and changed nothing. The control carries
 * data-hz-target like a stage row does, so it addresses its destination by the
 * same one mechanism, and assets/js/section-switcher.js resolves that mechanism
 * from anywhere in the panel host rather than only from the row strip.
 *
 * NO ROLE="TABLIST" HERE, AND THAT IS A DELIBERATE DIFFERENCE FROM SECTION 03.
 * Section 03 is a set of tabs over one panel region and carries
 * role="tablist" with role="tab" children because that is what the design has
 * there and what it measures. This section has no matching role="tabpanel" in
 * the design and no arrow key handling in its component, so the rows are toggle
 * buttons carrying aria-pressed and the design's own behaviour, which is click
 * to select. Adding a tablist here would promise arrow key support the design
 * does not have and this pattern does not implement.
 *
 * THE THREE QUESTIONS PER STAGE ARE THE DESIGN'S, NOT NEW ONES. The previous
 * version asked thirteen verification questions across the five stages, which is
 * more than the approved homepage states and more than the section needs.
 *
 * INSERTER HIDDEN, 2026-10-06. The header above reads Inserter: no, which is
 * the only mechanism WordPress offers here: Core registers these files by
 * scanning patterns/*.php and parsing the header, so there is no
 * register_block_pattern() call to edit. Change it back to Inserter: yes and
 * the pattern returns to the inserter on the next request. Nothing below this
 * comment is deleted, renamed or rewritten, so the section stays re-exposable.
 *
 * WHY IT IS HIDDEN RATHER THAN DELETED. Measured, this pattern is referenced
 * in 0 files across templates/, parts/ and content/, so the homepage renders
 * from content/pages/home.html and this is a second copy of a section the live
 * page already owns. Compared against the rendered live page, the copy agrees
 * with it to 97.8%.
 * The gap is one inner wrapper class.
 * Offering it in the inserter would let an owner put a section on a page the
 * design never showed. It is hidden rather than deleted because the markup and
 * this comment are the record of what was decided, and deleting them loses
 * that record.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The five engineering stages.
 */
$maulik_portfolio_stages = array(
	array(
		'id'          => 'understand',
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
		'id'          => 'design',
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
		'id'          => 'build',
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
		'id'          => 'verify',
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
		'id'          => 'improve',
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
 * The chevron that connects two stage rows, taken from the design's icon set.
 *
 * @return string The SVG element.
 */
function maulik_portfolio_chevron_down() {
	return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M12 5v14"></path><path d="m19 12-7 7-7-7"></path></svg>';
}

/**
 * The panel element id for a stage.
 *
 * The stage buttons address their panels by id through data-hz-target, so the
 * two are generated from one function rather than written out twice.
 *
 * @param string $stage_id The stage id.
 * @return string The panel element id.
 */
function maulik_portfolio_stage_panel_id( $stage_id ) {
	return 'hz-stage-panel-' . $stage_id;
}

/**
 * The five stage selector rows, as one HTML string.
 *
 * The chevron between two rows is part of this string rather than a separate
 * block, because it exists only in the gap between two rows and the design draws
 * it there. It is aria-hidden because it carries no information a reader does
 * not already have from the two rows it separates.
 *
 * @param array $stages The five engineering stages.
 * @return string The selector markup, without a surrounding element.
 */
function maulik_portfolio_stage_buttons( $stages ) {
	$markup = '';
	$last   = count( $stages ) - 1;

	foreach ( $stages as $index => $stage ) {
		$is_selected = 0 === $index;

		$markup .= sprintf(
			'<button type="button" class="hz-stage" data-hz-target="%1$s" aria-pressed="%2$s"><span class="hz-stage__body"><span class="hz-label hz-stage__number">%3$s %4$s</span><span class="hz-stage__name">%5$s</span><span class="hz-stage__headline">%6$s</span></span><span class="hz-stage__state" data-hz-state>%7$s</span></button>',
			esc_attr( maulik_portfolio_stage_panel_id( $stage['id'] ) ),
			$is_selected ? 'true' : 'false',
			esc_html__( 'Stage', 'maulik-portfolio' ),
			esc_html( $stage['number'] ),
			esc_html( $stage['stage'] ),
			esc_html( $stage['headline'] ),
			$is_selected
				? esc_html__( 'SELECTED', 'maulik-portfolio' )
				: esc_html__( 'VIEW', 'maulik-portfolio' )
		);

		if ( $index < $last ) {
			$markup .= '<span class="hz-arrow" aria-hidden="true">' . maulik_portfolio_chevron_down() . '</span>';
		}
	}

	return $markup;
}

/**
 * The block class string for one stage panel.
 *
 * The idle class is what collapses the four panels that are not selected, so
 * this is the one place that decision is expressed.
 *
 * @param bool $is_selected Whether this stage is the selected one.
 * @return string The class list.
 */
function maulik_portfolio_stage_panel_classes( $is_selected ) {
	return $is_selected
		? 'hz-panel hz-panel-rail hz-switch-panel'
		: 'hz-panel hz-panel-rail hz-switch-panel hz-switch-panel--idle';
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
					<!-- wp:html -->
					<?php
					/*
					 * The trailing newline is load bearing. PHP swallows the
					 * single newline that immediately follows a closing ?> tag,
					 * so without it the emitted markup and the block delimiter
					 * that closes it land on one line, the delimiter stops being
					 * first on its line, and the block parser stops seeing the
					 * block at all.
					 */
					echo '<div class="hz-stage-list" data-hz-switcher data-hz-tab="hz-stage" data-hz-state-selected="SELECTED" data-hz-state-idle="VIEW">' . maulik_portfolio_stage_buttons( $maulik_portfolio_stages ) . "</div>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
					<!-- /wp:html -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
			<!-- wp:group {"className":"hz-body__main hz-switch-panels","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-body__main hz-switch-panels">
				<?php foreach ( $maulik_portfolio_stages as $maulik_portfolio_stage_index => $maulik_portfolio_stage ) : ?>
					<?php
					$maulik_portfolio_stage_is_selected = 0 === $maulik_portfolio_stage_index;
					$maulik_portfolio_stage_panel_id    = maulik_portfolio_stage_panel_id( $maulik_portfolio_stage['id'] );
					$maulik_portfolio_stage_panel_class = maulik_portfolio_stage_panel_classes( $maulik_portfolio_stage_is_selected );
					?>
					<!-- wp:group {"anchor":<?php echo wp_json_encode( $maulik_portfolio_stage_panel_id ); ?>,"className":<?php echo wp_json_encode( $maulik_portfolio_stage_panel_class ); ?>,"layout":{"type":"default"}} -->
					<div class="wp-block-group <?php echo esc_html( $maulik_portfolio_stage_panel_class ); ?>" id="<?php echo esc_attr( $maulik_portfolio_stage_panel_id ); ?>">
						<!-- wp:group {"className":"hz-row","layout":{"type":"default"}} -->
						<div class="wp-block-group hz-row">
							<!-- wp:paragraph {"className":"hz-label"} -->
							<p class="hz-label"><?php echo esc_html__( 'Stage', 'maulik-portfolio' ); ?> <?php echo esc_html( $maulik_portfolio_stage['number'] ); ?> <?php echo esc_html__( 'of 05 · Engineering Execution', 'maulik-portfolio' ); ?></p>
							<!-- /wp:paragraph -->
							<!-- wp:heading {"level":3,"anchor":<?php echo wp_json_encode( 'stage-' . $maulik_portfolio_stage['number'] ); ?>,"className":"hz-domain__title"} -->
							<h3 class="wp-block-heading hz-domain__title" id="stage-<?php echo esc_html( $maulik_portfolio_stage['number'] ); ?>"><?php echo esc_html( $maulik_portfolio_stage['stage'] ); ?>: <?php echo esc_html( $maulik_portfolio_stage['headline'] ); ?></h3>
							<!-- /wp:heading -->
						</div>
						<!-- /wp:group -->
						<!-- wp:paragraph {"className":"hz-body-copy"} -->
						<p class="hz-body-copy"><?php echo esc_html( $maulik_portfolio_stage['description'] ); ?></p>
						<!-- /wp:paragraph -->
						<!-- wp:group {"className":"hz-stack-3","layout":{"type":"default"}} -->
						<div class="wp-block-group hz-stack-3">
							<!-- wp:paragraph {"className":"hz-label"} -->
							<p class="hz-label"><?php echo esc_html__( 'Key Architectural Questions Evaluated', 'maulik-portfolio' ); ?></p>
							<!-- /wp:paragraph -->
							<!-- wp:list {"className":"hz-questions","layout":{"type":"default"}} -->
							<ul class="wp-block-list hz-questions">
								<?php foreach ( $maulik_portfolio_stage['questions'] as $maulik_portfolio_question ) : ?>
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
							<span class="hz-label-faint"><?php echo esc_html__( 'Stage Outputs:', 'maulik-portfolio' ); ?> <?php echo esc_html( $maulik_portfolio_stage['artifacts'] ); ?></span>
							<!-- wp:html -->
							<button type="button" class="hz-approach__next" data-hz-target="<?php echo esc_attr( maulik_portfolio_stage_panel_id( $maulik_portfolio_stages[ ( $maulik_portfolio_stage_index + 1 ) % count( $maulik_portfolio_stages ) ]['id'] ) ); ?>"><?php echo esc_html__( 'Next Stage', 'maulik-portfolio' ); ?></button>
							<!-- /wp:html -->
						</div>
						<!-- /wp:group -->
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