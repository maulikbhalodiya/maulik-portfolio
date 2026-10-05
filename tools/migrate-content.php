<?php
/**
 * Migrate canonical page content into each page's post_content.
 *
 * Run it with wp eval-file, never with a bare php binary, because the whole
 * script depends on the WordPress block registry being loaded:
 *
 *     wp eval-file tools/migrate-content.php -- --path=/var/www/maulik-dev
 *     MAULIK_PORTFOLIO_APPLY=1 wp eval-file tools/migrate-content.php -- --path=/var/www/maulik-dev
 *     MAULIK_PORTFOLIO_ROLLBACK=1 wp eval-file tools/migrate-content.php -- --path=/var/www/maulik-dev
 *
 * THE SWITCHES ARE ENVIRONMENT VARIABLES, NOT COMMAND LINE FLAGS. wp-cli validates
 * every flag after the script path against eval-file's own synopsis and rejects
 * anything unrecognised, so `-- --apply` never reaches this script. It fails with
 * "unknown --apply parameter", which reads as though the tool is broken rather than
 * misdocumented, and it was the documented invocation for a while. Use
 * MAULIK_PORTFOLIO_APPLY=1. A bare `--apply` still works when this file is run as
 * plain PHP outside wp-cli.
 *
 * WHY THE SCRIPT EXISTS. Before this, the approved copy for every page lived in
 * page specific HTML templates, so Pages -> About -> Edit showed three blocks and
 * almost nothing of the real page. This moves that copy into post_content, which
 * is where the editor reads it. The content lives in content/pages/*.html because
 * the live database is not version controlled and an editor save leaves no trace
 * in git otherwise.
 *
 * PAGES ARE FOUND BY SLUG, NEVER BY POST ID. The slug is the filename without its
 * extension in content/pages/. A hardcoded ID would break the moment a page is
 * recreated, reimported, or the site is pointed at a different database, which is
 * exactly the situation this migration is most likely to run in.
 *
 * DRY RUN IS THE DEFAULT. Nothing is written unless --apply is passed. Dry run
 * touches no database rows at all and prints, per page, what the current state is,
 * what the canonical file would produce, and which of write, skip or conflict the
 * run would take.
 *
 * ONLY post_content IS TOUCHED. Page ID, post_title, post_name, post_status,
 * post_parent, post_date, menu_order and every piece of post meta other than the
 * three keys this script owns are left alone. The write goes through
 * wp_update_post(), which runs the normal sanitization, the revisions and the
 * save_post hooks, so a filtered post_content is respected rather than bypassed.
 * There is no raw SQL string replacement anywhere in this file, because string
 * replacing serialized post rows is how content gets silently corrupted.
 *
 * IDEMPOTENT BY CONSTRUCTION. The content is assigned, not appended. There is no
 * marker to find, no previous run to strip and no offset to walk. Running the
 * script twice writes the same bytes the second time and reports every page as
 * unchanged.
 *
 * THE CONFLICT POLICY, WHICH IS THE IMPORTANT PART. Each write records
 * md5( post_content ) in post meta under _maulik_portfolio_migrated_hash, together
 * with _maulik_portfolio_migrated_at. On a later run, if the stored hash exists
 * and does not match the hash of the content currently in the database, then
 * somebody has edited the page in the editor since this script last wrote it. That
 * page is reported as a conflict and SKIPPED. Its editor work is left alone.
 * Overriding that requires --force, which is an explicit statement that the
 * editor changes should be discarded in favour of the canonical file. There is no
 * third path: the script never decides on its own that editor work is expendable.
 *
 * A page whose stored hash is absent is not a conflict. That is the first run
 * against a database this script has never touched, and skipping those pages
 * would make the migration impossible to start. The stored hash is compared
 * against the content as found, so the first run on an untouched database simply
 * reports every page as a write.
 *
 * VALIDATION USES parse_blocks(), AND THAT IS NOT AN ARBITRARY CHOICE. A bare
 * new WP_Block_Parser() is constructed with an empty block type registry, so the
 * lexer cannot tell a block delimiter from an HTML comment and every block in the
 * document comes back as freeform. Validating with one reports every file in this
 * repository as invalid and is simply wrong. parse_blocks() goes through
 * get_blocks() and the registry that init has already populated, so it parses
 * exactly as the editor and the front end do. This script only ever calls
 * parse_blocks().
 *
 * FAIL LOUDLY. Missing page for a slug, a page that is not published, markup that
 * does not parse, a top level freeform block where a real block was intended, or a
 * file that reads back differently from what was written, all stop the run with a
 * non zero exit and a message naming the slug. A migration that half succeeded and
 * reported success is worse than one that stopped.
 *
 * BACKUPS AND ROLLBACK. Before any write the existing post_content is written to a
 * timestamped directory OUTSIDE the web root, alongside a JSON manifest recording
 * the page ID, slug, hash and backup time. --rollback reads the manifest and
 * restores post_content through wp_update_post() as well. The default backup root
 * is /var/backups/maulik-dev-content, which is outside both the theme directory
 * and the WordPress install, because wp-content/uploads is served over HTTP and
 * these backups contain the full page content of the site.
 * therefore outside anything rsynced to the live server; --backup-dir overrides it.
 *
 * USES NO EMAIL ADDRESS AND NO MAILTO. The approved contact surface is two
 * buttons, and nothing here adds a third channel.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta key holding md5( post_content ) as this script last wrote it.
 */
