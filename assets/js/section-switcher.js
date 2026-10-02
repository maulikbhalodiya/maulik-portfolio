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
 *   data-hz-selected-class  the class the selected button carries
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
		this.selectedClass =
			strip.getAttribute( 'data-hz-selected-class' ) || '';
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
	 * Bind the listeners on the control strip.
	 *
	 * One click listener on the strip rather than one per button, so the number
	 * of listeners does not grow with the number of tabs.
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
		}
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
			} else {
				tab.setAttribute(
					'aria-pressed',
					isSelected ? 'true' : 'false'
				);
			}

			if ( this.selectedClass ) {
				tab.classList.toggle( this.selectedClass, isSelected );
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

		this.edgeDot.setAttribute( 'cx', this.midpoint( edge, 'x1', 'x2' ) );
		this.edgeDot.setAttribute( 'cy', this.midpoint( edge, 'y1', 'y2' ) );
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
			const svg = this.root.querySelector( GRAPH_SVG_SELECTOR );

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
	};

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
		document.addEventListener( 'DOMContentLoaded', initSectionSwitchers );
	} else {
		initSectionSwitchers();
	}
} )();
