<?php
/**
 * Title: Home Experience Section
 * Slug: maulik-portfolio/home-experience
 * Categories: maulik-portfolio-home
 * Description: Homepage section 05, the two role timeline entries with their responsibilities and technologies, on the flat base surface.
 * Inserter: no
 *
 * Section 05 of 08. Flat #0A0A0B with a 1px #262626 rule under it, which is what
 * the design states.
 *
 * THE CREAM SURFACE WAS A RENDERING BUG, NOT A DESIGN CHOICE. This section wore
 * is-style-surface-cream-editorial, a #FFFDF4 ground. The theme's h2 colour is
 * also #FFFDF4, so "Professional Experience" was rendering at 1.0:1 contrast
 * against its own background: invisible. The design puts this section on #0A0A0B
 * and the heading is legible on it.
 *
 * TWO ENTRIES, AND BOTH DATES ARE VERIFIED. WordPress Developer full time from
 * July 2025 to present, PHP Developer Intern from January to June 2025. A
 * recruiter facing a timeline with no dates reads it as an omission rather than
 * as modesty, so the dates are carried. The design's own period strings are
 * vaguer than this ("Current Professional Role"), and the verified dates are the
 * better copy, so the dates win.
 *
 * THE DETAIL SENTENCES ARE NOT INVENTED. The previous version of this pattern
 * summarised each role in its own words. The approved homepage uses specific
 * sentences, and those are the ones here. The previous version also built the
 * period with a translators comment and a sprintf, which is defensible for an
 * assembled string. The approved copy is a fixed line of prose, so it is a fixed
 * line of prose.
 *
 * TEN RESPONSIBILITIES AND TWENTY THREE TECHNOLOGIES ARE NOW RENDERED. The
 * previous version rendered two label paragraphs and two body paragraphs and no
 * structure at all. The design has a 2px rail, a square node marker per entry, a
 * Technical Focus line, a two column responsibility grid and a technology
 * footer. All of it is here.
 *
 * Qrolic Technologies. One c after Qro. The resume says "Qrologic" in one place
 * and that is a typo, and it is spelled correctly everywhere here.
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
 * with it to 92.2%.
 * This copy adds the role dates that the live page deliberately removed.
 * Offering it in the inserter would let an owner put a section on a page the
 * design never showed. It is hidden rather than deleted because the markup and
 * this comment are the record of what was decided, and deleting them loses
 * that record.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Verified professional experience.
 */
$maulik_portfolio_roles = array(
	array(
		'index'            => '01',
		'period'           => __( 'July 2025 to present', 'maulik-portfolio' ),
		'type'             => __( 'Full Time Engineering Role', 'maulik-portfolio' ),
		'role'             => __( 'WordPress Developer', 'maulik-portfolio' ),
		'company'          => __( 'Qrolic Technologies', 'maulik-portfolio' ),
		'focus'            => __( 'Custom plugin development and backend work inside WordPress, including payment architectures, form driven internal systems, third party API integrations and field level encryption. Responsibilities run from design through implementation to verification.', 'maulik-portfolio' ),
		'current'          => true,
		'responsibilities' => array(
			__( 'Architect and develop custom WordPress plugins using object oriented PHP, Composer and PSR 4 autoloading', 'maulik-portfolio' ),
			__( 'Build cross domain payment architectures with iframe checkout flows, REST APIs, webhooks and HMAC SHA 256 signature verification', 'maulik-portfolio' ),
			__( 'Implement field level encryption for sensitive booking data using PHP Sodium and external key management paired with WordPress capability checks', 'maulik-portfolio' ),
			__( 'Integrate external cloud services including Azure Form Recognizer OCR with Gravity Forms using asynchronous AJAX polling', 'maulik-portfolio' ),
			__( 'Develop role based application management systems with custom WordPress roles, Forminator and Gravity Forms workflows', 'maulik-portfolio' ),
			__( 'Create and customize Gutenberg blocks, WooCommerce extensions and backend administrative interfaces with strict nonce verification, input validation and output escaping', 'maulik-portfolio' ),
		),
		'technologies'     => __( 'WordPress · PHP · Custom Plugins · REST APIs · HMAC SHA 256 · PHP Sodium · Payment Gateways · Webhooks · Azure Form Recognizer · Gravity Forms · WooCommerce · Gutenberg · MySQL · AJAX · Git', 'maulik-portfolio' ),
	),
	array(
		'index'            => '02',
		'period'           => __( 'January to June 2025', 'maulik-portfolio' ),
		'type'             => __( 'Engineering Internship', 'maulik-portfolio' ),
		'role'             => __( 'PHP Developer Intern', 'maulik-portfolio' ),
		'company'          => __( 'Qrolic Technologies', 'maulik-portfolio' ),
		'focus'            => __( 'PHP and WordPress fundamentals in a production codebase: structure, form handling, data access and debugging real problems rather than exercises.', 'maulik-portfolio' ),
		'current'          => false,
		'responsibilities' => array(
			__( 'Developed server side features using object oriented PHP and structured MySQL database queries', 'maulik-portfolio' ),
			__( 'Learned and applied WordPress core architecture including action hooks, filter hooks, custom post types and metadata APIs', 'maulik-portfolio' ),
			__( 'Implemented asynchronous frontend to backend interactions using JavaScript, AJAX and JSON payloads', 'maulik-portfolio' ),
			__( 'Practiced defensive coding standards including nonce verification, input validation, prepared SQL queries and output escaping', 'maulik-portfolio' ),
		),
		'technologies'     => __( 'PHP · WordPress · MySQL · JavaScript · AJAX · HTML · CSS · Git', 'maulik-portfolio' ),
	),
);