const MAULIK_PORTFOLIO_MIGRATED_HASH = '_maulik_portfolio_migrated_hash';

/**
 * Meta key holding the UTC time of the last successful write.
 */
const MAULIK_PORTFOLIO_MIGRATED_AT = '_maulik_portfolio_migrated_at';

/**
 * Meta key holding the canonical file the content came from.
 */
const MAULIK_PORTFOLIO_MIGRATED_SOURCE = '_maulik_portfolio_migrated_source';

/**
 * Exit code used for every hard failure, so a caller can test for it.
 */

/**
 * Default backup directory. It must never sit inside the web root, otherwise
 * outside the web root for a standard install.
 */
const MAULIK_PORTFOLIO_DEFAULT_BACKUP_DIR = '/var/backups/maulik-dev-content';

/**
 * Decision: the file differs from the database and would be written.
 */
const MAULIK_PORTFOLIO_DECISION_WRITE = 'write';

/**
 * Decision: the database already matches the file exactly.
 */
const MAULIK_PORTFOLIO_DECISION_UNCHANGED = 'unchanged';

/**
 * Decision: the page was edited in the editor since the last run.
 */
const MAULIK_PORTFOLIO_DECISION_CONFLICT = 'conflict';

/**
 * Print one line of report output, escaped.
 *
 * Every line this script prints goes through here. The values are slugs read from
 * filenames, titles read from the database and hashes this script computed, so
 * escaping the composed line is the correct place for it rather than escaping
 * each argument at twenty call sites.
 *
 * @param string $line Line to print, without its trailing newline.
 *
 * @return void
 */
function maulik_portfolio_migrate_out( $line ) {
	echo esc_html( $line ), "\n";
}

/**
 * Print a hard failure to standard error and stop the run.
 *
 * Every unrecoverable condition in this script routes through here, so no code
 * path can end the run quietly.
 *
 * @param string $message Message to print.
 *
 * @return void
 */
function maulik_portfolio_migrate_die( $message ) {
	maulik_portfolio_migrate_out( sprintf( 'FAIL: %s', $message ) );
	exit( 1 );
}

/**
 * Initialise WP_Filesystem and return it.
 *
 * @return WP_Filesystem_Direct Direct filesystem for this host.
 */
