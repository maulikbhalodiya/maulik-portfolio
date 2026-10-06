<?php
/**
 * Seed the project post type with the nine professional case studies.
 *
 * WHY THE VALUES ARE IN THIS FILE AND NOT READ FROM SOMEWHERE ELSE.
 *
 * The nine cards on the Projects page were static markup, so the copy behind
 * them lived in a template and existed nowhere a database could reach it. This
 * script is that copy in the form the CPT needs. The values are transcribed
 * from the live page, once, by hand, and they are checked into the repository:
 * a seed that reads its source at run time is a seed whose result depends on
 * whether some file still exists at that moment, and a temporary path is the
 * least durable place to keep the only copy of approved text. It was verified
 * against the rendered page before being transcribed here.
 *
 * Run it with wp eval-file, never with a bare php binary, because the script
 * depends on the post type and the taxonomy being registered:
 *
 *     wp eval-file tools/seed-projects.php -- --path=/var/www/maulik-dev
 *     MAULIK_PORTFOLIO_APPLY=1 wp eval-file tools/seed-projects.php -- --path=/var/www/maulik-dev
 *     MAULIK_PORTFOLIO_APPLY=1 MAULIK_PORTFOLIO_FORCE=1 wp eval-file tools/seed-projects.php
 *
 * THE SWITCHES ARE ENVIRONMENT VARIABLES, FOR THE SAME REASON AS IN
 * tools/migrate-content.php. wp-cli validates every flag that follows the
 * script path against eval-file's own synopsis and rejects anything it does
 * not recognise, so -- --apply never reaches the script and fails with an
 * "unknown --apply parameter" message instead. Command line switches are still
 * honoured for the same reason they are there: they work when the file is run
 * as plain PHP outside wp-cli.
 *
 * ORDER MATTERS, AND IT IS NOT THIS SCRIPT'S JOB TO CHANGE IT. The Projects
 * page is migrated to the block by tools/migrate-content.php, and the page
 * content and this seed are two separate deliberate steps. Run the seed first
 * if the posts are to exist before the page points at them, and never the other
 * way round into a half seeded state.
 *
 * PROJECTS ARE FOUND BY SLUG, NEVER BY POST ID. The slug is the one field that
 * both the card id and the case study anchor are derived from, and a hardcoded
 * ID would break the moment a project is recreated, reimported, or the site is
 * pointed at a different database.
 *
 * THE POSITION IN THE GRID IS menu_order, NOT A NUMBER IN A LABEL. Each
 * project is written with menu_order 1 through 9, which is what the block's
 * menu-order query sorts on, so the order the cards print in is stored as
 * order rather than typed into nine index labels. The index label is still
 * seeded, because the static page printed one and the card renders it.
 *
 * THE LINK LABEL IS NOT SEEDED. "View Case Study" is interface copy that
 * render.php prints through esc_html_x(), so it is translated with the rest of
 * the theme's strings and belongs to the theme rather than to one project.
 * Seeding it per project would make nine copies of one string the editor could
 * change to nine different things.
 *
 * THE TERM NAMES ARE NOT OVERWRITTEN. Terms are created by slug when they do
 * not exist and looked up by slug when they do, so a category the editor has
 * retitled in the admin keeps the name they gave it. What matters for the
 * filter tabs is the slug, and the slug is never rewritten.
 *
 * DRY RUN IS THE DEFAULT. Nothing is written unless MAULIK_PORTFOLIO_APPLY=1.
 * A dry run touches no rows and prints, per project, what the database holds,
 * what this file holds, and which of create, write, unchanged or conflict the
 * run would take.
 *
 * IDEMPOTENT BY CONSTRUCTION. Each write records md5() of the canonical payload
 * in post meta under _maulik_portfolio_seed_hash, with
 * _maulik_portfolio_seed_at. A later run rebuilds the payload from what is in
 * the database and compares it. Equal means nothing to do. Different means
 * somebody has edited the project in the admin since this script last wrote it,
 * and that project is reported as a conflict and SKIPPED; overriding it
 * requires MAULIK_PORTFOLIO_FORCE=1. A project with no stored hash is not a
 * conflict, because that is the first run against a database this script has
 * never touched.
 *
 * NO BACKUP DIRECTORY AND NO ROLLBACK, AND THE REASON IS RECORDED. This script
 * never touches post_content, which is what tools/migrate-content.php backs up,
 * and it only ever writes fields it owns to a project it identifies by slug. A
 * rollback here would be a delete of posts the owner may since have edited, and
 * a delete is not a recoverable operation to add to a script whose whole job is
 * to make the database agree with a file.
 *
 * FAIL LOUDLY. A missing project post type, a missing taxonomy, a slug that
 * resolves to a project with a different name than asked for, or a row that
 * reads back different from what was written, all stop the run with a non zero
 * exit and a message naming the slug. A seed that half succeeded and reported
 * success is worse than one that stopped.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta key holding md5() of the payload as this script last wrote it.
 */
