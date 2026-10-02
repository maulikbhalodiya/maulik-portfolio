<?php
/**
 * Registration and server side render for the hero interactive ecosystem block.
 *
 * The homepage hero is a twelve column grid. The left six columns hold the
 * name, the subhead and the calls to action, and the right six columns hold
 * this block: a two dimensional canvas that draws a projected three
 * dimensional node graph of the engineering stack, with hover highlighting
 * and click to select navigation.
 *
 * The drawing itself is a 2D canvas. It uses arc(), moveTo(), lineTo(),
 * stroke(), fill(), globalAlpha and requestAnimationFrame. There is no
 * WebGL, no Three.js and no 3D dependency, and none is introduced here.
 *
 * Two facts from docs/RESEARCH-02-BLOCK-THEME.md drive the structure of this
 * file. Section 9 states that server side registration is mandatory for a
 * block to be styled through theme.json, and states that these four canvas
 * visuals are presentation rather than reusable functionality, so they belong
 * in the theme. Lines 8 to 37 state that themes get no automatic block
 * registration from a blocks directory, so the registration below is a manual
 * register_block_type() call rather than something a directory scan would do.
 *
 * The block is dynamic and server rendered. It has no save() output, and the
 * view script is attached through the block's own view_script_handles, which
 * means WordPress enqueues it only on pages where this block actually renders.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'MAULIK_PORTFOLIO_HERO_ECOSYSTEM_BLOCK' ) ) {
	/**
	 * Block name. Kept as a constant so the render callback, the asset
	 * handles and the wrapper class can never drift apart.
	 */
	define( 'MAULIK_PORTFOLIO_HERO_ECOSYSTEM_BLOCK', 'maulik-portfolio/hero-ecosystem' );

	/**
	 * View script handle for the canvas.
	 */
	define( 'MAULIK_PORTFOLIO_HERO_ECOSYSTEM_HANDLE', 'maulik-portfolio-hero-ecosystem' );
}

