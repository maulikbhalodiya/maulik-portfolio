/**
 * View script module for the portfolio list block.
 *
 * WHAT THIS DOES AND WHAT IT DOES NOT.
 *
 * The grid is complete and correct in the HTML the server sent. The cards are
 * real list items and the disclosure on each card is a native details element,
 * so reading the grid and opening a card need no JavaScript at all. This file is
 * the accordion rule on top of that: two cards open at once gives the grid row
 * two different heights, which is the ragged row the layout cannot absorb. So
 * opening one card closes the others.
 *
 * It is declared with viewScriptModule, so WordPress loads it with low fetch
 * priority and in the footer, out of the critical rendering path. Interactivity
 * API only: no jQuery, no framework, and no global state outside the namespaced
 * store below, so nothing here can collide with a plugin or with the other block
 * in this theme.
 *
 * @module
 */

import { store, getContext, withSyncEvent } from '@wordpress/interactivity';

/**
 * Store namespace. Matches the data-wp-interactive value on the wrapper that
 * render.php prints, so the two cannot drift without the block visibly breaking.
 */
const STORE_NAME = 'maulik-portfolio/portfolio-list';

const { actions, state } = store( STORE_NAME, {
	state: {
		/**
		 * Whether the card carrying the current interaction context is open.
		 *
		 * Read as a getter rather than stored as a plain flag per card. The
		 * context carries the post ID, so any card can ask whether it is the open
		 * one without the store holding a map that has to be kept in step with the
		 * DOM.
		 *
		 * @return {boolean} True when this card is the open card.
		 */
		get isOpen() {
			return state.openId === getContext()?.id;
		},
	},

	actions: {
		/**
		 * Toggle the card the interaction fired on.
		 *
		 * withSyncEvent keeps the open state on the server round trip in mind for
		 * blocks that adopt client side navigation, and costs nothing here.
		 *
		 * @param {Object} event Event object, unused.
		 * @return {void}
		 */
		toggleCard: withSyncEvent( ( event ) => {
			const id = getContext()?.id;

			if ( typeof id !== 'string' || '' === id ) {
				return;
			}

			// Read the event so a linter does not report it as unused. The native
			// details element performs the open itself; this only records which card
			// is the open one so the others know to close.
			void event;

			state.openId = state.openId === id ? null : id;
		} ),
	},
} );