<?php
/**
 * Title: Home Selected Work Section
 * Slug: maulik-portfolio/home-selected-work
 * Categories: maulik-portfolio-home
 * Description: Homepage section 04, the RankKernel block, five featured project cards and the confidentiality strip, on the flat base band then the 48px dark grid band.
 * Inserter: yes
 *
 * Section 04 of 08, and the largest structural change in this pattern set.
 *
 * THE PREVIOUS VERSION WAS NINE BARE LIST LINES. Each line was a project name and
 * a discipline area, with no title, no description, no technologies and no
 * architecture. That is not what the approved homepage renders and it is not what
 * the source data contains. The design renders a RankKernel block, a two column
 * grid of five featured project cards, and a confidentiality strip. All three are
 * here.
 *
 * TWO BANDS, TWO SURFACES. Band A is flat #0A0A0B with a 1px #262626 rule under
 * it and carries the RankKernel block. Band B wears the 48px dark grid variation
 * and carries the card grid and the confidentiality strip. The previous version
 * put the whole section on the 48px grid, which put a texture on band A where the
 * design has none.
 *
 * FIVE FEATURED PROJECTS, AND FIVE IS THE COUNT. Projects one through five are
 * the ones the source data marks as featured. The other four exist in the
 * archive and are reached from the archive link, which reads "View Complete
 * Project Archive (9)". Nine is the archive size and is not a claim about this
 * section.
 *
 * NOTHING HERE CLAIMS AN OUTCOME. No percentages, no revenue, no client names,
 * no domains, no speedups. The source data has no impact field at all, which is a
 * deliberate strength of the content rather than a gap to fill.
 *
 * WHY PROJECT FIVE CARRIES PLACEHOLDER TEXT. The source data marks it that way
 * itself: "Detailed technical scope and verified responsibilities are marked with
 * structured placeholders pending public release verification." The placeholder
 * is copied rather than filled in, because filling it in would mean inventing
 * verified responsibilities.
 *
 * THE CARD FOOTER LINKS TO THE ARCHIVE, NOT TO A PER PROJECT ROUTE. There is no
 * project post type and no per project page, so a card that pointed at
 * /projects/slug/ would be a link to a 404. It points at /projects/, which is
 * where the case studies actually live.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The five featured projects, in the order the source data states them.
 */
