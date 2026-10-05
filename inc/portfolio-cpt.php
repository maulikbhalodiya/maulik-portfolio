<?php
/**
 * The project post type and the post meta the theme exposes for it.
 *
 * WHY PUBLIC TRUE AND PUBLICLY_QUERYABLE FALSE AT THE SAME TIME.
 *
 * This pair looks contradictory and it is deliberate, so the reason is recorded
 * here rather than left for the next reader to re-derive.
 *
 * public => false implies show_ui => false. WP_Post_Type::set_props() assigns
 * any argument left unset from public, and show_ui is one of those arguments,
 * so registering this type with public => false and no explicit show_ui
 * deletes the Projects admin menu and every admin screen with it. That is the
 * failure mode this file exists to avoid.
 *
 * publicly_queryable => false is the opposite intent, applied only to the front
 * end. It keeps WP::parse_request() from accepting ?project=slug, so a
 * project cannot be fetched as a front end query var even by a URL that is
 * typed by hand. rewrite => false and has_archive => false then register no
 * permastruct and no archive rule at all, so there is no /projects/ route of
 * any shape to resolve.
 *
 * The combination therefore leaves the type fully editable in wp-admin and
 * fully readable through REST, and unaddressable on the public site. That is
 * the shape this theme wants: projects are content the owner maintains and the
 * blocks below render, never routes a visitor can land on directly.
 *
 * show_in_rest => true is not optional. Without a REST route the block editor
 * cannot load or save the type, which is the whole reason supports carries
 * editor in the first place.
 *
 * rest_base is 'projects' so the route is /wp-json/wp/v2/projects. That is a
 * REST path only. It does not create a front end URL and it is not affected by
 * rewrite => false.
 *
 * No taxonomy is registered. None is needed yet, and registering one would add
 * a rewrite surface that this issue deliberately does not want.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'MAULIK_PORTFOLIO_PROJECT_META_ROLE' ) ) {
	/**
	 * The one project meta key this theme registers and reads.
	 *
	 * A constant rather than a repeated string literal so the register call,
	 * the guard above it and the reader in the block render files cannot drift
	 * apart.
	 */
	define( 'MAULIK_PORTFOLIO_PROJECT_META_ROLE', '_maulik_project_role' );
}

if ( ! function_exists( 'maulik_portfolio_register_project_post_type' ) ) {
	/**
	 * Register the project post type.
	 *
	 * Runs on init at the default priority 10, which is where register_post_type
	 * belongs. Registering earlier would run before taxonomy registration could
	 * attach, and later would be after rewrite rules are generated.
	 *
	 * @since 0.5.0
	 *
	 * @return void
	 */
	function maulik_portfolio_register_project_post_type() {
		$labels = array(
			'name'                  => _x( 'Projects', 'post type general name', 'maulik-portfolio' ),
			'singular_name'         => _x( 'Project', 'post type singular name', 'maulik-portfolio' ),
			'menu_name'             => _x( 'Projects', 'admin menu', 'maulik-portfolio' ),
			'add_new'               => __( 'Add New', 'maulik-portfolio' ),
			'add_new_item'          => __( 'Add New Project', 'maulik-portfolio' ),
			'edit_item'             => __( 'Edit Project', 'maulik-portfolio' ),
			'new_item'              => __( 'New Project', 'maulik-portfolio' ),
			'view_item'             => __( 'View Project', 'maulik-portfolio' ),
			'search_items'          => __( 'Search Projects', 'maulik-portfolio' ),
			'not_found'             => __( 'No projects found.', 'maulik-portfolio' ),
			'not_found_in_trash'    => __( 'No projects found in Trash.', 'maulik-portfolio' ),
			'all_items'             => __( 'All Projects', 'maulik-portfolio' ),
			'featured_image'        => __( 'Project cover image', 'maulik-portfolio' ),
			'set_featured_image'    => __( 'Set project cover image', 'maulik-portfolio' ),
			'remove_featured_image' => __( 'Remove project cover image', 'maulik-portfolio' ),
			'use_featured_image'    => __( 'Use as project cover image', 'maulik-portfolio' ),
			'item_published'        => __( 'Project published.', 'maulik-portfolio' ),
			'item_updated'          => __( 'Project updated.', 'maulik-portfolio' ),
		);

		$args = array(
			'public'              => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'has_archive'         => false,
			'rest_base'           => 'projects',
			'menu_icon'           => 'dashicons-portfolio',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'labels'              => $labels,
			'description'         => __( 'A single piece of portfolio work.', 'maulik-portfolio' ),
			'exclude_from_search' => true,
		);

		register_post_type( 'project', $args );
	}
}
add_action( 'init', 'maulik_portfolio_register_project_post_type', 10 );

