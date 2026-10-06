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
 * A TAXONOMY IS REGISTERED, AND IT IS REGISTERED WITH rewrite FALSE.
 *
 * project_cat exists so the pj-cat-* filter classes on a project card are real
 * data rather than class names somebody typed into a template by hand. The
 * static cards on the Projects page carried pj-cat-professional,
 * pj-cat-wordpress, pj-cat-php, pj-cat-api, pj-cat-payment,
 * pj-cat-security and others, and assets/js/project-filters.js selects on them
 * with card.classList.contains( 'pj-cat-' + slug ). Hand written classes and
 * taxonomy terms therefore have to render the identical string, so a term slug
 * IS the pj-cat-* suffix with the pj-cat- prefix removed, and the render file
 * builds the class as 'pj-cat-' . $term->slug. A term renamed on the Projects
 * page without also updating assets/js/project-filters.js stops matching, and
 * the tablist silently filters nothing out rather than reporting an error.
 *
 * rewrite => false is what keeps this from reintroducing the rewrite surface
 * the post type deliberately avoids. It registers no permastruct and no
 * archive rule, so /projects/category/... and every other shape of that route
 * do not exist. publicly_queryable => false and query_var => false go one step
 * further and keep ?project_cat=... from being parsed at all, so the taxonomy
 * is a label on a card and not an addressable collection on the front end,
 * exactly as the post type is editable but unaddressable.
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

/*
 * The remaining card fields follow the same prefix and the same convention: a
 * constant per key, so the register call, the seeder and the reader in the block
 * render file cannot drift apart. Each constant is named for the field it
 * carries on a project card, which is why they read as card vocabulary rather
 * than as database columns.
 */
if ( ! defined( 'MAULIK_PORTFOLIO_PROJECT_TAXONOMY' ) ) {
	/**
	 * Taxonomy holding the categories a project card is filtered by.
	 */
	define( 'MAULIK_PORTFOLIO_PROJECT_TAXONOMY', 'project_cat' );
}

if ( ! defined( 'MAULIK_PORTFOLIO_PROJECT_META_INDEX_LABEL' ) ) {
	/**
	 * The position and kind line, for example 01 - Professional Project.
	 */
	define( 'MAULIK_PORTFOLIO_PROJECT_META_INDEX_LABEL', '_maulik_project_index_label' );
}

if ( ! defined( 'MAULIK_PORTFOLIO_PROJECT_META_SCHEME' ) ) {
	/**
	 * The scheme label, for example Architecture Pipeline.
	 */
	define( 'MAULIK_PORTFOLIO_PROJECT_META_SCHEME', '_maulik_project_scheme' );
}

if ( ! defined( 'MAULIK_PORTFOLIO_PROJECT_META_NODE_COUNT' ) ) {
	/**
	 * The node count label, for example 5 System Nodes.
	 */
	define( 'MAULIK_PORTFOLIO_PROJECT_META_NODE_COUNT', '_maulik_project_node_count' );
}

if ( ! defined( 'MAULIK_PORTFOLIO_PROJECT_META_NODES' ) ) {
	/**
	 * The node strip, stored as a JSON array and including its separators.
	 */
	define( 'MAULIK_PORTFOLIO_PROJECT_META_NODES', '_maulik_project_nodes' );
}

if ( ! defined( 'MAULIK_PORTFOLIO_PROJECT_META_TECH' ) ) {
	/**
	 * The technology list, stored as a JSON array.
	 */
	define( 'MAULIK_PORTFOLIO_PROJECT_META_TECH', '_maulik_project_tech' );
}

if ( ! defined( 'MAULIK_PORTFOLIO_PROJECT_META_HREF' ) ) {
	/**
	 * The case study anchor the card link points at.
	 */
	define( 'MAULIK_PORTFOLIO_PROJECT_META_HREF', '_maulik_project_href' );
}

/**
 * Every project meta key this theme registers, with its register arguments.
 *
 * One table rather than seven register_post_meta() calls, because the argument
 * set is identical for all of them and a repeated copy of it is seven places to
 * forget an option. The key is the constant, so no string literal is spelled out
 * twice anywhere in this theme.
 *
 * 'type' is string for all seven, including the two array fields. A JSON string
 * in a string column round trips through the REST API, through wp_update_post()
 * and through update_post_meta() without the editor having to know that an
 * array is involved, and it stays readable in the Custom Fields panel. The
 * alternative, type array, makes the REST schema for the field an object with
 * no declared item type, which the block editor then renders as an untyped
 * field with no way to edit it. The list sanitizer below is what turns the
 * array in and out of the JSON.
 *
 * @return array<string,array<string,mixed>> Register arguments keyed by meta key.
 */