const MAULIK_PORTFOLIO_SEED_HASH = '_maulik_portfolio_seed_hash';

/**
 * Meta key holding the UTC time of the last successful write.
 */
const MAULIK_PORTFOLIO_SEED_AT = '_maulik_portfolio_seed_at';

/**
 * Meta key holding the file the payload came from, for traceability.
 */
const MAULIK_PORTFOLIO_SEED_SOURCE = '_maulik_portfolio_seed_source';

/**
 * Decision: the project does not exist and would be created.
 */
const MAULIK_PORTFOLIO_SEED_DECISION_CREATE = 'create';

/**
 * Decision: the project exists and differs from this file.
 */
const MAULIK_PORTFOLIO_SEED_DECISION_WRITE = 'write';

/**
 * Decision: the database already matches this file exactly.
 */
const MAULIK_PORTFOLIO_SEED_DECISION_UNCHANGED = 'unchanged';

/**
 * Decision: the project was edited in the admin since the last run.
 */
const MAULIK_PORTFOLIO_SEED_DECISION_CONFLICT = 'conflict';

/**
 * Human readable names for the category slugs the cards are filtered by.
 *
 * Slug to name only, never the reverse. The slug is what
 * assets/js/project-filters.js and the pj-cat-* classes match on, so it is the
 * field that must not be reworded.
 *
 * @return array<string,string> Map of term slug to term name.
 */
function maulik_portfolio_seed_category_names() {
	return array(
		'api'          => 'API',
		'automation'   => 'Automation',
		'payment'      => 'Payment',
		'php'          => 'PHP',
		'professional' => 'Professional',
		'security'     => 'Security',
		'woocommerce'  => 'WooCommerce',
		'wordpress'    => 'WordPress',
	);
}

/**
 * The nine professional case studies, in the order they appear on the page.
 *
 * Every string here is the string the static card printed. None of it is
 * paraphrased, reworded, pluralised or abbreviated, because the node count
 * label in particular is singular or plural only according to the number
 * already written into it, and the filter tabs match on the category slugs.
 *
 * The keys are the post meta constants except for slug, title, desc and cats,
 * which are post fields and taxonomy slugs respectively.
 *
 * @return array<int,array<string,mixed>> Ordered list of project payloads.
 */
