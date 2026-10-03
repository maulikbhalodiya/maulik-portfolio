<?php
/**
 * Title: Header Channel Links
 * Slug: maulik-portfolio/channel-links
 * Categories: maulik-portfolio-shell, maulik-portfolio-home
 * Description: The LinkedIn and GitHub icon row. Two of the three channels.
 * Inserter: yes
 *
 * THE HEADER ICON ROW, AND IT CARRIES TWO OF THE DESIGN'S THREE CHANNELS.
 *
 * The design has three: LinkedIn, GitHub and Mail. This file still emits the
 * first two only, and that is deliberate rather than a leftover. The third
 * channel in the live header is emitted by patterns/header.php, from a shared
 * closure that renders both the bar's copy and the mobile panel's copy, so that
 * the two cannot drift. Email cannot be authored here as a core/social-link at
 * all: that block keys its icon off the service name against Core's fixed table
 * of social services, and mail is not in it, so an authored service "mail"
 * yields an empty anchor with no mark in it. The header therefore emits the
 * Email entry as a wp:html list item carrying the same
 * wp-social-link wp-social-link site-header__social-link classes
 * _site-header.scss keys the box and the mark on, which is why it inherits the
 * identical box and mark as the two siblings this file emits.
 *
 * THE NO EMAIL RULE THIS FILE USED TO STATE IS SUPERSEDED. The earlier version
 * of this comment held that the mail affordance had to go because the address
 * was published and spam listed inside 48 hours, and that there was no email
 * affordance anywhere. The owner was told the address had been spam listed once
 * in production and ruled that it ships anyway, because the design's Email link
 * is part of the design and substituting something else for it is itself a
 * substitution. That decision is settled and is not to be reopened.
 *
 * THERE IS STILL NO EMAIL ADDRESS IN THIS FILE. Not in a URL, not in an
 * aria-label, not in a title attribute. That part of the rule holds, and it is
 * why this file stays free of the address while the theme publishes it elsewhere.
 * Contact runs through the contact page.
 *
 * core/social-links carries the icons and handles accessibility itself: it renders
 * the accessible name from the service name when no label is given, so the label
 * attribute here is belt and braces rather than the only source.
 *
 * ON THE STYLE VOCABULARY: this file carries no surface or rhythm class at all, so
 * there was no legacy name here to convert. The row is a bare core/social-links
 * list with one component class, "channel-links", which is a component identifier
 * and not a surface. It is left alone deliberately. The block inherits the
 * surface of whatever it is inserted into, which is the header, so hard coding a
 * texture here would freeze a surface choice that belongs to the placement.
 *
 * THE CI GATE WAS NARROWED, NOT REMOVED. The four workflows under .github/ run
 * a repo wide grep for mailto and for anything matching an address pattern on
 * pull request, and each of them now permits exactly one address,
 * maulikbhalodiya9999@gmail.com, and rejects every other mailto and every other
 * raw address. ci.yml, ai-review.yml, ai-audit.yml and live-deploy.yml all carry
 * that same permitted value, so the rule the gate enforces now is one address in
 * the theme and not a blanket ban on mail. This file is where that gate would
 * fail first if a second address were ever added.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The two verified external channels.
 */
$maulik_portfolio_channels = array(
	array(
		'service' => 'linkedin',
		'label'   => __( 'LinkedIn', 'maulik-portfolio' ),
		'url'     => 'https://www.linkedin.com/in/maulik-bhalodiya-',
	),
	array(
		'service' => 'github',
		'label'   => __( 'GitHub', 'maulik-portfolio' ),
		'url'     => 'https://github.com/maulikbhalodiya',
	),
);

?>
<!-- wp:social-links {"className":"channel-links","layout":{"type":"flex","justifyContent":"left"}} -->
<ul class="wp-block-social-links channel-links">
	<?php foreach ( $maulik_portfolio_channels as $maulik_portfolio_channel ) : ?>
		<!-- wp:social-link {"service":"<?php echo esc_attr( $maulik_portfolio_channel['service'] ); ?>","label":"<?php echo esc_attr( $maulik_portfolio_channel['label'] ); ?>","url":"<?php echo esc_url( $maulik_portfolio_channel['url'] ); ?>","rel":"noopener noreferrer"} /-->
	<?php endforeach; ?>
</ul>
<!-- /wp:social-links -->
