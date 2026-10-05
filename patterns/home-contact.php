<?php
/**
 * Title: Home Contact Section
 * Slug: maulik-portfolio/home-contact
 * Categories: maulik-portfolio-home
 * Description: Homepage section 08, the hiring call to action on an inner card over pure black, with resume, LinkedIn and email actions.
 * Inserter: yes
 *
 * Section 08 of 08, and the only section with no texture on it. It is the closing
 * band and it is deliberately flat: pure black behind a #0A0A0B card with a 1px
 * #262626 border at p-8 sm:p-12 md:p-16.
 *
 * THE INNER CARD IS NEW AND IT IS THE POINT OF THE SECTION. The previous version
 * put the eyebrow, the heading, the lede and two buttons straight onto black with
 * no card around them, and capped the measure at 576px. The design wraps
 * everything in a bordered card and caps the content at 768px. Without the card
 * the band is a black rectangle with text on it.
 *
 * THE HEADING IS JOB SEEKING LANGUAGE AND IT IS CORRECT. "Looking for a WordPress
 * or PHP developer?" is a recruiter speaking. It is the opposite of the pattern
 * this theme bans, and it is framed correctly. Do not soften it.
 *
 * THE OLD NO MAIL RULE IS SUPERSEDED, 2026-10-02. The reasoning this comment used
 * to carry, kept because it is why the reversal was hard: the design's third button
 * is "Email Me" behind a mail icon, and this file substituted a link to the contact
 * page form, because the prototype's address was published and spam listed inside
 * 48 hours and a mail icon promises a mail client the site did not offer. That
 * reasoning is no longer in force. The owner was told the address had been
 * spam-listed in production and ruled that it ships anyway, on the standing rule
 * that design or colour related work uses exactly what the demo design uses, with
 * no substitutions.
 *
 * THE EMAIL RULE IS REVERSED, 2026-10-02. Section 08 no longer owes the visitor
 * a route that avoids the mail client, and the design's third action is a mailto,
 * so the design ships. The one address,
 * maulikbhalodiya9999@gmail.com, is published in four places: the footer Connect
 * column, the section 08 Email Me call to action, the hero strip and the mobile
 * menu channel row. It is the only address in the theme.
 *
 * CI DOES NOT LIFT THE BLANKET BAN. The mailto and raw address gates were narrowed
 * rather than removed: they permit exactly this one address and still reject any
 * other, so an address nobody decided to publish fails the build. Everything else
 * is unchanged. Contact is still the form with an intent dropdown, and a
 * submission still cannot be delivered.
 *
 * WHAT THIS FILE ACTUALLY RENDERS. The three actions below are the design's three
 * actions, in the design's order, with the design's icons: View Resume, Connect on
 * LinkedIn, Email Me. The third action is a mailto to the one permitted address,
 * with the Mail glyph before the label, and it carries neither target nor rel
 * because a mailto opens a local client rather than a browsing context.
 *
 * THIS FILE CAUGHT UP WITH THE DESIGN, 2026-10-02. It used to render a third action
 * of "Start a conversation" pointing at /contact/, while the published homepage
 * already carried the design's Email Me mailto. The pattern and the page it
 * represents now agree, and the page markup in content/pages/home.html is the
 * reference: classes, icon attributes, aria attributes and the hz-hiring__actions
 * spacing wrapper are mirrored from it rather than re-invented here.
 *
 * WHY backgroundColor SURVIVES THE VOCABULARY CHANGE. has-black-background-color
 * is not a legacy name. It is the class WordPress emits for the backgroundColor
 * attribute, so removing it while keeping the attribute would produce markup that
 * disagrees with what Core renders. is-style-section is a style variation and
 * does get the prefix.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The two published external channels, which must match channel-links.php.
 */
$maulik_portfolio_cta_channels = array(
	array(
		'label' => __( 'Connect on LinkedIn', 'maulik-portfolio' ),
		'url'   => 'https://www.linkedin.com/in/maulik-bhalodiya-',
	),
);

/**
 * The one address this theme permits, the same value as patterns/footer.php and
 * patterns/header.php. It is a mailto, so esc_url() is the right wrapper: it is
 * the scheme and the address that are passed through, and the anchor carries
 * neither target nor rel, because a mailto opens a local client rather than a
 * browsing context. Adding a second address here fails CI.
 */
$maulik_portfolio_cta_mailto = 'mailto:maulikbhalodiya9999@gmail.com';

?>
<!-- wp:group {"anchor":"hiring-cta","ariaLabelledby":"heading-hiring-cta","className":"is-style-section section-cta hz-section hz-hiring is-style-surface-dark-grid","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section section-cta hz-section hz-hiring is-style-surface-dark-grid" id="hiring-cta" aria-labelledby="heading-hiring-cta">
	<!-- wp:group {"className":"hz-hiring__card","layout":{"type":"default"}} -->
	<div class="wp-block-group hz-hiring__card">
		<!-- wp:group {"className":"hz-stack-6 hz-hiring__inner","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-stack-6 hz-hiring__inner">
			<!-- wp:paragraph {"className":"hz-eyebrow"} -->
			<p class="hz-eyebrow"><?php echo esc_html__( '08 · Employment Opportunities and Technical Evaluation', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2,"anchor":"heading-hiring-cta","className":"hz-section__title"} -->
			<h2 class="wp-block-heading hz-section__title" id="heading-hiring-cta"><?php echo esc_html__( 'Looking for a WordPress or PHP developer?', 'maulik-portfolio' ); ?></h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"className":"hz-hiring__lede"} -->
			<p class="hz-hiring__lede"><?php echo esc_html__( 'I am interested in software engineering roles involving custom WordPress plugin development, PHP backend architecture, API integrations, payment workflows and technically challenging systems. Review my resume or connect directly to discuss how I can contribute to your engineering team.', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:group {"className":"hz-hiring__actions","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-hiring__actions">
				<!-- wp:buttons {"className":"hz-actions","layout":{"type":"flex","flexWrap":"wrap"}} -->
				<div class="wp-block-buttons hz-actions">
					<!-- wp:button {"className":"hz-btn-amber hz-btn-lg"} -->
					<div class="wp-block-button hz-btn-amber hz-btn-lg">
						<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( get_home_url( null, '/resume/' ) ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hz-icon" aria-hidden="true" focusable="false"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg><span><?php echo esc_html__( 'View Resume', 'maulik-portfolio' ); ?></span></a>
					</div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
				<?php foreach ( $maulik_portfolio_cta_channels as $maulik_portfolio_cta_channel ) : ?>
					<a class="hz-link hz-btn-dark hz-btn-lg" href="<?php echo esc_url( $maulik_portfolio_cta_channel['url'] ); ?>" target="_blank" rel="noopener noreferrer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hz-icon" aria-hidden="true" focusable="false"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect width="4" height="12" x="2" y="9"></rect><circle cx="4" cy="4" r="2"></circle></svg><span><?php echo esc_html( $maulik_portfolio_cta_channel['label'] ); ?></span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hz-icon" aria-hidden="true" focusable="false"><path d="M7 7h10v10"></path><path d="M7 17 17 7"></path></svg></a>
				<?php endforeach; ?>
				<a class="hz-link hz-btn-dark hz-btn-lg" href="<?php echo esc_url( $maulik_portfolio_cta_mailto ); ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hz-icon" aria-hidden="true" focusable="false"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg><span><?php echo esc_html__( 'Email Me', 'maulik-portfolio' ); ?></span></a>
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->