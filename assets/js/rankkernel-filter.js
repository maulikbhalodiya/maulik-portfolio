/**
 * RankKernel architecture panel: the subsystem status filter and the diagram
 * inspector.
 *
 * WHERE THE TWO PIECES OF BEHAVIOUR COME FROM.
 *
 * Both are ports of src/components/RankKernelArchitecture.tsx in the design
 * reference, driven in a browser and read back rather than inferred. The
 * reference holds selectedId, defaulting to "modules", plus hoveredId, and
 * resolves the panel from hoveredId || selectedId. Clicking a node, pressing
 * Enter or Space on one, or clicking one of the connected subsystem chips all
 * change selection, and the directory entry for the selected subsystem is the
 * one drawn in its selected state. Hovering a node previews it without
 * committing, and moving the pointer off reverts to the committed selection.
 *
 * Nothing about the panel text lives here. Every field of all nine panels is
 * authored in content/pages/rankkernel.html as real core blocks, one panel per
 * subsystem, and this script only decides which one is visible. The same is
 * true of the relationships: which subsystems are connected to which is stated
 * by the connected subsystem chips inside the panel, and which diagram edge
 * joins which two nodes is stated by the data attributes on the edge itself.
 * Reading them back from the document means the editor and the script cannot
 * disagree, and adding a tenth subsystem to the diagram does not mean editing
 * this file.
 *
 * THE FILTER.
 *
 * The four tabs are a real tablist. Selecting one removes the subsystems that do
 * not match from the status directory entirely rather than hiding them, so a
 * screen reader is never offered a control that filters nothing, and so the
 * visible subsystem count is the real count. The diagram nodes are dimmed to
 * the design's 0.3 for non-matching subsystems.
 *
 * A tab whose state has no subsystem behind it is hidden, by the same argument:
 * a control that always narrows the figure to nothing is a broken control. The
 * rule is derived from the document on every pass rather than declared, so it
 * holds now, when all nine modules are shipped and there is no planned member,
 * and it will hold after a module is marked planned, with no edit here. See
 * applyTabAvailability and watchStatuses.
 *
 * ONE VOCABULARY.
 *
 * A node's status is its status class and nothing else. There is no
 * data-rk-status attribute, which used to hold IMPLEMENTED or IN PROGRESS
 * beside a class that read is-completed or is-running. The tab values are the
 * same three words with the is- prefix removed, so the class, the filter value
 * and the badge all say one thing.
 *
 * Behaviour measured from the design reference: the tabs are 6px 12px padding
 * at 12px mono, the selected tab is #FACC15 on #000000 at weight 600, the
 * unselected tabs are #171719 on #D8D5CA with a #262626 border, the status mark
 * is drawn only on an unselected status tab, and the diagram node groups fall to
 * opacity 0.3. The reference carries no keyboard handling and no tabindex on
 * its tabs at all, which is why the arrow, Home and End behaviour
 * below is an addition to the reference rather than a port of it. The reference
 * likewise has no keydown handling on the diagram nodes, but it does declare
 * them as role button with tabindex 0, and a g element is not natively
 * activatable, so Enter and Space are handled here to make that promise true.
 *
 * THE FILTER IS A REGISTERED BLOCK, NOT MARKUP IN THE CONTENT.
 *
 * docs/ARCHITECTURE.md 2.4 forbids core/html in page content, and a core block
 * cannot carry role, aria-selected or a data attribute at all, because
 * WP_Block_Type::prepare_attributes_for_render() drops every attribute that is
 * not in the block's own schema. The row is therefore emitted by
 * inc/rankkernel-filters.php, following inc/project-filters.php, which is the
 * same answer given for the identical problem on the Projects archive.
 *
 * No jQuery, no globals, no window pollution, no build step, classic script.
 * One listener per event on the panel, so repeated clicks cannot double them up,
 * and every listener is removed on pagehide. Teardown also puts back the removed
 * directory entries, every subsystem panel and every node opacity, so the
 * document returns to its server rendered state. Teardown also reveals every tab
 * again, so a document already in a filtered state heals on unload rather than
 * inheriting a hidden control from a script that has stopped running.
 */

