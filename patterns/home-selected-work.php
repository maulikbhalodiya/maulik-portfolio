<?php
/**
 * Title: Home Selected Work Section
 * Slug: maulik-portfolio/home-selected-work
 * Categories: maulik-portfolio-home
 * Description: Homepage section 04, the nine selected projects as a list, on the 48px dark grid surface with the tighter rhythm.
 * Inserter: yes
 *
 * Section 04 of 08. The tight rhythm rather than the full section rhythm, because
 * this is the densest section on the page and nine list items plus a lede would
 * otherwise push the fold a long way down.
 *
 * NINE PROJECTS, NOT FOUR. The previous version of this pattern rendered a
 * RankKernel block and four "project-card" articles, on the reasoning that only
 * four projects are fully written up in the source content. That reasoning was
 * about the case study pages, not about this section. The approved homepage lists
 * all nine, one line each, with the discipline area after a middle dot. Nine lines
 * is not the same as nine claims: no client, no domain, no outcome and no
 * percentage appears in any of them.
 *
 * THE RANKKERNEL BLOCK IS GONE FROM HERE, NOT FROM THE SITE. RankKernel has its own
 * page, it is named in section 07, and the dedicated block that used to live here
 * carried a subsystem status line that the approved homepage does not carry.
 *
 * NOTHING HERE CLAIMS AN OUTCOME. No percentages, no revenue, no client names, no
 * domains, no speedups. The source data has no impact field at all, which is a
 * deliberate strength of the content rather than a gap to fill.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The nine selected projects.
 *
 * Each entry is one line of the approved list: the project name, a middle dot, and
 * the discipline area it belongs to.
 */
$maulik_portfolio_selected_projects = array(
	__( 'Cross Domain Payment Architecture · Payments and Security Architecture', 'maulik-portfolio' ),
	__( 'Employee Application Management System · Workflow and Access Control', 'maulik-portfolio' ),
	__( 'Identity Document OCR Integration · API Integration and Automation', 'maulik-portfolio' ),
	__( 'Secure WordPress Data Encryption · Security and Cryptography', 'maulik-portfolio' ),
	__( 'Rank Math SEO Engineering · SEO Engineering', 'maulik-portfolio' ),
	__( 'Brevo API Automation · API and Marketing Automation', 'maulik-portfolio' ),
	__( 'Distance Based Dynamic Pricing · Backend Calculation and E Commerce', 'maulik-portfolio' ),
	__( 'Object Storage Automation · Cloud Storage and Automation', 'maulik-portfolio' ),
	__( 'WooCommerce Integration · E Commerce and Backend Systems', 'maulik-portfolio' ),
);

?>
<!-- wp:group {"tagName":"section","className":"is-style-section-tight is-style-surface-dark-grid","anchor":"selected-work","ariaLabelledby":"heading-selected-work","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section-tight is-style-surface-dark-grid" id="selected-work" aria-labelledby="heading-selected-work">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '04 · Selected Development Work', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-selected-work"} -->
	<h2 class="wp-block-heading" id="heading-selected-work"><?php echo esc_html__( 'Selected Development Work', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php echo esc_html__( 'Nine projects, all completed as part of professional work. Client names, domains and proprietary details are withheld on purpose. Four are documented in full as case studies.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:list -->
	<ul class="wp-block-list">
		<?php foreach ( $maulik_portfolio_selected_projects as $maulik_portfolio_selected_project ) : ?>
			<!-- wp:list-item -->
			<li><?php echo esc_html( $maulik_portfolio_selected_project ); ?></li>
			<!-- /wp:list-item -->
		<?php endforeach; ?>
	</ul>
	<!-- /wp:list -->

	<!-- wp:buttons {"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( null, '/projects/' ) ); ?>"><?php echo esc_html__( 'View the archive', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</section>
<!-- /wp:group -->