if ( ! function_exists( 'maulik_portfolio_hero_ecosystem_nodes' ) ) {
	/**
	 * The orbital node set drawn by the hero canvas.
	 *
	 * Ported field for field from HERO_3D_NODES in the design reference at
	 * src/data/portfolioData.ts, lines 143 to 224. orbitRadius, orbitAngle and
	 * elevation are the layout maths consumed by the projection in the view
	 * script, so they are reproduced exactly rather than approximated.
	 *
	 * The label, category, summary and metrics are rendered server side so the
	 * inspector readout and the accessible node tabs carry real content before
	 * any JavaScript runs.
	 *
	 * @since 0.3.0
	 *
	 * @return array<int, array<string, mixed>> Ordered list of node definitions.
	 */
	function maulik_portfolio_hero_ecosystem_nodes() {
		return array(
			array(
				'id'           => 'wordpress',
				'label'        => 'WordPress',
				'category'     => 'Core Platform',
				'orbitRadius'  => 185,
				'orbitAngle'   => 0,
				'elevation'    => 30,
				'summary'      => 'Custom plugin architecture, hooks system, REST endpoints, Gutenberg blocks and WooCommerce extensions.',
				'metrics'      => 'Plugins · Hooks · REST API · Gutenberg',
				'inspectLabel' => '',
				'inspectUrl'   => '',
			),
			array(
				'id'           => 'php',
				'label'        => 'PHP',
				'category'     => 'Backend Runtime',
				'orbitRadius'  => 195,
				'orbitAngle'   => 45,
				'elevation'    => -25,
				'summary'      => 'Object oriented PHP, PSR 4 autoloading, Composer dependency management and server side data processing.',
				'metrics'      => 'OOP · Composer · PSR 4 · Server Processing',
				'inspectLabel' => '',
				'inspectUrl'   => '',
			),
			array(
				'id'           => 'plugin',
				'label'        => 'Plugin',
				'category'     => 'Modular Systems',
				'orbitRadius'  => 170,
				'orbitAngle'   => 90,
				'elevation'    => 45,
				'summary'      => 'Isolated business logic built as maintainable WordPress plugins with clean activation lifecycles and capability checks.',
				'metrics'      => 'Modular Architecture · Custom Tables · Roles',
				'inspectLabel' => '',
				'inspectUrl'   => '',
			),
			array(
				'id'           => 'api',
				'label'        => 'API',
				'category'     => 'Integration Layer',
				'orbitRadius'  => 205,
				'orbitAngle'   => 135,
				'elevation'    => -15,
				'summary'      => 'Authenticated REST APIs, webhook listeners, asynchronous polling workflows and third party service bridges.',
				'metrics'      => 'REST · Webhooks · cURL · JSON · Polling',
				'inspectLabel' => '',
				'inspectUrl'   => '',
			),
			array(
				'id'           => 'database',
				'label'        => 'Database',
				'category'     => 'Persistence',
				'orbitRadius'  => 175,
				'orbitAngle'   => 180,
				'elevation'    => -40,
				'summary'      => 'MySQL schema design, custom WordPress tables, prepared queries and structured metadata indexing.',
				'metrics'      => 'MySQL · Custom Tables · Prepared Queries',
				'inspectLabel' => '',
				'inspectUrl'   => '',
			),
			array(
				'id'           => 'security',
				'label'        => 'Security',
				'category'     => 'Verification and Crypto',
				'orbitRadius'  => 200,
				'orbitAngle'   => 225,
				'elevation'    => 20,
				'summary'      => 'HMAC SHA 256 request signing, PHP Sodium encryption, nonce verification, capability enforcement and escaping.',
				'metrics'      => 'HMAC SHA 256 · Sodium · Nonces · Capabilities',
				'inspectLabel' => '',
				'inspectUrl'   => '',
			),
			array(
				'id'           => 'git',
				'label'        => 'Git',
				'category'     => 'Engineering Workflow',
				'orbitRadius'  => 165,
				'orbitAngle'   => 270,
				'elevation'    => -30,
				'summary'      => 'Version controlled plugin development, structured branching, code review readiness and open source workflows.',
				'metrics'      => 'Git · Version Control · Modular Releases',
				'inspectLabel' => '',
				'inspectUrl'   => '',
			),
			array(
				'id'           => 'rankkernel',
				'label'        => 'RankKernel',
				'category'     => 'Independent Open Source',
				'orbitRadius'  => 215,
				'orbitAngle'   => 315,
				'elevation'    => 35,
				'summary'      => 'Independently developed open source SEO and schema engine for WordPress currently in active development.',
				'metrics'      => 'Personal Project · Open Source · In Development',
				'inspectLabel' => 'Inspect RankKernel',
				'inspectUrl'   => home_url( '/rankkernel/' ),
			),
		);
	}
}

