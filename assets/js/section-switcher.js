/**
 * Front end interaction layer for homepage sections 02, 03 and 06.
 *
 * WHAT THIS IS, AND WHY IT IS NOT A BLOCK
 *
 * The obvious precedent in this theme is inc/hero-ecosystem.php, a dynamic
 * presentational block with a view script. That block is the right precedent
 * there and the wrong one here, and the difference is what the content is.
 *
 * The hero canvas is a drawing. It has no prose, so there is nothing for an
 * editor to edit and nothing for post_content to carry. Sections 02, 03 and 06
 * are the opposite: a capability specification, forty one skill entries and a
 * five stage methodology are editorial content, and this theme is editor first,
 * so that content lives in post_content as core blocks where Pages, then Edit
 * shows it. Turning those three sections into dynamic blocks would move every
 * heading, paragraph and list item out of the editor and into block
 * attributes, which is the regression docs/RESEARCH-02-BLOCK-THEME.md and
 * tools/migrate-content.php exist to prevent.
 *
 * So the markup stays as core blocks and this file drives it. The contract is
 * deliberately narrow and entirely attribute based:
 *
 *   [data-hz-switcher]      the control strip, inside a section
 *   data-hz-tab             the class the tab buttons carry
 *   data-hz-target          on each button, the id of the panel it selects
 *   data-hz-rotates         present and "true" only where auto rotation is wanted
 *   .hz-switch-panels       the element that holds the panels
 *   .hz-switch-panel        a panel
 *   .hz-switch-panel--idle  a panel that is not the selected one
 *
 * and, for the node map in section 03 only:
 *
 *   svg                      the node map, read for g.hz-svg-label elements
 *   .hz-svg-label            the node's own text, matched to the tab text
 *
 * and, for the RankKernel architecture figure in section 03:
 *
 *   [data-hz-arch-status]    the figure root, carrying the applied filter
 *   data-hz-arch-filter      on each of the four filter buttons, the status it
 *                            selects: all, completed, running or planned
 *   .hz-work__arch-svg-node  a subsystem node, read for its status class
 *   .hz-work__arch-edge      a connection line inside that figure's svg
 *
 * and the two state classes this script toggles, which the stylesheet owns:
 *
 *   .is-arch-hidden          on a node the active filter excludes
 *   .is-arch-edge-off        on an edge with a filtered out endpoint
 *
 * Every one of those except the button attributes lives on a core block as an
 * ordinary className or a support attribute WordPress already emits, so the
 * markup round trips through the editor and block validation without complaint.
 * The one thing that cannot be expressed that way is the panel visibility
 * state, so it is a class the script adds and removes rather than a hidden
 * attribute written into the saved markup.
 *
 * WHY THE PANELS STAY IN THE DOM WITH THE OTHERS COLLAPSED
 *
 * Rendering all the panels and collapsing the unselected ones with display none
 * keeps the section height identical to rendering only the selected panel,
 * because a display none box contributes no height at all. That is what keeps
 * sections 03 and 06 at the height they already measure, which the page height
 * budget depends on. The alternative, swapping text out of data attributes,
 * would put the content in the markup twice and make it uneditable.
 *
 * WITH JAVASCRIPT OFF
 *
 * The saved markup carries the idle class on every unselected panel, so a page
 * with no JavaScript shows the same single selected panel it shows today, at
 * the same height. Nothing is hidden that the script then has to reveal.
 *
 * Enqueued from functions.php on the front end only, and only on a singular
 * view whose content actually carries one of these control strips.
 */

