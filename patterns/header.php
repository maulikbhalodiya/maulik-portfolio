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
 * THE SUBTLE ONE THAT BITES EVERYONE. Every value in these block comments is
 * JSON, and JSON is written with double quotes. That means esc_attr() cannot be
 * used on any of them, because esc_attr() turns a double quote into the entity
 * &quot;, and &quot; is not valid JSON. A block whose JSON does not parse fails
 * silently: no error, no markup, no attribute, and the menu or the icon row
 * simply is not there.
 *
 * This was a measured defect on the live site before it was fixed. The footer's
 * external links came out carrying target=&quot;_blank&quot;, with the quote
 * characters inside the attribute value and the rel truncated at the first
 * space, because a raw attribute fragment had been passed through esc_attr().
 * The same class of mistake was latent in the two rules below.
 *
 * So every value that goes into a block comment here is written with
 * wp_json_encode(), which emits a real quote and produces JSON that parses.
 * patterns/home-capabilities.php, patterns/home-approach.php and
 * patterns/home-technical-stack.php already do it this way, and this file now
 * matches them. Note what is NOT being claimed: wp_json_encode() escapes
 * forward slashes, so a URL reads with escaped slashes in the source. That is
 * valid JSON, Core decodes it, and the noise is the right trade for a block
 * that actually renders.
 *
 * esc_html() and esc_url() belong to attribute values in the rendered HTML, not
 * to the block comment. Using them inside the JSON also happens to work today,
 * but only because none of these strings currently contain a character that
 * they escape, which is a coincidence and not a property.
 *
 * THE ORDER INSIDE core/navigation IS THE ORDER THE OVERLAY RENDERS IN, and in
 * the DOM it is the order the owner asked for: the three channels first, then the
 * six nav links, then the close toggle last. Core builds the overlay from the
 * inner blocks in their authored order (get_inner_blocks_html walks
 * $inner_blocks and only opens a <ul> around the blocks that render an <li>), so
 * the order below is the order in the document. The social row therefore has to
 * be an INNER BLOCK of the navigation and not a sibling group in the bar: a
 * sibling is outside the overlay and is covered by it the moment the menu opens.
 *
 * DOM ORDER AND POSITION ON SCREEN ARE NOT THE SAME THING HERE, AND THE TOGGLE IS
 * WHERE THEY COME APART. The owner asked for the close control in the top right
 * corner of the open menu rather than at the end of the column below the channel
 * links. Doing that by moving the block would have made it the first thing Tab
 * reaches, undoing the earlier request that the hamburger be last, so the block
 * stays exactly where it is and the position is taken in CSS instead:
 * _site-header.scss sets position: absolute with top and right of 1.5rem against
 * .wp-block-navigation__responsive-container, the sheet itself, which Core
 * positions fixed with inset 0 while the menu is open. The result is a control
 * that is painted first and reached last, which is what was asked for. Nothing in
 * this file moves.
 *
 * THE CLOSE TOGGLE IS AUTHORED HERE RATHER THAN TAKEN FROM CORE. Core renders its
 * own close button as the FIRST child of the dialog, before the content, and it
 * cannot be moved from a pattern: there is no attribute for it and no filter on
 * this file. A control that is first in the DOM and last on screen is a tab order
 * that reads backwards. So the authored button below is the last inner block,
 * which puts it last in the DOM and therefore last in the tab sequence, and Core's
 * own close button is hidden at mobile widths by _site-header.scss so that exactly
 * one toggle is on screen at a time.
 *
 * It closes the menu through Core's own action, data-wp-on--click, which the
 * Interactivity API resolves against the core/navigation store that the <nav>
 * already carries. No script is added by this theme for it, no inline handler is
 * written, and no global is created: the button is bound, not programmed.
 *
 * THE NAVIGATION BLOCK NO LONGER CARRIES justifyContent. It carried right, and
 * Core turns that attribute into --navigation-layout-justification-setting, which
 * it then applies to align-items on the open overlay content. The result, measured
 * in Chrome at 390 before this change, was a list 76 pixels wide pinned to the
 * right edge with every link overflowing past x=390 and clipped by the screen
 * edge. That was the whole of the reported "the menu shows out of the screen":
 * the panel itself measured 390 by 844 and fully inside the viewport, and the
 * panel never widened the page. The attribute has no meaning at desktop, where
 * the list is shrink wrapped inside a space-between bar, so removing it changes
 * nothing there.
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
 * The three design channels, repeated here for the header icon row.
 *
 * EMITTED TWICE, ONCE IN THE BAR AND ONCE IN THE OPEN MOBILE MENU, AND NEVER BOTH
 * AT ONCE. This mirrors the design, which carries the same three channels in the
 * bar from Tailwind sm upwards and again inside its mobile panel below lg, so the
 * two copies are never on screen together: the bar's copy is hidden below $bp-sm
 * by _site-header.scss, the panel's copy lives inside the overlay, which is only
 * rendered at all while the menu is open.
 *
 * LinkedIn and GitHub are core/social-link with openInNewTab, because
 * core/social-link has no target attribute of its own: render_block_core_
 * social_link reads the target off the PARENT's openInNewTab context and is the
 * only thing that emits target="_blank" for those two services. The authored rel
 * stays noopener noreferrer on both, and Core appends its own noopener nofollow
 * on top of it when openInNewTab is on.
 *
 * THE DESIGN CARRIES THREE, AND SO DOES THIS ARRAY: LINKEDIN, GITHUB AND MAIL.
 * Mail is the one entry that is NOT a core/social-link, and it cannot be one.
 * That block keys its icon off the service name against a fixed table of social
 * services, and mail is not in it, so an authored core/social-link with
 * service "mail" renders an empty anchor with no mark in it. The glyph is the
 * design's own, taken from the rendered SVG at Header.tsx:277 rather than
 * retyped, and it is emitted as a wp:html list item carrying the same two
 * classes the stylesheet keys the box and the mark on.
 *
 * NO target AND NO rel, matching the design's Email anchor at Header.tsx:273 and
 * the hero's. A mailto opens a local client rather than a browsing context, so
 * there is no new browsing context to isolate. Because the entry is not a
 * core/social-link, the parent's openInNewTab cannot reach it either, so the
 * absence is structural rather than something left out.
 *
 * ONE ADDRESS ONLY. This is the same single address patterns/footer.php:299 and
 * the hero use, and the repo wide CI gates in .github/workflows/ci.yml permit
 * exactly that one mailto and that one raw address, so the header introduces
 * no second one. There is deliberately no bare address here and none in any
 * aria-label.
 *
 * THE ARIA-LABELS ARE THE DESIGN'S OWN SENTENCES, NOT INVENTIONS. Measured on
 * the design, its three channel anchors carry no text node at all and name
 * themselves:
 *   Connect with Maulik Bhalodiya on LinkedIn
 *   View Maulik Bhalodiya on GitHub
 *   Send an email to Maulik Bhalodiya
 * The short label stays in the array as well, because Core's own render callback
 * emits it as the screen reader only span inside the anchor, and it is the
 * fallback for anything that ignores aria-label.
 *
 * core/social-link CANNOT CARRY AN aria-label. render_block_core_social_link
 * builds the anchor itself and writes only href, class, rel and target onto it,
 * then emits the label as a span; no attribute on the block reaches the anchor.
 * So the two core/social-link entries get their names in functions.php, from
 * maulik_portfolio_header_social_link_names(), which rewrites the rendered block
 * by service name and only inside a block carrying this theme's own social-link
 * class. That function and not this file holds the LinkedIn and GitHub strings,
 * so each string is written down exactly once. The mail entry below is not a
 * core/social-link, so it is written as a wp:html list item and its aria-label
 * is read from the 'aria_label' key in the same array as everything else.
 *
 * The first two URLs are the ones patterns/channel-links.php carries, and they
 * are duplicated rather than shared because that pattern registers its own
 * block and returns nothing, and because a pattern cannot be required from
 * another pattern at the point patterns run, which is init. The third entry is
 * here only, which is why that pattern still declares two.
 *
 * THE ORDER HERE IS THE DESIGN'S ORDER AND IT IS ALSO THE PANEL'S: LinkedIn,
 * GitHub, then Mail. The design puts these three after its six nav rows rather
 * than before them, and this file puts them first, because the owner asked for
 * the channels first and the toggle last. The within-row order is the design's
 * either way.
 */