function maulik_portfolio_project_meta_schema() {
	return array(
		MAULIK_PORTFOLIO_PROJECT_META_ROLE        => array(
			'description'       => __( 'The short role line shown on a project card and on a project detail.', 'maulik-portfolio' ),
			'sanitize_callback' => 'sanitize_text_field',
		),
		MAULIK_PORTFOLIO_PROJECT_META_INDEX_LABEL => array(
			'description'       => __( 'The position and kind line at the top of a project card, for example 01 - Professional Project.', 'maulik-portfolio' ),
			'sanitize_callback' => 'sanitize_text_field',
		),
		MAULIK_PORTFOLIO_PROJECT_META_SCHEME      => array(
			'description'       => __( 'The scheme label above the node strip on a project card.', 'maulik-portfolio' ),
			'sanitize_callback' => 'sanitize_text_field',
		),
		MAULIK_PORTFOLIO_PROJECT_META_NODE_COUNT  => array(
			'description'       => __( 'The node count label at the far end of the scheme head, for example 5 System Nodes.', 'maulik-portfolio' ),
			'sanitize_callback' => 'sanitize_text_field',
		),
		MAULIK_PORTFOLIO_PROJECT_META_NODES       => array(
			'description'       => __( 'The node strip on a project card, as a JSON array. A slash marks a separator between two nodes.', 'maulik-portfolio' ),
			'sanitize_callback' => 'maulik_portfolio_project_meta_list_sanitize_cb',
		),
		MAULIK_PORTFOLIO_PROJECT_META_TECH        => array(
			'description'       => __( 'The technology list in the foot of a project card, as a JSON array.', 'maulik-portfolio' ),
			'sanitize_callback' => 'maulik_portfolio_project_meta_list_sanitize_cb',
		),
		MAULIK_PORTFOLIO_PROJECT_META_HREF        => array(
			'description'       => __( 'Where the View Case Study link on the card points. Usually the anchor of the card itself.', 'maulik-portfolio' ),
			'sanitize_callback' => 'sanitize_text_field',
		),
	);
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

			/*
			 * custom-fields is what puts the registered meta in the editor. The
			 * block editor reads a post type's meta through the post entity, and
			 * the post entity only exposes meta for a type that supports
			 * custom-fields, so without it these keys are registered, readable
			 * through get_post_meta() and invisible in the editor, which is the
			 * one thing the owner of the site needs from them. It also restores
			 * the legacy Custom Fields metabox on the classic editor, which is
			 * the known cost of asking for the sidebar panel.
			 */
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
			'labels'              => $labels,
			'description'         => __( 'A single piece of portfolio work.', 'maulik-portfolio' ),
			'exclude_from_search' => true,
		);

		register_post_type( 'project', $args );
	}
}
add_action( 'init', 'maulik_portfolio_register_project_post_type', 10 );