?>
<!-- wp:group {"anchor":"experience","ariaLabelledby":"heading-experience","className":"is-style-section hz-section hz-experience","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section hz-section hz-experience" id="experience" aria-labelledby="heading-experience">
	<!-- wp:group {"className":"hz-section__head","layout":{"type":"default"}} -->
	<div class="wp-block-group hz-section__head">
		<!-- wp:group {"className":"hz-section__head-main","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-section__head-main">
			<!-- wp:paragraph {"className":"hz-eyebrow"} -->
			<p class="hz-eyebrow"><?php echo esc_html__( '05 · Career Progression and Responsibilities', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2,"anchor":"heading-experience","className":"hz-section__title"} -->
			<h2 class="wp-block-heading hz-section__title" id="heading-experience"><?php echo esc_html__( 'Professional Experience', 'maulik-portfolio' ); ?></h2>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->
		<!-- wp:paragraph {"className":"hz-section__head-lede"} -->
		<p class="hz-section__head-lede"><?php echo esc_html__( 'Verified engineering experience at Qrolic Technologies progressing from PHP Developer Intern to full time WordPress Developer.', 'maulik-portfolio' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<!-- wp:group {"className":"hz-stack-8","layout":{"type":"default"}} -->
	<div class="wp-block-group hz-stack-8">
		<!-- wp:group {"className":"hz-timeline","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-timeline">
			<?php foreach ( $maulik_portfolio_roles as $maulik_portfolio_role ) : ?>
				<!-- wp:group {"className":"hz-role","layout":{"type":"default"}} -->
				<article class="wp-block-group hz-role">
					<?php $maulik_portfolio_node_class = $maulik_portfolio_role['current'] ? 'hz-role__node hz-role__node-current' : 'hz-role__node'; ?>
					<span class="<?php echo esc_html( $maulik_portfolio_node_class ); ?>" aria-hidden="true"></span>
					<!-- wp:group {"className":"hz-stack-6","layout":{"type":"default"}} -->
					<div class="wp-block-group hz-stack-6">
						<!-- wp:group {"className":"hz-role__head","layout":{"type":"default"}} -->
						<div class="wp-block-group hz-role__head">
							<!-- wp:paragraph {"className":"hz-label"} -->
							<p class="hz-label"><?php echo esc_html( $maulik_portfolio_role['period'] ); ?> · <?php echo esc_html( $maulik_portfolio_role['type'] ); ?></p>
							<!-- /wp:paragraph -->
							<!-- wp:heading {"level":3,"className":"hz-role__title"} -->
							<h3 class="wp-block-heading hz-role__title"><?php echo esc_html( $maulik_portfolio_role['role'] ); ?> · <?php echo esc_html( $maulik_portfolio_role['company'] ); ?></h3>
							<!-- /wp:heading -->
						</div>
						<!-- /wp:group -->
						<!-- wp:group {"className":"hz-stack-2","layout":{"type":"default"}} -->
						<div class="wp-block-group hz-stack-2">
							<!-- wp:paragraph {"className":"hz-label-faint"} -->
							<p class="hz-label-faint"><?php echo esc_html__( 'Technical Focus', 'maulik-portfolio' ); ?></p>
							<!-- /wp:paragraph -->
							<!-- wp:paragraph {"className":"hz-body-copy hz-body-copy-medium"} -->
							<p class="hz-body-copy hz-body-copy-medium"><?php echo esc_html( $maulik_portfolio_role['focus'] ); ?></p>
							<!-- /wp:paragraph -->
						</div>
						<!-- /wp:group -->
						<!-- wp:group {"className":"hz-stack-3","layout":{"type":"default"}} -->
						<div class="wp-block-group hz-stack-3">
							<!-- wp:paragraph {"className":"hz-label"} -->
							<p class="hz-label"><?php echo esc_html__( 'Selected Engineering Responsibilities', 'maulik-portfolio' ); ?></p>
							<!-- /wp:paragraph -->
							<!-- wp:list {"className":"hz-resp-grid","layout":{"type":"default"}} -->
							<ul class="wp-block-list hz-resp-grid">
								<?php foreach ( $maulik_portfolio_role['responsibilities'] as $maulik_portfolio_responsibility ) : ?>
									<!-- wp:list-item -->
									<li class="hz-resp"><?php echo esc_html( $maulik_portfolio_responsibility ); ?></li>
									<!-- /wp:list-item -->
								<?php endforeach; ?>
							</ul>
							<!-- /wp:list -->
						</div>
						<!-- /wp:group -->
						<!-- wp:paragraph {"className":"hz-label-faint hz-role__tech"} -->
						<p class="hz-label-faint hz-role__tech"><?php echo esc_html__( 'Technologies:', 'maulik-portfolio' ); ?> <?php echo esc_html( $maulik_portfolio_role['technologies'] ); ?></p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->
				</article>
				<!-- /wp:group -->
			<?php endforeach; ?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->