<?php
/**
 * Title: Home About Section
 * Slug: maulik-portfolio/home-about
 * Categories: maulik-portfolio-home
 * Description: Homepage section 07, the four paragraph engineering profile, on the 48px dark grid surface.
 * Inserter: yes
 *
 * Section 07 of 08. Back to the dark grid surface, and the full section rhythm,
 * because this is the last of the long prose sections before the closing call to
 * action.
 *
 * THE LIGHT SURFACES CHANGE ONE RULE, AND THIS SECTION IS NOT ONE OF THEM.
 * Amber on a light ground fails contrast badly, roughly 1.5:1 on #F8F7F0, so
 * nothing amber is text on section 05 or section 06. The eyebrow square keeps its
 * amber because it is a filled block rather than text. If an accent coloured
 * string is ever added to a light section it has to use the darkened accent
 * preset, not the amber one.
 *
 * FOUR PARAGRAPHS, NOT THREE HEADED BLOCKS. The previous version of this pattern
 * rendered three "about-block" groups titled Background, Works with and
 * Independent work, and the Works with block carried a ten item knowsAbout list.
 * All of that content is real and most of it lives on the about page, which is a
 * better home for it. This section is four paragraphs and one link, and the
 * approved homepage says so.
 *
 * THE B.TECH LINE MOVED, IT WAS NOT DELETED. Ganpat University, 2021 to 2025,
 * is on the about page and in the resume. The approved homepage summary does not
 * carry it, so this section does not either.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"tagName":"section","className":"is-style-section is-style-surface-dark-grid","anchor":"about","ariaLabelledby":"heading-about","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section is-style-surface-dark-grid" id="about" aria-labelledby="heading-about">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '07 · Engineering Profile', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-about"} -->
	<h2 class="wp-block-heading" id="heading-about"><?php echo esc_html__( 'About Me', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php echo esc_html__( 'I am a WordPress and PHP developer based in Rajkot, Gujarat. I work on the parts of a site that decide whether it stays maintainable: plugin structure, data handling, integrations and access control.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph -->
	<p><?php echo esc_html__( 'Most of my work arrives anonymised. Client names, domains and credentials are stripped out before a project can be described, which is the right thing to do and also makes the engineering the only part worth reading.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph -->
	<p><?php echo esc_html__( 'I also maintain RankKernel, an independent open source SEO and schema engine. It is my own project and it is not employer or client work.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph -->
	<p><?php echo esc_html__( 'The full profile, including education and a longer technical account, is on the about page.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( null, '/about/' ) ); ?>"><?php echo esc_html__( 'Read the profile', 'maulik-portfolio' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</section>
<!-- /wp:group -->