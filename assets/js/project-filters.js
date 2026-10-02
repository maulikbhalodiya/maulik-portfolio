/**
 * Projects archive filter tabs view script.
 *
 * WHAT THIS IS FOR
 *
 * The filter row on the Projects archive is a tablist in the design reference,
 * at src/pages/ProjectsArchivePage.tsx lines 110 to 134. Ten tabs, a real
 * button per tab, aria-selected on each one, and a React state variable that
 * narrows the case study grid on selection. WordPress has no React, so this is
 * that state variable in plain browser JavaScript, and nothing else.
 *
 * WHY THE MARKUP IS NOT BUILT HERE
 *
 * The tablist itself is server rendered by inc/project-filters.php, which is a
 * dynamic block in the inc/hero-ecosystem.php mould: registered by hand, no
 * save() output, and the script attached through view_script_handles so it only
 * downloads where the block rendered. That was preferred over scoped scripting
 * against core block markup because a core block cannot carry an arbitrary data
 * attribute: WP_Block_Type::prepare_attributes_for_render() drops anything
 * outside the block's own schema, so role, aria-selected and the filter slug
 * would have had no legal home in a core paragraph. The Interactivity API was
 * considered and rejected because its directives live in the HTML attributes,
 * which is the same authoring problem with a heavier runtime attached, and
 * because a ten state client side visibility filter gains nothing from state
 * that has to survive a round trip to the server.
 *
 * WHAT IS FILTERED AND HOW
 *
 * The design filters a single array with .filter() and maps the result, so a
 * project that does not match is never rendered at all. This file reproduces
 * that: a card that does not match is detached from the document rather than
 * hidden with CSS, because a hidden card would leave nine articles in the
 * accessibility tree and nine links in the tab order while looking filtered.
 * Detached nodes are held in a closure array and re-inserted with appendChild,
 * which is a move rather than a clone, so a card never loses its own state and
 * the page never accumulates duplicate cards.
 *
 * THE TWO SPECIAL CASED FILTERS ARE THE DESIGN'S, NOT INVENTIONS
 *
 * `all` is not a category and constrains nothing, and `integration` matches a
 * card carrying either the api or the automation tag. Those are the two
 * branches ProjectsArchivePage.tsx states explicitly at lines 43 to 57, and they
 * are reproduced rather than flattened into the per card tag list, because
 * flattening would mean writing `integration` onto seven cards by hand and
 * keeping that copy in step with `api`.
 *
 * This file is a dependency free IIFE: no jQuery, no module loader, no build
 * step, and no global is created or read. It binds exactly two listeners per
 * tablist plus one window listener for teardown, and it removes every one of
 * them on pagehide.
 */

