<?php
/**
 * Title: Home Hero
 * Slug: maulik-portfolio/home-hero
 * Categories: maulik-portfolio-home, featured
 * Description: Homepage opening section on the dark-grid surface, with the single h1.
 * Inserter: yes
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"tagName":"section","className":"hero dark-grid","anchor":"hero","ariaLabel":"<?php echo esc_attr__( 'Software Engineering Portfolio, WordPress and PHP', 'maulik-portfolio' ); ?>","layout":{"type":"constrained"}} -->
<section class="wp-block-group hero dark-grid" id="hero" aria-label="<?php echo esc_attr__( 'Software Engineering Portfolio, WordPress and PHP', 'maulik-portfolio' ); ?>">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( 'Software Engineering Portfolio · WordPress and PHP', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading"><?php echo esc_html__( 'Maulik Bhalodiya', 'maulik-portfolio' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"className":"hero-subhead"} -->
	<p class="hero-subhead"><?php echo esc_html__( 'WordPress Developer | PHP Engineer | Custom Plugin Developer', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph {"className":"hero-supporting"} -->
	<p class="hero-supporting"><?php echo esc_html__( 'I build custom WordPress solutions, backend systems, plugins and API integrations for complex technical requirements.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"className":"hero-actions","layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-buttons hero-actions">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#selected-work"><?php echo esc_html__( 'Explore My Work', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( '/resume/' ) ); ?>"><?php echo esc_html__( 'View Resume', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->

	<!-- wp:paragraph {"className":"hero-meta","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="hero-meta"><?php echo esc_html__( 'Role: WordPress Developer at Qrolic Technologies', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->
</section>
<!-- /wp:group -->