( function () {
	'use strict';

	const TABLIST_SELECTOR = '[data-rk-filter]';
	const TAB_SELECTOR = '[role="tab"]';
	const SCOPE_SELECTOR = '.rk-arch';

	const NODE_GROUP_SELECTOR = '.rk-svg-node';
	const PANEL_SELECTOR = '[data-rk-panel]';
	const CHIP_SELECTOR = '[data-rk-select]';
	const EDGE_SELECTOR = '.rk-svg-edge';
	const CORE_EDGE_SELECTOR = '[data-rk-edge]';
	const DIRECTORY_ITEM_SELECTOR = '.rk-directory__item';
	const DIRECTORY_STATUS_SELECTOR = '.rk-directory__status';
	const DIRECTORY_NAME_SELECTOR = '[data-rk-id]';

	const CLASS_IS_ACTIVE = 'is-active';
	const CLASS_IS_RELATED = 'is-related';
	const CLASS_IS_ON = 'is-on';
	const CLASS_IS_OFF = 'is-off';
	const CLASS_DIRECTORY_SELECTED = 'rk-directory__item--selected';

	/** Value of the tab that shows every subsystem. */
	const ALL = 'all';

	/*
	 * THE STATUS VOCABULARY, DECLARED ONCE.
	 *
	 * These three class names are the only module states in this project, and
	 * they are the single source of a node's status. The filter compares the
	 * token after the is- prefix, so completed, running and planned are the same
	 * words the diagram is styled from and the same words the tabs carry in
	 * data-rk-filter-value.
	 *
	 * There is deliberately no data-rk-status attribute any more. It used to
	 * read IMPLEMENTED or IN PROGRESS while the class beside it read is-completed
	 * or is-running, so the markup carried two vocabularies, only one of which
	 * anything acted on, and changing a module's state meant editing two things
	 * in two files in step. Nothing outside this file ever read the attribute:
	 * the stylesheet keys off the class, the directory entries carry the
	 * visible word, and the homepage figure has its own node markup entirely.
	 *
	 * is-active, is-related, is-on and is-off share the is- prefix and are not
	 * states, which is why the list is written out rather than matched by prefix.
	 */
	const STATUS_TOKENS = [ 'completed', 'running', 'planned' ];
	const STATUS_CLASS_PREFIX = 'is-';

	/** Opacity the design applies to a diagram node that does not match. */
	const DIMMED_OPACITY = '0.3';

	/** Opacity a diagram node has when it matches, or when nothing is filtered. */
	const FULL_OPACITY = '1';

	/**
	 * One initialised architecture panel.
	 *
	 * @param {HTMLElement} tablist The tablist container.
	 */
	function RankKernelPanel( tablist ) {
		this.tablist = tablist;
		this.scope = tablist.closest( SCOPE_SELECTOR ) || tablist.parentElement;
		this.tabs = [];
		this.directoryItems = [];
		this.directoryEntries = [];
		this.nodes = [];
		this.panels = [];
		this.edges = [];
		this.coreEdges = [];
		this.statuses = {};
		this.selected = '';
		this.committed = '';
		this.previewed = '';
		this.destroyed = false;

		this.observer = null;
		this.onClick = null;
		this.onKeyDown = null;
		this.onPointerOver = null;
		this.onPointerOut = null;
		this.onPageHide = null;

		this.init();
	}

	/**
	 * Collect the filterable and selectable elements and attach the listeners.
	 *
	 * @return {void}
	 */
	RankKernelPanel.prototype.init = function () {
		if ( ! this.scope ) {
			return;
		}

		this.tabs = Array.from( this.tablist.querySelectorAll( TAB_SELECTOR ) );

		this.readDirectoryItems();
		this.readNodes();
		this.readPanels();
		this.readEdges();

		const self = this;

		this.onClick = function ( event ) {
			self.handleClick( event );
		};

		this.onKeyDown = function ( event ) {
			self.handleKeyDown( event );
		};

		this.onPointerOver = function ( event ) {
			self.handlePointerOver( event );
		};

		this.onPointerOut = function ( event ) {
			self.handlePointerOut( event );
		};

		this.onPageHide = function () {
			self.destroy();
		};

		this.scope.addEventListener( 'click', this.onClick );
		this.scope.addEventListener( 'keydown', this.onKeyDown );
		this.scope.addEventListener( 'pointerover', this.onPointerOver );
		this.scope.addEventListener( 'pointerout', this.onPointerOut );
		window.addEventListener( 'pagehide', this.onPageHide );

		/*
		 * The server rendered markup already carries the All tab as selected and
		 * the design's own initial subsystem as the visible panel. Applying both
		 * once on init rather than trusting the markup means the selected tab
		 * and the selected node classes are correct even if the two ever
		 * disagree, and it is what makes the initial state identical whether or
		 * not this file ever runs.
		 *
		 * readStatuses runs first so the availability pass below is deciding on
		 * the statuses that are actually in the document rather than on whatever
		 * the read methods happened to cache.
		 */
		this.readStatuses();
		this.applyTabAvailability();

		this.apply( ALL );
		this.show( this.panels[ 0 ] ? this.panels[ 0 ].id : '', false );

		this.watchStatuses();
	};

	/**
	 * Read the status directory entries and where each one has to go back to.
	 *
	 * The status is read from the entry's own rendered status label rather than
	 * from a duplicated data attribute, so the entry's status has exactly one
	 * source in post_content and the two cannot drift.
	 *
	 * @return {void}
	 */
	RankKernelPanel.prototype.readDirectoryItems = function () {
		const items = this.scope.querySelectorAll( DIRECTORY_ITEM_SELECTOR );

		for ( let i = 0; i < items.length; i++ ) {
			const label = items[ i ].querySelector( DIRECTORY_STATUS_SELECTOR );

			if ( ! label ) {
				continue;
			}

			this.directoryItems.push( {
				element: items[ i ],
				status: normaliseStatus( label.textContent ),
				parent: items[ i ].parentNode,
				next: items[ i ].nextSibling,
			} );

			const name = items[ i ].querySelector( DIRECTORY_NAME_SELECTOR );

			if ( name ) {
				this.directoryEntries.push( {
					id: name.getAttribute( 'data-rk-id' ),
					element: items[ i ],
				} );
			}
		}
	};

	/**
	 * Read the diagram node groups and their statuses.
	 *
	 * A node's status is its own status class. There is no second copy of it
	 * anywhere, which is what lets a module change state by editing one class
	 * on one element.
	 *
	 * @return {void}
	 */
	RankKernelPanel.prototype.readNodes = function () {
		const nodes = this.scope.querySelectorAll( NODE_GROUP_SELECTOR );

		for ( let i = 0; i < nodes.length; i++ ) {
			this.nodes.push( {
				id: nodes[ i ].getAttribute( 'data-rk-id' ),
				element: nodes[ i ],
				status: statusFromClass( nodes[ i ] ),
			} );
		}
	};

	/**
	 * Read the nine subsystem panels and their connected subsystem chips.
	 *
	 * The connected list is read off the panel itself, which is the same place
	 * an editor sees it, so the diagram highlighting follows the authored panel
	 * rather than a second copy of the relationships in this file.
	 *
	 * @return {void}
	 */
	RankKernelPanel.prototype.readPanels = function () {
		const panels = this.scope.querySelectorAll( PANEL_SELECTOR );

		for ( let i = 0; i < panels.length; i++ ) {
			const chips = panels[ i ].querySelectorAll( CHIP_SELECTOR );
			const connected = [];

			for ( let j = 0; j < chips.length; j++ ) {
				connected.push( chips[ j ].getAttribute( 'data-rk-select' ) );
			}

			this.panels.push( {
				id: panels[ i ].getAttribute( 'data-rk-panel' ),
				element: panels[ i ],
				connected,
			} );
		}
	};

	/**
	 * Read the diagram edges, both the subsystem to subsystem lines and the
	 * centre to subsystem lines.
	 *
	 * @return {void}
	 */
	RankKernelPanel.prototype.readEdges = function () {
		const edges = this.scope.querySelectorAll( EDGE_SELECTOR );

		for ( let i = 0; i < edges.length; i++ ) {
			this.edges.push( {
				from: edges[ i ].getAttribute( 'data-rk-edge-from' ),
				to: edges[ i ].getAttribute( 'data-rk-edge-to' ),
				element: edges[ i ],
			} );
		}

		const coreEdges = this.scope.querySelectorAll( CORE_EDGE_SELECTOR );

		for ( let i = 0; i < coreEdges.length; i++ ) {
			this.coreEdges.push( {
				id: coreEdges[ i ].getAttribute( 'data-rk-edge' ),
				element: coreEdges[ i ],
			} );
		}
	};

	/**
	 * Re-read every status in the panel and report whether any of them moved.
	 *
	 * The stored status strings are refreshed in place, so the handles held in
	 * this.nodes and this.directoryItems never go stale. The boolean is what the
	 * observer uses to stay quiet: painting the diagram adds and removes
	 * is-active, is-related, is-on and is-off constantly, and every one of those
	 * mutations would otherwise re-enter this pass.
	 *
	 * A status is a member of a state if any node carries that state class or
	 * any directory entry shows that state word. The two are authored to agree,
	 * and counting either one alone would let a state look empty because the
	 * figure was edited without the list, or the reverse.
	 *
	 * @return {boolean} Whether the set of statuses changed since the last pass.
	 */
	RankKernelPanel.prototype.readStatuses = function () {
		const seen = [];
		let i;

		for ( i = 0; i < this.nodes.length; i++ ) {
			this.nodes[ i ].status = statusFromClass( this.nodes[ i ].element );

			seen.push( this.nodes[ i ].status );
		}

		for ( i = 0; i < this.directoryItems.length; i++ ) {
			const label = this.directoryItems[ i ].element.querySelector(
				DIRECTORY_STATUS_SELECTOR
			);

			this.directoryItems[ i ].status = label
				? normaliseStatus( label.textContent )
				: '';

			seen.push( this.directoryItems[ i ].status );
		}

		const signature = seen.join( '|' );

		if ( signature === this.statuses.signature ) {
			return false;
		}

		this.statuses.signature = signature;
		this.statuses.members = tallyStatuses( seen );

		return true;
	};

	/**
	 * Hide the tabs whose state has no member, and reveal any that now has one.
	 *
	 * A control that reliably narrows the figure to nothing is a broken
	 * control, so the row only offers states that have something behind them.
	 * This is derived, not declared: the tab is hidden because the document
	 * contains no member of its state, and it reappears the moment one exists,
	 * with no edit to this file and no edit to the tab list in PHP. Marking a
	 * module planned is therefore a one line change to one class in
	 * content/pages/rankkernel.html, and the PLANNED tab appears on its own.
	 *
	 * The attribute is used rather than a class because the tab is
	 * display: inline-flex and would otherwise keep its box, which is the same
	 * reason .rk-inspector__panel carries an explicit [hidden] rule.
	 *
	 * @return {void}
	 */
	RankKernelPanel.prototype.applyTabAvailability = function () {
		const members = this.statuses.members || {};
		let hiddenSelected = false;
		let i;

		for ( i = 0; i < this.tabs.length; i++ ) {
			const tab = this.tabs[ i ];
			const value = tab.getAttribute( 'data-rk-filter-value' ) || ALL;
			const isEmpty = value !== ALL && ! members[ value ];

			tab.hidden = isEmpty;

			if ( isEmpty && value === this.selected ) {
				hiddenSelected = true;
			}
		}

		/*
		 * A filter that has just been emptied cannot stay selected, because
		 * leaving it selected would leave the figure showing nothing with no
		 * visible control to put it back. Falling back to All is the only state
		 * that is always available.
		 */
		if ( hiddenSelected ) {
			this.selected = ALL;
		}
	};

	/**
	 * Watch for a status class being changed so an emptied or refilled state
	 * corrects itself without a reload.
	 *
	 * A MutationObserver is used rather than a second source of truth in this
	 * file, because the point of the class vocabulary is that the diagram is the
	 * truth. Two things here depend on that staying true: the row offered by
	 * this script and the counts a reader would count by hand.
	 *
	 * The callback does no work for the mutations this script makes itself. It
	 * asks readStatuses for a boolean and returns immediately when the statuses
	 * have not moved, so painting is-active on hover cannot feed the observer
	 * back into itself.
	 *
	 * @return {void}
	 */
	RankKernelPanel.prototype.watchStatuses = function () {
		if ( typeof window.MutationObserver !== 'function' ) {
			return;
		}

		const self = this;

		this.observer = new window.MutationObserver( function () {
			if ( self.destroyed || ! self.readStatuses() ) {
				return;
			}

			self.applyTabAvailability();
			self.apply( self.selected );
		} );

		this.observer.observe( this.scope, {
			subtree: true,
			childList: true,
			characterData: true,
			attributes: true,
			attributeFilter: [ 'class' ],
		} );
	};

	/**
	 * The panel click handler, covering the tabs, the diagram nodes and the
	 * connected subsystem chips with one delegated listener.
	 *
	 * @param {MouseEvent} event Click event.
	 * @return {void}
	 */
	RankKernelPanel.prototype.handleClick = function ( event ) {
		const tab = event.target.closest( TAB_SELECTOR );

		if ( tab && this.tablist.contains( tab ) ) {
			this.selectFilter(
				tab.getAttribute( 'data-rk-filter-value' ) || ALL,
				true
			);

			return;
		}

		const chip = event.target.closest( CHIP_SELECTOR );

		if ( chip ) {
			this.show( chip.getAttribute( 'data-rk-select' ), true );

			return;
		}

		const node = event.target.closest( NODE_GROUP_SELECTOR );

		if ( node ) {
			this.show( node.getAttribute( 'data-rk-id' ), true );
		}
	};

	/**
	 * The panel keydown handler.
	 *
	 * Two separate behaviours live here and neither is the other one's job.
	 *
	 * On the tablist this is the standard tabs pattern: arrow keys move focus
	 * and change selection together, which is the automatic activation variant
	 * and the one that matches a filter, because a visitor arrowing across the
	 * four states sees each filter applied as they pass. Enter and Space are not
	 * handled there: they are already the native activation behaviour of a real
	 * button element and fire click, which the click handler above picks up.
	 * Handling them again would select twice.
	 *
	 * On a diagram node, Enter and Space are handled because a g element with
	 * role button is not natively activatable. This is the design's own
	 * onKeyDown on the node group.
	 *
	 * @param {KeyboardEvent} event Keyboard event.
	 * @return {void}
	 */
	RankKernelPanel.prototype.handleKeyDown = function ( event ) {
		const node = event.target.closest( NODE_GROUP_SELECTOR );

		if ( node && ( 'Enter' === event.key || ' ' === event.key ) ) {
			event.preventDefault();
			this.show( node.getAttribute( 'data-rk-id' ), true );

			return;
		}

		this.handleTabKeyDown( event );
	};

	/**
	 * The tabs keyboard handler, implementing the standard tabs pattern.
	 *
	 * @param {KeyboardEvent} event Keyboard event.
	 * @return {void}
	 */
	RankKernelPanel.prototype.handleTabKeyDown = function ( event ) {
		const visible = this.visibleTabs();
		const index = visible.indexOf(
			this.tablist.ownerDocument.activeElement
		);

		if ( index < 0 ) {
			return;
		}

		const last = visible.length - 1;
		let target = null;

		switch ( event.key ) {
			case 'ArrowRight':
			case 'ArrowDown':
				target = index === last ? 0 : index + 1;
				break;
			case 'ArrowLeft':
			case 'ArrowUp':
				target = index === 0 ? last : index - 1;
				break;
			case 'Home':
				target = 0;
				break;
			case 'End':
				target = last;
				break;
			default:
				return;
		}

		event.preventDefault();

		const tab = visible[ target ];
		const value = tab.getAttribute( 'data-rk-filter-value' ) || ALL;

		this.selectFilter( value, true );
	};

	/**
	 * The tabs that are currently on offer.
	 *
	 * The arrow keys move through this rather than through every tab, because a
	 * hidden tab is out of the tab order and landing the keyboard on one would
	 * move focus somewhere a visitor cannot see.
	 *
	 * @return {Element[]} The visible tabs, in document order.
	 */
	RankKernelPanel.prototype.visibleTabs = function () {
		const visible = [];

		for ( let i = 0; i < this.tabs.length; i++ ) {
			if ( ! this.tabs[ i ].hidden ) {
				visible.push( this.tabs[ i ] );
			}
		}

		return visible;
	};

	/**
	 * The pointer hover handler.
	 *
	 * The design resolves the active subsystem as hoveredId || selectedId, so a
	 * pointer over a node previews it and moving away reverts. Hover never
	 * commits: only a click, Enter, Space or a chip changes the selection, which
	 * is what keeps the directory entry and the panel from flickering between
	 * two subsystems as the pointer travels across the diagram.
	 *
	 * @param {PointerEvent} event Pointer event.
	 * @return {void}
	 */
	RankKernelPanel.prototype.handlePointerOver = function ( event ) {
		const node = event.target.closest( NODE_GROUP_SELECTOR );
		const id = node ? node.getAttribute( 'data-rk-id' ) : '';

		if ( ! id ) {
			return;
		}

		// pointerover fires again for every child element the pointer crosses, so
		// an entry that stays inside one node is not a new hover.
		if ( isInside( event, node ) || id === this.previewed ) {
			return;
		}

		this.previewed = id;
		this.show( id, false );
	};

	/**
	 * Restore the committed selection when the pointer leaves a previewed node.
	 *
	 * @param {PointerEvent} event Pointer event.
	 * @return {void}
	 */
	RankKernelPanel.prototype.handlePointerOut = function ( event ) {
		const node = event.target.closest( NODE_GROUP_SELECTOR );
		const id = node ? node.getAttribute( 'data-rk-id' ) : '';

		if ( ! id || id !== this.previewed || isInside( event, node ) ) {
			return;
		}

		this.previewed = '';
		this.show( this.committed, false );
	};

	/**
	 * Select a filter value, optionally moving focus to its tab.
	 *
	 * @param {string}  value     Filter value to select.
	 * @param {boolean} moveFocus Whether to focus the matching tab.
	 * @return {void}
	 */
	RankKernelPanel.prototype.selectFilter = function ( value, moveFocus ) {
		let matched = -1;
		let i;

		for ( i = 0; i < this.tabs.length; i++ ) {
			if (
				this.tabs[ i ].getAttribute( 'data-rk-filter-value' ) ===
					value &&
				! this.tabs[ i ].hidden
			) {
				matched = i;
				break;
			}
		}

		if ( matched < 0 ) {
			for ( i = 0; i < this.tabs.length; i++ ) {
				if (
					this.tabs[ i ].getAttribute( 'data-rk-filter-value' ) ===
					ALL
				) {
					matched = i;
					break;
				}
			}
		}

		if ( matched < 0 ) {
			return;
		}

		this.selected = this.tabs[ matched ].getAttribute(
			'data-rk-filter-value'
		);

		if ( moveFocus ) {
			this.tabs[ matched ].focus();
		}

		this.apply( this.selected );
	};

	/**
	 * Reflect a filter value into the tabs, the directory and the diagram nodes.
	 *
	 * @param {string} value Filter value to apply.
	 * @return {void}
	 */
	RankKernelPanel.prototype.apply = function ( value ) {
		const showingAll = value === ALL;
		let i;

		for ( i = 0; i < this.tabs.length; i++ ) {
			const tab = this.tabs[ i ];
			const isSelected =
				tab.getAttribute( 'data-rk-filter-value' ) === value;

			tab.setAttribute( 'aria-selected', isSelected ? 'true' : 'false' );

			/*
			 * No roving tabindex. Every tab is a real button, so all four stay
			 * natively focusable and aria-selected is the only active state
			 * signal. Any tabindex left on an unselected tab is removed here so
			 * a document already in a tab broken state heals on load.
			 */
			tab.removeAttribute( 'tabindex' );
		}

		for ( i = 0; i < this.directoryItems.length; i++ ) {
			this.applyToDirectoryItem(
				this.directoryItems[ i ],
				showingAll || this.directoryItems[ i ].status === value
			);
		}

		for ( i = 0; i < this.nodes.length; i++ ) {
			const node = this.nodes[ i ];

			node.element.setAttribute(
				'opacity',
				showingAll || node.status === value
					? FULL_OPACITY
					: DIMMED_OPACITY
			);
		}
	};

	/**
	 * Show one subsystem panel and restate the diagram around it.
	 *
	 * @param {string}  id     Subsystem id to show.
	 * @param {boolean} commit Whether this is a committed selection, which is
	 *                         what a later pointer out returns to.
	 * @return {void}
	 */
	RankKernelPanel.prototype.show = function ( id, commit ) {
		let panel = null;
		let i;

		for ( i = 0; i < this.panels.length; i++ ) {
			if ( this.panels[ i ].id === id ) {
				panel = this.panels[ i ];
				break;
			}
		}

		if ( ! panel ) {
			return;
		}

		if ( commit ) {
			this.committed = id;
		}

		for ( i = 0; i < this.panels.length; i++ ) {
			const isCurrent = this.panels[ i ].id === id;

			this.panels[ i ].element.hidden = ! isCurrent;
			this.panels[ i ].element.classList.toggle(
				CLASS_IS_ACTIVE,
				isCurrent
			);
		}

		this.paintNodes( id, panel.connected );
		this.paintEdges( id, panel.connected );
		this.paintDirectory( id );
	};

	/**
	 * Mark the selected node, and the nodes it is connected to, in the diagram.
	 *
	 * @param {string}   id        Subsystem id to mark as selected.
	 * @param {string[]} connected Subsystem ids the selected one is connected to.
	 * @return {void}
	 */
	RankKernelPanel.prototype.paintNodes = function ( id, connected ) {
		for ( let i = 0; i < this.nodes.length; i++ ) {
			const node = this.nodes[ i ];

			node.element.classList.toggle( CLASS_IS_ACTIVE, node.id === id );
			node.element.classList.toggle(
				CLASS_IS_RELATED,
				node.id !== id && connected.indexOf( node.id ) > -1
			);
		}
	};

	/**
	 * Highlight the edges that touch the selected subsystem.
	 *
	 * A subsystem to subsystem edge lights when either end is the selected node,
	 * which is the design's own rule and the reason a hovered node reads as
	 * connected to something. A centre edge is in one of three states: the
	 * selected node's own edge is the amber one with the midpoint dot, the edges
	 * of the nodes it is connected to are cream, and the rest are the dim
	 * hairline.
	 *
	 * @param {string}   id        Subsystem id to highlight.
	 * @param {string[]} connected Subsystem ids the selected one is connected to.
	 * @return {void}
	 */
	RankKernelPanel.prototype.paintEdges = function ( id, connected ) {
		let i;

		for ( i = 0; i < this.edges.length; i++ ) {
			const edge = this.edges[ i ];

			toggleClass(
				edge.element,
				CLASS_IS_ON,
				id === edge.from || id === edge.to
			);
		}

		for ( i = 0; i < this.coreEdges.length; i++ ) {
			const edge = this.coreEdges[ i ];
			const isActive = edge.id === id;
			const isRelated = ! isActive && connected.indexOf( edge.id ) > -1;

			toggleClass( edge.element, CLASS_IS_ACTIVE, isActive );
			toggleClass( edge.element, CLASS_IS_RELATED, isRelated );
			toggleClass(
				edge.element,
				CLASS_IS_OFF,
				! isActive && ! isRelated
			);
		}
	};

	/**
	 * Move the directory's selected entry onto the selected subsystem.
	 *
	 * @param {string} id Subsystem id to select.
	 * @return {void}
	 */
	RankKernelPanel.prototype.paintDirectory = function ( id ) {
		for ( let i = 0; i < this.directoryEntries.length; i++ ) {
			toggleClass(
				this.directoryEntries[ i ].element,
				CLASS_DIRECTORY_SELECTED,
				this.directoryEntries[ i ].id === id
			);
		}
	};

	/**
	 * Show or remove one directory entry.
	 *
	 * Removal rather than hiding, so a filtered out subsystem is not in the
	 * document at all. The entry is detached rather than marked hidden so that
	 * a hidden entry cannot be reached by find in page, by a screen reader's
	 * browse mode, or by the browser's own scroll anchoring.
	 *
	 * @param {{element: HTMLElement, status: string, parent: Node, next: Node}} item Entry to update.
	 * @param {boolean}                                                          show Whether the entry matches the current filter.
	 * @return {void}
	 */
	RankKernelPanel.prototype.applyToDirectoryItem = function ( item, show ) {
		if ( show ) {
			if ( ! item.element.parentNode && item.parent ) {
				item.parent.insertBefore( item.element, item.next );
			}

			return;
		}

		if ( item.element.parentNode ) {
			item.element.parentNode.removeChild( item.element );
		}
	};

	/**
	 * Put the document back and remove every listener this instance added.
	 *
	 * Safe to call more than once.
	 *
	 * @return {void}
	 */
	RankKernelPanel.prototype.destroy = function () {
		let i;

		if ( this.destroyed ) {
			return;
		}

		this.destroyed = true;

		if ( this.observer ) {
			this.observer.disconnect();
			this.observer = null;
		}

		if ( this.onClick ) {
			this.scope.removeEventListener( 'click', this.onClick );
		}

		if ( this.onKeyDown ) {
			this.scope.removeEventListener( 'keydown', this.onKeyDown );
		}

		if ( this.onPointerOver ) {
			this.scope.removeEventListener( 'pointerover', this.onPointerOver );
		}

		if ( this.onPointerOut ) {
			this.scope.removeEventListener( 'pointerout', this.onPointerOut );
		}

		if ( this.onPageHide ) {
			window.removeEventListener( 'pagehide', this.onPageHide );
		}

		for ( i = 0; i < this.tabs.length; i++ ) {
			this.tabs[ i ].hidden = false;
			this.tabs[ i ].setAttribute( 'aria-selected', 'false' );
		}

		for ( i = 0; i < this.directoryItems.length; i++ ) {
			this.applyToDirectoryItem( this.directoryItems[ i ], true );
		}

		for ( i = 0; i < this.nodes.length; i++ ) {
			this.nodes[ i ].element.setAttribute( 'opacity', FULL_OPACITY );
		}

		for ( i = 0; i < this.panels.length; i++ ) {
			this.panels[ i ].element.hidden = false;
		}

		for ( i = 0; i < this.edges.length; i++ ) {
			this.edges[ i ].element.classList.remove( CLASS_IS_ON );
		}

		for ( i = 0; i < this.coreEdges.length; i++ ) {
			this.coreEdges[ i ].element.classList.remove(
				CLASS_IS_ACTIVE,
				CLASS_IS_RELATED,
				CLASS_IS_OFF
			);
		}
	};

	/**
	 * Whether a pointer event is a move within the element it started in.
	 *
	 * Pointer events fire on every element boundary the pointer crosses, so this
	 * is what tells a genuine entry to a node from travel across its own
	 * children.
	 *
	 * @param {Event}   event Pointer event.
	 * @param {Element} node  Element the event is being tested against.
	 * @return {boolean}   Whether the pointer came from inside the element.
	 */
	function isInside( event, node ) {
		const from = event.relatedTarget;

		return !! ( from && node && node.contains( from ) );
	}

	/**
	 * Add or remove one class on an element.
	 *
	 * @param {Element} element Element to change.
	 * @param {string}  name    Class name.
	 * @param {boolean} on      Whether the class should be present.
	 * @return {void}
	 */
	function toggleClass( element, name, on ) {
		if ( on ) {
			element.classList.add( name );
		} else {
			element.classList.remove( name );
		}
	}

	/**
	 * Reduce a status string to the token the filter compares against.
	 *
	 * A directory entry shows the status as an uppercase word and the tabs carry
	 * it lowercased, so the comparison is case insensitive in the direction that
	 * makes the two meet: everything is folded to lower case. The word in the
	 * markup stays uppercase for the visitor and the token stays lowercase for
	 * the script, which is why the two are different cases on purpose.
	 *
	 * @param {string} value Raw status text.
	 * @return {string} Lowercase status with runs of whitespace collapsed.
	 */
	function normaliseStatus( value ) {
		return String( value || '' )
			.replace( /\s+/g, ' ' )
			.trim()
			.toLowerCase();
	}

	/**
	 * The status a node carries, read from its own status class.
	 *
	 * @param {Element} element Node group to read.
	 * @return {string} The status token, or an empty string when the node
	 *                   carries none of the three state classes.
	 */
	function statusFromClass( element ) {
		for ( let i = 0; i < STATUS_TOKENS.length; i++ ) {
			if (
				element.classList.contains(
					STATUS_CLASS_PREFIX + STATUS_TOKENS[ i ]
				)
			) {
				return STATUS_TOKENS[ i ];
			}
		}

		return '';
	}

	/**
	 * Count how many entries carry each status.
	 *
	 * @param {string[]} statuses Status tokens to count.
	 * @return {Object<string, number>} Counts keyed by status token. Only
	 *                                  statuses with at least one member appear,
	 *                                  so a missing key means an empty state.
	 */
	function tallyStatuses( statuses ) {
		const counts = {};

		for ( let i = 0; i < statuses.length; i++ ) {
			const status = statuses[ i ];

			if ( ! status ) {
				continue;
			}

			counts[ status ] = ( counts[ status ] || 0 ) + 1;
		}

		return counts;
	}

	/**
	 * Initialise every architecture panel on the page exactly once.
	 *
	 * The ready attribute is the guard against a second pass attaching a second
	 * set of listeners to the same panel, which is what would happen if the
	 * script were both a view script and enqueued on its own.
	 *
	 * @return {void}
	 */
	function initPanels() {
		const tablists = document.querySelectorAll( TABLIST_SELECTOR );

		for ( let i = 0; i < tablists.length; i++ ) {
			const tablist = tablists[ i ];

			if ( tablist.getAttribute( 'data-rk-filter-ready' ) === 'yes' ) {
				continue;
			}

			tablist.setAttribute( 'data-rk-filter-ready', 'yes' );

			try {
				new RankKernelPanel( tablist );
			} catch {
				// A broken panel must never break the rest of the page.
				tablist.setAttribute( 'data-rk-filter-error', 'true' );
			}
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initPanels );
	} else {
		initPanels();
	}
} )();
