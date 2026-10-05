/**
 * Resume hero actions: no script required.
 *
 * WHAT THIS IS. A deliberate no-op. It is still enqueued by functions.php on
 * the Resume route, so it exists to state one thing in one place: both Resume
 * hero controls are now plain markup, and nothing on this page scripts the
 * browser any more.
 *
 * WHY THERE IS NO HANDLER. The "Print View" control used to call window.print()
 * on this page, which printed the themed page: the hero, all seven numbered
 * sections, the site header and the site footer. The owner asked for the print
 * output to be their own standalone resume document and not the page, so
 * "Print View" is now an anchor on the resume PDF endpoint with target="_blank",
 * and the browser's print dialog contains only that document. The behaviour a
 * script could add is behaviour the anchor now gets for free, so there is
 * nothing left to bind.
 *
 * NO window.print() ANYWHERE ON THIS PAGE. A handler that printed the themed
 * page is the exact defect this file used to carry, and it must not come back,
 * so it is removed rather than left bound to a control that no longer exists.
 *
 * WHAT THE TWO CONTROLS DO NOW, IN MARKUP ALONE.
 *
 * "Download Resume" is an anchor on /wp-admin/admin-ajax.php?action=maulik_resume
 * carrying the HTML download attribute, so the browser saves the PDF instead of
 * rendering it in place. "Print View" is the same anchor with target="_blank"
 * and rel="noopener noreferrer", which opens the PDF inline in a new tab so the
 * visitor's print dialog holds only the resume. Both hrefs are relative, so a
 * domain change moves them with the site.
 *
 * WHY ANCHORS RATHER THAN window.open. Opening a URL the visitor can see in the
 * status bar, or middle-click, or open in a new tab, is better than a scripted
 * popup the browser may block, and it works with scripting disabled. Print View
 * therefore stays an anchor rather than becoming a scripted window.open call.
 *
 * WHY THE FILE STAYS. functions.php enqueues assets/js/resume-actions.js on the
 * Resume route. Deleting the file would leave that enqueue pointing at nothing,
 * so the script remains, empty of behaviour, and loading it anywhere is inert
 * rather than an error.
 *
 * SHAPE. One IIFE, 'use strict', no jQuery, no globals, no console noise,
 * following assets/js/site-header.js. There are no listeners to tear down, so
 * there is no pagehide teardown to keep, and with no listener there is nothing
 * for a bfcache restore to fire twice.
 */

( function () {
	'use strict';
	/*
	 * Intentionally empty. See the header comment. The hero controls are anchors
	 * on the resume PDF endpoint and need no script to do their job.
	 */
} )();