if ( ! function_exists( 'maulik_portfolio_hero_ecosystem_css' ) ) {
	/**
	 * The block stylesheet, emitted by the render callback.
	 *
	 * This theme's styles live in assets/styles and are compiled to
	 * assets/css/theme.css, and this block is not allowed to add a Sass
	 * partial there. A block that cannot ship a stylesheet still needs
	 * presentation, so the rules are emitted as a scoped style element from
	 * the render callback instead. Every selector is scoped under the block
	 * wrapper class, so nothing here can leak into the rest of the page.
	 *
	 * Values are taken from the class names in the design reference component
	 * at src/components/Hero3DEcosystem.tsx, lines 373 to 465. There is no
	 * border radius and no box shadow anywhere, because the design has none.
	 *
	 * @since 0.3.0
	 *
	 * @return string CSS text without any surrounding style element.
	 */
	function maulik_portfolio_hero_ecosystem_css() {
		$scope = '.wp-block-maulik-portfolio-hero-ecosystem';

		// min-width:0 is load bearing and is not optional styling. The panel is a
		// grid item descendant and its header is nowrap by design, so without an
		// explicit zero the panel contributes a min-content width large enough to
		// push the hero's right hand column past the viewport. max-width:100% on
		// the canvas is the same guard one level further in, so no pixel
		// dimension inside the panel can reassert a minimum on any ancestor.
		return $scope . '{position:relative;width:100%;min-width:0;max-width:100%;height:500px;'
			. 'display:flex;flex-direction:column;'
			. 'justify-content:space-between;overflow:hidden;box-sizing:border-box;'
			. 'background:#111113;border:1px solid #262626;color:#D8D5CA;'
			. 'font-family:"IBM Plex Mono",ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;'
			. 'font-size:12px;line-height:1.5;-webkit-user-select:none;user-select:none;}'
			. '@media (min-width:1024px){' . $scope . '{height:580px}}'
			. $scope . ',' . $scope . ' *{box-sizing:border-box;}'
			. $scope . ' .maulik-hero-header{position:relative;z-index:10;display:flex;align-items:center;'
			. 'justify-content:space-between;gap:16px;padding:12px 16px;border-bottom:1px solid #262626;'
			. 'background:rgba(10,10,11,0.8);-webkit-backdrop-filter:blur(4px);backdrop-filter:blur(4px);'
			. 'color:#D8D5CA;white-space:nowrap;}'
			. $scope . ' .maulik-hero-header-title{display:flex;align-items:center;gap:8px;overflow:hidden;'
			. 'text-overflow:ellipsis;}'
			. $scope . ' .maulik-hero-header-dot{width:8px;height:8px;flex:0 0 8px;display:inline-block;'
			. 'background:#FACC15;}'
			. $scope . ' .maulik-hero-header-actions{display:flex;align-items:center;gap:8px;flex:0 0 auto;}'
			. $scope . ' .maulik-hero-button{padding:5px 10px;border:1px solid #262626;background:#171719;'
			. 'color:#D8D5CA;font:inherit;line-height:1.4;cursor:pointer;white-space:nowrap;'
			. 'display:inline-flex;align-items:center;gap:6px;transition:color .15s ease,border-color .15s ease;}'
			. $scope . ' .maulik-hero-button:hover{border-color:#77746C;color:#FACC15;}'
			. $scope . ' .maulik-hero-button:focus-visible{outline:2px solid #FACC15;outline-offset:1px;}'
			. $scope . ' .maulik-hero-button-icon{padding:6px;}'
			. $scope . ' .maulik-hero-button-icon svg{width:14px;height:14px;display:block;fill:none;'
			. 'stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}'
			// The Pause Orbit control measured zero by zero on the live site.
			//
			// Two reasons, and they are the same reason the content arrows were
			// rendering at 120px. The svg carries a viewBox and neither a width nor
			// a height, so it has no intrinsic box and as a flex item in a nowrap
			// row it collapsed to nothing. And unlike every other icon in this
			// panel it also carried no fill and no stroke, so its two paths painted
			// nothing even once a box existed.
			//
			// 12px is Hero3DEcosystem.tsx:388, which gives the Pause and Play marks
			// w-3 h-3. The two buttons beside each other in the design are gap-1.5,
			// 6px, which the button rule above already states.
			//
			// The header is named in the selector so this rule outranks the 16px
			// floor in _icon.scss on its own specificity, and :not(.maulik-hero-
			// button-icon) keeps it off the reset control beside it, which has its
			// own 14px above and its own design value, w-3.5 h-3.5 at
			// Hero3DEcosystem.tsx:400. Without the :not, this rule outranked that
			// one as well and the reset mark came out at 12px, which was measured
			// before it was added.
			. $scope . ' .maulik-hero-header .maulik-hero-button:not(.maulik-hero-button-icon)'
			. ' svg{width:12px;height:12px;'
			. 'display:block;flex-shrink:0;vertical-align:middle;fill:none;'
			. 'stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}'
			. $scope . ' .maulik-hero-canvas{position:absolute;top:0;right:0;bottom:0;left:0;z-index:1;'
			. 'display:block;width:100%;max-width:100%;height:100%;}'
			. $scope . ' .maulik-hero-footer{position:relative;z-index:10;margin-top:auto;'
			. 'border-top:1px solid #262626;background:rgba(10,10,11,0.92);'
			. '-webkit-backdrop-filter:blur(12px);backdrop-filter:blur(12px);padding:16px;'
			. 'display:flex;flex-direction:column;gap:12px;}'
			. $scope . ' .maulik-hero-tabs{display:flex;align-items:center;gap:6px;overflow-x:auto;'
			. 'padding-bottom:4px;}'
			// The tab label is text-xs in Hero3DEcosystem.tsx, which is 12px on a
			// 16px line, so the leading is 1.3333 and not the 1.4 that put it at
			// 16.8px. Nothing else in the panel changes: the canvas, the node data,
			// the selection behaviour and the teardown are all below or above this
			// one declaration and none of them read it.
			. $scope . ' .maulik-hero-tab{padding:5px 10px;border:1px solid #262626;background:#171719;'
			. 'color:#D8D5CA;font:inherit;line-height:1.3333;cursor:pointer;white-space:nowrap;'
			. 'flex:0 0 auto;transition:color .15s ease,border-color .15s ease;}'
			. $scope . ' .maulik-hero-tab:hover{border-color:#77746C;color:#FFFDF4;}'
			. $scope . ' .maulik-hero-tab:focus-visible{outline:2px solid #FACC15;outline-offset:1px;}'
			. $scope . ' .maulik-hero-tab[aria-selected="true"]{background:#FACC15;border-color:#FACC15;'
			. 'color:#000;font-weight:600;}'
			. $scope . ' .maulik-hero-readout{display:flex;flex-direction:column;gap:12px;padding-top:4px;}'
			. '@media (min-width:640px){' . $scope . ' .maulik-hero-readout{flex-direction:row;'
			. 'align-items:center;justify-content:space-between;}}'
			. $scope . ' .maulik-hero-readout-title{color:#FACC15;}'
			. $scope . ' .maulik-hero-readout-summary{margin:4px 0 0;max-width:576px;color:#D8D5CA;'
			. '-webkit-user-select:text;user-select:text;}'
			. $scope . ' .maulik-hero-readout-metrics{margin:4px 0 0;color:#77746C;'
			. '-webkit-user-select:text;user-select:text;}'
			. $scope . ' .maulik-hero-inspect{padding:6px 12px;border:1px solid rgba(250,204,21,0.5);'
			. 'background:#171719;color:#FACC15;font:inherit;line-height:1.4;cursor:pointer;'
			. 'white-space:nowrap;flex:0 0 auto;align-self:flex-start;text-decoration:none;'
			. 'transition:background .15s ease,color .15s ease;}'
			. $scope . ' .maulik-hero-inspect:hover{background:#FACC15;color:#000;}'
			. $scope . ' .maulik-hero-inspect:focus-visible{outline:2px solid #FACC15;outline-offset:1px;}'
			. $scope . ' .maulik-hero-noscript{display:block;position:relative;z-index:5;'
			. 'padding:16px;color:#D8D5CA;overflow-y:auto;max-height:100%;}'
			. $scope . ' .maulik-hero-noscript p{margin:0;}'
			. $scope . ' .maulik-hero-inventory{margin:12px 0 0;padding:0;list-style:none;'
			. 'display:flex;flex-direction:column;gap:8px;}'
			. $scope . ' .maulik-hero-inventory li{display:flex;flex-direction:column;gap:2px;'
			. 'padding:8px 10px;border:1px solid #262626;background:#171719;color:#D8D5CA;}'
			. $scope . ' .maulik-hero-inventory span:first-child{color:#FACC15;}';
	}
}