function maulik_portfolio_migrate_fs() {
	/*
	 * Instantiate WP_Filesystem_Direct directly rather than calling the
	 * WP_Filesystem() factory.
	 *
	 * The factory resolves an implementation through the filesystem_method
	 * filter, and under `wp eval-file` it does not reliably yield the Direct
	 * class. Its get_contents() then returns an empty string for files that are
	 * plainly readable, so the migration aborts with a misleading
	 * "Cannot read content file" message.
	 *
	 * This script runs on the same host as the files it reads, under WP-CLI,
	 * which is exactly the Direct case. Passing null lets the class derive
	 * ABSPATH itself.
	 *
	 * WP_Filesystem_Direct lives in class-wp-filesystem-direct.php, which is not
	 * loaded in a WP-CLI context. The WP_Filesystem() factory normally requires
	 * it, which is why calling the factory appeared to work.
	 */
	if ( ! class_exists( 'WP_Filesystem_Direct' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
	}

	return new WP_Filesystem_Direct( null );
}

/**
 * List the canonical content files.
 *
 * Only *.html is globbed. The directory also carries a README.md documenting the
 * copy sources, and globbing the extension rather than hardcoding a deny list
 * keeps documentation out of the migration without needing updating every time a
 * note file appears.
 *
 * @return string[] Absolute file paths, sorted.
 */
/**
 * Find files under a directory by shell-style pattern.
 *
 * WP_Filesystem has no glob method, and PHPCS flags the native glob() and
 * scandir(), so this uses an SPL iterator instead. One level of nesting is
 * supported because the backup manifests live one directory down.
 *
 * @param string $dir      Directory to scan.
 * @param string $pattern  Shell-style filename pattern, for example *.html.
 * @param bool   $nested   Whether to also scan immediate subdirectories.
 * @return string[] Absolute paths, sorted.
 */
function maulik_portfolio_migrate_find( $dir, $pattern, $nested = false ) {
	$found = array();
	if ( ! is_dir( $dir ) ) {
		return $found;
	}
	$scan = static function ( $path, $pattern, &$found ) {
		foreach ( new DirectoryIterator( $path ) as $item ) {
			if ( $item->isDot() || ! $item->isFile() ) {
				continue;
			}
			if ( fnmatch( $pattern, $item->getFilename() ) ) {
				$found[] = $item->getFilename();
			}
		}
	};
	$scan( $dir, $pattern, $found );
	if ( $nested ) {
		foreach ( new DirectoryIterator( $dir ) as $item ) {
			if ( $item->isDot() || ! $item->isDir() ) {
				continue;
			}
			$scan( $item->getPathname(), $pattern, $found );
		}
	}
	sort( $found );
	return $found;
}

/**
 * Read every page content source from the content directory.
 *
 * The repository is the source of truth for page copy, because the live
 * database is not version controlled. Filenames are the page slugs, which is
 * how the migration identifies its targets without hardcoding post IDs.
 *
 * @return array<string,string> Map of slug to raw block markup.
 */
function maulik_portfolio_migrate_content_files() {
	$fs  = maulik_portfolio_migrate_fs();
	$dir = dirname( __DIR__ ) . '/content/pages';

	if ( ! $fs->is_dir( $dir ) ) {
		maulik_portfolio_migrate_die( sprintf( 'Content directory not found: %s', $dir ) );
	}

	$found = maulik_portfolio_migrate_find( $dir, '*.html' );

	if ( array() === $found ) {
		maulik_portfolio_migrate_die( sprintf( 'No content files found in %s', $dir ) );
	}

	$files = array();

	foreach ( $found as $path ) {
		$files[] = trailingslashit( $dir ) . $path;
	}

	sort( $files );

	return $files;
}

/**
 * Read a canonical content file.
 *
 * @param string $file Absolute path to the file.
 *
 * @return string File contents, with trailing whitespace removed.
 */
function maulik_portfolio_migrate_read_file( $file ) {
	$fs   = maulik_portfolio_migrate_fs();
	$raw  = $fs->get_contents( $file );
	$name = basename( $file );

	if ( false === $raw || '' === $raw ) {
		maulik_portfolio_migrate_die(
			sprintf(
				'Cannot read content file: %s (path=%s, type=%s, filesize=%s)',
				$name,
				$file,
				gettype( $raw ),
				(string) ( file_exists( $file ) ? (int) filesize( $file ) : 'absent' )
			)
		);
	}

	return rtrim( $raw );
}

/**
 * Validate canonical markup with WordPress aware parsing.
 *
 * Deliberately parse_blocks() and never new WP_Block_Parser(). See the file
 * docblock for why.
 *
 * @param string $content Block markup to validate.
 * @param string $label   Human readable name, used in error messages.
 *
 * @return array<int,array<string,mixed>> The parsed top level blocks.
 */
/**
 * Whether a block type is registered with WordPress.
 *
 * There is no block_exists() function in WordPress core. The registry is the
 * only authority, and it is what parse_blocks() itself consults, so asking it
 * directly keeps validation honest.
 *
 * @param string $name Block name, for example core/group.
 * @return bool True when registered.
 */
function maulik_portfolio_migrate_block_registered( $name ) {
	if ( ! class_exists( 'WP_Block_Type_Registry' ) ) {
		return false;
	}

	$registry = WP_Block_Type_Registry::get_instance();

	return $registry->is_registered( $name );
}

/**
 * Parse canonical markup with the real WordPress block registry.
 *
 * A bare WP_Block_Parser has no registry, so every block looks freeform.
 *
 * @param string $content Raw block markup.
 * @param string $label   Human label for error messages.
 *
 * @return array Parsed blocks.
 */
function maulik_portfolio_migrate_parse( $content, $label ) {
	if ( '' === trim( $content ) ) {
		maulik_portfolio_migrate_die( sprintf( '%s is empty', $label ) );
	}

	$blocks = parse_blocks( $content );

	if ( array() === $blocks ) {
		maulik_portfolio_migrate_die( sprintf( '%s did not parse into any blocks', $label ) );
	}

	$freeform = array();

	foreach ( $blocks as $index => $block ) {
		if ( empty( $block['blockName'] ) && '' !== trim( $block['innerHTML'] ) ) {
			$freeform[] = $index;
		}
	}

	if ( array() !== $freeform ) {
		maulik_portfolio_migrate_die(
			sprintf(
				'%s has %d top level freeform block(s) at index %s. A real block was intended there.',
				$label,
				count( $freeform ),
				implode( ', ', $freeform )
			)
		);
	}

	foreach ( $blocks as $block ) {
		if ( ! empty( $block['blockName'] ) && ! maulik_portfolio_migrate_block_registered( $block['blockName'] ) ) {
			maulik_portfolio_migrate_die(
				sprintf( '%s uses unregistered block %s', $label, $block['blockName'] )
			);
		}
	}

	return $blocks;
}

/**
 * Count top level freeform blocks that carry content.
 *
 * @param string $content Block markup.
 *
 * @return int Number of top level blocks, and via the second return value the
 *               number of those that were freeform.
 */
function maulik_portfolio_migrate_describe( $content ) {
	$blocks = parse_blocks( $content );
	$free   = 0;

	foreach ( $blocks as $block ) {
		if ( empty( $block['blockName'] ) && '' !== trim( $block['innerHTML'] ) ) {
			++$free;
		}
	}

	return array(
		'total'    => count( $blocks ),
		'freeform' => $free,
	);
}

/**
 * Find the page for a slug, failing loudly when it is absent or not published.
 *
 * @param string $slug Page slug.
 *
 * @return WP_Post The page.
 */
/**
 * Resolve a content filename slug to its published WordPress page.
 *
 * @param string $slug Page slug, taken from the content filename.
 *
 * @return WP_Post The published page.
 */
function maulik_portfolio_migrate_find_page( $slug ) {
	/*
	 * Query by post_name, which is the WP_Query argument for slug lookup.
	 *
	 * The obvious mistake here is to pass 'name'. WP_Query has no 'name'
	 * argument, so it is discarded without warning, the query degrades to "the
	 * first published page", and every slug resolves to the same page. That
	 * silently rewrites one page with six different bodies while the other five
	 * are never touched.
	 */
	$pages = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => 'publish',
			'name'                   => $slug,
			'post_name'              => $slug,
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	if ( array() === $pages ) {
		maulik_portfolio_migrate_die(
			sprintf( 'No published page with slug "%s". Check the filename in content/pages.', $slug )
		);
	}

	$page = $pages[0];

	/*
	 * Fail loudly if the returned page is not the one asked for. This is the
	 * check that would have caught the 'name' argument mistake immediately,
	 * rather than after a dry run reported six writes against one page id.
	 */
	if ( $page->post_name !== $slug ) {
		maulik_portfolio_migrate_die(
			sprintf(
				'Slug lookup for "%s" returned page "%s" (id %d). Refusing to write.',
				$slug,
				$page->post_name,
				$page->ID
			)
		);
	}

	if ( 'publish' !== $page->post_status ) {
		maulik_portfolio_migrate_die(
			sprintf( 'Page "%s" has status %s, expected publish.', $slug, $page->post_status )
		);
	}

	return $page;
}

/**
 * Decide what a page would do on this run, without writing anything.
 *
 * @param WP_Post $page    The page.
 * @param string  $content Canonical content from the file.
 * @param bool    $force   Whether editor edits may be overwritten.
 *
 * @return string One of the MAULIK_PORTFOLIO_DECISION_* values.
 */
function maulik_portfolio_migrate_decide( $page, $content, $force ) {
	$current = (string) $page->post_content;

	if ( $content === $current ) {
		return MAULIK_PORTFOLIO_DECISION_UNCHANGED;
	}

	$stored = get_post_meta( $page->ID, MAULIK_PORTFOLIO_MIGRATED_HASH, true );

	if ( '' !== $stored && md5( $current ) !== $stored && ! $force ) {
		return MAULIK_PORTFOLIO_DECISION_CONFLICT;
	}

	return MAULIK_PORTFOLIO_DECISION_WRITE;
}

/**
 * Write the current post_content for a page into a timestamped backup directory.
 *
 * @param WP_Post $page        The page being backed up.
 * @param string  $backup_root Root directory holding all backups.
 *
 * @return array{content:string,manifest:string} Paths written.
 */
function maulik_portfolio_migrate_backup( $page, $backup_root ) {
	$fs            = maulik_portfolio_migrate_fs();
	$dir           = trailingslashit( $backup_root ) . gmdate( 'Ymd-His' ) . '-' . $page->post_name;
	$content_path  = trailingslashit( $dir ) . 'post_content.html';
	$manifest_path = trailingslashit( $dir ) . 'manifest.json';

	/*
	 * wp_mkdir_p, not WP_Filesystem_Direct::mkdir().
	 *
	 * That method calls @mkdir() with no recursive flag, so it cannot create the
	 * nested per-page backup directory and returns false. wp_mkdir_p is the
	 * recursive WordPress API and creates every missing level.
	 */
	if ( ! wp_mkdir_p( $dir ) ) {
		maulik_portfolio_migrate_die( sprintf( 'Cannot create backup directory %s', $dir ) );
	}

	if ( ! $fs->put_contents( $content_path, $page->post_content, 0644 ) ) {
		maulik_portfolio_migrate_die( sprintf( 'Cannot write backup %s', $content_path ) );
	}

	$manifest = array(
		'id'           => $page->ID,
		'slug'         => $page->post_name,
		'title'        => $page->post_title,
		'post_type'    => $page->post_type,
		'backed_up_at' => gmdate( 'c' ),
		'md5'          => md5( $page->post_content ),
		'bytes'        => strlen( $page->post_content ),
		'file'         => 'post_content.html',
	);

	$json = wp_json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

	if ( ! $fs->put_contents( $manifest_path, $json, 0644 ) ) {
		maulik_portfolio_migrate_die( sprintf( 'Cannot write manifest %s', $manifest_path ) );
	}

	return array(
		'content'  => $content_path,
		'manifest' => $manifest_path,
	);
}

/**
 * Apply canonical content to a page and record the hash.
 *
 * @param WP_Post $page    The page.
 * @param string  $content Canonical content.
 * @param string  $source  Canonical filename, recorded for traceability.
 *
 * @return array{id:int,error:bool,message:string} Outcome of the write.
 */
function maulik_portfolio_migrate_write( $page, $content, $source ) {
	$result = wp_update_post(
		array(
			'ID'           => $page->ID,
			'post_content' => $content,
		),
		true
	);

	if ( is_wp_error( $result ) ) {
		return array(
			'id'      => $page->ID,
			'error'   => true,
			'message' => $result->get_error_message(),
		);
	}

	update_post_meta( $page->ID, MAULIK_PORTFOLIO_MIGRATED_HASH, md5( $content ) );
	update_post_meta( $page->ID, MAULIK_PORTFOLIO_MIGRATED_AT, gmdate( 'c' ) );
	update_post_meta( $page->ID, MAULIK_PORTFOLIO_MIGRATED_SOURCE, $source );

	return array(
		'id'      => (int) $result,
		'error'   => false,
		'message' => '',
	);
}

/**
 * Read a page back from the database and confirm it holds what was written.
 *
 * This deliberately re-reads through get_post() rather than trusting the return
 * value of wp_update_post(), because a filter on save_post or post_content can
 * change what actually landed in the row.
 *
 * @param int    $post_id The post ID to verify.
 * @param string $slug    Page slug, used in messages.
 * @param string $source  Canonical file path, used in messages.
 *
 * @return int Number of top level blocks now stored.
 */
function maulik_portfolio_migrate_verify( $post_id, $slug, $source ) {
	$saved = get_post( $post_id );

	if ( ! $saved ) {
		maulik_portfolio_migrate_die( sprintf( 'Page "%s" disappeared after the write', $slug ) );
	}

	$expected = maulik_portfolio_migrate_read_file( $source );

	if ( $saved->post_content !== $expected ) {
		maulik_portfolio_migrate_die(
			sprintf(
				'Page "%s" read back different from %s. A filter on save_post or post_content changed it.',
				$slug,
				basename( $source )
			)
		);
	}

	return count( maulik_portfolio_migrate_parse( $saved->post_content, sprintf( 'post_content of "%s"', $slug ) ) );
}

/**
 * Restore every page in the newest backup directory under the backup root.
 *
 * @param string $backup_root Backup root to search.
 *
 * @return void
 */
function maulik_portfolio_migrate_rollback( $backup_root ) {
	$fs        = maulik_portfolio_migrate_fs();
	$manifests = maulik_portfolio_migrate_find( $backup_root, 'manifest.json', true );

	if ( array() === $manifests ) {
		maulik_portfolio_migrate_die( sprintf( 'No backups found under %s', $backup_root ) );
	}

	$latest = '';

	foreach ( $manifests as $name ) {
		$dir = dirname( $name );

		if ( '' === $latest || strcmp( $dir, $latest ) > 0 ) {
			$latest = $dir;
		}
	}

	maulik_portfolio_migrate_out( sprintf( 'Rollback from %s', $latest ) );

	$restored = 0;

	foreach ( maulik_portfolio_migrate_find( $latest, 'manifest.json', true ) as $name ) {
		$raw = $fs->get_contents( $name );

		if ( false === $raw ) {
			maulik_portfolio_migrate_die( sprintf( 'Cannot read manifest %s', basename( $name ) ) );
		}

		$manifest = json_decode( $raw, true );

		if ( ! is_array( $manifest ) || empty( $manifest['id'] ) || empty( $manifest['file'] ) ) {
			maulik_portfolio_migrate_die( sprintf( 'Manifest is not usable: %s', $name ) );
		}

		$content_path = trailingslashit( dirname( $name ) ) . $manifest['file'];
		$content      = $fs->get_contents( $content_path );

		if ( false === $content ) {
			maulik_portfolio_migrate_die( sprintf( 'Cannot read backup content %s', $content_path ) );
		}

		$page = get_post( (int) $manifest['id'] );

		if ( ! $page ) {
			maulik_portfolio_migrate_out(
				sprintf( '  SKIP  id=%d %s: post no longer exists', (int) $manifest['id'], $manifest['slug'] )
			);
			continue;
		}

		if ( $page->post_content === $content ) {
			maulik_portfolio_migrate_out( sprintf( '  SAME  id=%d %s', $page->ID, $page->post_name ) );
			continue;
		}

		$result = wp_update_post(
			array(
				'ID'           => $page->ID,
				'post_content' => $content,
			),
			true
		);

		if ( is_wp_error( $result ) ) {
			maulik_portfolio_migrate_die(
				sprintf( 'Rollback failed for "%s": %s', $manifest['slug'], $result->get_error_message() )
			);
		}

		++$restored;
		maulik_portfolio_migrate_out(
			sprintf( '  OK    id=%d %s restored, %d bytes', $page->ID, $page->post_name, strlen( $content ) )
		);
	}

	maulik_portfolio_migrate_out( sprintf( 'Restored %d page(s).', $restored ) );
}

/**
 * Print the current state and the file state for one page.
 *
 * @param WP_Post $page    The page.
 * @param string  $file    Canonical file path.
 * @param string  $content Canonical content.
 *
 * @return void
 */
function maulik_portfolio_migrate_report_page( $page, $file, $content ) {
	$before = (string) $page->post_content;
	$now    = maulik_portfolio_migrate_describe( $before );
	$want   = maulik_portfolio_migrate_describe( $content );

	maulik_portfolio_migrate_out(
		sprintf(
			'%s id=%d title=%s status=%s template=%s',
			$page->post_name,
			$page->ID,
			$page->post_title,
			$page->post_status,
			'' === $page->page_template ? '(default)' : $page->page_template
		)
	);
	maulik_portfolio_migrate_out(
		sprintf(
			'             now:  %d bytes, %d top level blocks, %d freeform, md5 %s',
			strlen( $before ),
			$now['total'],
			$now['freeform'],
			substr( md5( $before ), 0, 12 )
		)
	);
	maulik_portfolio_migrate_out(
		sprintf(
			'             file: %d bytes, %d top level blocks, %d freeform, md5 %s',
			strlen( $content ),
			$want['total'],
			$want['freeform'],
			substr( md5( $content ), 0, 12 )
		)
	);
	unset( $file );
}

/**
 * Run the migration.
 *
 * @param array $args Raw arguments from wp-cli.
 *
 * @return void
 */
function maulik_portfolio_migrate_run( $args ) {
	$opts = maulik_portfolio_migrate_args( $args );

	if ( $opts['rollback'] ) {
		maulik_portfolio_migrate_rollback( $opts['backup_dir'] );
		return;
	}

	maulik_portfolio_migrate_out(
		sprintf(
			'=== Content migration: %s ===',
			$opts['apply'] ? 'APPLY' : 'DRY RUN, no writes'
		)
	);
	maulik_portfolio_migrate_out( sprintf( 'Backup root: %s', $opts['backup_dir'] ) );
	maulik_portfolio_migrate_out( '' );

	$counts = array(
		MAULIK_PORTFOLIO_DECISION_WRITE     => 0,
		MAULIK_PORTFOLIO_DECISION_UNCHANGED => 0,
		MAULIK_PORTFOLIO_DECISION_CONFLICT  => 0,
		'failed'                            => 0,
	);

	foreach ( maulik_portfolio_migrate_content_files() as $file ) {
		$slug = basename( $file, '.html' );

		if ( '' !== $opts['slug'] && $opts['slug'] !== $slug ) {
			continue;
		}

		$content  = maulik_portfolio_migrate_read_file( $file );
		$blocks   = maulik_portfolio_migrate_parse( $content, sprintf( 'content/pages/%s.html', $slug ) );
		$page     = maulik_portfolio_migrate_find_page( $slug );
		$decision = maulik_portfolio_migrate_decide( $page, $content, $opts['force'] );

		maulik_portfolio_migrate_report_page( $page, $file, $content );

		if ( MAULIK_PORTFOLIO_DECISION_CONFLICT === $decision ) {
			++$counts[ MAULIK_PORTFOLIO_DECISION_CONFLICT ];
			maulik_portfolio_migrate_out( '             CONFLICT: edited in the editor since the last run. Skipped.' );
			maulik_portfolio_migrate_out(
				sprintf(
					'                      stored hash %s, current hash %s',
					substr( (string) get_post_meta( $page->ID, MAULIK_PORTFOLIO_MIGRATED_HASH, true ), 0, 12 ),
					substr( md5( (string) $page->post_content ), 0, 12 )
				)
			);
			maulik_portfolio_migrate_out( '                      re-run with --force to overwrite the editor changes.' );
			maulik_portfolio_migrate_out( '' );
			continue;
		}

		if ( MAULIK_PORTFOLIO_DECISION_UNCHANGED === $decision ) {
			++$counts[ MAULIK_PORTFOLIO_DECISION_UNCHANGED ];
			maulik_portfolio_migrate_out( '             UNCHANGED: post_content already matches the file.' );

			if ( $opts['apply'] ) {
				// Keep the bookkeeping honest even when the bytes did not move, so a
				// later run still compares editor edits against the right baseline.
				update_post_meta( $page->ID, MAULIK_PORTFOLIO_MIGRATED_HASH, md5( $content ) );
				update_post_meta( $page->ID, MAULIK_PORTFOLIO_MIGRATED_AT, gmdate( 'c' ) );
				update_post_meta( $page->ID, MAULIK_PORTFOLIO_MIGRATED_SOURCE, basename( $file ) );
			}

			maulik_portfolio_migrate_out( '' );
			continue;
		}

		if ( ! $opts['apply'] ) {
			++$counts[ MAULIK_PORTFOLIO_DECISION_WRITE ];
			maulik_portfolio_migrate_out(
				sprintf(
					'             WOULD WRITE: %d bytes, %d top level blocks. Backup would go to %s.',
					strlen( $content ),
					count( $blocks ),
					trailingslashit( $opts['backup_dir'] ) . gmdate( 'Ymd-His' ) . '-' . $slug
				)
			);
			maulik_portfolio_migrate_out( '' );
			continue;
		}

		$paths = maulik_portfolio_migrate_backup( $page, $opts['backup_dir'] );
		maulik_portfolio_migrate_out( sprintf( '             backup:   %s', $paths['content'] ) );
		maulik_portfolio_migrate_out( sprintf( '             manifest: %s', $paths['manifest'] ) );

		$write = maulik_portfolio_migrate_write( $page, $content, basename( $file ) );

		if ( $write['error'] ) {
			++$counts['failed'];
			maulik_portfolio_migrate_out( sprintf( '             FAILED: %s', $write['message'] ) );
			maulik_portfolio_migrate_out( '' );
			continue;
		}

		$after = maulik_portfolio_migrate_verify( $write['id'], $slug, $file );

		++$counts[ MAULIK_PORTFOLIO_DECISION_WRITE ];
		maulik_portfolio_migrate_out(
			sprintf(
				'             WROTE:    id=%d, %d top level blocks, md5 %s',
				$write['id'],
				$after,
				substr( md5( $content ), 0, 12 )
			)
		);
		maulik_portfolio_migrate_out( '' );
	}

	maulik_portfolio_migrate_out(
		sprintf(
			'=== %d write, %d unchanged, %d conflict, %d failed ===',
			$counts[ MAULIK_PORTFOLIO_DECISION_WRITE ],
			$counts[ MAULIK_PORTFOLIO_DECISION_UNCHANGED ],
			$counts[ MAULIK_PORTFOLIO_DECISION_CONFLICT ],
			$counts['failed']
		)
	);

	if ( $counts['failed'] > 0 ) {
		exit( 1 );
	}

	if ( ! $opts['apply'] ) {
		maulik_portfolio_migrate_out( 'Dry run only. Re-run with --apply to write.' );
	}
}

/**
 * Parse the command line arguments wp-cli forwards after the double dash.
 *
 * Recognised flags: --apply, --dry-run, --force, --rollback, --backup-dir=<path>,
 * --path=<wp root> and --slug=<slug> to restrict the run to one page.
 *
 * --dry-run is accepted explicitly as well as being the default, so the review
 * mode can be requested without relying on remembering that it is the default.
 *
 * @param array $args Raw arguments from wp-cli.
 *
 * @return array{apply:bool,force:bool,rollback:bool,backup_dir:string,slug:string} Parsed options.
 */
function maulik_portfolio_migrate_args( $args ) {
	$out = array(
		'apply'      => false,
		'force'      => false,
		'rollback'   => false,
		'backup_dir' => '',
		'slug'       => '',
	);

	/*
	 * Read from argv rather than the $args variable wp-cli hands to eval-file.
	 *
	 * wp-cli validates the flags that follow the script path, so --apply is
	 * rejected as an unknown eval-file parameter before the script ever runs.
	 * That failure is invisible when output is filtered, and it made --apply a
	 * silent no-op: the dry run reported six writes and the apply wrote nothing.
	 * argv is the only place the flags reliably survive.
	 *
	 * Flags arrive as environment variables, not command line switches.
	 *
	 * wp-cli validates every flag that follows the script path and rejects
	 * anything not in eval-file's synopsis, so --apply never reaches the
	 * script at all. That produced the worst possible failure: the dry run
	 * reported six writes, the apply wrote nothing, and the error was easy to
	 * miss because it is one line above the useful output.
	 *
	 * Environment variables are not validated by wp-cli, so they work:
	 *
	 *   MAULIK_PORTFOLIO_APPLY=1 wp eval-file tools/migrate-content.php
	 *   MAULIK_PORTFOLIO_APPLY=1 MAULIK_PORTFOLIO_FORCE=1 wp eval-file ...
	 *   MAULIK_PORTFOLIO_ROLLBACK=1 wp eval-file tools/migrate-content.php
	 *
	 * Command line switches are still honoured for convenience, since they work
	 * when the script is invoked as plain PHP rather than through wp-cli.
	 */
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
	if ( getenv( 'MAULIK_PORTFOLIO_ROLLBACK' ) ) {
		$raw[] = '--rollback';
	}
	if ( getenv( 'MAULIK_PORTFOLIO_BACKUP_DIR' ) ) {
		$raw[] = '--backup-dir=' . getenv( 'MAULIK_PORTFOLIO_BACKUP_DIR' );
	}
	if ( getenv( 'MAULIK_PORTFOLIO_SLUG' ) ) {
		$raw[] = '--slug=' . getenv( 'MAULIK_PORTFOLIO_SLUG' );
	}

	foreach ( $raw as $arg ) {
		$arg = (string) $arg;

		if ( '--apply' === $arg ) {
			$out['apply'] = true;
		} elseif ( '--dry-run' === $arg ) {
			$out['apply'] = false;
		} elseif ( '--force' === $arg ) {
			$out['force'] = true;
		} elseif ( '--rollback' === $arg ) {
			$out['rollback'] = true;
		} elseif ( 0 === strpos( $arg, '--backup-dir=' ) ) {
			$out['backup_dir'] = substr( $arg, strlen( '--backup-dir=' ) );
		} elseif ( 0 === strpos( $arg, '--slug=' ) ) {
			$out['slug'] = substr( $arg, strlen( '--slug=' ) );
		}
	}

	if ( '' === $out['backup_dir'] ) {
		/*
		 * An absolute default is used as-is. Prefixing it with the uploads
		 * basedir produced a nonsensical doubled path such as
		 * /var/www/.../wp-content/uploads//var/backups/... , because the
		 * default now sits outside the install entirely. Backups of full page
		 * content must never live under wp-content/uploads, which is served
		 * over HTTP.
		 */
		if ( '/' === substr( MAULIK_PORTFOLIO_DEFAULT_BACKUP_DIR, 0, 1 ) ) {
			$out['backup_dir'] = MAULIK_PORTFOLIO_DEFAULT_BACKUP_DIR;
		} else {
			$uploads           = wp_upload_dir();
			$out['backup_dir'] = trailingslashit( $uploads['basedir'] ) . MAULIK_PORTFOLIO_DEFAULT_BACKUP_DIR;
		}
	}

	return $out;
}

maulik_portfolio_migrate_run( isset( $args ) ? $args : array() );
