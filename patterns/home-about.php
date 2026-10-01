<?php
/**
 * Title: Home About Section
 * Slug: maulik-portfolio/home-about
 * Categories: maulik-portfolio-home
 * Description: Homepage section 07, the engineering profile, on the warm editorial surface.
 * Inserter: yes
 *
 * Section 07 of 08. The light editorial surface, which is the rhythm's one break in
 * the dark run and the reason the run reads as a rhythm at all.
 *
 * THE LIGHT SURFACE CHANGES ONE RULE. Amber on a light ground fails contrast
 * badly, roughly 1.5:1 on #F8F7F0. So nothing on this section is amber text. The
 * eyebrow square keeps its amber because it is a filled block, not text, and the
 * text here is near-black. If an accent coloured string is ever added to this
 * section it has to use the darkened accent preset, not the amber one.
 *
 * B.Tech in Computer Engineering, Ganpat University, 2021 to 2025. Those are the
 * verified education dates. The knowsAbout list is the ten entries from the
 * design's structured data, unchanged.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The ten verified areas of knowledge from the design's structured data.
 */
$maulik_portfolio_knows_about = array(
	__( 'WordPress Engineering', 'maulik-portfolio' ),
	__( 'PHP Development', 'maulik-portfolio' ),
	__( 'Custom WordPress Plugin Development', 'maulik-portfolio' ),
	__( 'REST API Integration', 'maulik-portfolio' ),
	__( 'Payment Integration', 'maulik-portfolio' ),
	__( 'WordPress Security', 'maulik-portfolio' ),
	__( 'Gutenberg Block Development', 'maulik-portfolio' ),
	__( 'WooCommerce', 'maulik-portfolio' ),
	__( 'MySQL', 'maulik-portfolio' ),
	__( 'HMAC SHA 256', 'maulik-portfolio' ),
);

?>
<!-- wp:group {"tagName":"section","className":"section section-about has-paper-background-color has-background","anchor":"about","ariaLabelledby":"heading-about","backgroundColor":"paper","layout":{"type":"constrained"}} -->
<section class="wp-block-group section section-about has-paper-background-color has-background" id="about" aria-labelledby="heading-about">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '07 · Engineering Profile', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-about"} -->
	<h2 class="wp-block-heading" id="heading-about"><?php echo esc_html__( 'About Me', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"className":"section-lede"} -->
	<p class="section-lede"><?php echo esc_html__( 'WordPress Developer at Qrolic Technologies. I write plugins, backend systems and integrations, and I care about the parts that are hard to check.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:group {"tagName":"div","className":"about-grid","layout":{"type":"constrained"}} -->
	<div class="wp-block-group about-grid">
		<!-- wp:group {"tagName":"div","className":"about-block","layout":{"type":"constrained"}} -->
		<div class="wp-block-group about-block">
			<!-- wp:heading {"level":3,"className":"about-block__title"} -->
			<h3 class="wp-block-heading about-block__title"><?php echo esc_html__( 'Background', 'maulik-portfolio' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html__( 'B.Tech in Computer Engineering at Ganpat University, 2021 to 2025.', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"tagName":"div","className":"about-block","layout":{"type":"constrained"}} -->
		<div class="wp-block-group about-block">
			<!-- wp:heading {"level":3,"className":"about-block__title"} -->
			<h3 class="wp-block-heading about-block__title"><?php echo esc_html__( 'Works with', 'maulik-portfolio' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:list {"className":"about-block__list"} -->
			<ul class="wp-block-list about-block__list">
				<?php foreach ( $maulik_portfolio_knows_about as $maulik_portfolio_knows_about_item ) : ?>
					<!-- wp:list-item -->
					<li><?php echo esc_html( $maulik_portfolio_knows_about_item ); ?></li>
					<!-- /wp:list-item -->
				<?php endforeach; ?>
			</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"tagName":"div","className":"about-block","layout":{"type":"constrained"}} -->
		<div class="wp-block-group about-block">
			<!-- wp:heading {"level":3,"className":"about-block__title"} -->
			<h3 class="wp-block-heading about-block__title"><?php echo esc_html__( 'Independent work', 'maulik-portfolio' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html__( 'RankKernel is my own open source project. It is not client work and it is not affiliated with my employer.', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"className":"about-block__link","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
			<p class="about-block__link"><a href="<?php echo esc_url( get_home_url( null, '/rankkernel/' ) ); ?>"><?php echo esc_html__( 'About RankKernel', 'maulik-portfolio' ); ?></a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