if ( ! function_exists( 'maulik_portfolio_hero_ecosystem_render' ) ) {
	/**
	 * Render the hero interactive ecosystem block.
	 *
	 * Returns a string rather than echoing. Echoing here would put the markup
	 * through the escape output sniff for no benefit, and returning is what the
	 * block render callback contract expects.
	 *
	 * Everything a reader needs is present in this markup before any script
	 * runs: the coordinate header, the node tabs as a real tablist, and the
	 * inspector readout for the selected node. If JavaScript is disabled, or
	 * the browser has no 2D canvas context, what remains is a labelled panel
	 * with the full node inventory, which is a degraded result rather than an
	 * empty box.
	 *
	 * @since 0.3.0
	 *
	 * @param array<string, mixed> $attributes Block attributes. Only
	 *                                         selectedNode is used.
	 * @param string               $content    Inner block content. Unused, the
	 *                                         block has no inner blocks.
	 * @return string Rendered block HTML.
	 */
	function maulik_portfolio_hero_ecosystem_render( $attributes = array(), $content = '' ) {
		unset( $content );

		$nodes = maulik_portfolio_hero_ecosystem_nodes();

		if ( array() === $nodes ) {
			return '';
		}

		$selected = isset( $attributes['selectedNode'] ) ? (string) $attributes['selectedNode'] : $nodes[0]['id'];
		$active   = $nodes[0];

		foreach ( $nodes as $node ) {
			if ( $node['id'] === $selected ) {
				$active = $node;
				break;
			}
		}

		$wrapper_attributes = get_block_wrapper_attributes();

		$payload = array();

		foreach ( $nodes as $node ) {
			$payload[] = array(
				'id'          => $node['id'],
				'label'       => $node['label'],
				'orbitRadius' => (float) $node['orbitRadius'],
				'orbitAngle'  => (float) $node['orbitAngle'],
				'elevation'   => (float) $node['elevation'],
			);
		}

		$details = array();

		foreach ( $nodes as $node ) {
			$details[ $node['id'] ] = array(
				'title'       => $node['label'] . ' · ' . $node['category'],
				'summary'     => $node['summary'],
				'metrics'     => $node['metrics'],
				'inspectUrl'  => (string) $node['inspectUrl'],
				'inspectText' => (string) $node['inspectLabel'],
			);
		}

		$tabs = '';

		foreach ( $nodes as $node ) {
			$is_active   = ( $node['id'] === $active['id'] );
			$tab_classes = $is_active
				? 'maulik-hero-tab is-active'
				: 'maulik-hero-tab';

			$tabs .= sprintf(
				'<button type="button" role="tab" id="maulik-hero-tab-%1$s" aria-controls="maulik-hero-panel" aria-selected="%2$s" class="%3$s" data-maulik-hero-node="%1$s">%4$s</button>',
				esc_attr( $node['id'] ),
				$is_active ? 'true' : 'false',
				esc_attr( $tab_classes ),
				esc_html( $node['label'] )
			);
		}

		$inspect = '';

		if ( '' !== (string) $active['inspectUrl'] ) {
			$inspect = sprintf(
				'<a class="maulik-hero-inspect" data-maulik-hero-inspect href="%1$s">%2$s</a>',
				esc_url( (string) $active['inspectUrl'] ),
				esc_html( (string) $active['inspectLabel'] )
			);
		}

		$canvas_label = __( 'Interactive spatial map of the engineering ecosystem: WordPress, PHP, Plugin, API, Database, Security, Git and RankKernel.', 'maulik-portfolio' );

		$header = sprintf(
			'<div class="maulik-hero-header">'
				. '<p class="maulik-hero-header-title"><span class="maulik-hero-header-dot" aria-hidden="true"></span>'
				. '<span>%1$s</span></p>'
				. '<div class="maulik-hero-header-actions">'
				. '<button type="button" class="maulik-hero-button" data-maulik-hero-toggle aria-label="%2$s">'
				. '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 4h4v16H6zM14 4h4v16h-4z"></path></svg>'
				. '<span data-maulik-hero-toggle-label>%3$s</span></button>'
				. '<button type="button" class="maulik-hero-button maulik-hero-button-icon" data-maulik-hero-reset aria-label="%4$s" title="%5$s">'
				. '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path><path d="M3 3v5h5"></path></svg>'
				. '</button>'
				. '</div>'
				. '</div>',
			esc_html__( 'Engineering Ecosystem, 3D Spatial Architecture', 'maulik-portfolio' ),
			esc_attr__( 'Pause the 3D ecosystem rotation', 'maulik-portfolio' ),
			esc_html__( 'Pause Orbit', 'maulik-portfolio' ),
			esc_attr__( 'Reset the 3D view orientation', 'maulik-portfolio' ),
			esc_attr__( 'Reset Orientation', 'maulik-portfolio' )
		);

		$footer = sprintf(
			'<div class="maulik-hero-footer">'
				. '<div class="maulik-hero-tabs" role="tablist" aria-label="%1$s">%2$s</div>'
				. '<div class="maulik-hero-readout" id="maulik-hero-panel" role="tabpanel" aria-live="polite">'
				. '<div><p class="maulik-hero-readout-title" data-maulik-hero-readout-title>%3$s</p>'
				. '<p class="maulik-hero-readout-summary" data-maulik-hero-readout-summary>%4$s</p>'
				. '<p class="maulik-hero-readout-metrics" data-maulik-hero-readout-metrics>%5$s</p></div>'
				. '<div data-maulik-hero-inspect-slot>%6$s</div>'
				. '</div></div>',
			esc_attr__( 'Inspect engineering ecosystem nodes', 'maulik-portfolio' ),
			$tabs,
			esc_html( $active['label'] . ' · ' . $active['category'] ),
			esc_html( $active['summary'] ),
			esc_html( $active['metrics'] ),
			$inspect
		);

		/*
		 * The inventory lives inside noscript so that it is part of the raw text
		 * node when scripting is enabled and is therefore never parsed into the
		 * live DOM. With scripting disabled it renders in place of the canvas, so
		 * the block degrades to a readable list of the same node set rather than
		 * an empty frame.
		 */
		$inventory = '<ul class="maulik-hero-inventory">';

		foreach ( $nodes as $node ) {
			$inventory .= sprintf(
				'<li><span>%1$s</span> <span>%2$s</span></li>',
				esc_html( $node['label'] ),
				esc_html( $node['summary'] )
			);
		}

		$inventory .= '</ul>';

		$noscript = sprintf(
			'<noscript><div class="maulik-hero-noscript"><p>%1$s</p>%2$s</div></noscript>',
			esc_html__( 'The interactive spatial map needs JavaScript. The engineering stack it maps is listed below.', 'maulik-portfolio' ),
			$inventory
		);

		return '<style>' . maulik_portfolio_hero_ecosystem_css() . '</style>'
			. '<div ' . $wrapper_attributes . ' data-maulik-hero-ecosystem'
			. ' data-maulik-hero-nodes="' . esc_attr( (string) wp_json_encode( $payload ) ) . '"'
			. ' data-maulik-hero-details="' . esc_attr( (string) wp_json_encode( $details ) ) . '">'
			. $header
			. '<canvas class="maulik-hero-canvas" data-maulik-hero-canvas role="img" aria-label="' . esc_attr( $canvas_label ) . '"></canvas>'
			. $noscript
			. $footer
			. '</div>';
	}
}

