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
 * THIS FILE AND THE HERO OF content/pages/home.html ARE THE SAME HERO. They are
 * kept identical deliberately. The page renders from post_content, so the copy in
 * content/pages/home.html is what a visitor sees and the copy here is what an
 * editor gets when they insert the pattern, and the two used to drift: this one
 * was flat, with no grid, no columns and no identity cluster, and it carried the
 * old eyebrow, the old secondary link and a role line with no prefix on it. Every
 * word, class, icon, link and block delimiter below is now the same as the page,
 * and the only differences are the PHP that prints a translatable string and the
 * home URL for the resume page. If one changes, the other changes with it.
 *
 * THE SECONDARY CALL TO ACTION IS A LINK HERE AND NOT A BUTTON. The design states
 * it as a <button> that opens the resume in a modal, which is a decision the React
 * prototype could make because it owns a router and a modal and this theme owns
 * neither. The theme already has a resume page at /resume/, so the link reaches
 * the same content, works without JavaScript, is reachable by keyboard, and
 * carries an href that a crawler and a reader without a mouse can both follow. A
 * button with no handler would reach nothing at all.
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
<!-- wp:group {"tagName":"section","className":"is-style-section is-style-surface-dark-grid hero-section","anchor":"hero","ariaLabelledby":"heading-hero","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section is-style-surface-dark-grid hero-section" id="hero" aria-labelledby="heading-hero">
	<!-- wp:group {"className":"hero__grid","layout":{"type":"constrained"}} -->
	<div class="wp-block-group hero__grid">
		<!-- wp:group {"className":"hero__col","layout":{"type":"constrained"}} -->
		<div class="wp-block-group hero__col">
			<!-- wp:group {"className":"hero__cluster","layout":{"type":"constrained"}} -->
			<div class="wp-block-group hero__cluster">
				<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
				<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( 'Software Engineering Portfolio · WordPress and PHP', 'maulik-portfolio' ); ?></span></p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":1,"anchor":"heading-hero"} -->
				<h1 class="wp-block-heading" id="heading-hero"><?php echo esc_html__( 'Maulik Bhalodiya', 'maulik-portfolio' ); ?></h1>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"hero-subhead"} -->
				<p class="hero-subhead"><?php echo esc_html__( 'WordPress Developer | PHP Engineer | Custom Plugin Developer', 'maulik-portfolio' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"className":"hero__lede"} -->
				<p class="hero__lede"><?php echo esc_html__( 'I build custom WordPress solutions, backend systems, plugins and API integrations for complex technical requirements.', 'maulik-portfolio' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
			<!-- wp:buttons {"className":"hero-actions","layout":{"type":"flex","flexWrap":"wrap"}} -->
			<div class="wp-block-buttons hero-actions">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#selected-work"><span><?php echo esc_html__( 'Explore My Work', 'maulik-portfolio' ); ?></span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hz-icon" aria-hidden="true" focusable="false"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg></a></div>
				<!-- /wp:button -->
				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( null, '/resume/' ) ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hz-icon" aria-hidden="true" focusable="false"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg><span><?php echo esc_html__( 'View Resume', 'maulik-portfolio' ); ?></span></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
			<!-- wp:group {"className":"hero__strip","layout":{"type":"constrained"}} -->
			<div class="wp-block-group hero__strip">
				<!-- wp:group {"className":"hero__strip-links","layout":{"type":"constrained"}} -->
				<div class="wp-block-group hero__strip-links"><a class="hero__strip-link" href="https://github.com/maulikbhalodiya" target="_blank" rel="noopener noreferrer" aria-label="GitHub Profile"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hz-icon" aria-hidden="true" focusable="false"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"></path><path d="M9 18c-4.51 2-5-2-7-2"></path></svg><span><?php echo esc_html__( 'GitHub', 'maulik-portfolio' ); ?></span></a> <a class="hero__strip-link" href="https://www.linkedin.com/in/maulikbhalodiya/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn Profile"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hz-icon" aria-hidden="true" focusable="false"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle></svg><span><?php echo esc_html__( 'LinkedIn', 'maulik-portfolio' ); ?></span></a> <a class="hero__strip-link" href="mailto:maulikbhalodiya9999@gmail.com" aria-label="Send Email"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hz-icon" aria-hidden="true" focusable="false"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg><span><?php echo esc_html__( 'Email', 'maulik-portfolio' ); ?></span></a></div>
				<!-- /wp:group -->
				<!-- wp:paragraph {"className":"hero__role","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
				<p class="hero__role"><?php echo esc_html__( 'Role: WordPress Developer at Qrolic Technologies', 'maulik-portfolio' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
		<!-- wp:group {"className":"hero__col hero__col--visual","layout":{"type":"constrained"}} -->
		<div class="wp-block-group hero__col hero__col--visual">
			<!-- wp:maulik-portfolio/hero-ecosystem /-->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->