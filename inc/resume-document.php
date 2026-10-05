<?php
/**
 * Resume document store and the HTML to PDF pipeline behind it.
 *
 * The resume is authored as HTML and served as PDF. HTML is the single source
 * of truth and the PDF is a derived artefact that is cached on disk next to it.
 * Every request compares the two and regenerates only when they disagree.
 *
 * The comparison is on the SHA-256 of the HTML bytes, never on the modification
 * time. A copy, a restore from backup or a rsync that preserves times can all
 * leave the mtime untouched or move it backwards, so an mtime comparison will
 * happily serve a PDF that was generated from an older revision of the document.
 * Content hashing cannot be fooled that way.
 *
 * The hash of the HTML that a given PDF was produced from is recorded in a
 * sidecar file beside the PDF rather than in an option. The sidecar travels
 * with the artefact, so copying the pair to another host keeps them valid, and
 * a stale sidecar cannot outlive its PDF because the regeneration path deletes
 * both before it writes anything.
 *
 * The HTML itself is the owner's own document and is deliberately exempt from
 * this theme's code standards. It lives under wp-content/uploads, which is
 * outside the repository and outside the theme directory, so no CI gate can see
 * it, and it contains content such as non-breaking punctuation and off-palette
 * colour values that the theme's house rules would otherwise forbid. Those
 * rules govern markup this theme renders, not an artefact the owner authored
 * and downloads.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'maulik_portfolio_resume_dir' ) ) {
	/**
	 * Return the absolute directory holding the resume document store.
	 *
	 * Resolved from the uploads base directory rather than from the theme
	 * directory on purpose. A theme deploy rsyncs into the theme folder with
	 * delete enabled, so anything the theme owns at runtime is destroyed on the
	 * next deploy. Uploads is outside that tree and the owner's document
	 * survives.
	 *
	 * @since 0.1.0
	 *
	 * @return string Absolute path with no trailing slash, or an empty string
	 *                when the uploads directory cannot be resolved.
	 */
	function maulik_portfolio_resume_dir() {
		$uploads = wp_get_upload_dir();

		if ( ! empty( $uploads['error'] ) || empty( $uploads['basedir'] ) ) {
			return '';
		}

		return trailingslashit( $uploads['basedir'] ) . 'maulik-resume';
	}
}

if ( ! function_exists( 'maulik_portfolio_resume_html_path' ) ) {
	/**
	 * Return the absolute path of the stored HTML document.
	 *
	 * @since 0.1.0
	 *
	 * @return string Absolute path with no trailing slash, or an empty string.
	 */
	function maulik_portfolio_resume_html_path() {
		$dir = maulik_portfolio_resume_dir();

		if ( '' === $dir ) {
			return '';
		}

		return $dir . '/resume.html';
	}
}

if ( ! function_exists( 'maulik_portfolio_resume_pdf_path' ) ) {
	/**
	 * Return the absolute path of the derived PDF.
	 *
	 * @since 0.1.0
	 *
	 * @return string Absolute path with no trailing slash, or an empty string.
	 */
	function maulik_portfolio_resume_pdf_path() {
		$dir = maulik_portfolio_resume_dir();

		if ( '' === $dir ) {
			return '';
		}

		return $dir . '/resume.pdf';
	}
}

if ( ! function_exists( 'maulik_portfolio_resume_hash_path' ) ) {
	/**
	 * Return the absolute path of the PDF hash sidecar.
	 *
	 * The sidecar holds the SHA-256 of the HTML that the PDF next to it was
	 * generated from. One hash comparison on a request decides whether the
	 * cached PDF is still current.
	 *
	 * @since 0.1.0
	 *
	 * @return string Absolute path with no trailing slash, or an empty string.
	 */
	function maulik_portfolio_resume_hash_path() {
		$dir = maulik_portfolio_resume_dir();

		if ( '' === $dir ) {
			return '';
		}

		return $dir . '/resume.pdf.sha256';
	}
}

if ( ! function_exists( 'maulik_portfolio_resume_html_hash' ) ) {
	/**
	 * Return the SHA-256 of the stored HTML document.
	 *
	 * @since 0.1.0
	 *
	 * @return string Hex digest, or an empty string when the HTML is missing
	 *                or unreadable.
	 */
	function maulik_portfolio_resume_html_hash() {
		$path = maulik_portfolio_resume_html_path();

		if ( '' === $path || ! is_readable( $path ) ) {
			return '';
		}

		$hash = hash_file( 'sha256', $path );

		if ( ! is_string( $hash ) ) {
			return '';
		}

		return $hash;
	}
}