if ( ! function_exists( 'maulik_portfolio_register_project_meta' ) ) {
	/**
	 * Register the post meta this theme exposes for a project.
	 *
	 * Only one key is registered, and it is the only key the portfolio blocks
	 * read. The rule this follows is that a meta key which is not used is a key
	 * nobody can see the purpose of, so it is not registered until a block
	 * reads it.
	 *
	 * One caveat is recorded rather than worked around. Registered meta does not
	 * appear in REST, and is not saved through the block editor, unless the post
	 * type also supports custom-fields. That support is deliberately NOT added
	 * here because the supports list for this issue is fixed at title, editor,
	 * excerpt and thumbnail, and custom-fields would also re-enable the legacy
	 * Custom Fields metabox. So the two blocks read this key with
	 * get_post_meta(), which does not go through REST, and the value is expected
	 * to be written by WP-CLI, by a migration, or by any future code that is
	 * given a reason to write it.
	 *
	 * register_post_meta() is guarded by registered_meta_key_exists() because
	 * its sanitize and auth callbacks are attached as filters and are never
	 * removed. Registering the same key twice chains both sets of callbacks and
	 * makes effective permissions depend on load order.
	 *
	 * @since 0.5.0
	 *
	 * @return void
	 */
	function maulik_portfolio_register_project_meta() {
		if ( registered_meta_key_exists( 'post', MAULIK_PORTFOLIO_PROJECT_META_ROLE, 'project' ) ) {
			return;
		}

		register_post_meta(
			'project',
			MAULIK_PORTFOLIO_PROJECT_META_ROLE,
			array(
				'type'              => 'string',
				'description'       => __( 'The short role line shown on a project card and on a project detail.', 'maulik-portfolio' ),
				'single'            => true,
				'default'           => '',
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => 'maulik_portfolio_project_meta_auth_cb',
			)
		);
	}
}
add_action( 'init', 'maulik_portfolio_register_project_meta', 10 );

if ( ! function_exists( 'maulik_portfolio_register_portfolio_blocks' ) ) {
	/**
	 * Register the two portfolio blocks from their block.json files.
	 *
	 * WHY THIS CALL TAKES A PATH RATHER THAN A NAME AND AN ARRAY.
	 *
	 * register_block_type() reads every metadata key from block.json when it is
	 * given a path: the title, the attributes, the supports, apiVersion, the icon,
	 * the keywords, and the "render": "file:./render.php" and
	 * "viewScriptModule": "file:./view.js" file references. Passing the same
	 * metadata again as an argument array would be a second copy of the schema,
	 * and a second copy is a second thing to forget when the block changes.
	 *
	 * NO wp:comment BLOCK IS INVOLVED ANYWHERE HERE. That is not a style
	 * preference. Serialising a dynamic block into post content by hand requires
	 * embedding JSON in an HTML comment, and the obvious way to escape that JSON
	 * is esc_attr(), which emits &quot; for every double quote. WordPress does
	 * not decode those entities before parsing the block comment, so the parser
	 * rejects the block and it vanishes from the post with no error anywhere.
	 * A file rendered block has no comment to escape, so the failure cannot occur.
	 * The only place JSON is printed here is nowhere: the REST controller builds
	 * it with wp_json_encode().
	 *
	 * The block type names are read back from the files rather than repeated, so
	 * a rename in block.json cannot leave a stale string in this function.
	 *
	 * @since 0.5.0
	 *
	 * @return void
	 */
	function maulik_portfolio_register_portfolio_blocks() {
		$portfolio_block_dirs = array(
			'blocks/portfolio-list',
			'blocks/portfolio-detail',
		);

		foreach ( $portfolio_block_dirs as $portfolio_block_dir ) {
			$portfolio_block_path = get_theme_file_path( $portfolio_block_dir );

			if ( ! file_exists( $portfolio_block_path . '/block.json' ) ) {
				continue;
			}

			register_block_type( $portfolio_block_path );
		}
	}
}
add_action( 'init', 'maulik_portfolio_register_portfolio_blocks', 10 );

if ( ! function_exists( 'maulik_portfolio_project_meta_auth_cb' ) ) {
	/**
	 * Authorisation callback for the registered project meta.
	 *
	 * Returns true only for a user who could edit the post the meta is attached
	 * to, which is current_user_can( 'edit_post', $post_id ). Anything looser
	 * would let a subscriber read or write a value they have no edit rights on.
	 *
	 * @since 0.5.0
	 *
	 * @param bool   $allowed   Whether the user can add the meta. Ignored.
	 * @param string $meta_key  The meta key. Ignored.
	 * @param int    $object_id The post the meta is attached to.
	 * @return bool True when the current user may edit that post.
	 */
	function maulik_portfolio_project_meta_auth_cb( $allowed, $meta_key, $object_id ) {
		unset( $allowed, $meta_key );

		return current_user_can( 'edit_post', (int) $object_id );
	}
}

if ( ! function_exists( 'maulik_portfolio_get_project_role' ) ) {
	/**
	 * Read the role line for a project.
	 *
	 * Lives here rather than in a render.php because WordPress includes
	 * render.php once per block instance on the request, so any function
	 * declared in it would be redeclared and fatal on the second block. The
	 * block files stay free of declarations.
	 *
	 * @since 0.5.0
	 *
	 * @param int $post_id Project post ID.
	 * @return string The role line, or an empty string when it is not set.
	 */
	function maulik_portfolio_get_project_role( $post_id ) {
		$role = get_post_meta( (int) $post_id, MAULIK_PORTFOLIO_PROJECT_META_ROLE, true );

		if ( ! is_string( $role ) ) {
			return '';
		}

		return trim( $role );
	}
}
