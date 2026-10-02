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
 * PATTERNS RUN ON init FOR THEIR METADATA, BUT THIS FILE IS EXECUTED AT RENDER
 * TIME. That distinction is the whole reason the active navigation item can be
 * computed here at all, so it is stated rather than assumed. WordPress reads the
 * file header on init to learn the slug, title and categories, then Core's
 * pattern registry includes the file when the pattern block renders, which is
 * after the main query has been run. The evidence is a probe written into this
 * file during the fix: it recorded did_action( 'wp' ) as 1, get_queried_object_id()
 * as 11 on the front page and as 6 on /about/, and is_front_page() true only on
 * the front page, on every route, on every request. The metadata pass cannot see
 * any of that, so anything below that needs the current route runs in the render
 * pass, which is the pass that needs it.
 *
 * THE ACTIVE NAV ITEM IS DECIDED HERE, FROM THE REQUEST PATH, NOT BY CORE.
 * THE BUG THIS FIXES. core/navigation-link only ever emits current-menu-item and
 * aria-current when the block carries an id, and only ever marks an item active
 * when kind points at a post type. Every link below is authored with kind
 * custom and an absolute URL, because that is what a block theme pattern has to
 * be, so Core attaches no state to any of them. The old stylesheet keyed the
 * active rule on current-menu-item and that rule could never match anything. On
 * the front page the Home item rendered bare, with no class and no aria-current.
 *
 * So the comparison is made here, from the current request path against each
 * item's own path, and two things are emitted for the winner: a class of this
 * theme's own, which the stylesheet keys on, and an id plus kind post-type,
 * which is what Core needs in order to add current-menu-item and aria-current in
 * its own render callback. Both come from the same single comparison, so the two
 * can never disagree. Registering a real nav menu was rejected because the menu
 * has to stay editable in the site editor, and this theme is a block theme.
 *
 * THE MATCHING RULES, AND WHY EACH ONE IS THERE.
 *   Home matches the site root and nothing else, so it never steals the state
 *   from a section on a prefix.
 *   Every other item matches its own path exactly, or a path nested inside it,
 *   so a child page under /projects/ keeps Projects lit rather than going dark.
 *   Both forms are compared with trailing slashes removed on both sides, so
 *   /about and /about/ are the same route and a redirect that drops the slash
 *   does not silently turn the nav grey.
 *   No nav path is a prefix of another, so at most one item can ever match and
 *   exactly zero match on a route that is not in the nav. That is the desired
 *   behaviour on a 404 or on any future page, rather than a bug.
 *
 * SIX ITEMS. Home, RankKernel, Projects, About, Resume, Contact. There is
 * deliberately no Writing or Blog link. Posts are supported by the theme but the
 * site does not advertise a blog, so the capability exists without being
 * announced.
 *
 * THE SUBTLE ONE THAT BITES EVERYONE. The navigation-link labels sit INSIDE the
 * JSON in the block comment, so they must not be passed through esc_attr().
 * esc_attr() turns every double quote into &quot;, and &quot; is not valid
 * JSON, so Core silently fails to parse the block and the menu does not render.
 * This is also why wp_json_encode() is not used here: it emits escaped forward
 * slashes for URLs, which is noise, and it produces bare values that then have
 * to be escaped, which lands straight back in the same trap. Core's own
 * patterns put the PHP between the JSON quotes and let the output be the value.
 * The rendered markup is checked for this in CI rather than assumed.
 *
 * get_home_url() TAKES ITS BLOG ID FIRST. get_home_url( '/resume/' ) passes a
 * path as $blog_id, which is typed int|null, so the path is cast away and every
 * link silently resolves to the bare homepage. That is a live trap in this
 * codebase, which is why every call below is get_home_url( null, $path ).
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Navigation items for the header.
 *
 * An own array iterated here is safe. Reaching for the queried object is not.
 *
 * The class column exists for one item only. core/navigation-link has no color
 * support at all: block.json gives it usesContext for textColor but never reads
 * it from its own attributes, so a per-item textColor would be parsed and then
 * discarded, and the Resume item would render in the default grey. A class is
 * the only thing a navigation-link can actually carry, so that is what it
 * carries, and the amber lives in the stylesheet.
 */