function maulik_portfolio_seed_projects() {
	return array(
		array(
			'slug'       => 'case-study-01',
			'title'      => 'Cross Domain Payment Architecture',
			'desc'       => 'Reusable cross domain payment architecture featuring an approved payment environment, iframe checkout interface, HMAC SHA 256 signed REST communication, shared secret authentication and multi gateway webhook processing.',
			'menu_order' => 1,
			'meta'       => array(
				MAULIK_PORTFOLIO_PROJECT_META_INDEX_LABEL => '01 · Professional Project',
				MAULIK_PORTFOLIO_PROJECT_META_ROLE        => 'Payments and Security Architecture',
				MAULIK_PORTFOLIO_PROJECT_META_SCHEME      => 'Architecture Pipeline',
				MAULIK_PORTFOLIO_PROJECT_META_NODE_COUNT  => '5 System Nodes',
				MAULIK_PORTFOLIO_PROJECT_META_NODES       => array(
					'Origin WordPress Site',
					'/',
					'HMAC SHA 256 Signer',
					'/',
					'Approved Payment Environment',
					'/',
					'Multi Gateway Router',
					'/',
					'Signed Webhook Callback',
				),
				MAULIK_PORTFOLIO_PROJECT_META_TECH        => array(
					'WordPress',
					'PHP',
					'Gravity Forms',
					'REST APIs',
					'Payment Gateways',
					'HMAC SHA 256',
				),
				MAULIK_PORTFOLIO_PROJECT_META_HREF        => '#case-study-01',
			),
			'cats'       => array( 'professional', 'wordpress', 'php', 'api', 'payment', 'security' ),
		),
		array(
			'slug'       => 'case-study-02',
			'title'      => 'Employee Application Management System',
			'desc'       => 'Role based employee application management workflow built on WordPress, PHP, Gravity Forms, Forminator and AJAX with application assignment, asynchronous data loading and structured status transitions.',
			'menu_order' => 2,
			'meta'       => array(
				MAULIK_PORTFOLIO_PROJECT_META_INDEX_LABEL => '02 · Professional Project',
				MAULIK_PORTFOLIO_PROJECT_META_ROLE        => 'Workflow and Access Control',
				MAULIK_PORTFOLIO_PROJECT_META_SCHEME      => 'Architecture Pipeline',
				MAULIK_PORTFOLIO_PROJECT_META_NODE_COUNT  => '4 System Nodes',
				MAULIK_PORTFOLIO_PROJECT_META_NODES       => array(
					'Form Intake Layer',
					'/',
					'Custom Role and Capability Guard',
					'/',
					'Assignment and Status Engine',
					'/',
					'Asynchronous AJAX Interface',
				),
				MAULIK_PORTFOLIO_PROJECT_META_TECH        => array(
					'WordPress',
					'PHP',
					'Gravity Forms',
					'Forminator',
					'AJAX',
					'Custom Roles',
				),
				MAULIK_PORTFOLIO_PROJECT_META_HREF        => '#case-study-02',
			),
			'cats'       => array( 'professional', 'wordpress', 'php', 'automation' ),
		),
		array(
			'slug'       => 'case-study-03',
			'title'      => 'Identity Document OCR Integration',
			'desc'       => 'Custom WordPress plugin integrating Azure Form Recognizer with Gravity Forms to analyze uploaded identity documents via asynchronous AJAX polling and automatically map extracted OCR data into form fields.',
			'menu_order' => 3,
			'meta'       => array(
				MAULIK_PORTFOLIO_PROJECT_META_INDEX_LABEL => '03 · Professional Project',
				MAULIK_PORTFOLIO_PROJECT_META_ROLE        => 'API Integration and Automation',
				MAULIK_PORTFOLIO_PROJECT_META_SCHEME      => 'Architecture Pipeline',
				MAULIK_PORTFOLIO_PROJECT_META_NODE_COUNT  => '4 System Nodes',
				MAULIK_PORTFOLIO_PROJECT_META_NODES       => array(
					'Gravity Forms Upload Trigger',
					'/',
					'WordPress PHP API Proxy',
					'/',
					'Azure Form Recognizer',
					'/',
					'AJAX Polling and Field Mapper',
				),
				MAULIK_PORTFOLIO_PROJECT_META_TECH        => array(
					'WordPress',
					'PHP',
					'Azure Form Recognizer',
					'AJAX',
					'Gravity Forms',
				),
				MAULIK_PORTFOLIO_PROJECT_META_HREF        => '#case-study-03',
			),
			'cats'       => array( 'professional', 'wordpress', 'php', 'api', 'automation' ),
		),
		array(
			'slug'       => 'case-study-04',
			'title'      => 'Secure WordPress Data Encryption',
			'desc'       => 'Field level encryption of sensitive LatePoint appointment data in WordPress using PHP Sodium and an external encryption key, paired with capability gated decryption for authorized roles.',
			'menu_order' => 4,
			'meta'       => array(
				MAULIK_PORTFOLIO_PROJECT_META_INDEX_LABEL => '04 · Professional Project',
				MAULIK_PORTFOLIO_PROJECT_META_ROLE        => 'Security and Cryptography',
				MAULIK_PORTFOLIO_PROJECT_META_SCHEME      => 'Architecture Pipeline',
				MAULIK_PORTFOLIO_PROJECT_META_NODE_COUNT  => '4 System Nodes',
				MAULIK_PORTFOLIO_PROJECT_META_NODES       => array(
					'LatePoint Booking Lifecycle',
					'/',
					'PHP Sodium Crypto Service',
					'/',
					'External Key Provider',
					'/',
					'Capability Decryption Guard',
				),
				MAULIK_PORTFOLIO_PROJECT_META_TECH        => array(
					'WordPress',
					'PHP',
					'Sodium',
					'LatePoint',
					'Encryption',
				),
				MAULIK_PORTFOLIO_PROJECT_META_HREF        => '#case-study-04',
			),
			'cats'       => array( 'professional', 'wordpress', 'php', 'security' ),
		),
		array(
			'slug'       => 'case-study-05',
			'title'      => 'Rank Math SEO Engineering',
			'desc'       => 'Professional engineering work involving Rank Math SEO within WordPress environments. Detailed technical scope and verified responsibilities are marked with structured placeholders pending public release verification.',
			'menu_order' => 5,
			'meta'       => array(
				MAULIK_PORTFOLIO_PROJECT_META_INDEX_LABEL => '05 · Professional Project',
				MAULIK_PORTFOLIO_PROJECT_META_ROLE        => 'SEO and WordPress Engineering',
				MAULIK_PORTFOLIO_PROJECT_META_SCHEME      => 'Architecture Pipeline',
				MAULIK_PORTFOLIO_PROJECT_META_NODE_COUNT  => '3 System Nodes',
				MAULIK_PORTFOLIO_PROJECT_META_NODES       => array(
					'WordPress Content Context',
					'/',
					'Rank Math Hook Interface',
					'/',
					'Verified Output Layer',
				),
				MAULIK_PORTFOLIO_PROJECT_META_TECH        => array(
					'WordPress',
					'PHP',
					'Rank Math SEO',
					'Metadata',
					'Schema',
				),
				MAULIK_PORTFOLIO_PROJECT_META_HREF        => '#case-study-05',
			),
			'cats'       => array( 'professional', 'wordpress', 'php' ),
		),
		array(
			'slug'       => 'case-study-06',
			'title'      => 'Brevo API Automation',
			'desc'       => 'Professional WordPress and PHP integration connecting site events with the Brevo REST API for automated contact synchronization and transactional communication workflows.',
			'menu_order' => 6,
			'meta'       => array(
				MAULIK_PORTFOLIO_PROJECT_META_INDEX_LABEL => '06 · Professional Project',
				MAULIK_PORTFOLIO_PROJECT_META_ROLE        => 'API and Marketing Automation',
				MAULIK_PORTFOLIO_PROJECT_META_SCHEME      => 'Architecture Pipeline',
				MAULIK_PORTFOLIO_PROJECT_META_NODE_COUNT  => '2 System Nodes',
				MAULIK_PORTFOLIO_PROJECT_META_NODES       => array(
					'WordPress Event Trigger',
					'/',
					'PHP Brevo API Client',
				),
				MAULIK_PORTFOLIO_PROJECT_META_TECH        => array(
					'WordPress',
					'PHP',
					'Brevo API',
					'REST APIs',
					'JSON',
				),
				MAULIK_PORTFOLIO_PROJECT_META_HREF        => '#case-study-06',
			),
			'cats'       => array( 'professional', 'wordpress', 'php', 'api', 'automation' ),
		),
		array(
			'slug'       => 'case-study-07',
			'title'      => 'Distance Based Dynamic Pricing',
			'desc'       => 'Custom PHP calculation workflow in WordPress for computing dynamic pricing rules based on distance parameters and integrating calculated totals into checkout flows.',
			'menu_order' => 7,
			'meta'       => array(
				MAULIK_PORTFOLIO_PROJECT_META_INDEX_LABEL => '07 · Professional Project',
				MAULIK_PORTFOLIO_PROJECT_META_ROLE        => 'Backend Calculation and E Commerce',
				MAULIK_PORTFOLIO_PROJECT_META_SCHEME      => 'Architecture Pipeline',
				MAULIK_PORTFOLIO_PROJECT_META_NODE_COUNT  => '2 System Nodes',
				MAULIK_PORTFOLIO_PROJECT_META_NODES       => array(
					'Customer Location Input',
					'/',
					'PHP Pricing Rule Engine',
				),
				MAULIK_PORTFOLIO_PROJECT_META_TECH        => array(
					'WordPress',
					'PHP',
					'Dynamic Pricing',
					'REST APIs',
					'AJAX',
				),
				MAULIK_PORTFOLIO_PROJECT_META_HREF        => '#case-study-07',
			),
			'cats'       => array( 'professional', 'wordpress', 'php', 'api', 'woocommerce' ),
		),
		array(
			'slug'       => 'case-study-08',
			'title'      => 'Object Storage Automation',
			'desc'       => 'Automated media and document offloading workflow connecting WordPress PHP upload lifecycles with external object storage APIs.',
			'menu_order' => 8,
			'meta'       => array(
				MAULIK_PORTFOLIO_PROJECT_META_INDEX_LABEL => '08 · Professional Project',
				MAULIK_PORTFOLIO_PROJECT_META_ROLE        => 'Cloud Storage and Automation',
				MAULIK_PORTFOLIO_PROJECT_META_SCHEME      => 'Architecture Pipeline',
				MAULIK_PORTFOLIO_PROJECT_META_NODE_COUNT  => '2 System Nodes',
				MAULIK_PORTFOLIO_PROJECT_META_NODES       => array(
					'WordPress Upload Lifecycle',
					'/',
					'Object Storage PHP Connector',
				),
				MAULIK_PORTFOLIO_PROJECT_META_TECH        => array(
					'WordPress',
					'PHP',
					'Object Storage',
					'REST APIs',
					'Automation',
				),
				MAULIK_PORTFOLIO_PROJECT_META_HREF        => '#case-study-08',
			),
			'cats'       => array( 'professional', 'wordpress', 'php', 'api', 'automation' ),
		),
		array(
			'slug'       => 'case-study-09',
			'title'      => 'WooCommerce Integration',
			'desc'       => 'Custom WooCommerce backend engineering covering order lifecycle hooks, custom product workflows and third party data synchronization.',
			'menu_order' => 9,
			'meta'       => array(
				MAULIK_PORTFOLIO_PROJECT_META_INDEX_LABEL => '09 · Professional Project',
				MAULIK_PORTFOLIO_PROJECT_META_ROLE        => 'E Commerce and Backend Systems',
				MAULIK_PORTFOLIO_PROJECT_META_SCHEME      => 'Architecture Pipeline',
				MAULIK_PORTFOLIO_PROJECT_META_NODE_COUNT  => '2 System Nodes',
				MAULIK_PORTFOLIO_PROJECT_META_NODES       => array(
					'WooCommerce Order Lifecycle',
					'/',
					'Custom PHP Extension Layer',
				),
				MAULIK_PORTFOLIO_PROJECT_META_TECH        => array(
					'WordPress',
					'PHP',
					'WooCommerce',
					'Hooks',
					'REST APIs',
				),
				MAULIK_PORTFOLIO_PROJECT_META_HREF        => '#case-study-09',
			),
			'cats'       => array( 'professional', 'wordpress', 'php', 'api', 'woocommerce' ),
		),
	);
}

