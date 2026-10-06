<?php
/**
 * Title: Home About Section
 * Slug: maulik-portfolio/home-about
 * Categories: maulik-portfolio-home
 * Description: Homepage section 07, the identity column and the two snapshot cards, on the flat base surface.
 * Inserter: no
 *
 * Section 07 of 08. Flat #0A0A0B with a 1px #262626 rule under it, which is what
 * the design states. It previously wore the 48px dark grid variation, which put a
 * texture on a section the design leaves flat.
 *
 * THE SHAPE CHANGED. The previous version was a single flat column of four
 * paragraphs, an identity line nowhere and no cards. The design has a two column
 * body: the eyebrow, the h2, a mono identity line and one button on the left at
 * five of twelve columns, and exactly three paragraphs plus two snapshot cards on
 * the right at seven.
 *
 * THREE PARAGRAPHS, NOT FOUR. The fourth paragraph said the full profile is on
 * the about page, and the left column now carries a button that says exactly
 * that, so the sentence is gone rather than restated.
 *
 * THE B.TECH LINE MOVED INTO A CARD, IT WAS NOT DELETED. Ganpat University,
 * 2021 to 2025, is now the value line of the Education snapshot card rather than
 * prose, which is where the design puts it.
 *
 * THE LIGHT SURFACES ARE GONE FROM THE HOMEPAGE BODY. Amber on a light ground
 * fails contrast badly, so nothing amber is text on a light section. Restoring
 * the flat dark ground removes the question rather than answering it.
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
 * with it to 88.3%.
 * This copy drops the trailing arrow SVG the live page carries.
 * Offering it in the inserter would let an owner put a section on a page the
 * design never showed. It is hidden rather than deleted because the markup and
 * this comment are the record of what was decided, and deleting them loses
 * that record.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"anchor":"about","ariaLabelledby":"heading-about","backgroundColor":"base","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section hz-section hz-about has-base-background-color has-background" id="about" aria-labelledby="heading-about">
	<!-- wp:group {"className":"hz-about__body","layout":{"type":"default"}} -->
	<div class="wp-block-group hz-about__body">
		<!-- wp:group {"className":"hz-about__lead","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-about__lead">
			<!-- wp:group {"className":"hz-stack-4","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-stack-4">
				<!-- wp:paragraph {"className":"hz-eyebrow"} -->
				<p class="hz-eyebrow"><?php echo esc_html__( '07 · Engineering Profile', 'maulik-portfolio' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":2,"anchor":"heading-about","className":"hz-section__title"} -->
				<h2 class="wp-block-heading hz-section__title" id="heading-about"><?php echo esc_html__( 'About Me', 'maulik-portfolio' ); ?></h2>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"hz-about__identity"} -->
				<p class="hz-about__identity"><?php echo esc_html__( 'WordPress Developer | PHP Engineer | Custom Plugin Developer', 'maulik-portfolio' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:group {"className":"hz-about__cta","layout":{"type":"default"}} -->
				<div class="wp-block-group hz-about__cta">
					<!-- wp:buttons {"className":"hz-actions","layout":{"type":"flex","flexWrap":"wrap"}} -->
					<div class="wp-block-buttons hz-actions">
						<!-- wp:button {"className":"hz-btn-outline"} -->
						<div class="wp-block-button hz-btn-outline">
							<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( null, '/about/' ) ); ?>"><?php echo esc_html__( 'Read Full About and Experience Page', 'maulik-portfolio' ); ?></a>
						</div>
						<!-- /wp:button -->
					</div>
					<!-- /wp:buttons -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
		<!-- wp:group {"className":"hz-about__main","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-about__main">
			<!-- wp:paragraph {"className":"hz-body-copy"} -->
			<p class="hz-body-copy"><?php echo esc_html__( 'I am a WordPress and PHP developer focused on building custom backend functionality rather than only visual website templates. My daily engineering work centers on custom WordPress plugins, REST APIs, database operations, third party integrations, payment workflows and application security.', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"hz-body-copy"} -->
			<p class="hz-body-copy"><?php echo esc_html__( 'In my professional role as a WordPress Developer at Qrolic Technologies, I architect and maintain production systems involving cross domain payment communication with HMAC SHA 256 signing, PHP Sodium field encryption, asynchronous document OCR pipelines and role based application workflows.', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"hz-body-copy"} -->
			<p class="hz-body-copy"><?php echo esc_html__( 'Alongside my professional role, I am independently building RankKernel, an open source SEO and schema engine for WordPress designed around modular PHP architecture and clean WordPress core integration.', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:group {"className":"hz-snap-grid","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-snap-grid">
				<!-- wp:group {"className":"hz-snap","layout":{"type":"default"}} -->
				<div class="wp-block-group hz-snap">
					<!-- wp:paragraph {"className":"hz-label"} -->
					<p class="hz-label"><?php echo esc_html__( 'Education', 'maulik-portfolio' ); ?></p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"hz-snap__value"} -->
					<p class="hz-snap__value"><?php echo esc_html__( 'B.Tech in Computer Engineering', 'maulik-portfolio' ); ?></p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"hz-snap__meta"} -->
					<p class="hz-snap__meta"><?php echo esc_html__( 'Ganpat University · 2021 to 2025', 'maulik-portfolio' ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
				<!-- wp:group {"className":"hz-snap","layout":{"type":"default"}} -->
				<div class="wp-block-group hz-snap">
					<!-- wp:paragraph {"className":"hz-label"} -->
					<p class="hz-label"><?php echo esc_html__( 'Independent Open Source', 'maulik-portfolio' ); ?></p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"hz-snap__value"} -->
					<p class="hz-snap__value"><?php echo esc_html__( 'RankKernel', 'maulik-portfolio' ); ?></p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"hz-snap__meta"} -->
					<p class="hz-snap__meta"><?php echo esc_html__( 'Personal Project · IN DEVELOPMENT', 'maulik-portfolio' ); ?></p>
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