<?php
/**
 * Maulik Portfolio functions and definitions.
 *
 * This file is intentionally a thin loader and nothing else. Every piece of
 * behaviour lives in a file under inc/ so that a child theme can replace a
 * single concern without having to fork this file, and so that the load order
 * stays obvious at a glance.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme version.
 *
 * Used to bust asset caches. Kept in sync with the Version header in
 * style.css by hand, because Core does not read style.css at runtime for this.
 */
define( 'MAULIK_PORTFOLIO_VERSION', '0.1.0' );

/**
 * Absolute path to the theme directory, with a trailing slash.
 */
define( 'MAULIK_PORTFOLIO_DIR', trailingslashit( get_parent_theme_file_path() ) );

/**
 * URL of the theme directory, with a trailing slash.
 */
define( 'MAULIK_PORTFOLIO_URI', trailingslashit( get_parent_theme_file_uri() ) );

require_once MAULIK_PORTFOLIO_DIR . 'inc/helpers.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/setup.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/assets.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/fonts.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/performance.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/patterns-category.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/hero-ecosystem.php';

/**
 * Enqueue the section switcher script.
 *
 * Sections 02, 03 and 06 are core blocks whose content stays in post_content,
 * so there is no dynamic block to hang a view script off the way
 * inc/hero-ecosystem.php does. That makes the conditional load explicit rather
 * than automatic, and it is worth being precise about why it is still cheap.
 *
 * The check is has_block() on the queried post, not a guess from the page slug.
 * has_block() is a string test against the already loaded post content, so it
 * costs nothing measurable, and it means the script is loaded on a singular view
 * whose post_content actually carries a core/html block, which is where every
 * one of the three control strips lives. A page with no interactive section
 * never downloads it.
 *
 * The script is registered unconditionally and enqueued conditionally, so a
 * child theme can declare it as a dependency without knowing the path.
 *
 * The dependency list is empty on purpose. The file is a dependency free IIFE
 * with no jQuery and no globals, so there is nothing to wait for.
 *
 * @since 0.1.0
 *
 * @return void
 */