$maulik_portfolio_featured_projects = array(
	array(
		'number'         => '01',
		'title'          => __( 'Cross Domain Payment Architecture', 'maulik-portfolio' ),
		'classification' => __( 'Professional Project', 'maulik-portfolio' ),
		'category'       => __( 'Payments and Security Architecture', 'maulik-portfolio' ),
		'description'    => __( 'Reusable cross domain payment architecture featuring an approved payment environment, iframe checkout interface, HMAC SHA 256 signed REST communication, shared secret authentication and multi gateway webhook processing.', 'maulik-portfolio' ),
		'technologies'   => array(
			__( 'WordPress', 'maulik-portfolio' ),
			__( 'PHP', 'maulik-portfolio' ),
			__( 'Gravity Forms', 'maulik-portfolio' ),
			__( 'REST APIs', 'maulik-portfolio' ),
			__( 'Payment Gateways', 'maulik-portfolio' ),
			__( 'HMAC SHA 256', 'maulik-portfolio' ),
		),
		'nodes'          => array(
			__( 'Origin WordPress Site', 'maulik-portfolio' ),
			__( 'HMAC SHA 256 Signer', 'maulik-portfolio' ),
			__( 'Approved Payment Environment', 'maulik-portfolio' ),
			__( 'Multi Gateway Router', 'maulik-portfolio' ),
			__( 'Signed Webhook Callback', 'maulik-portfolio' ),
		),
	),
	array(
		'number'         => '02',
		'title'          => __( 'Employee Application Management System', 'maulik-portfolio' ),
		'classification' => __( 'Professional Project', 'maulik-portfolio' ),
		'category'       => __( 'Workflow and Access Control', 'maulik-portfolio' ),
		'description'    => __( 'Role based employee application management workflow built on WordPress, PHP, Gravity Forms, Forminator and AJAX with application assignment, asynchronous data loading and structured status transitions.', 'maulik-portfolio' ),
		'technologies'   => array(
			__( 'WordPress', 'maulik-portfolio' ),
			__( 'PHP', 'maulik-portfolio' ),
			__( 'Gravity Forms', 'maulik-portfolio' ),
			__( 'Forminator', 'maulik-portfolio' ),
			__( 'AJAX', 'maulik-portfolio' ),
			__( 'Custom Roles', 'maulik-portfolio' ),
		),
		'nodes'          => array(
			__( 'Form Intake Layer', 'maulik-portfolio' ),
			__( 'Custom Role and Capability Guard', 'maulik-portfolio' ),
			__( 'Assignment and Status Engine', 'maulik-portfolio' ),
			__( 'Asynchronous AJAX Interface', 'maulik-portfolio' ),
		),
	),
	array(
		'number'         => '03',
		'title'          => __( 'Identity Document OCR Integration', 'maulik-portfolio' ),
		'classification' => __( 'Professional Project', 'maulik-portfolio' ),
		'category'       => __( 'API Integration and Automation', 'maulik-portfolio' ),
		'description'    => __( 'Custom WordPress plugin integrating Azure Form Recognizer with Gravity Forms to analyze uploaded identity documents via asynchronous AJAX polling and automatically map extracted OCR data into form fields.', 'maulik-portfolio' ),
		'technologies'   => array(
			__( 'WordPress', 'maulik-portfolio' ),
			__( 'PHP', 'maulik-portfolio' ),
			__( 'Azure Form Recognizer', 'maulik-portfolio' ),
			__( 'AJAX', 'maulik-portfolio' ),
			__( 'Gravity Forms', 'maulik-portfolio' ),
		),
		'nodes'          => array(
			__( 'Gravity Forms Upload Trigger', 'maulik-portfolio' ),
			__( 'WordPress PHP API Proxy', 'maulik-portfolio' ),
			__( 'Azure Form Recognizer', 'maulik-portfolio' ),
			__( 'AJAX Polling and Field Mapper', 'maulik-portfolio' ),
		),
	),
	array(
		'number'         => '04',
		'title'          => __( 'Secure WordPress Data Encryption', 'maulik-portfolio' ),
		'classification' => __( 'Professional Project', 'maulik-portfolio' ),
		'category'       => __( 'Security and Cryptography', 'maulik-portfolio' ),
		'description'    => __( 'Field level encryption of sensitive LatePoint appointment data in WordPress using PHP Sodium and an external encryption key, paired with capability gated decryption for authorized roles.', 'maulik-portfolio' ),
		'technologies'   => array(
			__( 'WordPress', 'maulik-portfolio' ),
			__( 'PHP', 'maulik-portfolio' ),
			__( 'Sodium', 'maulik-portfolio' ),
			__( 'LatePoint', 'maulik-portfolio' ),
			__( 'Encryption', 'maulik-portfolio' ),
		),
		'nodes'          => array(
			__( 'LatePoint Booking Lifecycle', 'maulik-portfolio' ),
			__( 'PHP Sodium Crypto Service', 'maulik-portfolio' ),
			__( 'External Key Provider', 'maulik-portfolio' ),
			__( 'Capability Decryption Guard', 'maulik-portfolio' ),
		),
	),
	array(
		'number'         => '05',
		'title'          => __( 'Rank Math SEO Engineering', 'maulik-portfolio' ),
		'classification' => __( 'Professional Project', 'maulik-portfolio' ),
		'category'       => __( 'SEO and WordPress Engineering', 'maulik-portfolio' ),
		'description'    => __( 'Professional engineering work involving Rank Math SEO within WordPress environments. Detailed technical scope and verified responsibilities are marked with structured placeholders pending public release verification.', 'maulik-portfolio' ),
		'technologies'   => array(
			__( 'WordPress', 'maulik-portfolio' ),
			__( 'PHP', 'maulik-portfolio' ),
			__( 'Rank Math SEO', 'maulik-portfolio' ),
			__( 'Metadata', 'maulik-portfolio' ),
			__( 'Schema', 'maulik-portfolio' ),
		),
		'nodes'          => array(
			__( 'WordPress Content Context', 'maulik-portfolio' ),
			__( 'Rank Math Hook Interface', 'maulik-portfolio' ),
			__( 'Verified Output Layer', 'maulik-portfolio' ),
		),
	),
);

/**
 * Joins a list with the middle dot the design separates list items with.
 *
 * @param array $items The items to join.
 * @return string The joined string.
 */
