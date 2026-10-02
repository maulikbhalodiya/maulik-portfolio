/**
 * Hero interactive ecosystem canvas view script.
 *
 * A direct port of the approved design reference component
 * src/components/Hero3DEcosystem.tsx (468 lines) to plain browser JavaScript.
 * There is no build step, no bundler and no dependency, and there is no WebGL
 * or Three.js. The reference draws a projected three dimensional node graph
 * with canvas.getContext('2d'), and this file does exactly the same thing.
 *
 * Ported behaviour:
 *   - the same 14 core vertices and 24 core edges of the octahedral lattice
 *   - the same two orbital reference rings
 *   - the same yaw and pitch projection, the same field of view and the same
 *     compact breakpoint
 *   - the same colours, alphas, line widths, label metrics and depths
 *   - the same hover detection radius and click to select interaction
 *   - the same pause, reset and reduced motion semantics
 *
 * Added on top of the reference, because a server rendered block needs it:
 *   - device pixel ratio and container resize handling
 *   - explicit teardown that cancels the animation frame and removes every
 *     listener, observer and timer, so nothing keeps running after the block is
 *     removed from the document
 *   - a pause when the block scrolls out of view or the tab is hidden
 *   - a total failure guard, so a broken canvas can never throw into the page
 */

( function () {
	'use strict';

	/**
	 * Central core vertices: outer octahedron, then the inner structural cube.
	 *
	 * @type {Array<{x: number, y: number, z: number}>}
	 */
	const CORE_VERTICES = [
		{ x: 0, y: -64, z: 0 },
		{ x: 0, y: 64, z: 0 },
		{ x: -52, y: 0, z: -52 },
		{ x: 52, y: 0, z: -52 },
		{ x: 52, y: 0, z: 52 },
		{ x: -52, y: 0, z: 52 },
		{ x: -26, y: -26, z: -26 },
		{ x: 26, y: -26, z: -26 },
		{ x: 26, y: 26, z: -26 },
		{ x: -26, y: 26, z: -26 },
		{ x: -26, y: -26, z: 26 },
		{ x: 26, y: -26, z: 26 },
		{ x: 26, y: 26, z: 26 },
		{ x: -26, y: 26, z: 26 },
	];

	/**
	 * Core edge pairs. Indices 12 and above are the inner cube edges, which the
	 * reference draws in signal amber rather than in bone white.
	 *
	 * @type {Array<[number, number]>}
	 */
	const CORE_EDGES = [
		[ 0, 2 ],
		[ 0, 3 ],
		[ 0, 4 ],
		[ 0, 5 ],
		[ 1, 2 ],
		[ 1, 3 ],
		[ 1, 4 ],
		[ 1, 5 ],
		[ 2, 3 ],
		[ 3, 4 ],
		[ 4, 5 ],
		[ 5, 2 ],
		[ 6, 7 ],
		[ 7, 8 ],
		[ 8, 9 ],
		[ 9, 6 ],
		[ 10, 11 ],
		[ 11, 12 ],
		[ 12, 13 ],
		[ 13, 10 ],
		[ 6, 10 ],
		[ 7, 11 ],
		[ 8, 12 ],
		[ 9, 13 ],
	];

	/** Viewer distance from the origin, used by the projection. */
	const DISTANCE = 520;

	/** Near plane clamp, so a vertex behind the camera cannot divide by zero. */
	const NEAR_LIMIT = 120;

	/** Field of view below and above the 480 pixel compact breakpoint. */
	const FOV_WIDE = 480;
	const FOV_COMPACT = 370;

	/** Width in CSS pixels below which the compact layout applies. */
	const COMPACT_WIDTH = 480;

	/** Pointer hover detection radius in CSS pixels. */
	const HOVER_RADIUS = 34;

	/** Rotation speed and signal packet time step per frame. */
	const YAW_STEP = 0.0028;
	const PULSE_STEP = 0.014;

	/** Pointer influence on yaw and pitch, and the pitch easing factor. */
	const YAW_POINTER_INFLUENCE = 0.32;
	const PITCH_POINTER_INFLUENCE = 0.22;
	const BASE_PITCH = 0.24;
	const PITCH_EASE = 0.08;

	/** Starting orientation, also the value the reset button restores. */
	const INITIAL_YAW = 0.4;
	const INITIAL_PITCH = 0.22;

	/** Node id that the design highlights with a distinct core fill. */
	const RANK_KERNEL_ID = 'rankkernel';

	/**
	 * Project a 3D point to 2D through yaw then pitch, with perspective divide.
	 *
	 * @param {number} x     World x.
	 * @param {number} y     World y.
	 * @param {number} z     World z.
	 * @param {number} yaw   Yaw in radians.
	 * @param {number} pitch Pitch in radians.
	 * @param {number} cx    Canvas centre x in CSS pixels.
	 * @param {number} cy    Canvas centre y in CSS pixels.
	 * @param {number} fov   Field of view scalar.
	 * @return {{x: number, y: number, z: number, scale: number}} Projected point.
	 */
	function project3D( x, y, z, yaw, pitch, cx, cy, fov ) {
		const cosY = Math.cos( yaw );
		const sinY = Math.sin( yaw );
		const x1 = x * cosY - z * sinY;
		const z1 = z * cosY + x * sinY;

		const cosX = Math.cos( pitch );
		const sinX = Math.sin( pitch );
		const y2 = y * cosX - z1 * sinX;
		const z2 = z1 * cosX + y * sinX;

		const scale = fov / Math.max( DISTANCE + z2, NEAR_LIMIT );

		return {
			x: cx + x1 * scale,
			y: cy + y2 * scale,
			z: z2,
			scale,
		};
	}

	/**
	 * One initialised hero block.
	 *
	 * @param {HTMLElement} container The block wrapper element.
	 * @class
	 */
	function HeroEcosystem( container ) {
		this.container = container;
		this.canvas = container.querySelector( '[data-maulik-hero-canvas]' );
		this.ctx = null;
		this.nodes = [];
		this.nodeDetails = [];
		this.projected = [];
		this.frameId = 0;
		this.resizeFrameId = 0;
		this.destroyed = false;
		this.inView = true;
		this.pageVisible = true;

		this.selectedNodeId = 'wordpress';
		this.hoveredNodeId = null;
		this.isPaused = false;
		this.prefersReducedMotion = false;

		this.pointer = { x: 0, y: 0, active: false };
		this.rotation = { yaw: INITIAL_YAW, pitch: INITIAL_PITCH };
		this.pulseTime = 0;

		this.mediaQuery = null;
		this.onMediaQueryChange = null;
		this.resizeObserver = null;
		this.intersectionObserver = null;
		this.onVisibilityChange = null;
		this.onPageHide = null;
		this.onPointerMove = null;
		this.onPointerLeave = null;
		this.onCanvasClick = null;
		this.onToggleClick = null;
		this.onResetClick = null;
		this.onKeyDown = null;

		this.init();
	}

	/**
	 * Read the server rendered node payload and wire up listeners.
	 *
	 * @return {void}
	 */
	HeroEcosystem.prototype.init = function () {
		if ( ! this.canvas || ! this.canvas.getContext ) {
			return;
		}

		try {
			this.ctx = this.canvas.getContext( '2d' );
		} catch {
			this.ctx = null;
		}

		if ( ! this.ctx ) {
			return;
		}

		try {
			this.nodes = JSON.parse(
				this.container.getAttribute( 'data-maulik-hero-nodes' ) || '[]'
			);
		} catch {
			this.nodes = [];
		}

		if ( ! Array.isArray( this.nodes ) || this.nodes.length === 0 ) {
			return;
		}

		this.readNodeDetails();

		this.initReducedMotion();
		this.initInteractions();
		this.observe();

		this.resizeCanvas();

		this.render();
	};

	/**
	 * Read the server rendered inspector copy for each node, so changing the
	 * selection updates the readout without a second round trip.
	 *
	 * @return {void}
	 */
	HeroEcosystem.prototype.readNodeDetails = function () {
		const details = this.container.getAttribute(
			'data-maulik-hero-details'
		);
		let parsed = null;

		try {
			parsed = JSON.parse( details || '{}' );
		} catch {
			parsed = null;
		}

		if ( ! parsed || typeof parsed !== 'object' ) {
			return;
		}

		for ( let i = 0; i < this.nodes.length; i++ ) {
			const id = this.nodes[ i ].id;

			if ( parsed[ id ] ) {
				this.nodeDetails[ id ] = parsed[ id ];
			}
		}
	};

	/**
	 * Detect the reduced motion preference and hold the scene still when it is
	 * set, exactly as the reference does by forcing the paused state.
	 *
	 * @return {void}
	 */
	HeroEcosystem.prototype.initReducedMotion = function () {
		const self = this;

		if ( typeof window.matchMedia !== 'function' ) {
			return;
		}

		this.mediaQuery = window.matchMedia(
			'(prefers-reduced-motion: reduce)'
		);
		this.prefersReducedMotion = this.mediaQuery.matches;

		if ( this.prefersReducedMotion ) {
			this.isPaused = true;
			this.syncToggleLabel();
		}

		this.onMediaQueryChange = function ( event ) {
			self.prefersReducedMotion = event.matches;

			if ( event.matches ) {
				self.isPaused = true;
			}

			self.syncToggleLabel();
			self.scheduleRender();
		};

		if ( typeof this.mediaQuery.addEventListener === 'function' ) {
			this.mediaQuery.addEventListener(
				'change',
				this.onMediaQueryChange
			);
		}
	};

	/**
	 * Attach pointer, keyboard and control listeners.
	 *
	 * @return {void}
	 */
	HeroEcosystem.prototype.initInteractions = function () {
		const self = this;

		this.onPointerMove = function ( event ) {
			self.handlePointerMove( event );
		};

		this.onPointerLeave = function () {
			self.pointer.active = false;

			if ( self.hoveredNodeId !== null ) {
				self.hoveredNodeId = null;
				self.scheduleRender();
			}
		};

		this.onCanvasClick = function () {
			if (
				self.hoveredNodeId &&
				self.hoveredNodeId !== self.selectedNodeId
			) {
				self.selectedNodeId = self.hoveredNodeId;
				self.syncReadout();
				self.scheduleRender();
			}
		};

		this.onToggleClick = function () {
			self.isPaused = ! self.isPaused;
			self.syncToggleLabel();
			self.scheduleRender();
		};

		this.onResetClick = function () {
			self.rotation.yaw = INITIAL_YAW;
			self.rotation.pitch = INITIAL_PITCH;
			self.pointer.active = false;
			self.selectedNodeId = 'wordpress';
			self.syncReadout();
			self.scheduleRender();
		};

		this.onKeyDown = function ( event ) {
			self.handleKeyDown( event );
		};

		this.container.addEventListener( 'pointermove', this.onPointerMove );
		this.container.addEventListener( 'pointerleave', this.onPointerLeave );
		this.container.addEventListener( 'keydown', this.onKeyDown );

		if ( this.canvas ) {
			this.canvas.addEventListener( 'click', this.onCanvasClick );
		}

		this.bindButtons();
	};

	/**
	 * Wire the pause, reset and node tab controls.
	 *
	 * @return {void}
	 */
	HeroEcosystem.prototype.bindButtons = function () {
		const self = this;
		const toggle = this.container.querySelector(
			'[data-maulik-hero-toggle]'
		);
		const reset = this.container.querySelector(
			'[data-maulik-hero-reset]'
		);
		const tabs = this.container.querySelectorAll(
			'[data-maulik-hero-node]'
		);
		let i;

		if ( toggle ) {
			toggle.addEventListener( 'click', this.onToggleClick );
		}

		if ( reset ) {
			reset.addEventListener( 'click', this.onResetClick );
		}

		for ( i = 0; i < tabs.length; i++ ) {
			( function ( tab ) {
				tab.addEventListener( 'click', function () {
					const id = tab.getAttribute( 'data-maulik-hero-node' );

					if ( ! id ) {
						return;
					}

					self.selectedNodeId = id;
					self.syncReadout();
					self.scheduleRender();
				} );
			} )( tabs[ i ] );
		}
	};

	/**
	 * Observe the container for resize, for intersection, and listen for tab
	 * visibility so the animation loop can idle instead of burning frames.
	 *
	 * @return {void}
	 */
	HeroEcosystem.prototype.observe = function () {
		const self = this;

		if ( typeof window.ResizeObserver === 'function' ) {
			this.resizeObserver = new window.ResizeObserver( function () {
				self.resizeCanvas();
				self.scheduleRender();
			} );
			this.resizeObserver.observe( this.container );
		}

		this.onVisibilityChange = function () {
			self.pageVisible = document.visibilityState !== 'hidden';

			if ( self.pageVisible ) {
				self.scheduleRender();
			}
		};

		document.addEventListener(
			'visibilitychange',
			this.onVisibilityChange
		);

		if ( typeof window.IntersectionObserver === 'function' ) {
			this.intersectionObserver = new window.IntersectionObserver(
				function ( entries ) {
					for ( let i = 0; i < entries.length; i++ ) {
						self.inView = entries[ i ].isIntersecting;
					}

					self.scheduleRender();
				},
				{ threshold: 0 }
			);

			this.intersectionObserver.observe( this.container );
		}

		this.onPageHide = function () {
			self.destroy();
		};

		window.addEventListener( 'pagehide', this.onPageHide );
	};

	/**
	 * Match the backing store to the CSS size and the device pixel ratio.
	 *
	 * @return {void}
	 */
	HeroEcosystem.prototype.resizeCanvas = function () {
		if ( this.destroyed || ! this.ctx ) {
			return;
		}

		const rect = this.container.getBoundingClientRect();

		if ( ! rect.width || ! rect.height ) {
			return;
		}

		const dpr = Math.min( window.devicePixelRatio || 1, 2 );

		this.canvas.width = Math.round( rect.width * dpr );
		this.canvas.height = Math.round( rect.height * dpr );
		this.ctx.setTransform( dpr, 0, 0, dpr, 0, 0 );
	};

	/**
	 * Whether the scene should advance on this frame.
	 *
	 * @return {boolean} True when motion is wanted.
	 */
	HeroEcosystem.prototype.isAnimating = function () {
		return (
			! this.isPaused &&
			! this.prefersReducedMotion &&
			this.inView &&
			this.pageVisible &&
			! this.destroyed
		);
	};

	/**
	 * Draw one frame, and queue the next frame only when motion is wanted.
	 *
	 * With JavaScript off, with reduced motion, or with the block scrolled away,
	 * this draws exactly one static frame and stops, which is the whole point
	 * of a static frame.
	 *
	 * @return {void}
	 */
	HeroEcosystem.prototype.render = function () {
		if ( this.destroyed || ! this.ctx ) {
			return;
		}

		try {
			this.draw();
		} catch {
			// A drawing failure must never escape into the page.
			this.destroy();
			return;
		}

		if ( this.isAnimating() ) {
			const self = this;

			this.frameId = window.requestAnimationFrame( function () {
				self.render();
			} );
		} else {
			this.frameId = 0;
		}
	};

	/**
	 * Coalesce redraw requests into a single frame.
	 *
	 * @return {void}
	 */
	HeroEcosystem.prototype.scheduleRender = function () {
		const self = this;

		if ( this.destroyed || this.frameId ) {
			return;
		}

		this.frameId = window.requestAnimationFrame( function () {
			self.frameId = 0;
			self.render();
		} );
	};

	/**
	 * The full drawing pass.
	 *
	 * @return {void}
	 */
	HeroEcosystem.prototype.draw = function () {
		const ctx = this.ctx;
		const rect = this.container.getBoundingClientRect();
		const width = rect.width;
		const height = rect.height;

		if ( ! width || ! height ) {
			return;
		}

		const cx = width / 2;
		const cy = height / 2 - 10;
		const isCompact = width < COMPACT_WIDTH;
		const fov = isCompact ? FOV_COMPACT : FOV_WIDE;
		let i;

		ctx.clearRect( 0, 0, width, height );

		// Subtle radial architectural lighting.
		const radialGrad = ctx.createRadialGradient(
			cx,
			cy,
			12,
			cx,
			cy,
			Math.max( width, height ) * 0.55
		);

		radialGrad.addColorStop( 0, 'rgba(250, 204, 21, 0.09)' );
		radialGrad.addColorStop( 0.45, 'rgba(23, 23, 25, 0.35)' );
		radialGrad.addColorStop( 1, 'rgba(10, 10, 11, 0)' );
		ctx.fillStyle = radialGrad;
		ctx.fillRect( 0, 0, width, height );

		if ( this.isAnimating() ) {
			this.rotation.yaw += YAW_STEP;
			this.pulseTime += PULSE_STEP;
		}

		const targetYawOffset = this.pointer.active
			? this.pointer.x * YAW_POINTER_INFLUENCE
			: 0;
		const targetPitch =
			BASE_PITCH +
			( this.pointer.active
				? this.pointer.y * PITCH_POINTER_INFLUENCE
				: 0 );
		const currentYaw = this.rotation.yaw + targetYawOffset;
		const currentPitch =
			this.rotation.pitch +
			( targetPitch - this.rotation.pitch ) * PITCH_EASE;

		this.rotation.pitch = currentPitch;

		const animating = this.isAnimating();

		// Two technical orbital reference rings.
		[ 140, 200 ].forEach( function ( radius, idx ) {
			ctx.beginPath();

			const steps = 64;

			for ( let s = 0; s <= steps; s++ ) {
				const theta = ( s / steps ) * Math.PI * 2;
				const rx = Math.cos( theta ) * radius;
				const rz = Math.sin( theta ) * radius;
				const ry =
					idx === 0
						? 12 * Math.sin( theta * 2 )
						: -12 * Math.cos( theta * 2 );
				const p = project3D(
					rx,
					ry,
					rz,
					currentYaw,
					currentPitch,
					cx,
					cy,
					fov
				);

				if ( s === 0 ) {
					ctx.moveTo( p.x, p.y );
				} else {
					ctx.lineTo( p.x, p.y );
				}
			}

			ctx.strokeStyle =
				idx === 0
					? 'rgba(255, 253, 244, 0.07)'
					: 'rgba(250, 204, 21, 0.11)';
			ctx.lineWidth = 1;
			ctx.stroke();
		} );

		// Central core edges.
		const projectedCore = CORE_VERTICES.map( function ( v ) {
			return project3D(
				v.x,
				v.y,
				v.z,
				currentYaw * 1.25,
				currentPitch,
				cx,
				cy,
				fov
			);
		} );

		for ( i = 0; i < CORE_EDGES.length; i++ ) {
			const p1 = projectedCore[ CORE_EDGES[ i ][ 0 ] ];
			const p2 = projectedCore[ CORE_EDGES[ i ][ 1 ] ];
			const avgZ = ( p1.z + p2.z ) / 2;
			const depthAlpha = Math.max(
				0.15,
				Math.min( 0.75, 0.55 - avgZ / 220 )
			);

			ctx.beginPath();
			ctx.moveTo( p1.x, p1.y );
			ctx.lineTo( p2.x, p2.y );
			ctx.strokeStyle =
				i >= 12
					? 'rgba(250, 204, 21, ' + depthAlpha * 0.9 + ')'
					: 'rgba(255, 253, 244, ' + depthAlpha * 0.55 + ')';
			ctx.lineWidth = i >= 12 ? 1.35 : 1;
			ctx.stroke();
		}

		// Orbital technical nodes.
		const centerProjected = project3D(
			0,
			0,
			0,
			currentYaw,
			currentPitch,
			cx,
			cy,
			fov
		);
		const currentNodes = [];
		const radiusScale = isCompact ? 0.78 : 1;

		for ( i = 0; i < this.nodes.length; i++ ) {
			const node = this.nodes[ i ];
			const baseRad = ( node.orbitAngle * Math.PI ) / 180;
			const r = node.orbitRadius * radiusScale;
			const nx = Math.cos( baseRad ) * r;
			const nz = Math.sin( baseRad ) * r;
			const ny = node.elevation * radiusScale;
			const proj = project3D(
				nx,
				ny,
				nz,
				currentYaw,
				currentPitch,
				cx,
				cy,
				fov
			);

			currentNodes.push( {
				id: node.id,
				label: node.label,
				index: i,
				x: proj.x,
				y: proj.y,
				z: proj.z,
				scale: proj.scale,
			} );
		}

		this.projected = currentNodes.map( function ( n ) {
			return { id: n.id, x: n.x, y: n.y, z: n.z, scale: n.scale };
		} );

		// Back to front by depth.
		const sortedNodes = currentNodes.slice().sort( function ( a, b ) {
			return b.z - a.z;
		} );

		// Inter node architectural mesh, connecting each node to the next.
		for ( i = 0; i < currentNodes.length; i++ ) {
			const n1 = currentNodes[ i ];
			const n2 = currentNodes[ ( i + 1 ) % currentNodes.length ];

			ctx.beginPath();
			ctx.moveTo( n1.x, n1.y );
			ctx.lineTo( n2.x, n2.y );
			ctx.strokeStyle = 'rgba(255, 253, 244, 0.08)';
			ctx.lineWidth = 1;
			ctx.stroke();
		}

		const self = this;

		sortedNodes.forEach( function ( node ) {
			const isSelected = node.id === self.selectedNodeId;
			const isHovered = node.id === self.hoveredNodeId;
			const isHighlighted = isSelected || isHovered;

			// Connection line to the central core.
			ctx.beginPath();
			ctx.moveTo( centerProjected.x, centerProjected.y );
			ctx.lineTo( node.x, node.y );
			ctx.strokeStyle = isHighlighted
				? 'rgba(250, 204, 21, 0.85)'
				: 'rgba(216, 213, 202, 0.18)';
			ctx.lineWidth = isHighlighted ? 1.6 : 1;
			ctx.stroke();

			// Signal packet travelling along the vector.
			if ( animating || isHighlighted ) {
				const t = isHighlighted
					? ( self.pulseTime * 1.4 + node.index * 0.15 ) % 1
					: ( self.pulseTime * 0.7 + node.index * 0.25 ) % 1;
				const px =
					centerProjected.x + ( node.x - centerProjected.x ) * t;
				const py =
					centerProjected.y + ( node.y - centerProjected.y ) * t;

				ctx.beginPath();
				ctx.arc( px, py, isHighlighted ? 3 : 2, 0, Math.PI * 2 );
				ctx.fillStyle = isHighlighted
					? '#FACC15'
					: 'rgba(250, 204, 21, 0.55)';
				ctx.fill();
			}

			// Node architectural marker.
			const nodeRadius =
				( isHighlighted ? 8 : 5.5 ) * Math.max( 0.75, node.scale );

			ctx.beginPath();
			ctx.arc( node.x, node.y, nodeRadius + 4, 0, Math.PI * 2 );
			ctx.fillStyle = isHighlighted
				? 'rgba(250, 204, 21, 0.18)'
				: 'rgba(10, 10, 11, 0.7)';
			ctx.fill();
			ctx.strokeStyle = isHighlighted
				? '#FACC15'
				: 'rgba(255, 253, 244, 0.32)';
			ctx.lineWidth = isHighlighted ? 1.5 : 1;
			ctx.stroke();

			ctx.beginPath();
			ctx.arc( node.x, node.y, nodeRadius * 0.55, 0, Math.PI * 2 );
			if ( isHighlighted ) {
				ctx.fillStyle = '#FACC15';
			} else if ( node.id === RANK_KERNEL_ID ) {
				ctx.fillStyle = '#FDE047';
			} else {
				ctx.fillStyle = '#FFFDF4';
			}
			ctx.fill();

			// Crisp node label in 3D space.
			ctx.font =
				( isHighlighted ? '600' : '500' ) +
				' 11px "IBM Plex Mono", monospace';
			const labelText = node.label;
			const textWidth = ctx.measureText( labelText ).width;
			const labelX = node.x - textWidth / 2;
			const labelY = node.y - nodeRadius - 12;

			ctx.fillStyle = isHighlighted
				? 'rgba(10, 10, 11, 0.92)'
				: 'rgba(17, 17, 19, 0.82)';
			ctx.fillRect( labelX - 6, labelY - 11, textWidth + 12, 16 );
			ctx.strokeStyle = isHighlighted
				? '#FACC15'
				: 'rgba(52, 52, 52, 0.8)';
			ctx.lineWidth = 1;
			ctx.strokeRect( labelX - 6, labelY - 11, textWidth + 12, 16 );

			ctx.fillStyle = isHighlighted ? '#FACC15' : '#FFFDF4';
			ctx.fillText( labelText, labelX, labelY + 1 );
		} );

		// Central core badge label.
		ctx.fillStyle = 'rgba(10, 10, 11, 0.9)';
		ctx.strokeStyle = '#FACC15';
		ctx.lineWidth = 1.2;
		const coreLabel = 'MAULIK.CORE';

		ctx.font = '600 11px "IBM Plex Mono", monospace';
		const cw = ctx.measureText( coreLabel ).width;

		ctx.fillRect(
			centerProjected.x - cw / 2 - 8,
			centerProjected.y - 9,
			cw + 16,
			18
		);
		ctx.strokeRect(
			centerProjected.x - cw / 2 - 8,
			centerProjected.y - 9,
			cw + 16,
			18
		);
		ctx.fillStyle = '#FACC15';
		ctx.fillText(
			coreLabel,
			centerProjected.x - cw / 2,
			centerProjected.y + 4
		);
	};

	/**
	 * Track the pointer and highlight the closest node within the hover radius.
	 *
	 * @param {PointerEvent} event Pointer event.
	 * @return {void}
	 */
	HeroEcosystem.prototype.handlePointerMove = function ( event ) {
		const rect = this.container.getBoundingClientRect();

		if ( ! rect.width || ! rect.height ) {
			return;
		}

		const relX = event.clientX - rect.left;
		const relY = event.clientY - rect.top;

		this.pointer.x = ( relX / rect.width - 0.5 ) * 2;
		this.pointer.y = ( relY / rect.height - 0.5 ) * 2;
		this.pointer.active = true;

		let closest = null;
		let minDist = HOVER_RADIUS;

		for ( let i = 0; i < this.projected.length; i++ ) {
			const node = this.projected[ i ];
			const dx = node.x - relX;
			const dy = node.y - relY;
			const dist = Math.sqrt( dx * dx + dy * dy );

			if ( dist < minDist ) {
				minDist = dist;
				closest = node.id;
			}
		}

		if ( closest !== this.hoveredNodeId ) {
			this.hoveredNodeId = closest;
			this.syncReadout();
			this.scheduleRender();
		}
	};

	/**
	 * Keyboard equivalent of the node tabs, so the block is operable without a
	 * pointer and the hovered node is never the only way to change selection.
	 *
	 * @param {KeyboardEvent} event Keyboard event.
	 * @return {void}
	 */
	HeroEcosystem.prototype.handleKeyDown = function ( event ) {
		if ( event.key !== 'ArrowRight' && event.key !== 'ArrowLeft' ) {
			return;
		}

		const current = this.nodes.findIndex(
			function ( node ) {
				return node.id === this.selectedNodeId;
			}.bind( this )
		);

		if ( current < 0 ) {
			return;
		}

		const step = event.key === 'ArrowRight' ? 1 : -1;
		const next = ( current + step + this.nodes.length ) % this.nodes.length;

		this.selectedNodeId = this.nodes[ next ].id;
		this.syncReadout();
		this.scheduleRender();

		event.preventDefault();
	};

	/**
	 * Reflect the active node into the tablist and the readout, so the server
	 * rendered markup and the canvas never disagree about what is selected.
	 *
	 * @return {void}
	 */
	HeroEcosystem.prototype.syncReadout = function () {
		const activeId = this.hoveredNodeId || this.selectedNodeId;
		const tabs = this.container.querySelectorAll(
			'[data-maulik-hero-node]'
		);
		let i;

		for ( i = 0; i < tabs.length; i++ ) {
			const isActive =
				tabs[ i ].getAttribute( 'data-maulik-hero-node' ) === activeId;

			tabs[ i ].setAttribute(
				'aria-selected',
				isActive ? 'true' : 'false'
			);
		}

		const details = this.findNodeDetails( activeId );

		if ( ! details ) {
			return;
		}

		this.writeText( '[data-maulik-hero-readout-title]', details.title );
		this.writeText( '[data-maulik-hero-readout-summary]', details.summary );
		this.writeText( '[data-maulik-hero-readout-metrics]', details.metrics );

		this.syncInspect( details );
	};

	/**
	 * Show or hide the inspect link for the active node.
	 *
	 * The link is built with createElement and textContent rather than
	 * innerHTML, so nothing from the data attribute is ever parsed as markup.
	 *
	 * @param {{inspectUrl: string, inspectText: string}} details Active node details.
	 * @return {void}
	 */
	HeroEcosystem.prototype.syncInspect = function ( details ) {
		const slot = this.container.querySelector(
			'[data-maulik-hero-inspect-slot]'
		);

		if ( ! slot ) {
			return;
		}

		const existing = slot.querySelector( '[data-maulik-hero-inspect]' );

		if ( ! details.inspectUrl || ! details.inspectText ) {
			if ( existing && existing.parentNode ) {
				existing.parentNode.removeChild( existing );
			}

			return;
		}

		if ( existing ) {
			existing.setAttribute( 'href', details.inspectUrl );
			existing.textContent = details.inspectText;
			return;
		}

		const link = document.createElement( 'a' );

		link.className = 'maulik-hero-inspect';
		link.setAttribute( 'data-maulik-hero-inspect', '' );
		link.setAttribute( 'href', details.inspectUrl );
		link.textContent = details.inspectText;
		slot.appendChild( link );
	};

	/**
	 * Write text into the first element matching a selector, if it exists.
	 *
	 * @param {string} selector CSS selector inside the block.
	 * @param {string} text     Text to write.
	 * @return {void}
	 */
	HeroEcosystem.prototype.writeText = function ( selector, text ) {
		const element = this.container.querySelector( selector );

		if ( element && text ) {
			element.textContent = text;
		}
	};

	/**
	 * Look up the server rendered copy for a node id, falling back to the copy
	 * captured at initialisation for the nodes that were not pre-rendered.
	 *
	 * @param {string} id Node id.
	 * @return {{title: string, summary: string, metrics: string, inspect: string}|null} Details or null.
	 */
	HeroEcosystem.prototype.findNodeDetails = function ( id ) {
		return this.nodeDetails[ id ] || null;
	};

	/**
	 * Keep the pause button label in step with the paused state.
	 *
	 * @return {void}
	 */
	HeroEcosystem.prototype.syncToggleLabel = function () {
		const toggle = this.container.querySelector(
			'[data-maulik-hero-toggle]'
		);
		const label = this.container.querySelector(
			'[data-maulik-hero-toggle-label]'
		);

		if ( ! toggle || ! label ) {
			return;
		}

		if ( this.isPaused ) {
			label.textContent = 'Resume Orbit';
			toggle.setAttribute(
				'aria-label',
				'Resume the 3D ecosystem rotation'
			);
		} else {
			label.textContent = 'Pause Orbit';
			toggle.setAttribute(
				'aria-label',
				'Pause the 3D ecosystem rotation'
			);
		}
	};

	/**
	 * Tear the instance down. Cancels the animation frame, disconnects both
	 * observers, and removes every listener it added. Safe to call twice.
	 *
	 * @return {void}
	 */
	HeroEcosystem.prototype.destroy = function () {
		const self = this;

		if ( this.destroyed ) {
			return;
		}

		this.destroyed = true;

		if ( this.frameId ) {
			window.cancelAnimationFrame( this.frameId );
			this.frameId = 0;
		}

		if ( this.resizeFrameId ) {
			window.cancelAnimationFrame( this.resizeFrameId );
			this.resizeFrameId = 0;
		}

		if ( this.resizeObserver ) {
			this.resizeObserver.disconnect();
			this.resizeObserver = null;
		}

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

		if ( this.onPointerMove ) {
			this.container.removeEventListener(
				'pointermove',
				this.onPointerMove
			);
		}

		if ( this.onPointerLeave ) {
			this.container.removeEventListener(
				'pointerleave',
				this.onPointerLeave
			);
		}

		if ( this.onKeyDown ) {
			this.container.removeEventListener( 'keydown', this.onKeyDown );
		}

		if ( this.canvas && this.onCanvasClick ) {
			this.canvas.removeEventListener( 'click', this.onCanvasClick );
		}

		if ( this.onToggleClick ) {
			this.detachControls();
		}

		self.ctx = null;
		self.projected = [];
	};

	/**
	 * Remove the click listeners added to the control buttons.
	 *
	 * @return {void}
	 */
	HeroEcosystem.prototype.detachControls = function () {
		const toggle = this.container.querySelector(
			'[data-maulik-hero-toggle]'
		);
		const reset = this.container.querySelector(
			'[data-maulik-hero-reset]'
		);
		const tabs = this.container.querySelectorAll(
			'[data-maulik-hero-node]'
		);
		let i;

		if ( toggle ) {
			toggle.removeEventListener( 'click', this.onToggleClick );
		}

		if ( reset ) {
			reset.removeEventListener( 'click', this.onResetClick );
		}

		for ( i = 0; i < tabs.length; i++ ) {
			tabs[ i ].replaceWith( tabs[ i ].cloneNode( true ) );
		}
	};

	/**
	 * Initialise every hero block on the page exactly once.
	 *
	 * @return {void}
	 */
	function initHeroEcosystems() {
		const containers = document.querySelectorAll(
			'[data-maulik-hero-ecosystem]'
		);

		for ( let i = 0; i < containers.length; i++ ) {
			const container = containers[ i ];

			if (
				container.getAttribute( 'data-maulik-hero-ready' ) === 'yes'
			) {
				continue;
			}

			container.setAttribute( 'data-maulik-hero-ready', 'yes' );

			try {
				new HeroEcosystem( container );
			} catch {
				// Never let one broken block break the page.
				container.setAttribute( 'data-maulik-hero-error', 'true' );
			}
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initHeroEcosystems );
	} else {
		initHeroEcosystems();
	}

	window.maulikHeroEcosystem = {
		init: initHeroEcosystems,
		HeroEcosystem,
	};
} )();