if ( ! function_exists( 'maulik_portfolio_register_project_taxonomy' ) ) {
	/**
	 * Register the taxonomy the project card filter classes are built from.
	 *
	 * Runs on init at priority 9, one tick before the post type is registered.
	 * Register_taxonomy() does not require the object type to exist yet, so this
	 * order is not a requirement, but it means the term list is in place by the
	 * time anything asks for it on the same request.
	 *
	 * @since 0.5.0
	 *
	 * @return void
	 */
	function maulik_portfolio_register_project_taxonomy() {
		/*
		 * 'popular_items' is deliberately absent from this array rather than
		 * present with a null value.
		 *
		 * WordPress core documents that label as unused and its own default
		 * for it is null. Writing it explicitly as null makes PHPStan infer
		 * array<string, string|null> for the entire labels array, which
		 * register_taxonomy() does not accept because the stub types labels as
		 * array<string>. Omitting the key leaves the array a plain
		 * array<string> and keeps core's own default behaviour.
		 */
		$labels = array(
			'name'                       => _x( 'Project Categories', 'taxonomy general name', 'maulik-portfolio' ),
			'singular_name'              => _x( 'Project Category', 'taxonomy singular name', 'maulik-portfolio' ),
			'menu_name'                  => __( 'Categories', 'maulik-portfolio' ),
			'all_items'                  => __( 'All Categories', 'maulik-portfolio' ),
			'edit_item'                  => __( 'Edit Category', 'maulik-portfolio' ),
			'update_item'                => __( 'Update Category', 'maulik-portfolio' ),
			'add_new_item'               => __( 'Add New Category', 'maulik-portfolio' ),
			'new_item_name'              => __( 'New Category Name', 'maulik-portfolio' ),
			'search_items'               => __( 'Search Categories', 'maulik-portfolio' ),
			'not_found'                  => __( 'No categories found.', 'maulik-portfolio' ),
			'back_to_items'              => __( 'Back to Categories', 'maulik-portfolio' ),
			'choose_from_most_used'      => __( 'Most Used', 'maulik-portfolio' ),
			'separate_items_with_commas' => __( 'Separate categories with commas', 'maulik-portfolio' ),
			'add_or_remove_items'        => __( 'Add or remove categories', 'maulik-portfolio' ),
		);

		$args = array(
			'public'             => true,
			'publicly_queryable' => false,
			'query_var'          => false,
			'hierarchical'       => false,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'show_in_rest'       => true,
			'rest_base'          => 'project-categories',
			'rewrite'            => false,
			'labels'             => $labels,
			'description'        => __( 'A category a project card can be filtered by. The term slug is what the filter tabs match on.', 'maulik-portfolio' ),
		);

		register_taxonomy( MAULIK_PORTFOLIO_PROJECT_TAXONOMY, array( 'project' ), $args );
	}
}
add_action( 'init', 'maulik_portfolio_register_project_taxonomy', 9 );

if ( ! function_exists( 'maulik_portfolio_register_project_meta' ) ) {
	/**
	 * Register the post meta this theme exposes for a project.
	 *
	 * Seven keys, one per field the project card renders, all of them read by
	 * blocks/portfolio-list/render.php. The rule this follows is that a meta key
	 * which is not used is a key nobody can see the purpose of, so it is not
	 * registered until a block reads it.
	 *
	 * THE CAVEAT THAT WAS HERE BEFORE IS NOW RESOLVED. Registered meta does not
	 * appear in REST, and is not saved through the block editor, unless the post
	 * type also supports custom-fields. That support is added to the post type
	 * above, so the fields are editable from the editor sidebar rather than only
	 * by WP-CLI. The blocks still read every key with get_post_meta(), which
	 * does not go through REST, so a value written by a migration, by
	 * tools/seed-projects.php or by the editor is read the same way either.
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
		foreach ( maulik_portfolio_project_meta_schema() as $maulik_portfolio_meta_key => $maulik_portfolio_meta_args ) {
			if ( registered_meta_key_exists( 'post', $maulik_portfolio_meta_key, 'project' ) ) {
				continue;
			}

			register_post_meta(
				'project',
				$maulik_portfolio_meta_key,
				array(
					'type'              => 'string',
					'description'       => $maulik_portfolio_meta_args['description'],
					'single'            => true,
					'default'           => '',
					'show_in_rest'      => true,
					'sanitize_callback' => $maulik_portfolio_meta_args['sanitize_callback'],
					'auth_callback'     => 'maulik_portfolio_project_meta_auth_cb',
				)
			);
		}
	}
}
add_action( 'init', 'maulik_portfolio_register_project_meta', 10 );

if ( ! function_exists( 'maulik_portfolio_project_meta_list_sanitize_cb' ) ) {
	/**
	 * Sanitize callback for the two JSON list fields, nodes and tech.
	 *
	 * The stored value is a JSON string, so the callback has to accept the three
	 * shapes a value can arrive in: a real array from a seeding script, a JSON
	 * string from the editor or from a second save, and anything else at all,
	 * which is treated as an empty list rather than being allowed to produce a
	 * notice. Returning a JSON string rather than an array is deliberate, so the
	 * registered type stays string and a second save of an unchanged value
	 * produces the same bytes.
	 *
	 * Separators are data, not decoration. A slash in the nodes list is the
	 * separator between two nodes, so it is sanitized as text and kept, and the
	 * render file decides from the value which element type to print.
	 *
	 * @since 0.5.0
	 *
	 * @param mixed $value Raw value from the editor, a seeder or a filter.
	 * @return string A JSON array string, always.
	 */
	function maulik_portfolio_project_meta_list_sanitize_cb( $value ) {
		$items = $value;

		if ( is_string( $items ) ) {
			$decoded = json_decode( $items, true );
			$items   = is_array( $decoded ) ? $decoded : array();
		}

		if ( ! is_array( $items ) ) {
			$items = array();
		}

		$clean = array();

		foreach ( $items as $item ) {
			if ( ! is_scalar( $item ) ) {
				continue;
			}

			$item = sanitize_text_field( (string) $item );

			if ( '' !== $item ) {
				$clean[] = $item;
			}
		}

		/*
		 * $clean is already a list. It is only ever appended to after being
		 * initialised as an empty array, and every unset or empty entry is
		 * skipped rather than spliced out, so its keys are already sequential
		 * and array_values() would have no effect. Calling it anyway is what
		 * PHPStan flagged as arrayValues.list.
		 */
		return (string) wp_json_encode( $clean, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	}
}

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
		return maulik_portfolio_get_project_field( $post_id, MAULIK_PORTFOLIO_PROJECT_META_ROLE );
	}
}