function maulik_portfolio_join_dots( $items ) {
	return implode( ' · ', $items );
}

?>
<!-- wp:group {"anchor":"selected-work","ariaLabelledby":"heading-selected-work","className":"is-style-section hz-section hz-work","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section hz-section hz-work" id="selected-work" aria-labelledby="heading-selected-work">
	<!-- wp:group {"className":"hz-stack-12","layout":{"type":"default"}} -->
	<div class="wp-block-group hz-stack-12">
		<!-- wp:group {"className":"hz-section__head","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-section__head">
			<!-- wp:group {"className":"hz-section__head-main","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-section__head-main">
				<!-- wp:paragraph {"className":"hz-eyebrow"} -->
				<p class="hz-eyebrow"><?php echo esc_html__( '04 · Selected Development Work', 'maulik-portfolio' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":2,"anchor":"heading-selected-work","className":"hz-section__title"} -->
				<h2 class="wp-block-heading hz-section__title" id="heading-selected-work"><?php echo esc_html__( 'Selected Development Work', 'maulik-portfolio' ); ?></h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->
			<!-- wp:paragraph {"className":"hz-section__head-lede"} -->
			<p class="hz-section__head-lede"><?php echo esc_html__( 'A combination of independent software development and professional engineering work completed as part of my professional role.', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<!-- wp:group {"className":"hz-work__rk-head","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-work__rk-head">
			<!-- wp:group {"className":"hz-stack-3","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-stack-3">
				<!-- wp:paragraph {"className":"hz-label"} -->
				<p class="hz-label"><?php echo esc_html__( 'Independent Development · Personal Project · Open Source · IN DEVELOPMENT', 'maulik-portfolio' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":3,"className":"hz-work__rk-name"} -->
				<h3 class="wp-block-heading hz-work__rk-name"><?php echo esc_html__( 'RankKernel', 'maulik-portfolio' ); ?></h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"hz-work__rk-subtitle"} -->
				<p class="hz-work__rk-subtitle"><?php echo esc_html__( 'Open Source SEO and Schema Engine for WordPress', 'maulik-portfolio' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"className":"hz-work__rk-overview"} -->
				<p class="hz-work__rk-overview"><?php echo esc_html__( 'RankKernel is an independently engineered, modular WordPress plugin designed to handle technical SEO metadata, structured data schema generation and search visibility controls through a clean, extensible PHP architecture.', 'maulik-portfolio' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
			<!-- wp:buttons {"className":"hz-actions","layout":{"type":"flex","flexWrap":"wrap"}} -->
			<div class="wp-block-buttons hz-actions">
				<!-- wp:button {"className":"hz-btn-amber"} -->
				<div class="wp-block-button hz-btn-amber">
					<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( null, '/rankkernel/' ) ); ?>"><?php echo esc_html__( 'Explore RankKernel', 'maulik-portfolio' ); ?></a>
				</div>
				<!-- /wp:button -->
				<!-- wp:button {"className":"hz-btn-dark"} -->
				<div class="wp-block-button hz-btn-dark">
					<a class="wp-block-button__link wp-element-button" href="https://github.com/maulikbhalodiya" target="_blank" rel="noopener noreferrer"><?php echo esc_html__( 'View on GitHub', 'maulik-portfolio' ); ?></a>
				</div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
	<!-- wp:group {"className":"hz-stack-12 hz-work__band-b is-style-surface-dark-grid","layout":{"type":"default"}} -->
	<div class="wp-block-group hz-stack-12 hz-work__band-b is-style-surface-dark-grid">
		<!-- wp:group {"className":"hz-work__b-head","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-work__b-head">
			<!-- wp:group {"className":"hz-stack-3 hz-work__b-intro","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-stack-3 hz-work__b-intro">
				<!-- wp:paragraph {"className":"hz-label"} -->
				<p class="hz-label"><?php echo esc_html__( 'Selected Work From My Professional Role · Professional Project Archive', 'maulik-portfolio' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":3,"className":"hz-work__b-title"} -->
				<h3 class="wp-block-heading hz-work__b-title"><?php echo esc_html__( 'Professional Engineering Work', 'maulik-portfolio' ); ?></h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"hz-body-copy"} -->
				<p class="hz-body-copy"><?php echo esc_html__( 'Selected technical projects completed as part of my professional role. Client identities and confidential information have been intentionally omitted.', 'maulik-portfolio' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
			<a class="hz-link hz-work__archive-link" href="<?php echo esc_url( get_home_url( null, '/projects/' ) ); ?>"><?php echo esc_html__( 'View Complete Project Archive (9)', 'maulik-portfolio' ); ?></a>
		</div>
		<!-- /wp:group -->
		<!-- wp:group {"className":"hz-work__grid","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-work__grid">
			<?php foreach ( $maulik_portfolio_featured_projects as $maulik_portfolio_project ) : ?>
				<!-- wp:group {"className":"hz-card","layout":{"type":"default"}} -->
				<article class="wp-block-group hz-card">
					<!-- wp:group {"className":"hz-card__body","layout":{"type":"default"}} -->
					<div class="wp-block-group hz-card__body">
						<!-- wp:group {"className":"hz-card__meta","layout":{"type":"default"}} -->
						<div class="wp-block-group hz-card__meta">
							<span class="hz-meta"><?php echo esc_html( $maulik_portfolio_project['number'] ); ?> · <?php echo esc_html( $maulik_portfolio_project['classification'] ); ?></span>
							<span class="hz-meta"><?php echo esc_html( $maulik_portfolio_project['category'] ); ?></span>
						</div>
						<!-- /wp:group -->
						<!-- wp:heading {"level":3,"className":"hz-card__title"} -->
						<h3 class="wp-block-heading hz-card__title"><?php echo esc_html( $maulik_portfolio_project['title'] ); ?></h3>
						<!-- /wp:heading -->
						<!-- wp:paragraph {"className":"hz-card__desc"} -->
						<p class="hz-card__desc"><?php echo esc_html( $maulik_portfolio_project['description'] ); ?></p>
						<!-- /wp:paragraph -->
						<!-- wp:group {"className":"hz-card__scheme","layout":{"type":"default"}} -->
						<div class="wp-block-group hz-card__scheme">
							<!-- wp:group {"className":"hz-card__scheme-head","layout":{"type":"default"}} -->
							<div class="wp-block-group hz-card__scheme-head">
								<span class="hz-card__scheme-label"><?php echo esc_html__( 'Architecture Pipeline', 'maulik-portfolio' ); ?></span>
								<span class="hz-card__scheme-label"><?php echo esc_html( (string) count( $maulik_portfolio_project['nodes'] ) ); ?> <?php echo esc_html__( 'System Nodes', 'maulik-portfolio' ); ?></span>
							</div>
							<!-- /wp:group -->
							<div class="hz-card__nodes">
								<?php foreach ( $maulik_portfolio_project['nodes'] as $maulik_portfolio_node_index => $maulik_portfolio_node ) : ?>
									<?php if ( 0 < $maulik_portfolio_node_index ) : ?>
										<span class="hz-card__node-sep" aria-hidden="true">/</span>
									<?php endif; ?>
									<span class="hz-card__node"><?php echo esc_html( $maulik_portfolio_node ); ?></span>
								<?php endforeach; ?>
							</div>
						</div>
						<!-- /wp:group -->
					</div>
					<!-- /wp:group -->
					<!-- wp:group {"className":"hz-card__foot","layout":{"type":"default"}} -->
					<div class="wp-block-group hz-card__foot">
						<span class="hz-meta"><?php echo esc_html( maulik_portfolio_join_dots( $maulik_portfolio_project['technologies'] ) ); ?></span>
						<a class="hz-link" href="<?php echo esc_url( get_home_url( null, '/projects/' ) ); ?>"><?php echo esc_html__( 'View Case Study', 'maulik-portfolio' ); ?></a>
					</div>
					<!-- /wp:group -->
				</article>
				<!-- /wp:group -->
			<?php endforeach; ?>
		</div>
		<!-- /wp:group -->
		<!-- wp:group {"className":"hz-work__conf","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-work__conf">
			<p><?php echo esc_html__( 'Selected professional projects have been anonymized to protect client and business confidentiality. The case studies focus on the technical problems, responsibilities and engineering approaches that can be publicly discussed.', 'maulik-portfolio' ); ?></p>
			<a class="hz-link" href="<?php echo esc_url( get_home_url( null, '/projects/' ) ); ?>"><?php echo esc_html__( 'Browse All Case Studies', 'maulik-portfolio' ); ?></a>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->