/**
 * Print one line of report output, escaped.
 *
 * Every line this script prints goes through here, for the same reason
 * tools/migrate-content.php does: the values are titles, slugs and hashes, and
 * escaping the composed line is the one place that needs to be right.
 *
 * @param string $line Line to print, without its trailing newline.
 *
 * @return void
 */
function maulik_portfolio_seed_out( $line ) {
	echo esc_html( $line ), "\n";
}

/**
 * Print a hard failure to the report and stop the run.
 *
 * @param string $message Message to print.
 *
 * @return void
 */
function maulik_portfolio_seed_die( $message ) {
	maulik_portfolio_seed_out( sprintf( 'FAIL: %s', $message ) );
	exit( 1 );
}

/**
 * Refuse to run unless the post type and the taxonomy are registered.
 *
 * Running against a theme that is not active, or before init has registered the
 * project type, would otherwise create nothing and report success, which is the
 * failure mode a seed script must never have.
 *
 * @return void
 */
function maulik_portfolio_seed_require_types() {
	if ( ! post_type_exists( 'project' ) ) {
		maulik_portfolio_seed_die( 'The project post type is not registered. The maulik-portfolio theme must be active.' );
	}

	if ( ! taxonomy_exists( MAULIK_PORTFOLIO_PROJECT_TAXONOMY ) ) {
		maulik_portfolio_seed_die(
			sprintf( 'The %s taxonomy is not registered.', MAULIK_PORTFOLIO_PROJECT_TAXONOMY )
		);
	}
}