( function () {
	'use strict';

	const TABLIST_SELECTOR = '.pj-filters__list[role="tablist"]';
	const TAB_SELECTOR = '[role="tab"]';
	const WORK_SELECTOR = '.pj-work';
	const GRID_SELECTOR = '.pj-work__grid';
	const COUNT_SELECTOR = '.pj-work__count';
	const EMPTY_SELECTOR = '.pj-work__empty';
	const INDEPENDENT_SELECTOR = '.pj-indep';
	const SELECTED_CLASS = 'pj-tab-selected';
	const EMPTY_VISIBLE_CLASS = 'pj-work__empty--visible';
	const CARD_CLASS_PREFIX = 'pj-cat-';

	/*
	 * Mirrors the two branches of ProjectsArchivePage.tsx lines 43 to 57. Kept as
	 * named constants rather than inline literals so the special cases are
	 * greppable from here rather than hidden inside a loop.
	 */
	const FILTER_NONE = 'all';
	const FILTER_INTEGRATION = 'integration';
	const INTEGRATION_MEMBERS = [ 'api', 'automation' ];

	/*
	 * The count line is authored prose that happens to contain two numbers, and
	 * only the first of them changes. The pattern is the narrowest thing that
	 * identifies "N of M". If an author rewrites the sentence into a shape this
	 * does not match, the line is left exactly as written rather than corrupted.
	 */
	const COUNT_PATTERN = /\d+\s+of\s+\d+/;

	/**
	 * Split a show list into a lookup.
	 *
	 * @param {string} value Space separated section names.
	 * @return {Object} Map of section name to true.
	 */
	function toSet( value ) {
		const set = {};

		String( value || '' )
			.split( /\s+/ )
			.forEach( ( name ) => {
				if ( name ) {
					set[ name ] = true;
				}
			} );

		return set;
	}

	/**
	 * Decide whether a card belongs to the active filter.
	 *
	 * @param {Element} card The card element.
	 * @param {string}  slug The active filter slug.
	 * @return {boolean} True when the card should be on the page.
	 */
	function cardMatches( card, slug ) {
		if ( slug === FILTER_NONE ) {
			return true;
		}

		if ( slug === FILTER_INTEGRATION ) {
			return INTEGRATION_MEMBERS.some( ( member ) =>
				card.classList.contains( CARD_CLASS_PREFIX + member )
			);
		}

		return card.classList.contains( CARD_CLASS_PREFIX + slug );
	}

	/**
	 * One tablist and the content it narrows.
	 *
	 * @param {Element} tablist The tablist container.
	 */
	function ProjectFilters( tablist ) {
		this.tablist = tablist;
		this.tabs = Array.prototype.slice.call(
			tablist.querySelectorAll( TAB_SELECTOR )
		);

		this.work = document.querySelector( WORK_SELECTOR );
		this.grid = this.work ? this.work.querySelector( GRID_SELECTOR ) : null;
		this.count = this.work
			? this.work.querySelector( COUNT_SELECTOR )
			: null;
		this.empty = this.work
			? this.work.querySelector( EMPTY_SELECTOR )
			: null;
		this.emptyName = this.empty
			? this.empty.querySelector( '.pj-work__empty-filter' )
			: null;
		this.emptyReset = this.empty
			? this.empty.querySelector( '.pj-work__empty-reset' )
			: null;
		this.independent = document.querySelector( INDEPENDENT_SELECTOR );

		/*
		 * The authored order, captured once. Cards are restored in this order so
		 * that filtering back and forth cannot reorder the grid, which would
		 * silently renumber the "01" to "09" the cards print.
		 */
		this.cards = this.grid
			? Array.prototype.slice.call( this.grid.children )
			: [];

		this.total = this.cards.length;
		this.countTemplate = this.count ? this.count.textContent : '';
		this.destroyed = false;

		this.onClick = this.onClick.bind( this );
		this.onKeyDown = this.onKeyDown.bind( this );
		this.onResetClick = this.onResetClick.bind( this );

		this.init();
	}

	/**
	 * Bind, take the empty state out of the document, and apply the server
	 * rendered selection so the client state and the markup never disagree.
	 *
	 * @return {void}
	 */
	ProjectFilters.prototype.init = function () {
		if ( ! this.tabs.length || ! this.grid ) {
			return;
		}

		this.tablist.addEventListener( 'click', this.onClick );
		this.tablist.addEventListener( 'keydown', this.onKeyDown );

		if ( this.emptyReset ) {
			this.emptyReset.addEventListener( 'click', this.onResetClick );
		}

		/*
		 * The empty state is authored into the page so an editor can see and
		 * change it, and the stylesheet keeps it out of the layout until it is
		 * needed. Here it leaves the document entirely, so a visitor with no
		 * matching project gets an empty result rather than an empty panel.
		 */
		this.placeholder = this.detach( this.empty );

		if ( this.independent && this.independent.parentNode ) {
			this.independentPlaceholder = document.createComment( 'pj-indep' );

			this.independent.parentNode.replaceChild(
				this.independentPlaceholder,
				this.independent
			);
		}

		if ( this.count ) {
			this.count.setAttribute( 'aria-live', 'polite' );
			this.count.setAttribute( 'aria-atomic', 'true' );
		}

		this.select( 0, false );
	};

	/**
	 * Remove a node from the document, leaving a comment behind in its place.
	 *
	 * The comment is the restore anchor. Holding a reference to the parent and
	 * the next sibling instead would break the moment a sibling moved, which is
	 * exactly what filtering does to the professional section.
	 *
	 * @param {Element} node Node to detach.
	 * @param {Node}    [at] Existing anchor to reuse, when restoring.
	 * @return {Node|null} The anchor, or null when there was nothing to detach.
	 */
	ProjectFilters.prototype.detach = function ( node, at ) {
		if ( ! node || ! node.parentNode ) {
			return null;
		}

		const anchor = at || document.createComment( node.className );

		node.parentNode.replaceChild( anchor, node );

		return anchor;
	};

	/**
	 * Put a detached node back at its anchor.
	 *
	 * @param {Element} node   Node to restore.
	 * @param {Node}    anchor The comment left by detach().
	 * @return {void}
	 */
	ProjectFilters.prototype.restore = function ( node, anchor ) {
		if ( ! node || ! anchor || ! anchor.parentNode ) {
			return;
		}

		anchor.parentNode.replaceChild( node, anchor );
	};

	/**
	 * Find a tab by its filter slug.
	 *
	 * @param {string} slug Filter slug.
	 * @return {number} Index of the tab, or 0 when there is no match.
	 */
	ProjectFilters.prototype.indexOfFilter = function ( slug ) {
		for ( let i = 0; i < this.tabs.length; i++ ) {
			if ( this.tabs[ i ].getAttribute( 'data-pj-filter' ) === slug ) {
				return i;
			}
		}

		return 0;
	};

	/**
	 * Make one tab the selected tab and apply its filter.
	 *
	 * @param {number}  index     Index into this.tabs.
	 * @param {boolean} moveFocus Whether to move focus onto the tab. Arrow
	 *                            navigation passes true, a click passes false
	 *                            because the browser has already focused it.
	 * @return {void}
	 */
	ProjectFilters.prototype.select = function ( index, moveFocus ) {
		if ( this.destroyed || ! this.tabs.length ) {
			return;
		}

		const tab = this.tabs[ index ];
		const slug = tab.getAttribute( 'data-pj-filter' );
		const show = toSet( tab.getAttribute( 'data-pj-show' ) );

		this.tabs.forEach( ( candidate, i ) => {
			const isSelected = i === index;

			candidate.setAttribute(
				'aria-selected',
				isSelected ? 'true' : 'false'
			);

			/*
			 * There is no roving tabindex. Every tab is a real button, so all
			 * ten are natively focusable and all ten stay in the tab order.
			 * removeAttribute rather than simply not setting it, so a document
			 * that was served before this change heals on load instead of
			 * leaving nine tabs unreachable.
			 */
			candidate.removeAttribute( 'tabindex' );
			candidate.classList.toggle( SELECTED_CLASS, isSelected );
		} );

		if ( show.independent ) {
			this.restore( this.independent, this.independentPlaceholder );
		} else {
			this.detach( this.independent, this.independentPlaceholder );
		}

		const matching = [];

		/*
		 * ONE PASS OVER THE AUTHORED ORDER, SPLIT ACROSS TWO PARENTS.
		 *
		 * appendChild is a move, so appending the matching cards to the grid
		 * walks them into the grid in authored order and leaves nothing else
		 * behind, and appending the rest to an off document DocumentFragment
		 * takes them out of the document entirely rather than hiding them. A
		 * card that does not match therefore leaves the accessibility tree, the
		 * tab order and the grid at the same moment, which is the whole point:
		 * a card hidden with CSS would still be announced and still be
		 * focusable, and the archive would claim to be filtered while offering
		 * nine case studies.
		 *
		 * The fragment is a plain container, not a cache of the filtered set,
		 * so it holds the cards in the same authored order and a later
		 * appendChild walks them back out unchanged. No card is ever cloned,
		 * so nothing inside one can lose its own state and the grid can never
		 * accumulate duplicates.
		 */
		const stash = document.createDocumentFragment();

		this.cards.forEach( ( card ) => {
			if ( cardMatches( card, slug ) ) {
				matching.push( card );
				this.grid.appendChild( card );
			} else {
				stash.appendChild( card );
			}
		} );

		if ( this.count && COUNT_PATTERN.test( this.countTemplate ) ) {
			this.count.textContent = this.countTemplate.replace(
				COUNT_PATTERN,
				matching.length + ' of ' + this.total
			);
		}

		if ( matching.length ) {
			if ( this.empty ) {
				this.empty.classList.remove( EMPTY_VISIBLE_CLASS );
			}

			this.detach( this.empty, this.placeholder );
		} else {
			this.restore( this.empty, this.placeholder );
			this.empty.classList.add( EMPTY_VISIBLE_CLASS );

			if ( this.emptyName ) {
				this.emptyName.textContent = tab.textContent.trim();
			}
		}

		if ( moveFocus ) {
			tab.focus();
		}
	};

	/**
	 * Resolve the tab an event landed on.
	 *
	 * @param {Event} event The event.
	 * @return {Element|null} The tab inside this tablist, or null.
	 */
	ProjectFilters.prototype.tabFromEvent = function ( event ) {
		const target = event.target;

		if ( ! target || typeof target.closest !== 'function' ) {
			return null;
		}

		const tab = target.closest( TAB_SELECTOR );

		return tab && this.tablist.contains( tab ) ? tab : null;
	};

	/**
	 * Mouse and touch selection, delegated from the tablist.
	 *
	 * Delegated rather than one listener per tab so the count of listeners does
	 * not grow with the number of tabs and teardown has exactly two removals.
	 *
	 * @param {Event} event The click event.
	 * @return {void}
	 */
	ProjectFilters.prototype.onClick = function ( event ) {
		const tab = this.tabFromEvent( event );

		if ( ! tab ) {
			return;
		}

		this.select( this.tabs.indexOf( tab ), false );
	};

	/**
	 * The empty state reset control.
	 *
	 * @return {void}
	 */
	ProjectFilters.prototype.onResetClick = function () {
		this.select( this.indexOfFilter( FILTER_NONE ), false );
	};

	/**
	 * Keyboard support.
	 *
	 * Arrow keys move and select together, which is the tabs pattern, so the
	 * tab that has focus is always the tab that is applied. Home and End jump to
	 * the ends. Enter and Space are handled here rather than left to the native
	 * button activation, so that a keyboard activation arrives at the same code
	 * path as a click and is applied exactly once.
	 *
	 * @param {Event} event The keydown event.
	 * @return {void}
	 */
	ProjectFilters.prototype.onKeyDown = function ( event ) {
		const tab = this.tabFromEvent( event );

		if ( ! tab ) {
			return;
		}

		const index = this.tabs.indexOf( tab );
		let next = null;

		switch ( event.key ) {
			case 'ArrowRight':
			case 'ArrowDown':
				next = ( index + 1 ) % this.tabs.length;
				break;
			case 'ArrowLeft':
			case 'ArrowUp':
				next = ( index - 1 + this.tabs.length ) % this.tabs.length;
				break;
			case 'Home':
				next = 0;
				break;
			case 'End':
				next = this.tabs.length - 1;
				break;
			case 'Enter':
			case ' ':
			case 'Spacebar':
				next = index;
				break;
			default:
				return;
		}

		event.preventDefault();

		this.select( next, true );
	};

	/**
	 * Remove every listener and put the authored markup back exactly as the
	 * server rendered it.
	 *
	 * Called on pagehide so nothing keeps running once the document is being
	 * discarded, and so a page restored from the back forward cache comes back
	 * with its original content rather than a half filtered grid and dead
	 * controls.
	 *
	 * @return {void}
	 */
	ProjectFilters.prototype.destroy = function () {
		if ( this.destroyed ) {
			return;
		}

		this.destroyed = true;

		this.tablist.removeEventListener( 'click', this.onClick );
		this.tablist.removeEventListener( 'keydown', this.onKeyDown );
		this.tablist.removeAttribute( BOUND_ATTRIBUTE );

		if ( this.emptyReset ) {
			this.emptyReset.removeEventListener( 'click', this.onResetClick );
		}

		/*
		 * Every authored node goes back where the server put it. restore()
		 * replaces the comment anchor with the node, so the anchor leaves with
		 * it and there is nothing to clean up afterwards.
		 */
		if ( this.grid ) {
			this.cards.forEach( ( card ) => this.grid.appendChild( card ) );
		}

		/*
		 * The tabs go back to the state the server rendered too, so a restored
		 * document cannot pair the authored content with a stale
		 * aria-selected="true" on a filter that is no longer applied.
		 */
		this.tabs.forEach( ( tab, i ) => {
			tab.setAttribute( 'aria-selected', 0 === i ? 'true' : 'false' );
			tab.removeAttribute( 'tabindex' );
			tab.classList.toggle( SELECTED_CLASS, 0 === i );
		} );

		this.restore( this.independent, this.independentPlaceholder );
		this.restore( this.empty, this.placeholder );

		if ( this.empty ) {
			this.empty.classList.remove( EMPTY_VISIBLE_CLASS );
		}

		if ( this.count ) {
			this.count.removeAttribute( 'aria-live' );
			this.count.removeAttribute( 'aria-atomic' );
			this.count.textContent = this.countTemplate;
		}
	};

	const BOUND_ATTRIBUTE = 'data-pj-bound';

	/*
	 * WordPress enqueues a handle once, so in production this runs once. The
	 * guard is here because a second copy of the file must never bind a second
	 * set of listeners to the same tablist: the behaviour would still look
	 * right, because selecting a tab twice is idempotent, and the duplication
	 * would only show up as a listener count nobody is checking. Marking the
	 * tablist makes the second pass a no-op that can be measured.
	 */
	const instances = Array.prototype.map
		.call( document.querySelectorAll( TABLIST_SELECTOR ), ( tablist ) => {
			if ( 'true' === tablist.getAttribute( BOUND_ATTRIBUTE ) ) {
				return null;
			}

			tablist.setAttribute( BOUND_ATTRIBUTE, 'true' );

			return new ProjectFilters( tablist );
		} )
		.filter( Boolean );

	/**
	 * Tear every instance down once the document is going away.
	 *
	 * @return {void}
	 */
	function onPageHide() {
		instances.forEach( ( instance ) => instance.destroy() );
	}

	window.addEventListener( 'pagehide', onPageHide );
} )();