if ( ! function_exists( 'maulik_portfolio_hero_ecosystem_register' ) ) {
	/**
	 * Register the hero interactive ecosystem block and its view script.
	 *
	 * The script is registered as a block view script rather than enqueued on
	 * wp_enqueue_scripts, so WordPress only prints it on pages where the block
	 * actually rendered. Pages without the hero never download the canvas code.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	function maulik_portfolio_hero_ecosystem_register() {
		$script_relative = 'assets/js/hero-ecosystem.js';

		$version = defined( 'MAULIK_PORTFOLIO_VERSION' ) ? (string) MAULIK_PORTFOLIO_VERSION : '0.0.0';

		if ( function_exists( 'maulik_portfolio_asset_version' ) ) {
			$version = maulik_portfolio_asset_version( $script_relative );
		}

		wp_register_script(
			MAULIK_PORTFOLIO_HERO_ECOSYSTEM_HANDLE,
			get_parent_theme_file_uri( $script_relative ),
			array(),
			$version,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);

		register_block_type(
			MAULIK_PORTFOLIO_HERO_ECOSYSTEM_BLOCK,
			array(
				'api_version'         => 3,
				'title'               => __( 'Hero Interactive Ecosystem', 'maulik-portfolio' ),
				'category'            => 'design',
				'icon'                => 'art',
				'description'         => __( 'A two dimensional canvas rendering of the engineering ecosystem, with hover highlighting and click to select node navigation.', 'maulik-portfolio' ),
				'keywords'            => array( 'hero', 'canvas', 'ecosystem', 'skills' ),
				'textdomain'          => 'maulik-portfolio',
				'attributes'          => array(
					'selectedNode' => array(
						'type'    => 'string',
						'default' => 'wordpress',
					),
				),
				'supports'            => array(
					'html'      => false,
					'align'     => array( 'wide', 'full' ),
					'spacing'   => array(
						'margin'  => true,
						'padding' => false,
					),
					'className' => true,
				),
				'view_script_handles' => array( MAULIK_PORTFOLIO_HERO_ECOSYSTEM_HANDLE ),
				'render_callback'     => 'maulik_portfolio_hero_ecosystem_render',
			)
		);
	}
}
add_action( 'init', 'maulik_portfolio_hero_ecosystem_register' );
