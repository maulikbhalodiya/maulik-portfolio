/**
 * View script module for the portfolio detail block.
 *
 * WHAT THIS DOES AND WHAT IT DOES NOT.
 *
 * Everything the visitor reads is already in the HTML the server sent: the
 * cover, the title, the excerpt, the meta line and the project content. The meta
 * line is a native details element, so it opens and closes with no JavaScript at
 * all. This file adds one behaviour, which is that an open meta line stays open
 * across a client side navigation rather than snapping shut on the next render.
 *
 * It is declared with viewScriptModule, so WordPress loads it with low fetch
 * priority and in the footer, out of the critical rendering path. Interactivity
 * API only: no jQuery, no framework, and no global state outside the namespaced
 * store below.
 *
 * @module
 */

import { store, getElement, withSyncEvent } from '@wordpress/interactivity';

/**
 * Store namespace. Matches the data-wp-interactive value on the wrapper that
 * render.php prints.
 */
const STORE_NAME = 'maulik-portfolio/portfolio-detail';

store( STORE_NAME, {
	state: {
		/**
		 * Whether the meta line should be open.
		 *
		 * A getter rather than a stored flag, because the source of truth is the
		 * element itself. The details element has already flipped by the time any
		 * handler runs, so reading it back is the only way to agree with the
		 * visitor. A stored boolean set inside the handler is always one tick
		 * behind the click that caused it.
		 *
		 * @return {boolean} True when the meta line is open.
		 */
		get isOpen() {
			return Boolean( getElement()?.open );
		},
	},

	actions: {
		/**
		 * Placeholder action referenced by the toggle directive.
		 *
		 * There is deliberately nothing to do here. The state is derived from the
		 * element, so the binding updates on its own. The action exists so the
		 * click can be bound with withSyncEvent and so the store has a named
		 * behaviour rather than an inline expression that would have to be
		 * duplicated if this block ever grows one.
		 *
		 * @return {void}
		 */
		onToggle: withSyncEvent( () => {} ),
	},
} );