( function () {
	'use strict';

	const STRIP_ATTRIBUTE = 'data-hz-switcher';
	const TARGET_ATTRIBUTE = 'data-hz-target';
	const STATE_ATTRIBUTE = 'data-hz-state';
	const PANELS_CLASS = 'hz-switch-panels';
	const IDLE_CLASS = 'hz-switch-panel--idle';

	/*
	 * The graph in section 03, and the two-way link between a node and a tab.
	 *
	 * WHAT IDENTIFIES A NODE. This is the one place the contract above does not
	 * reach, so it is worth stating exactly. Measured on the served markup, the
	 * node map is an svg.hz-stack__svg whose children are an ellipse, two
	 * circles, seven lines and eight g elements. None of the g elements carries a
	 * class, an id, a data attribute or a role. Each one carries a transform and
	 * two text children, and the first text child of a domain node carries the
	 * class hz-svg-label and reads the domain name: WordPress, PHP, APIs,
	 * Databases, Security, Integrations, Tools. Those seven strings are the same
	 * seven strings the seven tabs carry as their own text, in the same order.
	 *
	 * So the assumed identifier is the text of .hz-svg-label, matched against the
	 * text of the tab. It is a content join rather than a structural one, which
	 * is the only join this markup supports, and it degrades safely: a node
	 * whose label matches no tab is left alone rather than guessed at, and a
	 * duplicate label would leave the first match in charge. The centre node is
	 * excluded for free, because its only text child carries hz-svg-name and
	 * hz-svg-count and no hz-svg-label.
	 *
	 * The design's node is role button with tabIndex 0 and an aria-label of
	 * "Select WordPress technical domain", and it selects on click and on Enter
	 * or Space. None of that is in the markup, so all of it is added here, which
	 * is the same trade every other piece of this file makes: the markup stays
	 * core blocks and the script drives them.
	 */
	const GRAPH_SVG_SELECTOR = 'svg';
	const GRAPH_NODE_LABEL_CLASS = 'hz-svg-label';
	const GRAPH_NODE_CLASS = 'hz-svg-node';
	const GRAPH_NODE_SELECTED_CLASS = 'hz-svg-node--selected';

	/*
	 * The edge from the centre to a node, and the dot on its midpoint.
	 *
	 * The design draws these per selected node, so leaving the markup's single
	 * hardcoded edge on the first node would show an amber line to a node that
	 * is no longer selected, which is a visible lie the moment a node is
	 * clickable. The lines are the svg's own line children and they run in the
	 * same order as the domain nodes, so pairing is by position and is guarded:
	 * if the counts do not agree, no line is touched at all and the node state
	 * still works.
	 */
	const GRAPH_EDGE_SELECTOR = 'line';
	const GRAPH_EDGE_SELECTED_CLASS = 'hz-svg-edge--selected';
	const GRAPH_EDGE_IDLE_STROKE = '#343434';
	const GRAPH_EDGE_SELECTED_STROKE = '#FACC15';

	/*
	 * The RankKernel architecture figure in section 03, and its status filter.
	 *
	 * WHY THIS IS NOT PART OF THE STRIP ABOVE. A strip drives panels by id and
	 * one selected tab; this figure drives nine nodes inside one svg by status
	 * and has no panels at all. It also carries role group rather than
	 * role tablist, because there are no tabpanels for a tablist to own, so
	 * folding it into the strip contract would have meant faking the panel
	 * relationship. It is bound from the same init and destroy paths instead.
	 *
	 * WHAT THE DESIGN DOES WITH AN EDGE. Read from
	 * RankKernelArchitecture.tsx: the primary core-to-subsystem edges are dimmed
	 * on the same condition as their own subsystem, so an edge follows its
	 * subsystem and nothing else. The secondary subsystem-to-subsystem lines
	 * carry no filter term at all; the design varies them only by whether an
	 * endpoint is the active node. So a filter in the design hides no line
	 * outright, it dims the nine primary edges, and it leaves the secondary
	 * lines alone.
	 *
	 * The owner asked for the lines to actually change, so the treatment here is
	 * the design's dimmed read taken one step to its conclusion: an edge whose
	 * endpoint is filtered out is switched off. An edge is switched off when
	 * EITHER endpoint is filtered out, never only when both are, because a line
	 * running to a node that is no longer drawn is a line pointing at nothing.
	 * The core node is never filtered, so the nine primary edges follow their
	 * subsystem, which is the design's rule exactly. The secondary lines follow
	 * both of their endpoints, which is the design's rule extended to the edges
	 * the design left unresponsive.
	 *
	 * THE EDGE SET IS DERIVED, NOT COUNTED. Every line inside this figure's own
	 * svg is read, which is what scopes the work to this figure: the page carries
	 * other line elements, including the seven in section 03's node map, and those
	 * are none of this figure's business. The stylesheet marks the same lines
	 * with hz-work__arch-edge, but pairing is by coordinate: every subsystem
	 * node's centre is readable from its translate transform, and every line's
	 * two endpoints are readable
	 * from x1/y1 and x2/y2. An endpoint therefore names the node it belongs to,
	 * and a line whose endpoints cannot both be resolved is left alone rather
	 * than guessed at. Nothing here depends on how many lines the markup
	 * happens to carry.
	 *
	 * THE COUNT IS NOT TOUCHED. The design reads "All States (9)" and carries
	 * no count on the other three labels, and it does not recount as the filter
	 * changes. The markup already says 9, which is the true total, so the count
	 * is left exactly as the markup has it rather than a count format being
	 * invented for the other three.
	 *
	 * STATE IS CLASS AND ATTRIBUTE ONLY. is-arch-hidden and is-arch-edge-off are
	 * the stylesheet's to style, which is what keeps this out of the inline
	 * style trap that three RankKernel filter tabs once fell into.
	 */
	const ARCH_STATUS_ATTRIBUTE = 'data-hz-arch-status';
	const ARCH_FILTER_ATTRIBUTE = 'data-hz-arch-filter';
	const ARCH_ROOT_SELECTOR = `[${ ARCH_STATUS_ATTRIBUTE }]`;
	const ARCH_FILTER_CLASS = 'hz-work__arch-filter';
	const ARCH_FILTER_ACTIVE_CLASS = 'is-on';
	const ARCH_SVG_SELECTOR = '.hz-work__arch-svg';
	const ARCH_NODE_SELECTOR = '.hz-work__arch-svg-node';
	const ARCH_EDGE_CLASS = 'hz-work__arch-edge';
	const ARCH_NODE_HIDDEN_CLASS = 'is-arch-hidden';
	const ARCH_EDGE_OFF_CLASS = 'is-arch-edge-off';
	const ARCH_ALL = 'all';

	/*
	 * THE STATUS VOCABULARY, and the only place it is written down in JavaScript.
	 *
	 * Three class names, and they are the same three the RankKernel page uses.
	 * The block that defines them for CSS is in _page-rankkernel.scss.
	 *
	 *   is-completed   shipped and enabled by default
	 *   is-running     shipped but default off, or partially delivered
	 *   is-planned     registered in the module registry, nothing on disk
	 *
	 * The filter button carries a slug of its own, data-hz-arch-filter, because a
	 * class name cannot be an attribute value containing a hyphenated sentence.
	 * The join between the two vocabularies therefore lives here and nowhere
	 * else, which is what makes a status change a one line edit: change the
	 * class on the node, and if the button set changes, change this table.
	 *
	 * The visible word next to each node is authored in the markup, not
	 * generated here. The class and the word must change together.
	 */
	const ARCH_STATUS_BY_NODE_CLASS = {
		'is-completed': 'completed',
		'is-running': 'running',
		'is-planned': 'planned',
	};

	/**
	 * The RankKernel architecture status filter, one instance per figure.
	 *
	 * @param {HTMLElement} root The figure root carrying data-hz-arch-status.
	 */
	function ArchStatusFilter( root ) {
		this.root = root;

		/*
		 * The control row sits in the figure's head and the drawing sits in its
		 * figure column, so the element carrying the status attribute is not
		 * necessarily the element holding both. The .hz-work__arch wrapper is,
		 * and when the attribute has been placed on the wrapper already then
		 * closest finds that same element, so one expression covers both.
		 */
		this.host =
			root.closest( '.hz-work__arch' ) ||
			( root.classList.contains( 'hz-work__arch' ) ? root : null ) ||
			root;

		this.svg = this.host.querySelector( ARCH_SVG_SELECTOR );
		this.filterHost = this.host.querySelector( '.hz-work__arch-filters' );

		this.buttons = [];
		this.nodes = [];
		this.edges = [];
		this.status = ARCH_ALL;

		this.onRootClick = null;
		this.onFilterKeyDown = null;
		this.onPageHide = null;
		this.destroyed = false;

		this.init();
	}

	/**
	 * Read the figure, bind one click listener and one key listener, then apply
	 * whatever filter the markup already states.
	 *
	 * @return {void}
	 */
	ArchStatusFilter.prototype.init = function () {
		if ( ! this.svg || ! this.filterHost ) {
			return;
		}

		this.readButtons();
		this.readNodes();
		this.readEdges();

		if ( ! this.buttons.length ) {
			return;
		}

		this.onRootClick = ( event ) => {
			const button =
				event.target && event.target.closest
					? event.target.closest(
							`button[${ ARCH_FILTER_ATTRIBUTE }]`
						)
					: null;

			if ( ! button || ! this.host.contains( button ) ) {
				return;
			}

			const status = button.getAttribute( ARCH_FILTER_ATTRIBUTE );

			if ( ! status ) {
				return;
			}

			this.applyStatus( status );
		};

		/*
		 * Arrow, Home and End move between the four buttons. The design has no
		 * key handler on these at all, so this is an addition rather than parity,
		 * and it is a shortcut rather than the only route: every button is a
		 * real button, so Tab alone still reaches all four and Enter or Space
		 * still activate whichever one has focus.
		 *
		 * The buttons are a role group, not a tablist, so focus moves without the
		 * filter changing. Applying the filter from a keypress as well would make
		 * arrowing across the row filter as it went, which is a tablist
		 * behaviour and not a toggle group's.
		 */
		this.onFilterKeyDown = ( event ) => {
			const current = this.buttons.indexOf(
				this.filterHost.ownerDocument.activeElement
			);

			if ( current < 0 ) {
				return;
			}

			let next = -1;

			switch ( event.key ) {
				case 'ArrowRight':
				case 'ArrowDown':
					next = ( current + 1 ) % this.buttons.length;
					break;
				case 'ArrowLeft':
				case 'ArrowUp':
					next =
						( current - 1 + this.buttons.length ) %
						this.buttons.length;
					break;
				case 'Home':
					next = 0;
					break;
				case 'End':
					next = this.buttons.length - 1;
					break;
				default:
					return;
			}

			event.preventDefault();

			this.buttons[ next ].focus();
		};

		this.host.addEventListener( 'click', this.onRootClick );
		this.filterHost.addEventListener( 'keydown', this.onFilterKeyDown );

		this.onPageHide = () => {
			this.destroy();
		};

		window.addEventListener( 'pagehide', this.onPageHide );

		this.applyStatus( this.initialStatus() );
	};

	/**
	 * The four filter buttons, read fresh from the markup.
	 *
	 * @return {void}
	 */
	ArchStatusFilter.prototype.readButtons = function () {
		this.buttons = Array.prototype.slice.call(
			this.filterHost.querySelectorAll(
				`button.${ ARCH_FILTER_CLASS }[${ ARCH_FILTER_ATTRIBUTE }]`
			)
		);
	};

	/**
	 * The subsystem nodes, each paired with the status its class states.
	 *
	 * @return {void}
	 */
	ArchStatusFilter.prototype.readNodes = function () {
		this.nodes = Array.prototype.slice
			.call( this.svg.querySelectorAll( ARCH_NODE_SELECTOR ) )
			.map( ( group ) => ( {
				group,
				status: this.statusOfNode( group ),
				point: this.nodePoint( group ),
			} ) )
			.filter( ( node ) => !! node.status );
	};

	/**
	 * The lines inside this figure, each paired with the nodes its two
	 * endpoints land on.
	 *
	 * Scoped to this svg rather than queried from the document, because the page
	 * carries other line elements, including the seven in section 03's node map,
	 * and those are none of this figure's business.
	 *
	 * @return {void}
	 */
	ArchStatusFilter.prototype.readEdges = function () {
		const lines = Array.prototype.slice.call(
			this.svg.querySelectorAll( 'line' )
		);

		/*
		 * The stylesheet marks these lines with hz-work__arch-edge, so where that
		 * class is present it names the edge set exactly and is preferred. Where
		 * it is absent, every line inside this figure's own svg is read instead,
		 * which is the same set: this svg contains two background circles and
		 * nothing but edges and nodes.
		 */
		const marked = lines.filter( ( line ) =>
			line.classList.contains( ARCH_EDGE_CLASS )
		);

		( marked.length ? marked : lines )
			.map( ( line ) => ( {
				line,
				ends: this.edgeEndpoints( line ),
			} ) )
			.filter( ( edge ) => null !== edge.ends )
			.forEach( ( edge ) => {
				this.edges.push( edge );
			} );
	};

	/**
	 * The status a node's own class states, or an empty string when it states
	 * none, which leaves the node out of the filter entirely rather than
	 * guessing at it.
	 *
	 * @param {Element} group The node's g element.
	 * @return {string} The filter status, or an empty string.
	 */
	ArchStatusFilter.prototype.statusOfNode = function ( group ) {
		const names = Object.keys( ARCH_STATUS_BY_NODE_CLASS );

		for ( let i = 0; i < names.length; i++ ) {
			if ( group.classList.contains( names[ i ] ) ) {
				return ARCH_STATUS_BY_NODE_CLASS[ names[ i ] ];
			}
		}

		return '';
	};

	/**
	 * The centre of a node, read from its own translate transform.
	 *
	 * @param {Element} group The node's g element.
	 * @return {Object|null} { x, y }, or null when it carries no transform.
	 */
	ArchStatusFilter.prototype.nodePoint = function ( group ) {
		const transform = group.getAttribute( 'transform' ) || '';
		const match = transform.match(
			/translate\(\s*(-?[\d.]+)[ ,]+(-?[\d.]+)\s*\)/
		);

		if ( ! match ) {
			return null;
		}

		const x = parseFloat( match[ 1 ] );
		const y = parseFloat( match[ 2 ] );

		if ( isNaN( x ) || isNaN( y ) ) {
			return null;
		}

		return { x, y };
	};

	/**
	 * The nodes a line's two endpoints land on.
	 *
	 * A line is only usable as a filter edge when both of its endpoints resolve
	 * to a node. The primary edges run from the central core, which carries no
	 * status class and is never filtered, so one endpoint resolves to no node
	 * and the pair is kept as a null, meaning that edge follows its single
	 * subsystem rather than being skipped. Both endpoints unresolved means the
	 * line is not part of this figure's node graph at all, and the whole edge is
	 * dropped rather than switched off on a guess.
	 *
	 * @param {Element} line The line element.
	 * @return {Object|null} { a, b }, or null when the line is not usable.
	 */
	ArchStatusFilter.prototype.edgeEndpoints = function ( line ) {
		const a = this.nodeAtPoint( line, 'x1', 'y1' );
		const b = this.nodeAtPoint( line, 'x2', 'y2' );

		if ( ! a && ! b ) {
			return null;
		}

		return { a, b };
	};

	/**
	 * The node a line endpoint sits on, or null.
	 *
	 * @param {Element} line  The line element.
	 * @param {string}  xAttr The x coordinate attribute.
	 * @param {string}  yAttr The y coordinate attribute.
	 * @return {Object|null} The node, or null.
	 */
	ArchStatusFilter.prototype.nodeAtPoint = function ( line, xAttr, yAttr ) {
		const x = parseFloat( line.getAttribute( xAttr ) );
		const y = parseFloat( line.getAttribute( yAttr ) );

		if ( isNaN( x ) || isNaN( y ) ) {
			return null;
		}

		return (
			this.nodes.find(
				( node ) =>
					node.point && node.point.x === x && node.point.y === y
			) || null
		);
	};

	/**
	 * The filter the markup already states, which is aria-pressed on a button
	 * when there is one and the root's own attribute otherwise.
	 *
	 * @return {string} The status to start on, defaulting to all.
	 */
	ArchStatusFilter.prototype.initialStatus = function () {
		const pressed = this.buttons.find(
			( button ) => 'true' === button.getAttribute( 'aria-pressed' )
		);

		if ( pressed ) {
			return pressed.getAttribute( ARCH_FILTER_ATTRIBUTE ) || ARCH_ALL;
		}

		return this.root.getAttribute( ARCH_STATUS_ATTRIBUTE ) || ARCH_ALL;
	};

	/**
	 * Apply a status to the figure: the nodes, the lines and the four buttons.
	 *
	 * @param {string} status all, completed, running or planned.
	 * @return {void}
	 */
	ArchStatusFilter.prototype.applyStatus = function ( status ) {
		if ( ARCH_ALL !== status ) {
			const known = Object.keys( ARCH_STATUS_BY_NODE_CLASS ).some(
				( name ) => ARCH_STATUS_BY_NODE_CLASS[ name ] === status
			);

			if ( ! known ) {
				return;
			}
		}

		const filtered = ( value ) => ARCH_ALL !== status && value !== status;

		this.nodes.forEach( ( node ) => {
			node.group.classList.toggle(
				ARCH_NODE_HIDDEN_CLASS,
				filtered( node.status )
			);
		} );

		/*
		 * Either endpoint being filtered out switches the line off, and the core
		 * is never filtered so a primary edge follows its subsystem alone. That
		 * is the design's own rule for the nine primary edges, extended to the
		 * secondary lines the design left unresponsive.
		 */
		this.edges.forEach( ( edge ) => {
			const off = !! (
				( edge.ends.a && filtered( edge.ends.a.status ) ) ||
				( edge.ends.b && filtered( edge.ends.b.status ) )
			);

			edge.line.classList.toggle( ARCH_EDGE_OFF_CLASS, off );
		} );

		this.buttons.forEach( ( button ) => {
			const isOn =
				button.getAttribute( ARCH_FILTER_ATTRIBUTE ) === status;

			button.classList.toggle( ARCH_FILTER_ACTIVE_CLASS, isOn );
			button.setAttribute( 'aria-pressed', isOn ? 'true' : 'false' );
		} );

		this.root.setAttribute( ARCH_STATUS_ATTRIBUTE, status );

		this.status = status;
	};

	/**
	 * Tear the instance down: every listener it added, and nothing else. The
	 * class and attribute state is left as it stands, because a page going away
	 * has no further state to get right.
	 *
	 * Safe to call twice, which matters because pagehide can be followed by
	 * unload and both reach this.
	 *
	 * @return {void}
	 */
	ArchStatusFilter.prototype.destroy = function () {
		if ( this.destroyed ) {
			return;
		}

		this.destroyed = true;

		if ( this.onRootClick ) {
			this.host.removeEventListener( 'click', this.onRootClick );
		}

		if ( this.onFilterKeyDown && this.filterHost ) {
			this.filterHost.removeEventListener(
				'keydown',
				this.onFilterKeyDown
			);
		}

		if ( this.onPageHide ) {
			window.removeEventListener( 'pagehide', this.onPageHide );
		}

		this.onRootClick = null;
		this.onFilterKeyDown = null;
		this.onPageHide = null;

		this.buttons = [];
		this.nodes = [];
		this.edges = [];
	};

	/**
	 * Automatic advance interval in milliseconds.
	 *
	 * The design reference has no auto rotation anywhere in section 03, which
	 * was verified three ways before this was built: sampling the selected tab
	 * at 0, 2, 4, 6 and 8 seconds never changed it, a click held for eight
	 * seconds did not change it, and synthesised pointer events changed
	 * nothing. The site owner has decided against that reference behaviour and
	 * asked for rotation here, knowingly. Everything else in this file follows
	 * the reference exactly. The interval is the one number the owner fixed.
	 */
	const ROTATE_INTERVAL = 3000;

	/**
	 * The share of a detail panel the viewport must already be showing before a
	 * control click is allowed to move the page.
	 *
	 * The previous guard asked only whether any part of the panel was below the
	 * fold, which a panel whose top edge sat 700px down a phone viewport answered
	 * no to while showing 16% of its detail. A share is the question a visitor is
	 * actually asking: did the thing I tapped actually come into view. 0.6 sits
	 * below the 82.7% to 88.7% the stacked sections already reached on their own,
	 * so a tap that already worked still does nothing at all.
	 */
	const MIN_PANEL_VISIBLE = 0.6;

	/**
	 * One initialised section.
	 *
	 * @param {HTMLElement} strip The control strip element.
	 * @param {HTMLElement} root  The section holding the strip and the panels.
	 */
	function SectionSwitcher( strip, root ) {
		this.strip = strip;
		this.root = root;
		this.panelsHost = root.querySelector( `.${ PANELS_CLASS }` );
		this.tabClass = ( strip.getAttribute( 'data-hz-tab' ) || '' ).replace(
			/^\./,
			''
		);
		/*
		 * The strip states the two words the selected and unselected state
		 * labels use, so the visible text is swapped along with the attribute.
		 * Without this the label would keep saying ACTIVE or INSPECT on a card
		 * that is no longer selected, which is worse than no label at all: it
		 * states the opposite of the truth. Absent, no labels are touched.
		 */
		this.selectedState =
			strip.getAttribute( STATE_ATTRIBUTE + '-selected' ) || '';
		this.idleState = strip.getAttribute( STATE_ATTRIBUTE + '-idle' ) || '';
		this.isTablist = 'tablist' === strip.getAttribute( 'role' );
		this.rotates = 'true' === strip.getAttribute( 'data-hz-rotates' );

		this.selectedIndex = -1;
		this.knownCount = 0;

		this.intervalId = 0;
		this.intersectionObserver = null;
		this.inView = false;
		this.pointerInside = false;
		this.focusInside = false;
		this.destroyed = false;

		this.mediaQuery = null;
		this.prefersReducedMotion = false;

		this.nodes = [];
		this.edges = [];
		this.edgeDot = null;
		this.graphSvg = null;

		this.onStripClick = null;
		this.onStripKeyDown = null;
		this.onGraphClick = null;
		this.onGraphKeyDown = null;
		this.onPanelMouseEnter = null;
		this.onPanelMouseLeave = null;
		this.onPanelFocusIn = null;
		this.onPanelFocusOut = null;
		this.onVisibilityChange = null;
		this.onPageHide = null;
		this.onMediaQueryChange = null;

		this.init();
	}

	/**
	 * Read the initial state, bind every listener, then start or refuse to
	 * rotate.
	 *
	 * @return {void}
	 */
	SectionSwitcher.prototype.init = function () {
		if ( ! this.panelsHost || ! this.tabClass ) {
			return;
		}

		this.readReducedMotion();

		const tabs = this.getTabs();

		if ( tabs.length < 1 ) {
			return;
		}

		this.bindStrip();
		this.bindPanel();
		this.bindGraph();
		this.bindLifecycle();

		this.selectedIndex = this.findSelectedIndex( tabs );
		this.knownCount = tabs.length;

		if ( this.selectedIndex < 0 ) {
			this.applySelection( 0, false );
		} else {
			this.applySelection( this.selectedIndex, false );
		}

		if ( this.rotates ) {
			this.observe();
		}
	};

	/**
	 * Detect the reduced motion preference.
	 *
	 * Rotation never starts under prefers-reduced-motion. The tabs still work
	 * by click and by keyboard, because nothing here is conditional on motion
	 * other than the timer itself.
	 *
	 * @return {void}
	 */
	SectionSwitcher.prototype.readReducedMotion = function () {
		if ( typeof window.matchMedia !== 'function' ) {
			return;
		}

		this.mediaQuery = window.matchMedia(
			'(prefers-reduced-motion: reduce)'
		);
		this.prefersReducedMotion = this.mediaQuery.matches;

		this.onMediaQueryChange = ( event ) => {
			this.prefersReducedMotion = event.matches;

			if ( event.matches ) {
				this.stopTimer();
			} else {
				this.startTimer();
			}
		};

		if ( typeof this.mediaQuery.addEventListener === 'function' ) {
			this.mediaQuery.addEventListener(
				'change',
				this.onMediaQueryChange
			);
		}
	};

	/**
	 * Bind the click and key listeners this section needs.
	 *
	 * The click listener is on the section root rather than on the strip, so a
	 * control that names its destination with data-hz-target can live anywhere
	 * in the section, not only in the row strip. The key listener stays on the
	 * strip, because arrow key navigation is a tablist behaviour and belongs to
	 * the tablist. Neither listener count grows with the number of tabs.
	 *
	 * @return {void}
	 */
	SectionSwitcher.prototype.bindStrip = function () {
		/*
		 * The listener is on the section root rather than on the strip, because
		 * a control that names its destination with data-hz-target can live
		 * anywhere in the section, not only in the row strip. Section 03 has no
		 * such control; section 06 does, because the design puts a Next Stage
		 * button in the panel foot.
		 *
		 * One listener on the root rather than one per button, so the listener
		 * count does not grow with the number of tabs.
		 */
		this.onStripClick = ( event ) => {
			const control = event.target.closest( `[${ TARGET_ATTRIBUTE }]` );

			if ( ! control || ! this.root.contains( control ) ) {
				return;
			}

			const index = this.indexOfControl( control );

			if ( index < 0 ) {
				return;
			}

			this.select( index, true );
		};

		this.onStripKeyDown = ( event ) => {
			if ( ! this.isTablist ) {
				return;
			}

			if ( this.handleTablistKey( event ) ) {
				event.preventDefault();
			}
		};

		this.root.addEventListener( 'click', this.onStripClick );
		this.strip.addEventListener( 'keydown', this.onStripKeyDown );
	};

	/**
	 * Link the node map in section 03 to this strip, in both directions.
	 *
	 * The nodes are read once here, matched to tabs by the label text described
	 * at the top of this file, and given the design's button semantics: role,
	 * tabIndex 0 and an aria-label naming the domain it selects. A node is then
	 * a control exactly like a tab, so it gets its own click and key handling
	 * rather than being folded into the strip listeners, which only see the
	 * strip and the section 06 control.
	 *
	 * Selecting still happens through select(), so a node click resets the
	 * rotation countdown exactly as a tab click does.
	 *
	 * @return {void}
	 */
	SectionSwitcher.prototype.bindGraph = function () {
		const svg = this.root.querySelector( GRAPH_SVG_SELECTOR );

		if ( ! svg ) {
			return;
		}

		/*
		 * Held rather than looked up again in destroy(). A second querySelector
		 * returns whatever the first one found only for as long as the DOM is
		 * unchanged, and the listeners were attached to this element, so this is
		 * the only reference guaranteed to name them.
		 */
		this.graphSvg = svg;

		const tabs = this.getTabs();

		if ( tabs.length < 1 ) {
			return;
		}

		Array.prototype.forEach.call(
			svg.querySelectorAll( 'g' ),
			( group ) => {
				const label = group.querySelector(
					`.${ GRAPH_NODE_LABEL_CLASS }`
				);

				if ( ! label ) {
					return;
				}

				const index = this.indexOfTabByText( label.textContent );

				if ( index < 0 ) {
					return;
				}

				group.classList.add( GRAPH_NODE_CLASS );
				group.setAttribute( 'role', 'button' );
				group.setAttribute( 'tabindex', '0' );
				group.setAttribute(
					'aria-label',
					`Select ${ this.tabText( index ) } technical domain`
				);

				this.nodes.push( { group, index } );
			}
		);

		if ( ! this.nodes.length ) {
			return;
		}

		/*
		 * The edges, paired to the nodes by position. The design draws the edge
		 * to the selected node in amber at two pixels and the rest in #343434 at
		 * one, plus a dot at the midpoint of the selected edge. The markup
		 * carries exactly one amber edge and one dot, hardcoded to the first
		 * node, so both are reused rather than created: the dot is moved and
		 * shown whenever a selection exists, which is what the design does,
		 * since it only ever draws the one.
		 */
		const edges = Array.prototype.slice.call(
			svg.querySelectorAll( GRAPH_EDGE_SELECTOR )
		);

		if ( edges.length === this.nodes.length ) {
			this.edges = edges;
			this.edgeDot = svg.querySelector( 'circle[r="3.5"]' );
		}

		this.onGraphClick = ( event ) => {
			const node = this.nodeFromEvent( event );

			if ( ! node ) {
				return;
			}

			this.select( node.index, true );
		};

		this.onGraphKeyDown = ( event ) => {
			if ( 'Enter' !== event.key && ' ' !== event.key ) {
				return;
			}

			const node = this.nodeFromEvent( event );

			if ( ! node ) {
				return;
			}

			event.preventDefault();

			this.select( node.index, true );
		};

		svg.addEventListener( 'click', this.onGraphClick );
		svg.addEventListener( 'keydown', this.onGraphKeyDown );
	};

	/**
	 * The node map entry an event landed on, or null.
	 *
	 * @param {Event} event The event.
	 * @return {Object|null} The node, or null when it landed on the background.
	 */
	SectionSwitcher.prototype.nodeFromEvent = function ( event ) {
		const group =
			event.target && event.target.closest
				? event.target.closest( `.${ GRAPH_NODE_CLASS }` )
				: null;

		if ( ! group ) {
			return null;
		}

		return this.nodes.find( ( node ) => node.group === group ) || null;
	};

	/**
	 * The tab whose own text matches a string, ignoring case and whitespace.
	 *
	 * @param {string} text The node label text.
	 * @return {number} The tab index, or -1 when nothing matches.
	 */
	SectionSwitcher.prototype.indexOfTabByText = function ( text ) {
		const wanted = String( text || '' )
			.trim()
			.toLowerCase();

		if ( ! wanted ) {
			return -1;
		}

		return this.getTabs().findIndex(
			( tab ) =>
				String( tab.textContent || '' )
					.trim()
					.toLowerCase() === wanted
		);
	};

	/**
	 * The visible text of a tab, trimmed.
	 *
	 * @param {number} index The tab index.
	 * @return {string} The tab text.
	 */
	SectionSwitcher.prototype.tabText = function ( index ) {
		const tab = this.getTabs()[ index ];

		return tab ? String( tab.textContent || '' ).trim() : '';
	};

	/**
	 * Arrow, Home and End handling for a tablist.
	 *
	 * Enter and Space are deliberately not handled here. A real button element
	 * already activates itself on both keys and that activation arrives as the
	 * click event bound above, so handling them again would do the work twice
	 * and restart the countdown twice.
	 *
	 * @param {KeyboardEvent} event Keyboard event.
	 * @return {boolean} True when the key was consumed.
	 */
	SectionSwitcher.prototype.handleTablistKey = function ( event ) {
		const tabs = this.getTabs();

		if ( tabs.length < 1 ) {
			return false;
		}

		const current = tabs.indexOf( this.strip.ownerDocument.activeElement );

		if ( current < 0 ) {
			return false;
		}

		let next = -1;

		switch ( event.key ) {
			case 'ArrowRight':
			case 'ArrowDown':
				next = ( current + 1 ) % tabs.length;
				break;
			case 'ArrowLeft':
			case 'ArrowUp':
				next = ( current - 1 + tabs.length ) % tabs.length;
				break;
			case 'Home':
				next = 0;
				break;
			case 'End':
				next = tabs.length - 1;
				break;
			default:
				return false;
		}

		this.applySelection( next, false );
		tabs[ next ].focus();

		return true;
	};

	/**
	 * Bind the hover and focus listeners on the panel host.
	 *
	 * These are mouseenter, mouseleave, focusin and focusout rather than
	 * mouseover, mouseout, focus and blur. The first of each pair fires once
	 * when the pointer or the focus enters the panel and its subtree, so moving
	 * between two children inside the panel does not thrash the paused state.
	 * The bubbling pair fires on every child transition instead.
	 *
	 * @return {void}
	 */
	SectionSwitcher.prototype.bindPanel = function () {
		this.onPanelMouseEnter = () => {
			this.pointerInside = true;
			this.stopTimer();
		};

		this.onPanelMouseLeave = () => {
			this.pointerInside = false;
			this.startTimer();
		};

		this.onPanelFocusIn = () => {
			this.focusInside = true;
			this.stopTimer();
		};

		this.onPanelFocusOut = ( event ) => {
			if (
				event.relatedTarget &&
				this.panelsHost.contains( event.relatedTarget )
			) {
				return;
			}

			this.focusInside = false;
			this.startTimer();
		};

		this.panelsHost.addEventListener(
			'mouseenter',
			this.onPanelMouseEnter
		);
		this.panelsHost.addEventListener(
			'mouseleave',
			this.onPanelMouseLeave
		);
		this.panelsHost.addEventListener( 'focusin', this.onPanelFocusIn );
		this.panelsHost.addEventListener( 'focusout', this.onPanelFocusOut );
	};

	/**
	 * Listen for tab visibility and for the page going away.
	 *
	 * @return {void}
	 */
	SectionSwitcher.prototype.bindLifecycle = function () {
		this.onVisibilityChange = () => {
			if ( 'hidden' === document.visibilityState ) {
				this.stopTimer();
			} else {
				this.startTimer();
			}
		};

		this.onPageHide = () => {
			this.destroy();
		};

		document.addEventListener(
			'visibilitychange',
			this.onVisibilityChange
		);
		window.addEventListener( 'pagehide', this.onPageHide );
	};

	/**
	 * Create the single IntersectionObserver that gates rotation.
	 *
	 * The observer is created once per section and never again. It is only
	 * created at all for a strip that asked to rotate, so sections 02 and 06
	 * hold no observer and no timer.
	 *
	 * @return {void}
	 */
	SectionSwitcher.prototype.observe = function () {
		if ( typeof window.IntersectionObserver !== 'function' ) {
			return;
		}

		this.intersectionObserver = new window.IntersectionObserver(
			( entries ) => {
				entries.forEach( ( entry ) => {
					this.inView = entry.isIntersecting;
				} );

				if ( this.inView ) {
					this.startTimer();
				} else {
					this.stopTimer();
				}
			},
			{ threshold: 0 }
		);

		this.intersectionObserver.observe( this.root );
	};

	/**
	 * The tab buttons of this strip, read fresh.
	 *
	 * @return {Array<HTMLElement>} The tab buttons in document order.
	 */
	SectionSwitcher.prototype.getTabs = function () {
		return Array.prototype.filter.call(
			this.strip.querySelectorAll( 'button' ),
			( button ) => button.classList.contains( this.tabClass )
		);
	};

	/**
	 * The position of a button among this strip's tabs.
	 *
	 * @param {HTMLElement} tab A tab button.
	 * @return {number} The index, or -1 when the button is not a tab.
	 */
	SectionSwitcher.prototype.indexOfTab = function ( tab ) {
		return this.getTabs().indexOf( tab );
	};

	/**
	 * The tab a control selects, whether that control is a tab or something
	 * else that names a panel by id.
	 *
	 * A control outside the row strip, such as section 06's Next Stage button,
	 * is matched by the panel it names rather than by its position, so it
	 * selects the same tab the row strip would.
	 *
	 * @param {HTMLElement} control The clicked control.
	 * @return {number} The tab index, or -1 when it names no tab panel.
	 */
	SectionSwitcher.prototype.indexOfControl = function ( control ) {
		const asTab = this.indexOfTab( control );

		if ( asTab >= 0 ) {
			return asTab;
		}

		const target = control.getAttribute( TARGET_ATTRIBUTE );

		if ( ! target ) {
			return -1;
		}

		return this.getTabs().findIndex(
			( tab ) => tab.getAttribute( TARGET_ATTRIBUTE ) === target
		);
	};

	/**
	 * The panel a button selects.
	 *
	 * @param {HTMLElement} tab A tab button.
	 * @return {HTMLElement|null} The panel, or null when the id resolves to
	 *                            nothing, which is a markup fault rather than
	 *                            something to throw on.
	 */
	SectionSwitcher.prototype.panelForTab = function ( tab ) {
		const id = tab.getAttribute( TARGET_ATTRIBUTE );

		if ( ! id ) {
			return null;
		}

		return document.getElementById( id );
	};

	/**
	 * The id a tabpanel should be labelled by, which is the id of its tab.
	 *
	 * WHY IT MAY HAVE TO MAKE ONE. A tabpanel needs its tab to have an id for
	 * aria-labelledby to name it, and the saved markup does not give every tab
	 * one. Rather than leave those panels unnamed, an id is derived from the
	 * panel the tab already names through data-hz-target, which is content the
	 * markup is carrying anyway. It is therefore stable across reloads, unique
	 * per panel, and readable, and an id the editor later writes into the markup
	 * is preferred over a derived one rather than overwritten.
	 *
	 * A tab that carries an id on something inside itself is labelled by that
	 * instead. The capability cards do exactly this: the heading inside the button
	 * carries the id, and naming the heading names the capability, which is
	 * better than naming the whole button including its state label.
	 *
	 * @param {HTMLElement} tab    The tab button.
	 * @param {string|null} target The panel id the tab controls.
	 * @return {string} An id that resolves to a real element.
	 */
	SectionSwitcher.prototype.labelIdForTab = function ( tab, target ) {
		if ( tab.id ) {
			return tab.id;
		}

		const labelled = tab.querySelector( '[id]' );

		if ( labelled ) {
			return labelled.id;
		}

		const base = target || 'hz-switch-panel';
		let candidate = base + '-tab';
		let attempt = 2;

		while (
			document.getElementById( candidate ) &&
			document.getElementById( candidate ) !== tab
		) {
			candidate = base + '-tab-' + attempt;
			attempt += 1;
		}

		tab.id = candidate;

		return candidate;
	};

	/**
	 * The index the saved markup says is selected when the script starts.
	 *
	 * @param {Array<HTMLElement>} tabs The tab buttons.
	 * @return {number} The index, or -1 when the markup selects nothing.
	 */
	SectionSwitcher.prototype.findSelectedIndex = function ( tabs ) {
		return tabs.findIndex(
			( tab ) =>
				'true' === tab.getAttribute( 'aria-selected' ) ||
				'true' === tab.getAttribute( 'aria-pressed' )
		);
	};

	/**
	 * Select a tab, from a click, a key or the timer.
	 *
	 * @param {number}  index       The tab index.
	 * @param {boolean} fromVisitor True when a click or a key caused this.
	 * @return {void}
	 */
	SectionSwitcher.prototype.select = function ( index, fromVisitor ) {
		const tabs = this.getTabs();

		if ( index < 0 || index >= tabs.length ) {
			return;
		}

		this.applySelection( index, false );

		/*
		 * A visitor action resets the countdown, so the next automatic advance
		 * is a full interval away rather than whatever was left of the previous
		 * one.
		 *
		 * The clear has to be explicit. startTimer leaves an interval that
		 * already exists alone, so calling it here on its own would leave the
		 * pending tick exactly where it was and a click would be overwritten
		 * part way through the interval the visitor is looking at. Clearing
		 * first is also what keeps this to one timer: startTimer then creates
		 * the replacement, and refuses a second one while it exists.
		 */
		if ( fromVisitor ) {
			this.stopTimer();
			this.startTimer();
			this.revealPanel( tabs[ index ] );
		}
	};

	/**
	 * Bring the swapped detail panel into view when the layout stacks it.
	 *
	 * WHAT THIS IS FOR. Sections 02 and 06 put their detail panel below their
	 * controls once the twelve column body collapses, which is below 1024px. At
	 * 390 the five capability tabs occupy 941px and the panel begins 2670px down
	 * the page, so a visitor who taps a tab swaps content that is entirely below
	 * the fold and sees nothing happen. CSS cannot fix that: the stacking itself
	 * is already correct, and the design stacks it the same way. Only a script
	 * knows which panel a tap selected.
	 *
	 * This is an addition, not design parity. The design reference has no such
	 * behaviour. Its onClick for both sections is the setState and nothing else,
	 * so the panel stays below the fold there too. The owner asked for the scroll,
	 * so it is here, and it is called out as an addition in the change report.
	 *
	 * WHY IT IS GATED ON GEOMETRY RATHER THAN ON A WIDTH. Both sections already
	 * stack correctly at mobile, which was measured against the design rather
	 * than assumed. So there is no layout fix to make and no media query to
	 * invent. Whether the panel needs revealing is a question about where the two
	 * boxes actually are, so it is asked about the boxes. That is self adjusting:
	 * at 1440 the panel and the controls share a top edge and nothing happens,
	 * and no width constant has to be kept in step with _breakpoints.scss.
	 *
	 * WHY ROTATING SECTIONS ARE EXCLUDED. Section 03 is the only strip that
	 * rotates, and a timer driven scroll would fight the visitor and the
	 * prefers-reduced-motion contract for no benefit. Excluding it by this.rotates
	 * leaves exactly sections 02 and 06, which are the two the request names.
	 *
	 * WHY ONLY WHEN TOO LITTLE OF THE PANEL IS ON SCREEN. A visitor reading a
	 * panel that is already on screen must not have the page moved under them by
	 * a tap on a control further up. The panel is taller than a phone viewport,
	 * so what matters is not whether any part of it is visible but how much of
	 * it is: a panel whose top edge sat just above the fold line still showed
	 * 16% of its detail and the tap appeared to do nothing.
	 *
	 * @param {HTMLElement} tab The tab that was selected.
	 * @return {void}
	 */
	SectionSwitcher.prototype.revealPanel = function ( tab ) {
		if ( this.rotates ) {
			return;
		}

		const panel = this.panelForTab( tab );

		if ( ! panel || 'function' !== typeof panel.scrollIntoView ) {
			return;
		}

		const panelBox = panel.getBoundingClientRect();
		const stripBox = this.strip.getBoundingClientRect();
		const viewHeight = panel.ownerDocument.defaultView.innerHeight;

		/*
		 * Stacked means the panel starts below the whole control strip. The two
		 * halves are one pixel either side of the line so a fractional layout
		 * cannot read as exactly flush and trigger a scroll on a side by side
		 * layout. The 32px is the design's own gap-8 between the two columns
		 * once they are stacked, so there is a wide margin either way.
		 */
		if ( panelBox.top < stripBox.bottom - 1 ) {
			return;
		}

		if ( ! panelBox.height ) {
			return;
		}

		const visible =
			Math.min( panelBox.bottom, viewHeight ) -
			Math.max( panelBox.top, 0 );

		if ( visible / panelBox.height >= MIN_PANEL_VISIBLE ) {
			return;
		}

		panel.scrollIntoView( {
			block: 'start',
			behavior: this.prefersReducedMotion ? 'auto' : 'smooth',
		} );
	};

	/**
	 * Write a selection into the tablist and the panels.
	 *
	 * @param {number} index The tab index.
	 * @return {void}
	 */
	SectionSwitcher.prototype.applySelection = function ( index ) {
		const tabs = this.getTabs();

		if ( index < 0 || index >= tabs.length ) {
			return;
		}

		tabs.forEach( ( tab, position ) => {
			const isSelected = position === index;
			const panel = this.panelForTab( tab );

			if ( this.isTablist ) {
				tab.setAttribute(
					'aria-selected',
					isSelected ? 'true' : 'false'
				);

				/*
				 * Every tab is one tab stop, so Tab alone reaches all of them.
				 *
				 * Roving tabindex, where only the selected tab is focusable and
				 * the arrow keys do the moving, is only a correct pattern when
				 * arrow handling exists and the visitor knows to use it. It was
				 * measured here as six of seven tabs unreachable by Tab, which
				 * is true for a visitor who tabs rather than arrows. The design
				 * reference reaches the same conclusion by a different route:
				 * every node in TechnicalSkillsEcosystem is tabIndex 0 with its
				 * own Enter and Space handling. So all seven are focusable, and
				 * ArrowLeft, ArrowRight, ArrowUp, ArrowDown, Home and End are
				 * still handled below as a shortcut, not as the only route.
				 */
				tab.setAttribute( 'tabindex', '0' );

				/*
				 * The tab names the panel it controls, and the panel is given the
				 * matching role, so the pair a tab announces is the pair that is
				 * on screen. Both are written here rather than saved into
				 * post_content because the panel is a group block wrapper,
				 * which WordPress renders from its own block attributes.
				 */
				const target = tab.getAttribute( TARGET_ATTRIBUTE );

				if ( target ) {
					tab.setAttribute( 'aria-controls', target );
				}

				if ( panel && ! panel.getAttribute( 'role' ) ) {
					panel.setAttribute( 'role', 'tabpanel' );
				}

				/*
				 * A tabpanel with no accessible name is announced as an unnamed
				 * panel, so the relationship the visitor hears stated on the tab
				 * is the only place it is stated at all. Each panel is therefore
				 * labelled by the tab that controls it.
				 */
				if ( panel ) {
					panel.setAttribute(
						'aria-labelledby',
						this.labelIdForTab( tab, target )
					);
				}
			} else {
				tab.setAttribute(
					'aria-pressed',
					isSelected ? 'true' : 'false'
				);
			}

			/*
			 * The visible state label follows the selection. This is the non
			 * colour signal that the card is selected, so it has to be correct:
			 * an ACTIVE label left on an unselected card would state the
			 * opposite of what is selected, which is worse than no label.
			 */
			if ( this.selectedState && this.idleState ) {
				const label = tab.querySelector( `[${ STATE_ATTRIBUTE }]` );

				if ( label ) {
					label.textContent = isSelected
						? this.selectedState
						: this.idleState;
				}
			}

			/*
			 * Panels with no matching tab are left alone rather than force
			 * hidden. A panel nobody can select is a content decision, not an
			 * interaction one, and hiding it here would quietly delete copy
			 * from the page.
			 */
			if ( panel ) {
				panel.classList.toggle( IDLE_CLASS, ! isSelected );
			}
		} );

		/*
		 * The node map follows the tabs. The design drives both from one piece
		 * of state, so a node is selected by exactly the same index as its tab,
		 * and the class and the aria state are written from the same loop that
		 * writes the tab's. This is what makes the two controls agree in both
		 * directions: select a tab and the matching node inverts, click a node
		 * and select() runs applySelection and the matching tab inverts.
		 *
		 * aria-selected is written on the node as well as on the tab, so the
		 * selected state is announced from either control. The node also keeps
		 * role button, which is what the design gives it, and aria-pressed is
		 * not written because a role button that also carries aria-selected
		 * states the same fact twice.
		 */
		this.nodes.forEach( ( node ) => {
			const isSelected = node.index === index;

			node.group.classList.toggle(
				GRAPH_NODE_SELECTED_CLASS,
				isSelected
			);

			node.group.setAttribute(
				'aria-selected',
				isSelected ? 'true' : 'false'
			);
		} );

		this.applyEdgeSelection( index );

		this.selectedIndex = index;
	};

	/**
	 * Move the amber edge and its midpoint dot onto the selected node.
	 *
	 * The stroke and stroke-width are written as attributes rather than left to
	 * a class, because these are SVG presentation attributes in the markup and a
	 * class would need the same two values restated per state in the
	 * stylesheet. The dot is repositioned to the midpoint of whichever edge is
	 * now selected, which is the only thing the design varies about it.
	 *
	 * @param {number} index The tab index.
	 * @return {void}
	 */
	SectionSwitcher.prototype.applyEdgeSelection = function ( index ) {
		if ( this.edges.length !== this.nodes.length ) {
			return;
		}

		this.edges.forEach( ( edge, position ) => {
			const isSelected = position === index;

			edge.classList.toggle( GRAPH_EDGE_SELECTED_CLASS, isSelected );
			edge.setAttribute(
				'stroke',
				isSelected ? GRAPH_EDGE_SELECTED_STROKE : GRAPH_EDGE_IDLE_STROKE
			);
			edge.setAttribute( 'stroke-width', isSelected ? '2' : '1' );
		} );

		if ( ! this.edgeDot ) {
			return;
		}

		const edge = this.edges[ index ];

		if ( ! edge ) {
			return;
		}

		const midpointX = this.midpoint( edge, 'x1', 'x2' );
		const midpointY = this.midpoint( edge, 'y1', 'y2' );

		/*
		 * midpoint returns null rather than a number when either coordinate is
		 * absent, and writing that null into an attribute would publish the
		 * literal string "null" as a coordinate. A dot in its last known place is
		 * better than a dot at the origin.
		 */
		if ( null === midpointX || null === midpointY ) {
			return;
		}

		this.edgeDot.setAttribute( 'cx', midpointX );
		this.edgeDot.setAttribute( 'cy', midpointY );
	};

	/**
	 * The average of two attributes of an edge, as a number.
	 *
	 * @param {Element} edge The line element.
	 * @param {string}  from The first attribute name.
	 * @param {string}  to   The second attribute name.
	 * @return {number|null} The midpoint, or null when either value is absent.
	 */
	SectionSwitcher.prototype.midpoint = function ( edge, from, to ) {
		const start = parseFloat( edge.getAttribute( from ) );
		const end = parseFloat( edge.getAttribute( to ) );

		if ( isNaN( start ) || isNaN( end ) ) {
			return null;
		}

		return ( start + end ) / 2;
	};

	/**
	 * Advance one tab, wrapping at the end.
	 *
	 * The tab list is read on every tick rather than cached, so a change to the
	 * number of tabs is picked up and the timer restarted against the new count
	 * rather than running against a stale index.
	 *
	 * @return {void}
	 */
	SectionSwitcher.prototype.advance = function () {
		const tabs = this.getTabs();

		if ( tabs.length !== this.knownCount ) {
			this.knownCount = tabs.length;
			this.selectedIndex = this.findSelectedIndex( tabs );

			if ( this.selectedIndex < 0 ) {
				this.applySelection( 0 );
			}

			this.startTimer();

			return;
		}

		if ( tabs.length < 1 ) {
			return;
		}

		/*
		 * One tick is one step, taken from what is on screen right now rather
		 * than from a remembered counter. The rendered selection is the state
		 * the visitor can actually see, so reading it here is what keeps the
		 * advance sequential even if the markup selected something else between
		 * two ticks. Reading a cached index instead is how a tick ends up
		 * skipping a tab or arriving somewhere the visitor never saw go past.
		 */
		const current = this.findSelectedIndex( tabs );
		let next = ( current >= 0 ? current : this.selectedIndex ) + 1;

		if ( next < 0 ) {
			next = 0;
		}

		if ( next >= tabs.length ) {
			next = 0;
		}

		this.applySelection( next );
	};

	/**
	 * Whether rotation should be running at this moment.
	 *
	 * @return {boolean} True when a timer is wanted.
	 */
	SectionSwitcher.prototype.shouldRotate = function () {
		return (
			this.rotates &&
			! this.prefersReducedMotion &&
			! this.destroyed &&
			this.inView &&
			! this.pointerInside &&
			! this.focusInside &&
			'hidden' !== document.visibilityState
		);
	};

	/**
	 * Create the one interval, or leave the existing one alone.
	 *
	 * @return {void}
	 */
	SectionSwitcher.prototype.startTimer = function () {
		if ( ! this.shouldRotate() ) {
			return;
		}

		if ( this.intervalId ) {
			return;
		}

		this.intervalId = window.setInterval( () => {
			this.advance();
		}, ROTATE_INTERVAL );
	};

	/**
	 * Clear the interval, if there is one.
	 *
	 * @return {void}
	 */
	SectionSwitcher.prototype.stopTimer = function () {
		if ( ! this.intervalId ) {
			return;
		}

		window.clearInterval( this.intervalId );
		this.intervalId = 0;
	};

	/**
	 * Tear the instance down: observer, timer and every listener it added.
	 *
	 * Safe to call twice, which matters because pagehide can be followed by
	 * unload and both reach this.
	 *
	 * @return {void}
	 */
	SectionSwitcher.prototype.destroy = function () {
		if ( this.destroyed ) {
			return;
		}

		this.destroyed = true;

		this.stopTimer();

		if ( this.intersectionObserver ) {
			this.intersectionObserver.disconnect();
			this.intersectionObserver = null;
		}

		if ( this.mediaQuery && this.onMediaQueryChange ) {
			if ( typeof this.mediaQuery.removeEventListener === 'function' ) {
				this.mediaQuery.removeEventListener(
					'change',
					this.onMediaQueryChange
				);
			} else if ( typeof this.mediaQuery.removeListener === 'function' ) {
				this.mediaQuery.removeListener( this.onMediaQueryChange );
			}
		}

		if ( this.onVisibilityChange ) {
			document.removeEventListener(
				'visibilitychange',
				this.onVisibilityChange
			);
		}

		if ( this.onPageHide ) {
			window.removeEventListener( 'pagehide', this.onPageHide );
		}

		if ( this.onStripClick ) {
			this.root.removeEventListener( 'click', this.onStripClick );
		}

		if ( this.onStripKeyDown ) {
			this.strip.removeEventListener( 'keydown', this.onStripKeyDown );
		}

		if ( this.onGraphClick || this.onGraphKeyDown ) {
			const svg = this.graphSvg;

			if ( svg ) {
				if ( this.onGraphClick ) {
					svg.removeEventListener( 'click', this.onGraphClick );
				}

				if ( this.onGraphKeyDown ) {
					svg.removeEventListener( 'keydown', this.onGraphKeyDown );
				}
			}
		}

		if ( this.panelsHost ) {
			if ( this.onPanelMouseEnter ) {
				this.panelsHost.removeEventListener(
					'mouseenter',
					this.onPanelMouseEnter
				);
			}

			if ( this.onPanelMouseLeave ) {
				this.panelsHost.removeEventListener(
					'mouseleave',
					this.onPanelMouseLeave
				);
			}

			if ( this.onPanelFocusIn ) {
				this.panelsHost.removeEventListener(
					'focusin',
					this.onPanelFocusIn
				);
			}

			if ( this.onPanelFocusOut ) {
				this.panelsHost.removeEventListener(
					'focusout',
					this.onPanelFocusOut
				);
			}
		}

		this.onStripClick = null;
		this.onStripKeyDown = null;
		this.onGraphClick = null;
		this.onGraphKeyDown = null;
		this.onPanelMouseEnter = null;
		this.onPanelMouseLeave = null;
		this.onPanelFocusIn = null;
		this.onPanelFocusOut = null;
		this.onVisibilityChange = null;
		this.onPageHide = null;
		this.onMediaQueryChange = null;

		this.nodes = [];
		this.edges = [];
		this.edgeDot = null;
		this.graphSvg = null;
	};

	/**
	 * Initialise every RankKernel architecture figure on the page.
	 *
	 * Separate from the strip pass because the figure is not a strip: it carries
	 * no data-hz-switcher, has no panels and is a role group rather than a
	 * tablist. Both passes run from the same readyState gate and each instance
	 * tears itself down on pagehide, so neither leaks on its own.
	 *
	 * @return {void}
	 */
	function initArchStatusFilters() {
		Array.prototype.forEach.call(
			document.querySelectorAll( ARCH_ROOT_SELECTOR ),
			( root ) => {
				if ( 'true' === root.getAttribute( 'data-hz-arch-ready' ) ) {
					return;
				}

				root.setAttribute( 'data-hz-arch-ready', 'true' );

				try {
					new ArchStatusFilter( root );
				} catch {
					root.setAttribute( 'data-hz-error', 'true' );
				}
			}
		);
	}

	/**
	 * Initialise every control strip on the page exactly once.
	 *
	 * @return {void}
	 */
	function initSectionSwitchers() {
		document
			.querySelectorAll( `[${ STRIP_ATTRIBUTE }]` )
			.forEach( ( strip ) => {
				const root = strip.closest( 'section' );

				if (
					! root ||
					'true' === root.getAttribute( 'data-hz-ready' )
				) {
					return;
				}

				root.setAttribute( 'data-hz-ready', 'true' );

				try {
					new SectionSwitcher( strip, root );
				} catch {
					root.setAttribute( 'data-hz-error', 'true' );
				}
			} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			initSectionSwitchers();
			initArchStatusFilters();
		} );
	} else {
		initSectionSwitchers();
		initArchStatusFilters();
	}
} )();