/**
 * Find the project for a slug, or report that there is none.
 *
 * Both name and post_name are passed for the reason recorded in
 * tools/migrate-content.php: the lookup must resolve by slug, and the result
 * is checked against the slug asked for so a lookup that quietly degraded into
 * "the first published project" cannot rewrite nine posts into nine copies of
 * one.
 *
 * @param string $slug Project slug.
 *
 * @return WP_Post|null The project, or null when no such project exists.
 */
function maulik_portfolio_seed_find_project( $slug ) {
	$found = get_posts(
		array(
			'post_type'              => 'project',
			'post_status'            => 'any',
			'name'                   => $slug,
			'post_name'              => $slug,
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => true,
		)
	);

	if ( array() === $found ) {
		return null;
	}

	$project = $found[0];

	if ( $project->post_name !== $slug ) {
		maulik_portfolio_seed_die(
			sprintf(
				'Slug lookup for "%s" returned "%s" (id %d). Refusing to write.',
				$slug,
				$project->post_name,
				$project->ID
			)
		);
	}

	return $project;
}

/**
 * Read the category slugs on a project.
 *
 * @param int $post_id Project post ID.
 *
 * @return string[] Term slugs, sorted, so a hash of them does not depend on the
 *                   order the database happens to return terms in.
 */
function maulik_portfolio_seed_read_cats( $post_id ) {
	$terms = get_the_terms( $post_id, MAULIK_PORTFOLIO_PROJECT_TAXONOMY );

	if ( ! is_array( $terms ) ) {
		return array();
	}

	$slugs = array();

	foreach ( $terms as $term ) {
		if ( $term instanceof WP_Term ) {
			$slugs[] = $term->slug;
		}
	}

	sort( $slugs );

	return $slugs;
}

/**
 * Normalise a payload into the canonical form that is hashed and compared.
 *
 * Category order is normalised away and list order is not, because the order of
 * the nodes and of the technologies is the order they print in, while the order
 * the database returns terms in is not a fact about the project.
 *
 * @param array<string,mixed> $payload Raw payload.
 *
 * @return array<string,mixed> Canonical payload.
 */
function maulik_portfolio_seed_canonical( $payload ) {
	$cats = isset( $payload['cats'] ) ? (array) $payload['cats'] : array();
	sort( $cats );

	$meta = isset( $payload['meta'] ) ? (array) $payload['meta'] : array();
	ksort( $meta );

	return array(
		'slug'       => (string) $payload['slug'],
		'title'      => (string) $payload['title'],
		'desc'       => (string) $payload['desc'],
		'menu_order' => (int) $payload['menu_order'],
		'meta'       => $meta,
		'cats'       => array_values( $cats ),
	);
}

/**
 * Read a project back from the database and describe it in payload shape.
 *
 * The list fields are read through the theme's own reader, so the comparison
 * sees decoded arrays rather than JSON text and a difference in JSON formatting
 * is not reported as a change to the project.
 *
 * @param WP_Post $project The project.
 *
 * @return array<string,mixed> Canonical payload as the database holds it.
 */
function maulik_portfolio_seed_read_project( $project ) {
	$meta = array();

	foreach ( maulik_portfolio_project_meta_schema() as $meta_key => $unused ) {
		unset( $unused );

		$meta[ $meta_key ] = ( MAULIK_PORTFOLIO_PROJECT_META_NODES === $meta_key || MAULIK_PORTFOLIO_PROJECT_META_TECH === $meta_key )
			? maulik_portfolio_get_project_list( $project->ID, $meta_key )
			: maulik_portfolio_get_project_field( $project->ID, $meta_key );
	}

	return maulik_portfolio_seed_canonical(
		array(
			'slug'       => (string) $project->post_name,
			'title'      => (string) $project->post_title,
			'desc'       => (string) $project->post_excerpt,
			'menu_order' => (int) $project->menu_order,
			'meta'       => $meta,
			'cats'       => maulik_portfolio_seed_read_cats( $project->ID ),
		)
	);
}