$maulik_portfolio_header_channels = array(
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

	/*
	 * The mail entry, and it is declared with its own 'mailto' key rather
	 * than through 'url'. See the note above the array: it is not a
	 * core/social-link, it is emitted by the branch below that tests for
	 * this key, and the branch is tested first so that isset() on 'url'
	 * cannot claim it. Same ordering as the footer.
	 */
	array(
		'label'      => __( 'Email', 'maulik-portfolio' ),
		'aria_label' => __( 'Send an email to Maulik Bhalodiya', 'maulik-portfolio' ),
		'mailto'     => 'mailto:maulikbhalodiya9999@gmail.com',
	),
);

/**
 * Renders every channel as the markup that goes inside one core/social-links
 * list, so the bar's copy and the panel's copy are emitted by the same code.
 *
 * Two entries are core/social-link block comments, which is what Core renders
 * into the list item, the anchor and the mark. The third is a wp:html list
 * item, because Core has no mail service and would render an empty anchor for
 * one.
 *
 * @param array $channels The channel array above.
 * @return string Block comments and list items, ready to echo.
 */
$maulik_portfolio_render_header_channels = static function ( array $channels ) {
	$markup = '';

	foreach ( $channels as $channel ) {
		/*
		 * THE MAILTO BRANCH IS TESTED FIRST, exactly as it is in the footer.
		 * isset() on 'url' below would otherwise claim this entry, and the
		 * design's email anchor carries neither target nor rel, which is only
		 * reachable on the branch that adds neither.
		 */
		if ( isset( $channel['mailto'] ) ) {
			$markup .= '<!-- wp:html -->' . "\n";
			$markup .= '<li class="wp-social-link wp-social-link site-header__social-link"><a class="wp-block-social-link-anchor" href="' . esc_url( $channel['mailto'] ) . '" aria-label="' . esc_attr( $channel['aria_label'] ) . '">';

			/*
			 * THE DESIGN'S OWN MAIL GLYPH, lifted from the rendered SVG rather
			 * than retyped, so the paths are the ones the design ships. The
			 * mark carries width and height attributes of 16, which is both the
			 * design's w-4 h-4 at Header.tsx:277 and the size _site-header.scss
			 * pins the other two marks to, so this one cannot be the 45 or 120
			 * pixel mark an unmarked glyph measures at.
			 */
			$markup .= '<svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg>';

			/*
			 * The accessible name, and it is an aria-label, not a visible label.
			 * MEASURED ON THE DESIGN, all three of its channel anchors are icon
			 * only, carry no text node at all, and each names itself with a
			 * longer sentence that includes the person:
			 *   Connect with Maulik Bhalodiya on LinkedIn
			 *   View Maulik Bhalodiya on GitHub
			 *   Send an email to Maulik Bhalodiya
			 * So this anchor carries the design's own sentence, and the
			 * screen reader only span below stays as the fallback for anything
			 * that does not read aria-label.
			 *
			 * There is still no plaintext address here and none in the
			 * aria-label, which is why the repo wide CI gates in
			 * .github/workflows/ci.yml permit exactly this one mailto and
			 * exactly one raw address. The address is written literally once,
			 * in the array above.
			 */
			$markup .= '<span class="wp-block-social-link-label screen-reader-text">' . esc_html( $channel['label'] ) . '</span>';
			$markup .= '</a></li>' . "\n";
			$markup .= '<!-- /wp:html -->' . "\n";

			continue;
		}

		$markup .= '<!-- wp:social-link {"service":' . wp_json_encode( $channel['service'] )
			. ',"label":' . wp_json_encode( $channel['label'] )
			. ',"url":' . wp_json_encode( $channel['url'] )
			. ',"rel":"noopener noreferrer","className":"site-header__social-link"} /-->' . "\n";
	}

	return $markup;
};

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

		<!-- wp:navigation {"className":"site-header__nav","overlayMenu":"mobile","layout":{"type":"flex"}} -->
		<nav class="wp-block-navigation site-header__nav">
			<?php
			/*
			 * THE CHANNELS COME FIRST INSIDE THE NAVIGATION. This is the first
			 * inner block of core/navigation, so it is the first thing in the open
			 * overlay, which is the order the owner asked for. It is emitted here
			 * and not in the bar because the overlay covers the bar while it is
			 * open: a sibling group in the bar is not in the menu at any width.
			 */
			?>
			<!-- wp:social-links {"openInNewTab":true,"className":"site-header__menu-social","layout":{"type":"flex","flexWrap":"nowrap"}} -->
			<ul class="wp-block-social-links site-header__menu-social">
				<?php echo $maulik_portfolio_render_header_channels( $maulik_portfolio_header_channels ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Every interpolated value is escaped inside the closure, or is a block comment carrying wp_json_encode()d JSON. ?>
			</ul>
			<!-- /wp:social-links -->

			<?php
			foreach ( $maulik_portfolio_nav_items as $maulik_portfolio_nav_item ) :
				$maulik_portfolio_nav_label = $maulik_portfolio_nav_item['label'];
				$maulik_portfolio_nav_url   = get_home_url( null, $maulik_portfolio_nav_item['path'] );
				$maulik_portfolio_nav_class = $maulik_portfolio_nav_item['class'];

				// The item path reduced to the same shape as the current path, so
				// that one comparison below covers /about, /about/ and /about//.
				$maulik_portfolio_nav_route = '/' . trim( $maulik_portfolio_nav_item['path'], '/' );

				/*
				 * No empty case to normalise here. The route is built by
				 * concatenating a leading slash, so it is never an empty string:
				 * the shortest it can be is '/'. The comparison below is therefore
				 * the only one needed, and a test for '' here could never pass and
				 * was dead weight rather than a live guard.
				 */
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
				<!-- wp:navigation-link {"label":<?php echo wp_json_encode( $maulik_portfolio_nav_label ); ?>,"url":<?php echo wp_json_encode( $maulik_portfolio_nav_url ); ?>,"kind":<?php echo wp_json_encode( $maulik_portfolio_nav_kind ); ?>,"isTopLevelLink":true
				<?php if ( $maulik_portfolio_nav_id > 0 ) : ?>
					,"id":<?php echo (int) $maulik_portfolio_nav_id; ?>
				<?php endif; ?>
				<?php if ( '' !== $maulik_portfolio_nav_class ) : ?>
					,"className":<?php echo wp_json_encode( $maulik_portfolio_nav_class ); ?>
				<?php endif; ?>
				} /-->
			<?php endforeach; ?>

			<?php
			/*
			 * THE CLOSE TOGGLE, LAST. It is the final inner block of the
			 * navigation, so it is the final child of the overlay content, which
			 * is what makes it last in the DOM and therefore last in the tab
			 * sequence rather than first on screen and last in the DOM.
			 *
			 * LAST IN THE DOM AND TOP RIGHT ON SCREEN ARE NOT IN CONFLICT, and the
			 * distinction is the whole reason nothing is reordered here. The owner
			 * wants this control in the top right corner of the open menu, and the
			 * owner also wants the hamburger to be the last tab stop. Those are a
			 * document position and a painting position, and they are satisfied by
			 * different mechanisms. Moving the block to the top of the navigation
			 * would satisfy the first and break the second. _site-header.scss
			 * therefore takes this button out of the flow and pins it to the top
			 * right of the sheet, leaving this file exactly as it was.
			 *
			 * data-wp-on--click resolves against the core/navigation store that
			 * the <nav> above already declares, so this is a binding and not a
			 * script: no file, no inline handler, no global. The same store
			 * restores focus to the opening button when the menu closes.
			 *
			 * aria-expanded is true and is true honestly: this control exists
			 * only inside the overlay, which is display:none until it is open.
			 *
			 * The mark carries width and height attributes AND the class that
			 * pins it to 20 pixels, which is the design's w-5 h-5 at
			 * Header.tsx:194. The 24 pixel attribute Core hands the same glyph
			 * is what this theme's icon floor already measured as 20.
			 */
			?>
			<!-- wp:html -->
			<button type="button" class="site-header__menu-toggle" aria-expanded="true" aria-label="<?php echo esc_attr__( 'Close menu', 'maulik-portfolio' ); ?>" data-wp-on--click="actions.closeMenuOnClick"><svg class="site-header__menu-toggle-icon" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m13.06 12 6.47-6.47-1.06-1.06L12 10.94 5.53 4.47 4.47 5.53 10.94 12l-6.47 6.47 1.06 1.06L12 13.06l6.47 6.47 1.06-1.06L13.06 12Z"></path></svg></button>
			<!-- /wp:html -->
		</nav>
		<!-- /wp:navigation -->

		<?php
		/*
		 * THE BAR'S COPY OF THE SAME THREE CHANNELS. This is the desktop row and
		 * it is hidden below the design's sm by _site-header.scss, so it and the
		 * copy inside the overlay are never both on screen. It is rendered by the
		 * same closure as the panel's copy, so the two cannot drift apart.
		 */
		?>
		<!-- wp:social-links {"openInNewTab":true,"className":"site-header__social","layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<ul class="wp-block-social-links site-header__social">
			<?php echo $maulik_portfolio_render_header_channels( $maulik_portfolio_header_channels ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- See the note on the panel's copy above. ?>
		</ul>
		<!-- /wp:social-links -->
	</div>
	<!-- /wp:group -->
</header>
<!-- /wp:group -->