if ( ! function_exists( 'maulik_portfolio_resume_read_recorded_hash' ) ) {
	/**
	 * Return the HTML hash recorded beside the stored PDF.
	 *
	 * Reads the whole file and trims it rather than taking a fixed byte count,
	 * so a sidecar written with a trailing newline by a shell command still
	 * compares equal to one written by this code.
	 *
	 * @since 0.1.0
	 *
	 * @return string Hex digest, or an empty string when there is no sidecar.
	 */
	function maulik_portfolio_resume_read_recorded_hash() {
		$path = maulik_portfolio_resume_hash_path();

		if ( '' === $path || ! is_readable( $path ) ) {
			return '';
		}

		$contents = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local file, not a remote URL.

		if ( ! is_string( $contents ) ) {
			return '';
		}

		return trim( $contents );
	}
}

if ( ! function_exists( 'maulik_portfolio_resume_is_current' ) ) {
	/**
	 * Decide whether the stored PDF still matches the stored HTML.
	 *
	 * Declared impure because it reads the filesystem, and two consecutive calls
	 * with nothing in between are genuinely allowed to answer differently: the
	 * generator changes the files this looks at.
	 *
	 * @since 0.1.0
	 *
	 * @phpstan-impure
	 *
	 * @return bool True when a PDF exists and was generated from the HTML as
	 *              it stands right now.
	 */
	function maulik_portfolio_resume_is_current() {
		$pdf = maulik_portfolio_resume_pdf_path();

		if ( '' === $pdf || ! is_readable( $pdf ) || filesize( $pdf ) < 1 ) {
			return false;
		}

		$html_hash = maulik_portfolio_resume_html_hash();

		if ( '' === $html_hash ) {
			return false;
		}

		return hash_equals( $html_hash, maulik_portfolio_resume_read_recorded_hash() );
	}
}

if ( ! function_exists( 'maulik_portfolio_resume_discard_pdf' ) ) {
	/**
	 * Remove the derived PDF and its sidecar.
	 *
	 * Called before every regeneration so that a failure part way through leaves
	 * no stale PDF behind for the next request to serve. The sidecar goes with
	 * it, because a hash that describes a file which no longer exists is worse
	 * than no hash at all.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function maulik_portfolio_resume_discard_pdf() {
		$targets = array( maulik_portfolio_resume_pdf_path(), maulik_portfolio_resume_hash_path() );

		foreach ( $targets as $target ) {
			if ( '' !== $target && file_exists( $target ) ) {
				wp_delete_file( $target );
			}
		}
	}
}

if ( ! function_exists( 'maulik_portfolio_resume_generator_command' ) ) {
	/**
	 * Build the headless Chrome command that renders the HTML to a PDF.
	 *
	 * Every argument is a constant. Nothing derived from the request reaches
	 * this string, which is the property that makes the call safe to expose on a
	 * public endpoint. escapeshellcmd is applied to each argument even so, since
	 * the point of that call is that a later edit to one of these constants
	 * cannot turn this into an injection point.
	 *
	 * The flags are the minimum that makes this reliable as root on a server
	 * with no display and no usable shared memory.
	 *
	 * The environment prefix is not decoration and is the reason this works at
	 * all under the web server. MEASURED: with no prefix, running as www-data,
	 * Chrome exits without writing a file and prints two errors, an unwritable
	 * HOME creating /var/www/.local/share/applications/mimeapps.list and
	 * "chrome_crashpad_handler: --database is required". www-data has a home of
	 * /var/www, which does not exist as a directory it can write to, so the XDG
	 * lookup fails, crashpad has nowhere to put its database and Chrome gives up
	 * before it renders. Setting HOME alone fixes it and the XDG_CONFIG_HOME and
	 * XDG_CACHE_HOME prefixes make it robust to the exact HOME value the SAPI
	 * happens to inherit. The same run with the prefix produces the expected
	 * 100590 byte two page PDF.
	 *
	 * The timeout is the coreutils timeout at /usr/bin/timeout, confirmed
	 * present. It wraps Chrome rather than relying on a PHP execution limit,
	 * because max_execution_time does not interrupt a blocking shell call.
	 *
	 * @since 0.1.0
	 *
	 * @param string $html_path Absolute path of the HTML to render.
	 * @param string $pdf_path  Absolute path of the PDF to write.
	 * @param string $home      Absolute directory to use as HOME.
	 * @param string $profile   Absolute path used as the Chrome profile directory.
	 *
	 * @return string Command line ready to hand to the process runner.
	 */
	function maulik_portfolio_resume_generator_command( $html_path, $pdf_path, $home, $profile ) {
		$binary = '/usr/bin/google-chrome';

		$env = array(
			'HOME=' . escapeshellcmd( $home ),
			'XDG_CONFIG_HOME=' . escapeshellcmd( $home . '/.config' ),
			'XDG_CACHE_HOME=' . escapeshellcmd( $home . '/.cache' ),
		);

		$args = array_merge(
			$env,
			array(
				'timeout',
				'30',
				escapeshellcmd( $binary ),
				'--headless',
				'--no-sandbox',
				'--disable-gpu',
				'--disable-dev-shm-usage',
				'--no-first-run',
				'--no-default-browser-check',
				'--disable-extensions',

				/*
				 * MEASURED, and this flag is the whole reason the pipeline
				 * works at all under the web server.
				 *
				 * /usr/lib/systemd/system/apache2.service sets
				 * MemoryDenyWriteExecute=yes. V8 allocates executable memory to
				 * JIT, which that option forbids, so under Apache a Chrome
				 * launched with the ordinary flags does not crash. It starts, logs
				 * a GCM registration error roughly two seconds in, then sits there
				 * until the timeout kills it, having written no file at all.
				 * Measured: baseline under Apache exits 124 at the 30 second
				 * timeout with 0 bytes written.
				 *
				 * Reproduced outside Apache with systemd-run and the same
				 * property. With MemoryDenyWriteExecute=yes and no extra flags,
				 * Chrome is killed at the timeout with 0 bytes. With the same
				 * property plus --js-flags=--jitless, it exits 0 and writes the
				 * expected 100590 byte PDF. Under a plain shell with no such
				 * property, both variants succeed, so the flag is harmless where
				 * it is not needed and decisive where it is.
				 *
				 * jitless means V8 interprets instead of compiling. That costs
				 * render speed on a document this size, which is irrelevant here,
				 * and it costs nothing at all on the cache path because the
				 * cached PDF is never regenerated until the HTML changes.
				 */
				'--js-flags=--jitless',
				'--virtual-time-budget=10000',
				'--user-data-dir=' . escapeshellcmd( $profile ),
				'--no-pdf-header-footer',
				'--print-to-pdf=' . escapeshellcmd( $pdf_path ),
				'file://' . escapeshellcmd( $html_path ),
			)
		);

		return implode( ' ', $args );
	}
}

