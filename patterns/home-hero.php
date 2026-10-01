<?php
/**
 * Title: Home Hero Section
 * Slug: maulik-portfolio/home-hero
 * Categories: maulik-portfolio-home, featured
 * Description: Homepage section 01, the opening hero with the single h1, on the 48px dark grid surface. Insert on a page that has no other h1.
 * Inserter: yes
 *
 * Section 01 of 08. The dark grid surface and the only h1 in the pattern set, so
 * a page built from these patterns has one top level heading and no skipped
 * levels below it.
 *
 * EVERY WORD IS VERBATIM FROM templates/front-page.html. The template is the
 * approved live rendering, so it is the source of truth for copy. Nothing here
 * is shortened, expanded or rewritten, and no metric, client name, project count
 * or testimonial has been added. If the two ever disagree, the template wins.
 *
 * WHAT SURVIVED FROM THE OLD VERSION OF THIS PATTERN. Almost nothing. The eyebrow
 * used to read "Software Engineering Portfolio - WordPress and PHP", the
 * supporting paragraph was different, and the bottom strip read "Role: WordPress
 * Developer at Qrolic Technologies". The template carries the same information
 * with different words, so the template's words are used.
 *
 * Qrolic Technologies is spelled with one c after Qro, which is the correct
 * spelling and the one the resume gets wrong.
 *
 * WHY THE STYLING NAMES LOOK REPETITIVE. "is-style-" is not decoration, it is the
 * class WordPress emits when an editor applies a block style variation registered
 * from styles/surface-dark-grid.json. Writing the plain name instead produces a
 * class that no stylesheet targets, which is why the grid textures were rendering
 * as flat colour. The surface classes and the rhythm classes are both style
 * variations on core/group, so they live in the className of the group.
 *
 * PATTERNS RUN ON init, NOT AT RENDER TIME. When this file is included the main
 * query has not run, so the conditional tags, the queried post accessor, the loop
 * and the title accessors are all unavailable and are not used. What does work is
 * esc_html__(), esc_url(), esc_attr(), get_home_url() and iterating this file's
 * own arrays.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"tagName":"section","className":"is-style-section is-style-surface-dark-grid","anchor":"hero","ariaLabelledby":"heading-hero","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section is-style-surface-dark-grid" id="hero" aria-labelledby="heading-hero">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '01 · Identity and Focus', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":1,"anchor":"heading-hero"} -->
	<h1 class="wp-block-heading" id="heading-hero"><?php echo esc_html__( 'Maulik Bhalodiya', 'maulik-portfolio' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"className":"hero-subhead"} -->
	<p class="hero-subhead"><?php echo esc_html__( 'WordPress Developer | PHP Engineer | Custom Plugin Developer', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph -->
	<p><?php echo esc_html__( 'I work on WordPress as an engineer rather than as a page builder. That means custom plugins, backend systems, integrations and the decisions behind them, built to hold up when the requirement is technical rather than visual.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"className":"hero-actions","layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-buttons hero-actions">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#selected-work"><?php echo esc_html__( 'Explore My Work', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->

		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( null, '/resume/' ) ); ?>"><?php echo esc_html__( 'View Resume', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->

	<!-- wp:paragraph {"typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p><?php echo esc_html__( 'WordPress Developer at Qrolic Technologies', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->
</section>
<!-- /wp:group -->