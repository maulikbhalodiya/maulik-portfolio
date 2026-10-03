/**
 * Resume hero actions.
 *
 * WHAT THIS IS. One control on the Resume hero, the design's "Print View", which
 * opens the browser's own print dialog so the visitor gets the printed resume
 * document. The theme already owns a print stylesheet
 * (assets/styles/components/_print-surfaces.scss) and the page already marks its
 * chrome with no-print, so the control is not a promise the theme cannot keep.
 *
 * WHY A SCRIPT AND NOT MARKUP. window.print() is a scripting context method.
 * There is no element, attribute or URL that triggers a print dialog on its
 * own, so the behaviour cannot live in block markup, and the design itself calls
 * window.print() from an onClick handler at src/pages/ResumePage.tsx line 71. A
 * control that did nothing would be worse than an absent one, so the handler has
 * to exist for the button to be honest.
 *
 * WHY A BUTTON ELEMENT. core/button declares tagName with an enum of "a" or
 * "button" and its save function emits a real <button type="button"> when
 * tagName is "button", so this is a core authored control rather than pasted
 * markup. docs/ARCHITECTURE.md 2.4 requires a core block wherever a core block
 * fits, and this one fits.
 *
 * THE SELECTOR IS A DATA ATTRIBUTE, NOT A CLASS. The class on the block is the
 * styling hook and an editor may rename or remove it. The data attribute is
 * declared here and nowhere else, so the behaviour and the markup it needs are
 * stated in one place each and neither can drift into a control that silently
 * stops working.
 *
 * NO jQUERY, NO GLOBALS, NO BUILD STEP. One listener on one element, removed
 * again on pagehide, following assets/js/site-header.js. Every listener is torn
 * down so a bfcache restore cannot leave two handlers on the same button, which
 * would open two print dialogs.
 */

( function () {
	'use strict';

	const PRINT_SELECTOR = '[data-rs-print]';

	/**
	 * Open the print dialog for the current document.
	 *
	 * @return {void}
	 */
	function onPrintClick() {
		window.print();
	}

	const buttons = document.querySelectorAll( PRINT_SELECTOR );

	/*
	 * The button lives in the Resume hero, which is content rather than chrome,
	 * so on every other route in the theme this query finds nothing. That is a
	 * legitimate state and not an error: the script has to be inert rather than
	 * throw when it is loaded somewhere the control does not exist.
	 */
	buttons.forEach( function ( button ) {
		button.addEventListener( 'click', onPrintClick );
	} );

	if ( ! buttons.length ) {
		return;
	}

	/**
	 * Remove every listener this file added.
	 *
	 * @return {void}
	 */
	function teardown() {
		buttons.forEach( function ( button ) {
			button.removeEventListener( 'click', onPrintClick );
		} );
	}

	window.addEventListener( 'pagehide', teardown );
} )();