/**
 * The two verified external channels, repeated here for the header icon row.
 *
 * THE DESIGN CARRIES THREE: LINKEDIN, GITHUB AND MAIL. THIS ARRAY CARRIES TWO.
 *
 * patterns/channel-links.php states, and a repo wide CI grep enforces, that this
 * theme has no email affordance anywhere: not a mailto, not an address, not an
 * aria-label and not a mail icon. The address behind the prototype's mail icon
 * was published and spam listed, and the icon promised a mail client that the
 * site deliberately does not offer. So the header takes the same two channels
 * the pattern takes and the design's third affordance is not reproduced. Contact
 * runs through the contact page.
 *
 * The URLs must match patterns/channel-links.php exactly. They are duplicated
 * rather than shared because that pattern registers its own block and returns
 * nothing, and because a pattern cannot be required from another pattern at the
 * point patterns run, which is init.
 */
$maulik_portfolio_header_channels = array(
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

$maulik_portfolio_nav_items = array(
	array(
		'label' => __( 'Home', 'maulik-portfolio' ),
		'path'  => '/',
		'class' => '',
	),
	array(
		'label' => __( 'RankKernel', 'maulik-portfolio' ),
		'path'  => '/rankkernel/',
		'class' => '',
	),
	array(
		'label' => __( 'Projects', 'maulik-portfolio' ),
		'path'  => '/projects/',
		'class' => '',
	),
	array(
		'label' => __( 'About', 'maulik-portfolio' ),
		'path'  => '/about/',
		'class' => '',
	),
	array(
		// Always amber, active or not. It is a different kind of action, not
		// another destination in the same series.
		'label' => __( 'Resume', 'maulik-portfolio' ),
		'path'  => '/resume/',
		'class' => 'site-header__nav-resume',
	),
	array(
		'label' => __( 'Contact', 'maulik-portfolio' ),
		'path'  => '/contact/',
		'class' => '',
	),
);

/*
 * THE CURRENT ROUTE, AS A PATH WITH NO TRAILING SLASH AND A SINGLE LEADING ONE.
 *
 * Left empty on purpose. This file is also executed on requests that are not a
 * front end page view, such as the site editor and the REST routes, and there is
 * no route to compare against on those. An empty value matches no item, so the
 * nav renders with nothing lit rather than with Home lit by accident.
 */
$maulik_portfolio_current_path = '';

$maulik_portfolio_request_uri = isset( $_SERVER['REQUEST_URI'] )
	? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
	: '';

if ( '' !== $maulik_portfolio_request_uri ) {
	$maulik_portfolio_home_path = (string) wp_parse_url( get_home_url(), PHP_URL_PATH );

	if ( '' === $maulik_portfolio_home_path ) {
		$maulik_portfolio_home_path = '/';
	}

	$maulik_portfolio_request_path = (string) wp_parse_url( $maulik_portfolio_request_uri, PHP_URL_PATH );

	/*
	 * A site installed in a subdirectory serves every route under the home path,
	 * so the home path prefix is removed before anything is compared. On a site
	 * at the domain root the home path is a single slash and this never runs.
	 */
	if ( '/' !== $maulik_portfolio_home_path && 0 === strpos( $maulik_portfolio_request_path, $maulik_portfolio_home_path ) ) {
		$maulik_portfolio_request_path = substr( $maulik_portfolio_request_path, strlen( $maulik_portfolio_home_path ) );
	}

	$maulik_portfolio_current_path = '/' . ltrim( $maulik_portfolio_request_path, '/' );

	if ( '/' !== $maulik_portfolio_current_path ) {
		$maulik_portfolio_current_path = rtrim( $maulik_portfolio_current_path, '/' );
	}

	if ( '' === $maulik_portfolio_current_path ) {
		$maulik_portfolio_current_path = '/';
	}
}

?>
<!-- wp:group {"tagName":"header","className":"site-header no-print","layout":{"type":"constrained"}} -->
<header class="wp-block-group site-header no-print">
	<!-- wp:group {"tagName":"div","className":"site-header__inner","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group site-header__inner">
		<!-- wp:site-title {"level":0,"className":"site-header__title"} /-->

		<!-- wp:navigation {"className":"site-header__nav","overlayMenu":"mobile","layout":{"type":"flex","justifyContent":"right"}} -->
		<nav class="wp-block-navigation site-header__nav">
			<?php
			foreach ( $maulik_portfolio_nav_items as $maulik_portfolio_nav_item ) :
				$maulik_portfolio_nav_label = $maulik_portfolio_nav_item['label'];
				$maulik_portfolio_nav_url   = get_home_url( null, $maulik_portfolio_nav_item['path'] );
				$maulik_portfolio_nav_class = $maulik_portfolio_nav_item['class'];

				// The item path reduced to the same shape as the current path, so
				// that one comparison below covers /about, /about/ and /about//.
				$maulik_portfolio_nav_route = '/' . trim( $maulik_portfolio_nav_item['path'], '/' );

				if ( '' === $maulik_portfolio_nav_route ) {
					$maulik_portfolio_nav_route = '/';
				}

				if ( '/' === $maulik_portfolio_nav_route ) {
					$maulik_portfolio_nav_is_current = ( '/' === $maulik_portfolio_current_path );
				} else {
					$maulik_portfolio_nav_is_current = ( $maulik_portfolio_nav_route === $maulik_portfolio_current_path )
						|| ( 0 === strpos( $maulik_portfolio_current_path, $maulik_portfolio_nav_route . '/' ) );
				}

				/*
				 * THE ID IS ONLY RESOLVED FOR THE WINNER, and url_to_postid() is
				 * only called when there is a winner. That is one lookup on a nav
				 * page and none at all on every other page, rather than six
				 * lookups on every request to decide which one matters.
				 *
				 * Core's own render callback compares this id against
				 * get_queried_object_id(), so it agrees with the comparison above
				 * by construction and adds current-menu-item to the list item and
				 * aria-current to the anchor without a filter of any kind.
				 */
				$maulik_portfolio_nav_id   = 0;
				$maulik_portfolio_nav_kind = 'custom';

				if ( $maulik_portfolio_nav_is_current ) {
					$maulik_portfolio_nav_id = (int) url_to_postid( $maulik_portfolio_nav_url );

					if ( $maulik_portfolio_nav_id > 0 ) {
						$maulik_portfolio_nav_kind = 'post-type';
					}
				}

				/*
				 * The class is the stylesheet's hook and is what makes the active
				 * state appear at all. It is emitted regardless of the id above,
				 * so a nav page whose route is nested rather than exact still
				 * lights its section even though Core has no id to compare.
				 */
				if ( $maulik_portfolio_nav_is_current ) {
					$maulik_portfolio_nav_class = trim( $maulik_portfolio_nav_class . ' site-header__nav-item--current' );
				}
				?>
				<!-- wp:navigation-link {"label":"<?php echo esc_html( $maulik_portfolio_nav_label ); ?>","url":"<?php echo esc_url( $maulik_portfolio_nav_url ); ?>","kind":"<?php echo esc_attr( $maulik_portfolio_nav_kind ); ?>","isTopLevelLink":true
				<?php if ( $maulik_portfolio_nav_id > 0 ) : ?>
					,"id":<?php echo (int) $maulik_portfolio_nav_id; ?>
				<?php endif; ?>
				<?php if ( '' !== $maulik_portfolio_nav_class ) : ?>
					,"className":"<?php echo esc_attr( $maulik_portfolio_nav_class ); ?>"
				<?php endif; ?>
				} /-->
			<?php endforeach; ?>
		</nav>
		<!-- /wp:navigation -->

		<!-- wp:group {"tagName":"div","className":"site-header__social","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
		<div class="wp-block-group site-header__social">
			<?php foreach ( $maulik_portfolio_header_channels as $maulik_portfolio_header_channel ) : ?>
				<!-- wp:social-link {"service":"<?php echo esc_attr( $maulik_portfolio_header_channel['service'] ); ?>","label":"<?php echo esc_attr( $maulik_portfolio_header_channel['label'] ); ?>","url":"<?php echo esc_url( $maulik_portfolio_header_channel['url'] ); ?>","rel":"noopener noreferrer","className":"site-header__social-link"} /-->
			<?php endforeach; ?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</header>
<!-- /wp:group -->
