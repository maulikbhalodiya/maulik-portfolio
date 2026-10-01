<?php
/**
 * Title: Header Channel Links
 * Slug: maulik-portfolio/channel-links
 * Categories: maulik-portfolio-shell, maulik-portfolio-home
 * Description: The LinkedIn and GitHub icon row. Two channels, no email.
 * Inserter: yes
 *
 * THE HEADER ICON ROW, AND IT CARRIES EXACTLY TWO CHANNELS.
 *
 * The prototype had three, the third being a mail icon pointing at an email
 * address. That address was published and spam listed inside 48 hours, and a mail
 * icon promises a mail client, so the affordance was a lie as well as a liability.
 * It is gone. What
 * remains is LinkedIn and GitHub, which is what the design phase checklist asks for
 * and what the section on compliance defects in the design spec requires.
 *
 * THERE IS NO EMAIL ADDRESS IN THIS FILE. Not in a URL, not in an aria-label, not
 * in a title attribute. Contact runs through the contact page.
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
 * THE NO EMAIL RULE IS CHECKED IN CI, NOT ONLY ASSERTED HERE. A repo wide grep for
 * mailto and for anything matching an address pattern runs on pull request, and
 * this file is where it would fail first if a third channel were ever added back.
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
		'url'     => 'https://www.linkedin.com/in/maulikbhalodiya/',
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