function maulik_portfolio_enqueue_section_switcher_script() {
	$rel = 'assets/js/section-switcher.js';

	wp_register_script(
		'maulik-portfolio-section-switcher',
		get_parent_theme_file_uri( $rel ),
		array(),
		maulik_portfolio_asset_version( $rel ),
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	if ( ! is_singular() ) {
		return;
	}

	if ( ! has_block( 'core/html', get_queried_object_id() ) ) {
		return;
	}

	wp_enqueue_script( 'maulik-portfolio-section-switcher' );
}
add_action( 'wp_enqueue_scripts', 'maulik_portfolio_enqueue_section_switcher_script' );
require_once MAULIK_PORTFOLIO_DIR . 'inc/project-filters.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/rankkernel-filters.php';
require_once MAULIK_PORTFOLIO_DIR . 'inc/resume-document.php';

/**
 * Enqueue the header scroll state script.
 *
 * The design header is fully transparent until the visitor scrolls past 24
 * pixels, at which point it takes the near black band, the hairline border and
 * the backdrop blur. A scroll position cannot be read in CSS, so it is read in
 * one small file that adds and removes a class on the header element.
 *
 * wp_enqueue_scripts fires on the front end only, which is the whole of the
 * requirement: the site editor, the REST API and wp-admin never load it, and
 * there is nothing to switch off for them.
 *
 * The handle is registered and enqueued separately rather than only enqueued,
 * so a child theme can declare it as a dependency of its own script without
 * having to know the path.
 *
 * The dependency list is empty on purpose. The file is a dependency free IIFE
 * with no jQuery, so there is nothing to wait for.
 *
 * @since 0.1.0
 *
 * @return void
 */
function maulik_portfolio_enqueue_site_header_script() {
	$rel = 'assets/js/site-header.js';

	wp_register_script(
		'maulik-portfolio-site-header',
		get_parent_theme_file_uri( $rel ),
		array(),
		maulik_portfolio_asset_version( $rel ),
		true
	);

	wp_enqueue_script( 'maulik-portfolio-site-header' );

	// The Resume page hero carries two controls, Download Resume and Print View.
	// Both are anchors pointing at the PDF endpoint registered in
	// inc/resume-document.php, and neither is a button any more. There is
	// deliberately no print handler here: calling window.print() on this page
	// would emit the whole themed page, hero, all seven sections, site header and
	// site footer, which is not the document the owner wants printed. Print View
	// now opens the derived PDF instead. This script is therefore enqueued but
	// inert, and it stays dependency free and front-end only so that removing it
	// later is a one line change rather than an unqueue.
	$resume_rel = 'assets/js/resume-actions.js';

	wp_register_script(
		'maulik-portfolio-resume-actions',
		get_parent_theme_file_uri( $resume_rel ),
		array(),
		maulik_portfolio_asset_version( $resume_rel ),
		true
	);

	wp_enqueue_script( 'maulik-portfolio-resume-actions' );
}
add_action( 'wp_enqueue_scripts', 'maulik_portfolio_enqueue_site_header_script' );

/**
 * Enqueue the RankKernel subsystem status filter script.
 *
 * The filter is one scoped IIFE with no jQuery and no dependency, following
 * assets/js/site-header.js, and it is the only thing that adds behaviour to the
 * RankKernel status directory.
 *
 * It is enqueued from render_block rather than from wp_enqueue_scripts, so the
 * browser only downloads it on a response that actually rendered the filter.
 * That is the same mechanism WordPress itself uses for per-block view scripts
 * under should_load_separate_core_block_assets, applied here explicitly so it
 * does not depend on that setting being on. A response without the filter never
 * prints the file at all.
 *
 * Registration happens on init rather than on wp_enqueue_scripts because
 * render_block fires while the content is being rendered, which for a classic
 * block theme is after wp_head and before wp_footer. A script enqueued at that
 * point is still printed, because it is registered in the footer.
 *
 * The front end is the only place the script belongs. The site editor renders
 * blocks too, but its own preview already carries its interactivity, and the
 * filter is inert in the editor because no view script runs there.
 *
 * @since 0.1.0
 *
 * @param string $block_content Rendered block content.
 * @return string Rendered block content, unchanged.
 */
function maulik_portfolio_enqueue_rankkernel_filter_script( $block_content ) {
	if ( is_admin() || ! is_string( $block_content ) ) {
		return $block_content;
	}

	if ( false !== strpos( $block_content, 'data-rk-filter' ) ) {
		wp_enqueue_script( 'maulik-portfolio-rankkernel-filter' );
	}

	return $block_content;
}
add_filter( 'render_block', 'maulik_portfolio_enqueue_rankkernel_filter_script', 10, 1 );

/**
 * Register the RankKernel subsystem status filter script.
 *
 * Registered separately from the enqueue above so the handle exists by the time
 * the first block renders, and so a child theme can declare it as a dependency.
 *
 * @since 0.1.0
 *
 * @return void
 */
function maulik_portfolio_register_rankkernel_filter_script() {
	$rel = 'assets/js/rankkernel-filter.js';

	wp_register_script(
		'maulik-portfolio-rankkernel-filter',
		get_parent_theme_file_uri( $rel ),
		array(),
		maulik_portfolio_asset_version( $rel ),
		true
	);
}
add_action( 'init', 'maulik_portfolio_register_rankkernel_filter_script' );

/*
 * NO document_title_separator FILTER, AND THAT IS DELIBERATE. This note records
 * the two wrong diagnoses that were chased first, so nobody repeats them.
 *
 * MEASURED ON ALL SIX ROUTES, BY CODEPOINT AND NOT BY EYE, every title carried
 * U+2013, "Home EN DASH Maulik Bhalodiya", and so on for all six.
 *
 * WRONG DIAGNOSIS ONE: that WordPress core emitted it. Core 7.1.2 does not.
 * wp_get_document_title() in wp-includes/general-template.php applies
 * document_title_separator with a plain hyphen as the default, "-" , on its own
 * line. There is no &ndash; in that call path in this version.
 *
 * WRONG DIAGNOSIS TWO: that a document_title_separator filter would fix it. It
 * would not, and the filter that was written for it was dead code. The active
 * RankKernel plugin hooks pre_get_document_title at priority 10, from
 * RankKernel\Modules\Metadata\HeadRenderer::title, and resolves its own template
 * instead of letting core build one at all. pre_get_document_title
 * short-circuits: core applies it, and if the value is non-empty core returns it
 * immediately without ever reaching its own separator line. So the separator
 * filter never ran. It was registered, has_filter reported it at priority 1, and
 * it changed nothing on any route.
 *
 * THE ACTUAL SOURCE is the plugin's own settings, which are data and not code:
 *   rankkernel_settings['title_template'] = "%%title%% %%sep%% %%sitename%%"
 *   rankkernel_settings['separator']      = the en dash, U+2013
 * Context::separator() reads that value and TagsReplacer substitutes it for
 * %%sep%%, and HeadRenderer builds the title, og:title, twitter:title and the
 * schema name from it. All four therefore carry the same dash. An HTML entity
 * would have been wrong in any case, because the value is emitted as text into
 * the title, not into an attribute.
 *
 * This also explains why every CI gate read green throughout. All four run
 * git grep over this repository. The dash was never in the repository. It was in
 * a plugin option in the database, which no repository gate can see.
 *
 * WHY A FILTER RATHER THAN ONLY A SETTING WRITE. The stored value was set to a
 * plain ASCII hyphen, U+002D, and all six titles were verified ASCII afterwards.
 * Hours later the stored value was the en dash again and all six titles had
 * regressed. The plugin's sanitiser accepts a hyphen, since it only runs
 * sanitize_text_field on this key, and nothing in the plugin rewrites the option
 * on a frontend request, so the writer was not identified. Since the cause is
 * unknown the value is now also forced at read time below, which no write can
 * undo, and the stored value is kept as a hyphen so the settings screen agrees.
 */

/**
 * Force the RankKernel title separator to a plain ASCII hyphen at read time.
 *
 * The plugin builds the document title, og:title, twitter:title and the schema
 * name from rankkernel_settings['separator'], and its own pre_get_document_title
 * handler short-circuits core, so no theme filter on the title reaches those
 * four tags. The option is therefore forced on the way out of get_option.
 *
 * The plugin's own filter is removed for the nested read so this does not
 * recurse, then restored. If the stored value is not an array the stored value
 * is passed through untouched rather than guessed at.
 *
 * @param mixed  $preempt      Short-circuit value. False unless something else filtered.
 * @param string $option       Option name.
 * @param mixed  $default_value Default value WordPress would have returned.
 * @return mixed
 */
function maulik_portfolio_force_hyphen_title_separator( $preempt, $option, $default_value ) {
	if ( 'rankkernel_settings' !== $option ) {
		return $preempt;
	}

	remove_filter( 'pre_option_rankkernel_settings', 'maulik_portfolio_force_hyphen_title_separator', 10 );
	$settings = get_option( $option, $default_value );
	add_filter( 'pre_option_rankkernel_settings', 'maulik_portfolio_force_hyphen_title_separator', 10, 3 );

	if ( ! is_array( $settings ) ) {
		return $preempt;
	}

	$settings['separator'] = '-';

	return $settings;
}
add_filter( 'pre_option_rankkernel_settings', 'maulik_portfolio_force_hyphen_title_separator', 10, 3 );

/**
 * Make /projects/{slug}/ answer 404 rather than a 301 to a page it is not.
 *
 * MEASURED, NOT INFERRED. Before this change:
 * - /projects/rankkernel/ answered 301 to /rankkernel/, X-Redirect-By WordPress
 * - /projects/a answered 301 to /about/, X-Redirect-By WordPress
 * - /projects/nope/ answered 404
 * - /projects/x/y/ answered 404
 * - /projects/ answered 200
 *
 * Two different redirects, neither of them configured anywhere: core's
 * redirect_canonical is guessing a post from the last path segment and matching
 * it on post_name. It is a suggestion engine, and this site has a standing rule
 * that no per project route exists, so its guess is never right here. The 301 is
 * also worse than the 404 it replaces, because a permanent redirect is cached by
 * browsers for a year and there is nothing behind it to cache.
 *
 * The elimination is already done and it is recorded here because every step of
 * it ruled something out rather than assuming it: there is no wp_rankkernel_
 * redirects table, the plugin's Redirects module is default off and not enabled,
 * there is no _wp_old_slug postmeta, no rewrite rule matching "project", no
 * mu-plugin, and no redirect_canonical or template_redirect hook anywhere in the
 * theme or in any plugin. The server is Apache and Apache did not issue the
 * redirect. What is left is core's own hook.
 *
 * WHY remove_action AND NOT A REDIRECT. The only alternative is to let
 * redirect_canonical run and send a 404 after it, which cannot work: the
 * canonical redirect calls wp_redirect() and exits, so nothing after it runs. The
 * hook is therefore removed, for this request only, on this pattern only.
 *
 * Priority 1 rather than 10, which does not change what remove_action does, so
 * that the decision is made before anything else on that hook has had a chance to
 * send a header.
 *
 * THE PATTERN IS EXACTLY ^/projects/[^/]+/$ AND NOTHING ELSE. One level, no
 * deeper, and only under /projects/. /projects/ itself is untouched and still
 * answers 200, and no other route can reach this branch: /about/ does not match,
 * /projects/a/b/ does not match, and the bare root does not match. Nothing here
 * reads or writes permalink_structure and nothing here adds a rewrite rule,
 * because the whole point is that the route does not exist and the cheapest
 * correct statement of that is a 404 rather than a rule.
 *
 * status_header and nocache_headers are both called because a 404 that a cache
 * is allowed to keep is a permanent statement about a route that will still not
 * exist tomorrow.
 *
 * @since 0.1.0
 *
 * @return void
 */
function maulik_portfolio_404_for_per_project_routes() {
	$request_path = isset( $_SERVER['REQUEST_URI'] )
		? (string) wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH )
		: '';

	if ( '' === $request_path ) {
		return;
	}

	// A site in a subdirectory serves every route under its home path, so the
	// prefix is removed before anything is compared. On a site at the domain
	// root the home path is a single slash and this never runs.
	$home_path = (string) wp_parse_url( get_home_url(), PHP_URL_PATH );

	if ( '/' !== $home_path && 0 === strpos( $request_path, $home_path ) ) {
		$request_path = substr( $request_path, strlen( $home_path ) );
	}

	if ( ! preg_match( '#^/projects/[^/]+/?$#', $request_path ) ) {
		return;
	}

	remove_action( 'template_redirect', 'redirect_canonical', 10 );

	global $wp_query;

	if ( $wp_query instanceof WP_Query ) {
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}
add_action( 'template_redirect', 'maulik_portfolio_404_for_per_project_routes', 1 );

/**
 * Print the font preloads at the exact URL the @font-face rules ask for.
 *
 * MEASURED ON A COLD PROFILE, CACHE DISABLED, AT 1440, before this change:
 * - syne-latin-700-800.woff2, 2 requests, 69,792 bytes
 * - plus-jakarta-sans-latin-400-600.woff2, 2 requests, 55,273 bytes
 * - ibm-plex-mono-latin-400.woff2, 1 request
 * - ibm-plex-mono-latin-600.woff2, 1 request
 *
 * The cause is a query string. inc/fonts.php prints the preload through
 * maulik_portfolio_asset_version(), which appends ?ver= and an mtime, and
 * assets/styles/base/_fonts.scss declares the same faces with a bare src, so the
 * preload asked for
 * ".../syne-latin-700-800.woff2?ver=0.1.0.1790847795" and the stylesheet asked
 * for "../fonts/syne-latin-700-800.woff2". Two URLs that differ by a query
 * string are two different URLs to the browser: the preload is fetched, and then
 * the stylesheet asks for the unadorned one and fetches it again. Chrome logged
 * "was preloaded using link preload but not used within a few seconds" for both,
 * which is the browser saying out loud that the preload bought nothing.
 *
 * THE FIX IS TO MAKE THE TWO URLS BYTE IDENTICAL, and this is the half of that
 * the theme can do from functions.php. The @font-face rules live in
 * assets/styles/base/_fonts.scss and the original printer lives in inc/fonts.php,
 * and neither is this file, so rather than edit either, the printer is replaced
 * with one that builds the same URL the stylesheet asks for.
 *
 * Dropping the version is not a regression. The unadorned URL is the one the
 * stylesheet already requests on every single load, so a font file has never been
 * cache busted by this preload and is not now: the browser already holds a copy
 * keyed on the bare path and it matches. What changes is that the preload and
 * the request are now one request, so the first one is the one that is used.
 *
 * The file list still comes from maulik_portfolio_preload_fonts(), so the filter
 * a child theme uses, and the fallback for a checkout with no font binaries, are
 * both still in force. Only the query string is gone.
 *
 * @since 0.1.0
 *
 * @return void
 */
function maulik_portfolio_print_font_preloads_matching_font_face() {
	$preloads = maulik_portfolio_preload_fonts();

	if ( empty( $preloads ) ) {
		return;
	}

	foreach ( $preloads as $relative_path ) {
		printf(
			'<link rel="preload" href="%1$s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( get_theme_file_uri( $relative_path ) )
		);
	}
}

/**
 * Swap the versioned font preload printer for the unversioned one.
 *
 * The printer in inc/fonts.php is registered on wp_head at priority 1 from
 * after_setup_theme. This runs at priority 99 of the same hook, which is after
 * it, and removes it by callback rather than by clearing the hook, so the
 * original is left intact for a child theme that wants it back.
 *
 * @since 0.1.0
 *
 * @return void
 */
function maulik_portfolio_use_matching_font_preloads() {
	remove_action( 'wp_head', 'maulik_portfolio_print_font_preloads', 1 );
	add_action( 'wp_head', 'maulik_portfolio_print_font_preloads_matching_font_face', 1 );
}
add_action( 'after_setup_theme', 'maulik_portfolio_use_matching_font_preloads', 99 );

/**
 * Name the two core/social-link channels the way the design names them.
 *
 * MEASURED ON THE DESIGN, all three of its channel anchors are icon only, carry
 * no text node at all, and name themselves with a sentence that includes the
 * person: "Connect with Maulik Bhalodiya on LinkedIn", "View Maulik Bhalodiya
 * on GitHub" and "Send an email to Maulik Bhalodiya". Ours announced one word
 * each, LinkedIn, GitHub and Email, which is the screen reader only span that
 * render_block_core_social_link emits.
 *
 * There is no way to fix that in the block. render_block_core_social_link builds
 * the anchor itself and writes href, class, rel and target onto it, and
 * core/social-link has no attribute that reaches the anchor, so an aria-label
 * authored on the block parses and is discarded, exactly the way a textColor on a
 * navigation-link parses and is discarded. The mail channel is not a
 * core/social-link, so patterns/header.php writes its aria-label directly, and
 * these two have to be rewritten after Core has rendered them.
 *
 * The map is keyed by Core's own service name, which is on the list item as
 * wp-social-link-linkedin and wp-social-link-github, so the key is read back off
 * the rendered markup rather than guessed from the URL.
 *
 * THE SCOPE IS DELIBERATELY TIGHT. The filter returns early for every block that
 * is not a core/social-link, and again for every social link that does not carry
 * this theme's own site-header__social-link class, so a social link anywhere else
 * on the site, in a page's content or in another pattern, is untouched. The third
 * channel is a wp:html list item and never reaches here at all.
 *
 * The label span Core emits is left in place underneath the aria-label. It is
 * invisible either way, aria-label wins for anything that reads it, and it is the
 * only thing that names the link for anything that does not.
 *
 * @since 0.1.0
 *
 * @param string $block_content Rendered block content.
 * @param mixed  $block         The parsed block.
 * @return string Filtered block content.
 */
function maulik_portfolio_header_social_link_names( $block_content, $block ) {
	if ( ! is_string( $block_content ) || '' === $block_content ) {
		return $block_content;
	}

	if ( ! $block instanceof WP_Block || 'core/social-link' !== $block->name ) {
		return $block_content;
	}

	// This theme's own class, and nothing else. One strpos on a short string,
	// on one block per channel per row, twice per page.
	if ( false === strpos( $block_content, 'site-header__social-link' ) ) {
		return $block_content;
	}

	$names = array(
		'wp-social-link-linkedin' => __( 'Connect with Maulik Bhalodiya on LinkedIn', 'maulik-portfolio' ),
		'wp-social-link-github'   => __( 'View Maulik Bhalodiya on GitHub', 'maulik-portfolio' ),
	);

	foreach ( $names as $service_class => $name ) {
		if ( false === strpos( $block_content, $service_class ) ) {
			continue;
		}

		$processor = new WP_HTML_Tag_Processor( $block_content );

		if ( ! $processor->next_tag( array( 'tag_name' => 'A' ) ) ) {
			continue;
		}

		$processor->set_attribute( 'aria-label', $name );

		return $processor->get_updated_html();
	}

	return $block_content;
}
add_filter( 'render_block', 'maulik_portfolio_header_social_link_names', 10, 2 );