/**
 * The payload as the database currently holds it for a project.
 *
 * @param WP_Post $project The project.
 *
 * @return string md5 of the canonical payload.
 */
function maulik_portfolio_seed_current_hash( $project ) {
	$payload = maulik_portfolio_seed_read_project( $project );

	return md5( (string) wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
}

/**
 * Decide what one project would do on this run, without writing anything.
 *
 * @param WP_Post|null $project The existing project, or null.
 * @param array        $payload The payload from this file, canonicalised.
 * @param bool         $force   Whether admin edits may be overwritten.
 *
 * @return string One of the MAULIK_PORTFOLIO_SEED_DECISION_* values.
 */
function maulik_portfolio_seed_decide( $project, $payload, $force ) {
	if ( ! $project instanceof WP_Post ) {
		return MAULIK_PORTFOLIO_SEED_DECISION_CREATE;
	}

	$wanted  = md5( (string) wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
	$current = maulik_portfolio_seed_current_hash( $project );
	$stored  = (string) get_post_meta( $project->ID, MAULIK_PORTFOLIO_SEED_HASH, true );

	if ( '' !== $stored && $wanted === $stored ) {
		return MAULIK_PORTFOLIO_SEED_DECISION_UNCHANGED;
	}

	if ( '' !== $stored && $current !== $stored && ! $force ) {
		return MAULIK_PORTFOLIO_SEED_DECISION_CONFLICT;
	}

	return MAULIK_PORTFOLIO_SEED_DECISION_WRITE;
}

/**
 * Resolve category slugs to term IDs, creating any term that does not exist.
 *
 * A term that already exists is used as it is. Passing a name for an existing
 * term would rename it, and a category the editor has retitled must keep the
 * name they gave it; only the slug is load bearing.
 *
 * @param string[] $slugs Category slugs.
 * @param bool     $apply Whether terms may be created. False in a dry run, so
 *                        the dry run reports the terms that would be created
 *                        rather than creating them.
 *
 * @return int[] Term IDs in the order the slugs were given.
 */
function maulik_portfolio_seed_term_ids( $slugs, $apply ) {
	$names = maulik_portfolio_seed_category_names();
	$ids   = array();

	foreach ( $slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, MAULIK_PORTFOLIO_PROJECT_TAXONOMY );

		if ( $term instanceof WP_Term ) {
			$ids[] = (int) $term->term_id;
			continue;
		}

		if ( ! isset( $names[ $slug ] ) ) {
			maulik_portfolio_seed_die(
				sprintf( 'Category "%s" has no name in this script. Add it before seeding.', $slug )
			);
		}

		if ( ! $apply ) {
			maulik_portfolio_seed_out( sprintf( '             would create the category term %s', $slug ) );
			$ids[] = 0;
			continue;
		}

		$created = wp_insert_term( $names[ $slug ], MAULIK_PORTFOLIO_PROJECT_TAXONOMY, array( 'slug' => $slug ) );

		if ( is_wp_error( $created ) ) {
			maulik_portfolio_seed_die(
				sprintf( 'Cannot create the category term "%s": %s', $slug, $created->get_error_message() )
			);
		}

		maulik_portfolio_seed_out( sprintf( '             created the category term %s', $slug ) );
		$ids[] = (int) $created['term_id'];
	}

	return array_values( array_filter( $ids ) );
}

/**
 * Create or update one project and record the payload hash.
 *
 * The post content column is never touched. The card renders from the post
 * fields, the meta and the taxonomy, and the content column is left exactly as
 * it is found so seeding cannot destroy an editor's body copy.
 *
 * @param WP_Post|null $project Existing project, or null to create one.
 * @param array        $payload Canonical payload from this file.
 * @param string[]     $term_ids Category term IDs.
 *
 * @return array{id:int,error:bool,message:string} Outcome of the write.
 */
function maulik_portfolio_seed_write( $project, $payload, $term_ids ) {
	$postarr = array(
		'post_type'    => 'project',
		'post_status'  => 'publish',
		'post_title'   => $payload['title'],
		'post_name'    => $payload['slug'],
		'post_excerpt' => $payload['desc'],
		'menu_order'   => $payload['menu_order'],
	);

	if ( $project instanceof WP_Post ) {
		$postarr['ID'] = $project->ID;
	}

	$result = wp_insert_post( $postarr, true );

	if ( is_wp_error( $result ) ) {
		return array(
			'id'      => 0,
			'error'   => true,
			'message' => $result->get_error_message(),
		);
	}

	$post_id = (int) $result;

	/*
	 * update_post_meta() runs sanitize_meta() on the value, so the two list
	 * fields can be written as the PHP arrays they are and the registered
	 * sanitizer is what turns them into the JSON the block reads. Passing JSON
	 * in by hand here would mean the same encoding rule exists twice.
	 */
	foreach ( $payload['meta'] as $meta_key => $meta_value ) {
		update_post_meta( $post_id, $meta_key, $meta_value );
	}

	$terms = wp_set_object_terms( $post_id, $term_ids, MAULIK_PORTFOLIO_PROJECT_TAXONOMY, false );

	if ( is_wp_error( $terms ) ) {
		return array(
			'id'      => $post_id,
			'error'   => true,
			'message' => $terms->get_error_message(),
		);
	}

	update_post_meta( $post_id, MAULIK_PORTFOLIO_SEED_HASH, md5( (string) wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ) );
	update_post_meta( $post_id, MAULIK_PORTFOLIO_SEED_AT, gmdate( 'c' ) );
	update_post_meta( $post_id, MAULIK_PORTFOLIO_SEED_SOURCE, 'tools/seed-projects.php' );

	return array(
		'id'      => $post_id,
		'error'   => false,
		'message' => '',
	);
}

/**
 * Read a project back and confirm it holds what was written.
 *
 * This re-reads through get_post() rather than trusting wp_insert_post(),
 * because a filter on save_post or on post_content can change what landed in
 * the row.
 *
 * @param int    $post_id The project to verify.
 * @param string $slug    Project slug, used in messages.
 * @param array  $payload Canonical payload that was written.
 *
 * @return void
 */
function maulik_portfolio_seed_verify( $post_id, $slug, $payload ) {
	$saved = get_post( $post_id );

	if ( ! $saved ) {
		maulik_portfolio_seed_die( sprintf( 'Project "%s" disappeared after the write', $slug ) );
	}

	$wanted   = md5( (string) wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
	$observed = maulik_portfolio_seed_current_hash( $saved );

	if ( $wanted !== $observed ) {
		maulik_portfolio_seed_die(
			sprintf(
				'Project "%s" read back different from this file. A filter on save_post, post_content or the meta changed it.',
				$slug
			)
		);
	}
}

/**
 * Print what the database holds and what this file holds for one project.
 *
 * @param WP_Post|null $project Existing project, or null.
 * @param array        $payload Canonical payload from this file.
 *
 * @return void
 */
function maulik_portfolio_seed_report( $project, $payload ) {
	if ( ! $project instanceof WP_Post ) {
		maulik_portfolio_seed_out( sprintf( '%s: not present', $payload['slug'] ) );
	} else {
		$cats = maulik_portfolio_seed_read_cats( $project->ID );

		maulik_portfolio_seed_out(
			sprintf(
				'%s id=%d status=%s menu_order=%d cats=%s',
				$project->post_name,
				$project->ID,
				$project->post_status,
				$project->menu_order,
				array() === $cats ? '(none)' : implode( ',', $cats )
			)
		);
	}

	maulik_portfolio_seed_out(
		sprintf(
			'             file: title="%s" menu_order=%d cats=%s',
			$payload['title'],
			$payload['menu_order'],
			implode( ',', $payload['cats'] )
		)
	);

	$summary = array();

	foreach ( $payload['meta'] as $meta_key => $meta_value ) {
		if ( MAULIK_PORTFOLIO_PROJECT_META_NODES === $meta_key || MAULIK_PORTFOLIO_PROJECT_META_TECH === $meta_key ) {
			$summary[] = sprintf( '%s=%d items', $meta_key, count( (array) $meta_value ) );
			continue;
		}

		$summary[] = sprintf( '%s="%s"', $meta_key, (string) $meta_value );
	}

	maulik_portfolio_seed_out( sprintf( '             meta: %s', implode( ' ', $summary ) ) );

	if ( $project instanceof WP_Post ) {
		maulik_portfolio_seed_out(
			sprintf(
				'             now:  md5 %s, stored hash %s',
				substr( maulik_portfolio_seed_current_hash( $project ), 0, 12 ),
				substr( (string) get_post_meta( $project->ID, MAULIK_PORTFOLIO_SEED_HASH, true ), 0, 12 )
			)
		);
	}
}

/**
 * Run the seed.
 *
 * @param array $args Raw arguments from wp-cli.
 *
 * @return void
 */
function maulik_portfolio_seed_run( $args ) {
	$opts = maulik_portfolio_seed_args( $args );

	maulik_portfolio_seed_require_types();

	maulik_portfolio_seed_out(
		sprintf(
			'=== Project seed: %s ===',
			$opts['apply'] ? 'APPLY' : 'DRY RUN, no writes'
		)
	);
	maulik_portfolio_seed_out( sprintf( 'Taxonomy: %s', MAULIK_PORTFOLIO_PROJECT_TAXONOMY ) );
	maulik_portfolio_seed_out( '' );

	$counts = array(
		MAULIK_PORTFOLIO_SEED_DECISION_CREATE    => 0,
		MAULIK_PORTFOLIO_SEED_DECISION_WRITE     => 0,
		MAULIK_PORTFOLIO_SEED_DECISION_UNCHANGED => 0,
		MAULIK_PORTFOLIO_SEED_DECISION_CONFLICT  => 0,
		'failed'                                 => 0,
	);

	foreach ( maulik_portfolio_seed_projects() as $entry ) {
		$payload  = maulik_portfolio_seed_canonical( $entry );
		$project  = maulik_portfolio_seed_find_project( $payload['slug'] );
		$decision = maulik_portfolio_seed_decide( $project, $payload, $opts['force'] );

		maulik_portfolio_seed_report( $project, $payload );

		if ( MAULIK_PORTFOLIO_SEED_DECISION_CONFLICT === $decision ) {
			++$counts[ MAULIK_PORTFOLIO_SEED_DECISION_CONFLICT ];
			maulik_portfolio_seed_out( '             CONFLICT: edited in the admin since the last run. Skipped.' );
			maulik_portfolio_seed_out( '                      re-run with MAULIK_PORTFOLIO_FORCE=1 to overwrite those edits.' );
			maulik_portfolio_seed_out( '' );
			continue;
		}

		if ( MAULIK_PORTFOLIO_SEED_DECISION_UNCHANGED === $decision ) {
			++$counts[ MAULIK_PORTFOLIO_SEED_DECISION_UNCHANGED ];
			maulik_portfolio_seed_out( '             UNCHANGED: the database already matches this file.' );
			maulik_portfolio_seed_out( '' );
			continue;
		}

		if ( ! $opts['apply'] ) {
			++$counts[ $decision ];
			maulik_portfolio_seed_out(
				sprintf( '             WOULD %s: title="%s" menu_order=%d', strtoupper( $decision ), $payload['title'], $payload['menu_order'] )
			);
			maulik_portfolio_seed_term_ids( $payload['cats'], false );
			maulik_portfolio_seed_out( '' );
			continue;
		}

		$term_ids = maulik_portfolio_seed_term_ids( $payload['cats'], true );
		$write    = maulik_portfolio_seed_write( $project, $payload, $term_ids );

		if ( $write['error'] ) {
			++$counts['failed'];
			maulik_portfolio_seed_out( sprintf( '             FAILED: %s', $write['message'] ) );
			maulik_portfolio_seed_out( '' );
			continue;
		}

		maulik_portfolio_seed_verify( $write['id'], $payload['slug'], $payload );

		++$counts[ $decision ];
		maulik_portfolio_seed_out(
			sprintf(
				'             %s: id=%d, %d category term(s), md5 %s',
				strtoupper( $decision ),
				$write['id'],
				count( $term_ids ),
				substr( md5( (string) wp_json_encode( $payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ), 0, 12 )
			)
		);
		maulik_portfolio_seed_out( '' );
	}

	maulik_portfolio_seed_out(
		sprintf(
			'=== %d create, %d write, %d unchanged, %d conflict, %d failed ===',
			$counts[ MAULIK_PORTFOLIO_SEED_DECISION_CREATE ],
			$counts[ MAULIK_PORTFOLIO_SEED_DECISION_WRITE ],
			$counts[ MAULIK_PORTFOLIO_SEED_DECISION_UNCHANGED ],
			$counts[ MAULIK_PORTFOLIO_SEED_DECISION_CONFLICT ],
			$counts['failed']
		)
	);

	if ( $counts['failed'] > 0 ) {
		exit( 1 );
	}

	if ( ! $opts['apply'] ) {
		maulik_portfolio_seed_out( 'Dry run only. Re-run with MAULIK_PORTFOLIO_APPLY=1 to write.' );
	}
}

/**
 * Parse the switches, from the environment first and argv second.
 *
 * @param array $args Raw arguments from wp-cli.
 *
 * @return array{apply:bool,force:bool} Parsed options.
 */
function maulik_portfolio_seed_args( $args ) {
	$out = array(
		'apply' => false,
		'force' => false,
	);

	$raw = array();

	if ( isset( $args ) && is_array( $args ) ) {
		$raw = $args;
	}

	foreach ( ( isset( $_SERVER['argv'] ) ? (array) $_SERVER['argv'] : array() ) as $candidate ) {
		$candidate = (string) $candidate;

		if ( 0 === strpos( $candidate, '--' ) ) {
			$raw[] = $candidate;
		}
	}

	if ( getenv( 'MAULIK_PORTFOLIO_APPLY' ) ) {
		$raw[] = '--apply';
	}

	if ( getenv( 'MAULIK_PORTFOLIO_FORCE' ) ) {
		$raw[] = '--force';
	}

	foreach ( $raw as $arg ) {
		$arg = (string) $arg;

		if ( '--apply' === $arg ) {
			$out['apply'] = true;
		} elseif ( '--dry-run' === $arg ) {
			$out['apply'] = false;
		} elseif ( '--force' === $arg ) {
			$out['force'] = true;
		}
	}

	return $out;
}

maulik_portfolio_seed_run( isset( $args ) ? $args : array() );
