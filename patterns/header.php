<?php
/**
 * Title: Header
 * Slug: maulik-portfolio/header
 * Categories: header, maulik-portfolio-shell
 * Description: The site header, with the six navigation items.
 * Inserter: yes
 * Block Types: core/template-part/header
 *
 * THE TRANSLATABLE SOURCE OF parts/header.html.
 *
 * A template part cannot hold words, because a block theme template part is a
 * .html file and is parsed rather than executed: no PHP, no esc_html__(), no
 * get_bloginfo(). The structure lives in the part and every string lives here,
 * where translation is actually available.
 *
 * PATTERNS RUN ON init, NOT AT RENDER TIME. That is the constraint that catches
 * people. When this file is included the main query has not been run, so the
 * conditional tags, the queried post accessor, the loop and the title accessors
 * are all meaningless and must not appear here. What does work: esc_html__(),
 * esc_url(), esc_attr(), get_home_url(), wp_date() and iterating this file's own
 * arrays.
 *
 * SIX ITEMS. Home, RankKernel, Projects, About, Resume, Contact. There is
 * deliberately no Writing or Blog link. Posts are supported by the theme but the
 * site does not advertise a blog, so the capability exists without being
 * announced.
 *
 * A SUBTLE ONE THAT BITES EVERYONE. The navigation-link labels sit INSIDE the
 * JSON in the block comment, so they must not be passed through esc_attr().
 * esc_attr() turns every double quote into &quot;, and &quot; is not valid
 * JSON, so Core silently fails to parse the block and the menu does not render.
 * This is also why wp_json_encode() is not used here: it emits escaped forward
 * slashes for URLs, which is noise, and it produces bare values that then have
 * to be escaped, which lands straight back in the same trap. Core's own
 * patterns put the PHP between the JSON quotes and let the output be the value.
 * The rendered markup is checked for this in CI rather than assumed.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Navigation items for the header.
 *
 * An own array iterated here is safe. Reaching for the queried object is not.
 *
 * @var array<int, array{label: string, path: string}> $maulik_portfolio_nav_items Header navigation items.
 */
$maulik_portfolio_nav_items = array(
	array(
		'label' => __( 'Home', 'maulik-portfolio' ),
		'path'  => '/',
	),
	array(
		'label' => __( 'RankKernel', 'maulik-portfolio' ),
		'path'  => '/rankkernel/',
	),
	array(
		'label' => __( 'Projects', 'maulik-portfolio' ),
		'path'  => '/projects/',
	),
	array(
		'label' => __( 'About', 'maulik-portfolio' ),
		'path'  => '/about/',
	),
	array(
		'label' => __( 'Resume', 'maulik-portfolio' ),
		'path'  => '/resume/',
	),
	array(
		'label' => __( 'Contact', 'maulik-portfolio' ),
		'path'  => '/contact/',
	),
);

?>
<!-- wp:group {"tagName":"header","className":"site-header","layout":{"type":"constrained"}} -->
<header class="wp-block-group site-header">
	<!-- wp:group {"tagName":"div","className":"site-header__inner","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group site-header__inner">
		<!-- wp:site-title {"level":0,"className":"site-header__title"} /-->

		<!-- wp:navigation {"className":"site-header__nav","overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"right"}} -->
		<nav class="wp-block-navigation site-header__nav">
			<?php foreach ( $maulik_portfolio_nav_items as $maulik_portfolio_nav_item ) : ?>
				<?php
				$maulik_portfolio_nav_label = $maulik_portfolio_nav_item['label'];
				$maulik_portfolio_nav_url   = get_home_url( null, $maulik_portfolio_nav_item['path'] );
				?>
				<!-- wp:navigation-link {"label":"<?php echo esc_html( $maulik_portfolio_nav_label ); ?>","type":"custom","url":"<?php echo esc_url( $maulik_portfolio_nav_url ); ?>","kind":"custom","isTopLevelLink":true} /-->
			<?php endforeach; ?>
		</nav>
		<!-- /wp:navigation -->
	</div>
	<!-- /wp:group -->
</header>
<!-- /wp:group -->