if ( ! function_exists( 'maulik_portfolio_get_project_field' ) ) {
	/**
	 * Read one single line project field.
	 *
	 * An absent value and a value of the wrong type are the same case here: both
	 * are an empty string. That is what lets the card render a project that was
	 * created without the optional fields filled in, without a notice, without a
	 * warning, and without an element that is empty of meaning.
	 *
	 * @since 0.5.0
	 *
	 * @param int    $post_id  Project post ID.
	 * @param string $meta_key Meta key constant.
	 * @return string The trimmed value, or an empty string when it is not set.
	 */
	function maulik_portfolio_get_project_field( $post_id, $meta_key ) {
		$value = get_post_meta( (int) $post_id, $meta_key, true );

		if ( ! is_string( $value ) ) {
			return '';
		}

		return trim( $value );
	}
}

if ( ! function_exists( 'maulik_portfolio_get_project_list' ) ) {
	/**
	 * Read one of the two JSON list fields, nodes or tech.
	 *
	 * Handles both stored shapes, so it does not matter whether the row holds a
	 * JSON string, which is what the sanitizer writes, or a serialised array,
	 * which is what a direct update_post_meta() call with a PHP array writes.
	 * Anything that is neither is an empty list.
	 *
	 * @since 0.5.0
	 *
	 * @param int    $post_id  Project post ID.
	 * @param string $meta_key Meta key constant.
	 * @return string[] Trimmed, non empty list values in stored order.
	 */
	function maulik_portfolio_get_project_list( $post_id, $meta_key ) {
		$raw = get_post_meta( (int) $post_id, $meta_key, true );

		if ( is_string( $raw ) ) {
			$trimmed = trim( $raw );

			if ( '' === $trimmed ) {
				return array();
			}

			$decoded = json_decode( $trimmed, true );
			$raw     = is_array( $decoded ) ? $decoded : array();
		}

		if ( ! is_array( $raw ) ) {
			return array();
		}

		$items = array();

		foreach ( $raw as $item ) {
			if ( ! is_scalar( $item ) ) {
				continue;
			}

			$item = trim( (string) $item );

			if ( '' !== $item ) {
				$items[] = $item;
			}
		}

		return $items;
	}
}

if ( ! function_exists( 'maulik_portfolio_get_project_category_classes' ) ) {
	/**
	 * Read the pj-cat-* class names a project is filtered by.
	 *
	 * The class is the term slug with the pj-cat- prefix put back on, which is
	 * the string assets/js/project-filters.js and the filter tabs already match
	 * on. Terms come back in name order, so the class list order follows the
	 * category names rather than the order they were ticked in; nothing reads
	 * the order, because the filter tests for the presence of a class and not
	 * for a position.
	 *
	 * @since 0.5.0
	 *
	 * @param int $post_id Project post ID.
	 * @return string[] pj-cat-* class names, deduplicated.
	 */
	function maulik_portfolio_get_project_category_classes( $post_id ) {
		$terms = get_the_terms( (int) $post_id, MAULIK_PORTFOLIO_PROJECT_TAXONOMY );

		if ( ! is_array( $terms ) ) {
			return array();
		}

		$classes = array();

		foreach ( $terms as $term ) {
			if ( ! $term instanceof WP_Term ) {
				continue;
			}

			$slug = sanitize_html_class( $term->slug );

			if ( '' !== $slug ) {
				$classes[] = 'pj-cat-' . $slug;
			}
		}

		return array_values( array_unique( $classes ) );
	}
}