if ( ! function_exists( 'maulik_portfolio_resume_generate' ) ) {
	/**
	 * Render the stored HTML to a PDF and record which HTML it came from.
	 *
	 * Two requests can arrive at the same moment with no PDF present. Both would
	 * otherwise start Chrome and both would write the same output file, leaving
	 * a half written PDF that later requests would serve as if it were cached.
	 * An exclusive lock on a lock file in the same directory serialises them,
	 * and the loser of the race rechecks the cache once it has the lock and
	 * finds the winner's PDF already current, so it does no work at all.
	 *
	 * The generator writes to a temporary name inside the destination directory
	 * and the finished file is moved into place with rename(), which is atomic
	 * within a filesystem. A reader therefore only ever sees a complete PDF or
	 * no PDF, never a partial one.
	 *
	 * @since 0.1.0
	 *
	 * @return bool True when a current PDF exists after this call.
	 */
	function maulik_portfolio_resume_generate() {
		/*
		 * This is the generator failure path and it is the reason the PDF
		 * endpoint does not hand back an empty file when the constants point at
		 * a path that does not exist. Every one of these constants is returned
		 * by a resolver, so a bad path produces an empty string rather than a
		 * path, and the run is abandoned before any process is started.
		 */
		$dir  = maulik_portfolio_resume_dir();
		$html = maulik_portfolio_resume_html_path();
		$pdf  = maulik_portfolio_resume_pdf_path();
		$hash = maulik_portfolio_resume_hash_path();

		if ( '' === $dir || '' === $html || '' === $pdf || '' === $hash ) {
			return false;
		}

		if ( ! is_readable( $html ) ) {
			return false;
		}

		if ( ! wp_mkdir_p( $dir ) ) {
			return false;
		}

		$lock_path = $dir . '/resume.pdf.lock';
		$handle    = fopen( $lock_path, 'c' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Lock file needs a raw handle.

		if ( false === $handle ) {
			return false;
		}

		$locked = flock( $handle, LOCK_EX );

		if ( ! $locked ) {
			fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing a raw handle.
			return false;
		}

		// Recheck under the lock. Another request may have generated it while
		// this one was waiting.
		if ( maulik_portfolio_resume_is_current() ) {
			flock( $handle, LOCK_UN );
			fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing a raw handle.
			return true;
		}

		maulik_portfolio_resume_discard_pdf();

		/*
		 * The Chrome profile lives under the system temporary directory, not
		 * under uploads. A browser profile inside a web served directory would
		 * be downloadable by anyone and carries cache and history files that
		 * have no business being public. The name is stable because the lock
		 * above guarantees only one generator runs at a time.
		 */
		if ( ! wp_is_writable( $dir ) ) {
			flock( $handle, LOCK_UN );
			fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing a raw handle.
			return false;
		}

		$home    = trailingslashit( get_temp_dir() ) . 'maulik-resume-chrome-home';
		$profile = $home . '/profile';
		$temp    = $dir . '/resume.pdf.' . wp_generate_password( 12, false, false ) . '.tmp';

		if ( ! wp_mkdir_p( $home ) || ! wp_is_writable( $home ) ) {
			flock( $handle, LOCK_UN );
			fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing a raw handle.
			return false;
		}

		$command = maulik_portfolio_resume_generator_command( $html, $temp, $home, $profile );

		// Output is captured but deliberately not consulted. MEASURED: Chrome
		// 154 writes its "N bytes written to file" line to STDERR, not stdout, so
		// shell_exec returns an empty string on a fully successful render. An
		// earlier version of this function treated a non-empty stdout as the
		// success signal and therefore rejected every successful render. The
		// decision is made from the file itself, which is the only thing that
		// matters.
		shell_exec( $command ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_shell_exec -- Constant argument list, see maulik_portfolio_resume_generator_command().

		$html_hash = maulik_portfolio_resume_html_hash();

		$generated = (
			is_readable( $temp )
			&& filesize( $temp ) > 0
			&& '%PDF' === substr( (string) file_get_contents( $temp, false, null, 0, 5 ), 0, 4 ) // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reads the first bytes of a local file to check the magic number.
			&& '' !== $html_hash
		);

		if ( $generated && ! rename( $temp, $pdf ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.rename_rename -- rename() is atomic within a filesystem, which WP_Filesystem::move() does not guarantee.
			$generated = false;
		}

		if ( $generated ) {
			wp_delete_file( $temp );

			$written = file_put_contents( $hash, $html_hash ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- Local cache sidecar.

			if ( false === $written ) {
				// Without the sidecar the next request cannot prove the PDF is
				// current, so it would regenerate on every hit. Drop the PDF too
				// rather than serve something that will be rebuilt anyway.
				maulik_portfolio_resume_discard_pdf();
			}
		}

		flock( $handle, LOCK_UN );
		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closing a raw handle.

		return maulik_portfolio_resume_is_current();
	}
}

if ( ! function_exists( 'maulik_portfolio_resume_resolve' ) ) {
	/**
	 * Return a PDF that matches the stored HTML, generating it if needed.
	 *
	 * @since 0.1.0
	 *
	 * @return string Absolute path of a current PDF, or an empty string when
	 *                one could not be produced.
	 */
	function maulik_portfolio_resume_resolve() {
		$pdf = maulik_portfolio_resume_pdf_path();

		if ( '' === $pdf ) {
			return '';
		}

		if ( maulik_portfolio_resume_is_current() ) {
			return $pdf;
		}

		if ( ! maulik_portfolio_resume_generate() ) {
			return '';
		}

		// Checked as a statement rather than as a ternary because this predicate
		// reads the filesystem and PHPStan infers it as pure, which would make a
		// ternary on its result look like a constant to a reader and to the tool.
		if ( maulik_portfolio_resume_is_current() ) {
			return $pdf;
		}

		return '';
	}
}

if ( ! function_exists( 'maulik_portfolio_serve_resume_html' ) ) {
	/**
	 * Serve the stored resume HTML.
	 *
	 * Also the fallback when the PDF cannot be produced, so a generator failure
	 * shows the owner a readable document rather than a broken or empty file.
	 *
	 * The status code is a parameter rather than a constant because the two
	 * callers want different things. The HTML action always answers 200, while
	 * the PDF fallback answers 503: the request for a PDF genuinely failed, and
	 * saying 200 would tell a browser, a crawler or a monitoring check that a
	 * PDF arrived when none did.
	 *
	 * WHY THIS FUNCTION EXITS RATHER THAN RETURING, and this is the single most
	 * important thing about serving a document from admin-ajax.php.
	 *
	 * MEASURED, twice, and both measurements first read as a bug in this code
	 * before the cause turned out to be one line at the bottom of
	 * wp-admin/admin-ajax.php: that file ends with wp_die( '0' ). On an Ajax
	 * request wp_die routes to _ajax_wp_die_handler, which defaults its
	 * response to 200, calls status_header() again, and then dies with the
	 * string '0'. So whatever status this endpoint set is discarded and
	 * replaced with 200, and a stray '0' is appended to the body.
	 *
	 * With a deliberately broken document path, this endpoint set 503, the
	 * error log recorded http_response_code() as 503, and the wire still
	 * carried "HTTP/1.1 200 OK". The appended zero also overran the
	 * Content-Length of 0, which is why curl reported a one byte body.
	 *
	 * Returning normally hands control back to admin-ajax.php and all of that
	 * happens. Calling die() here ends the request while the status this
	 * function set is still the one on the wire. Nothing is lost by ending the
	 * request early: the two actions registered in this file are the entire
	 * purpose of the request, and there is no shutdown work on this path that
	 * needs to run.
	 *
	 * @since 0.1.0
	 *
	 * @param int $status HTTP status to send.
	 *
	 * @return void
	 */
	function maulik_portfolio_serve_resume_html( $status = 200 ) {
		$path = maulik_portfolio_resume_html_path();

		nocache_headers();
		header( 'Content-Type: text/html; charset=utf-8' );
		header( 'Content-Disposition: inline' );
		header( 'X-Robots-Tag: noindex' );

		$body = is_readable( $path ) ? file_get_contents( $path ) : false; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local file, not a remote URL.

		if ( false === $body ) {
			// Nothing readable to serve at all, so there is no fallback to
			// offer. Report the stored document as missing.
			header( 'Content-Length: 0' );
			status_header( 404 );

			die();
		}

		header( 'Content-Length: ' . strlen( $body ) );
		status_header( $status );

		echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The owner's own document, served verbatim by contract.

		die();
	}
}

if ( ! function_exists( 'maulik_portfolio_serve_resume_pdf' ) ) {
	/**
	 * Serve the derived resume PDF.
	 *
	 * Falls back to the HTML with a clear status when the generator fails or
	 * times out. A visitor asking for a PDF is better served by a document they
	 * can read than by an empty download, but the response is a 503 so that
	 * nothing upstream mistakes the HTML for a PDF that arrived.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function maulik_portfolio_serve_resume_pdf() {
		$pdf = maulik_portfolio_resume_resolve();

		// Both failure branches hand off to the HTML fallback, which exits the
		// request itself. See the docblock on that function for why returning
		// normally here would lose the status code.
		if ( '' === $pdf ) {
			maulik_portfolio_serve_resume_html( 503 );
		}

		$body = file_get_contents( $pdf ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local file, not a remote URL.

		if ( false === $body ) {
			maulik_portfolio_serve_resume_html( 503 );
		}

		nocache_headers();
		header( 'Content-Type: application/pdf' );
		header( 'Content-Disposition: inline; filename="Maulik-Bhalodiya-WordPress-PHP-Developer-Resume.pdf"' );
		header( 'Content-Length: ' . strlen( $body ) );
		header( 'X-Robots-Tag: noindex' );

		status_header( 200 );

		echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Binary PDF stream, not markup.

		/*
		 * Exits before admin-ajax.php can reach its own wp_die( '0' ), which
		 * would reset the status to 200 and append a stray byte to a PDF.
		 * MEASURED: without this the successful PDF response is fine but every
		 * error status is lost. The full explanation is in the docblock on
		 * maulik_portfolio_serve_resume_html().
		 */
		die();
	}
}

if ( ! function_exists( 'maulik_portfolio_resume_actions' ) ) {
	/**
	 * Register the resume endpoints for logged in and logged out visitors.
	 *
	 * A portfolio has no accounts, so no visitor is logged in and the nopriv
	 * registration is the one that actually serves traffic. Both are registered
	 * because a logged in editor following the resume link should get the same
	 * document rather than a blank ajax response.
	 *
	 * Both actions are served through admin-ajax.php so that no full page cache
	 * can hold a stale copy of a PDF that the owner has just edited.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function maulik_portfolio_resume_actions() {
		add_action( 'wp_ajax_maulik_resume', 'maulik_portfolio_serve_resume_pdf' );
		add_action( 'wp_ajax_nopriv_maulik_resume', 'maulik_portfolio_serve_resume_pdf' );
		add_action( 'wp_ajax_maulik_resume_html', 'maulik_portfolio_serve_resume_html' );
		add_action( 'wp_ajax_nopriv_maulik_resume_html', 'maulik_portfolio_serve_resume_html' );
	}
	add_action( 'init', 'maulik_portfolio_resume_actions' );
}
