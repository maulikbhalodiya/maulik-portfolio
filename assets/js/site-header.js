/**
 * Site header scroll state.
 *
 * The design header is transparent with a transparent bottom border and no blur
 * until the visitor has scrolled past 24 pixels, at which point it takes the
 * near black band at 92 percent, the hairline border and a 12 pixel backdrop
 * blur, with the colour transition the design's duration 200.
 *
 * A scroll position is not expressible in CSS, so it is read here. The file is a
 * dependency free IIFE: no jQuery, no build step, no module loader, and it runs
 * as a classic script. It is enqueued on the front end only.
 *
 * The threshold, the class name and the selector are all declared once at the
 * top so that the script and the stylesheet have a single place to agree.
 */

( function () {
	'use strict';

	const SCROLL_THRESHOLD = 24;
	const SCROLLED_CLASS = 'is-scrolled';
	const HEADER_SELECTOR = '.site-header';

	const header = document.querySelector( HEADER_SELECTOR );

	/*
	 * A missing header is a legitimate state rather than an error: the header
	 * template part can be removed in the site editor, and this script has to be
	 * inert in that case instead of throwing on every scroll.
	 */
	if ( ! header ) {
		return;
	}

	let frameRequested = false;

	/**
	 * Apply the scrolled class from the current scroll position.
	 *
	 * The class is added above the threshold and removed at or below it, so
	 * returning to the top of the page restores the transparent bar exactly
	 * rather than leaving the scrolled band stuck there.
	 *
	 * @return {void}
	 */
	function applyScrollState() {
		frameRequested = false;

		if ( window.scrollY > SCROLL_THRESHOLD ) {
			header.classList.add( SCROLLED_CLASS );
		} else {
			header.classList.remove( SCROLLED_CLASS );
		}
	}

	/**
	 * Scroll handler.
	 *
	 * The state is applied once per animation frame rather than once per scroll
	 * event. The scroll event can fire far more often than the browser paints,
	 * and the class only needs to be right at paint time. The guard is a plain
	 * flag rather than a cancel and re-request so that a burst of events still
	 * results in exactly one scheduled frame.
	 *
	 * @return {void}
	 */
	function onScroll() {
		if ( frameRequested ) {
			return;
		}

		frameRequested = true;
		window.requestAnimationFrame( applyScrollState );
	}

	/*
	 * Applied once on load rather than only on the first scroll. A visitor who
	 * reloads a scrolled page, or arrives on it from a back navigation, is
	 * already past the threshold and the bar must reflect that immediately
	 * instead of waiting for a pixel of movement.
	 */
	applyScrollState();

	window.addEventListener( 'scroll', onScroll, { passive: true } );
} )();
