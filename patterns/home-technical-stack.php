<?php
/**
 * Title: Home Technical Stack Section
 * Slug: maulik-portfolio/home-technical-stack
 * Categories: maulik-portfolio-home
 * Description: Homepage section 03, the node map and the seven verified domains with all forty one skill entries, on the 48px dark grid surface.
 * Inserter: yes
 *
 * Section 03 of 08. The 48px dark grid surface, which is what the design states.
 * It previously wore the 32px dark subgrid variation, a texture the design does
 * not use on this section at all.
 *
 * FORTY ONE SKILL ENTRIES, AND THAT IS THE DESIGN'S OWN COUNT. The previous
 * version of this pattern rendered seven system descriptions and no individual
 * skill, so the section claimed a granularity it did not have. The design
 * enumerates forty one entries across the seven domains: six, six, six, five,
 * six, six, five. All forty one are here.
 *
 * NO LEVEL, NO WEIGHT AND NO PERCENTAGE. The design attaches none to any entry
 * and neither does this. Every entry carries a name, a context sentence that
 * says where the technology is actually used, and an index. Putting a relevance
 * figure on any of them would put a number on the page that nothing in the
 * source data supports.
 *
 * THE NODE MAP IS SERVER RENDERED SVG, NOT A SCRIPT. The design draws a radial
 * graph with a MAULIK node at the centre and seven domain nodes around it. The
 * coordinates are ported verbatim from the design's component. The first domain
 * is drawn selected, which is the state the component starts in, so the static
 * rendering matches the first paint of the interactive one.
 *
 * THE SEVEN TABS ARE ANCHOR LINKS, NOT BUTTONS. Each tab jumps to its domain
 * heading in the breakdown panel below, so a tab is never a control that does
 * nothing. The first carries the selected treatment and aria-current.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The seven verified stack systems and their skill entries.
 */
$maulik_portfolio_domains = array(
	array(
		'id'          => 'wordpress',
		'label'       => __( 'WordPress', 'maulik-portfolio' ),
		'code'        => 'SYS.01',
		'description' => __( 'Core application architecture, custom plugin development, block editor integrations and e commerce systems built natively on WordPress APIs.', 'maulik-portfolio' ),
		'skills'      => array(
			array( __( 'Plugins', 'maulik-portfolio' ), __( 'Modular custom plugin architecture with clean lifecycle management and zero theme coupling.', 'maulik-portfolio' ) ),
			array( __( 'Hooks', 'maulik-portfolio' ), __( 'Deep integration using WordPress action and filter hooks across core and third party plugins.', 'maulik-portfolio' ) ),
			array( __( 'REST API', 'maulik-portfolio' ), __( 'Custom namespace endpoints, schema definitions and permission verification callbacks.', 'maulik-portfolio' ) ),
			array( __( 'Gutenberg', 'maulik-portfolio' ), __( 'Block development, structured editorial attributes and block theme readiness.', 'maulik-portfolio' ) ),
			array( __( 'WooCommerce', 'maulik-portfolio' ), __( 'Order workflow extensions, custom product pricing logic and checkout integrations.', 'maulik-portfolio' ) ),
			array( __( 'WP CLI', 'maulik-portfolio' ), __( 'Command line administrative operations and developer workflow tooling.', 'maulik-portfolio' ) ),
		),
		'projects'    => __( 'Employee Application Management System · RankKernel SEO and Schema Engine', 'maulik-portfolio' ),
	),
	array(
		'id'          => 'php',
		'label'       => __( 'PHP', 'maulik-portfolio' ),
		'code'        => 'SYS.02',
		'description' => __( 'Server side engineering using modern object oriented PHP standards, dependency management and structured backend data handling.', 'maulik-portfolio' ),
		'skills'      => array(
			array( __( 'OOP', 'maulik-portfolio' ), __( 'Encapsulated service classes, clear responsibilities and maintainable object oriented patterns.', 'maulik-portfolio' ) ),
			array( __( 'Composer', 'maulik-portfolio' ), __( 'Package management and dependency resolution for modular PHP projects.', 'maulik-portfolio' ) ),
			array( __( 'PSR 4', 'maulik-portfolio' ), __( 'Standardized namespace autoloading across plugin modules and core services.', 'maulik-portfolio' ) ),
			array( __( 'MySQL', 'maulik-portfolio' ), __( 'Relational query optimization, indexing awareness and safe database interactions.', 'maulik-portfolio' ) ),
			array( __( 'Custom Tables', 'maulik-portfolio' ), __( 'Designing dedicated database tables when standard postmeta structures do not scale.', 'maulik-portfolio' ) ),
			array( __( 'Backend Processing', 'maulik-portfolio' ), __( 'Asynchronous handlers, queue style polling and structured data transformation.', 'maulik-portfolio' ) ),
		),
		'projects'    => __( 'Cross Domain Payment Architecture · Secure WordPress Data Encryption', 'maulik-portfolio' ),
	),
	array(
		'id'          => 'apis',
		'label'       => __( 'APIs', 'maulik-portfolio' ),
		'code'        => 'SYS.03',
		'description' => __( 'Designing and consuming RESTful services, authenticated endpoints and asynchronous webhook pipelines.', 'maulik-portfolio' ),
		'skills'      => array(
			array( __( 'REST', 'maulik-portfolio' ), __( 'Structured request and response contracts between distributed WordPress and external services.', 'maulik-portfolio' ) ),
			array( __( 'Authentication', 'maulik-portfolio' ), __( 'Shared secret verification, token headers and cryptographic request validation.', 'maulik-portfolio' ) ),
			array( __( 'API Keys', 'maulik-portfolio' ), __( 'External credential storage and protected server to server authorization headers.', 'maulik-portfolio' ) ),
			array( __( 'Webhooks', 'maulik-portfolio' ), __( 'Event driven receivers that process payment settlements and external system notifications.', 'maulik-portfolio' ) ),
			array( __( 'JSON', 'maulik-portfolio' ), __( 'Structured payload serialization, schema validation and JSON LD graph construction.', 'maulik-portfolio' ) ),
			array( __( 'cURL', 'maulik-portfolio' ), __( 'Server side HTTP transport configuration, timeout handling and response parsing.', 'maulik-portfolio' ) ),
		),
		'projects'    => __( 'Cross Domain Payment Architecture · Identity Document OCR Integration', 'maulik-portfolio' ),
	),
	array(
		'id'          => 'databases',
		'label'       => __( 'Databases', 'maulik-portfolio' ),
		'code'        => 'SYS.04',
		'description' => __( 'Relational data modeling in MySQL and WordPress storage layers with attention to data integrity and access control.', 'maulik-portfolio' ),
		'skills'      => array(
			array( __( 'MySQL', 'maulik-portfolio' ), __( 'Relational schema design, joins and indexed lookups for application data.', 'maulik-portfolio' ) ),
			array( __( 'Custom Tables', 'maulik-portfolio' ), __( 'Dedicated storage tables for structured plugin records and audit states.', 'maulik-portfolio' ) ),
			array( __( 'Prepared Queries', 'maulik-portfolio' ), __( 'Parameterized SQL queries via wpdb prepare to prevent SQL injection vulnerabilities.', 'maulik-portfolio' ) ),
			array( __( 'Encrypted Storage', 'maulik-portfolio' ), __( 'Storing sensitive appointment and identity records as ciphertext in database columns.', 'maulik-portfolio' ) ),
			array( __( 'Metadata Storage', 'maulik-portfolio' ), __( 'Structured post, user and term metadata management for SEO and workflow plugins.', 'maulik-portfolio' ) ),
		),
		'projects'    => __( 'Secure WordPress Data Encryption · Employee Application Management System', 'maulik-portfolio' ),
	),
	array(
		'id'          => 'security',
		'label'       => __( 'Security', 'maulik-portfolio' ),
		'code'        => 'SYS.05',
		'description' => __( 'Defensive application engineering combining cryptographic signing, field encryption and WordPress authorization boundaries.', 'maulik-portfolio' ),
		'skills'      => array(
			array( __( 'HMAC SHA 256', 'maulik-portfolio' ), __( 'Cryptographic signature generation and verification for cross domain REST payloads.', 'maulik-portfolio' ) ),
			array( __( 'Nonce Verification', 'maulik-portfolio' ), __( 'Protecting form submissions and AJAX actions against cross site request forgery.', 'maulik-portfolio' ) ),
			array( __( 'Encryption', 'maulik-portfolio' ), __( 'Authenticated symmetric encryption of sensitive database records using PHP Sodium.', 'maulik-portfolio' ) ),
			array( __( 'Input Validation', 'maulik-portfolio' ), __( 'Strict schema validation and sanitization of untrusted request parameters.', 'maulik-portfolio' ) ),
			array( __( 'Output Escaping', 'maulik-portfolio' ), __( 'Context specific escaping before rendering dynamic data into HTML or attributes.', 'maulik-portfolio' ) ),
			array( __( 'Capability Checks', 'maulik-portfolio' ), __( 'Role based authorization restricting sensitive views and decryption operations.', 'maulik-portfolio' ) ),
		),
		'projects'    => __( 'Secure WordPress Data Encryption · Cross Domain Payment Architecture', 'maulik-portfolio' ),
	),
	array(
		'id'          => 'integrations',
		'label'       => __( 'Integrations', 'maulik-portfolio' ),
		'code'        => 'SYS.06',
		'description' => __( 'Bridging WordPress plugins with enterprise cloud APIs, form engines, booking systems and payment gateways.', 'maulik-portfolio' ),
		'skills'      => array(
			array( __( 'Azure Form Recognizer', 'maulik-portfolio' ), __( 'Asynchronous OCR document analysis and automated form field extraction.', 'maulik-portfolio' ) ),
			array( __( 'Payment Gateways', 'maulik-portfolio' ), __( 'Multi gateway transaction routing, iframe checkout hosting and webhook settlement.', 'maulik-portfolio' ) ),
			array( __( 'Gravity Forms', 'maulik-portfolio' ), __( 'Custom field mapping, submission hooks, payment add on workflows and AJAX polling.', 'maulik-portfolio' ) ),
			array( __( 'Forminator', 'maulik-portfolio' ), __( 'Custom application intake workflows and role based submission management.', 'maulik-portfolio' ) ),
			array( __( 'LatePoint', 'maulik-portfolio' ), __( 'Appointment booking lifecycle hooks paired with Sodium data encryption.', 'maulik-portfolio' ) ),
			array( __( 'Brevo and Object Storage', 'maulik-portfolio' ), __( 'Automated transactional synchronization and external media storage workflows.', 'maulik-portfolio' ) ),
		),
		'projects'    => __( 'Identity Document OCR Integration · Cross Domain Payment Architecture', 'maulik-portfolio' ),
	),
	array(
		'id'          => 'tools',
		'label'       => __( 'Tools', 'maulik-portfolio' ),
		'code'        => 'SYS.07',
		'description' => __( 'Core languages and development tooling used daily to build, inspect, test and maintain WordPress and PHP software.', 'maulik-portfolio' ),
		'skills'      => array(
			array( __( 'Git', 'maulik-portfolio' ), __( 'Source control, modular commit history and collaborative repository management.', 'maulik-portfolio' ) ),
			array( __( 'Composer', 'maulik-portfolio' ), __( 'Autoloading configuration and PHP dependency management.', 'maulik-portfolio' ) ),
			array( __( 'JavaScript and AJAX', 'maulik-portfolio' ), __( 'Asynchronous frontend to backend communication, polling UI updates and DOM state.', 'maulik-portfolio' ) ),
			array( __( 'HTML and CSS', 'maulik-portfolio' ), __( 'Semantic markup, accessible document structure and responsive interface styling.', 'maulik-portfolio' ) ),
			array( __( 'WP CLI', 'maulik-portfolio' ), __( 'Environment inspection, database operations and plugin lifecycle verification.', 'maulik-portfolio' ) ),
		),
		'projects'    => __( 'RankKernel SEO and Schema Engine · Employee Application Management System', 'maulik-portfolio' ),
	),
);

/**
 * The radial node coordinates, verbatim from the design's component.
 */
$maulik_portfolio_node_positions = array(
	array( 260, 58 ),
	array( 435, 115 ),
	array( 465, 245 ),
	array( 365, 355 ),
	array( 155, 355 ),
	array( 55, 245 ),
	array( 85, 115 ),
);

/**
 * The domain drawn as selected in the static node map, which is the first one.
 */
$maulik_portfolio_active_domain = $maulik_portfolio_domains[0];

/**
 * Builds the node map SVG, with the first domain drawn selected.
 *
 * @param array $domains    The seven domains, in their display order.
 * @param array $positions  Radial coordinates, one pair per domain.
 * @return string The SVG element.
 */
function maulik_portfolio_node_map_svg( $domains, $positions ) {
	$centre_x = 260;
	$centre_y = 210;
	$edges    = '';
	$nodes    = '';

	foreach ( $domains as $index => $domain ) {
		$x         = $positions[ $index ][0];
		$y         = $positions[ $index ][1];
		$is_active = 0 === $index;

		$stroke = $is_active ? '#FACC15' : '#343434';
		$width  = $is_active ? '2' : '1';

		$edges .= sprintf(
			'<line x1="%d" y1="%d" x2="%d" y2="%d" stroke="%s" stroke-width="%s"></line>',
			$centre_x,
			$centre_y,
			$x,
			$y,
			$stroke,
			$width
		);

		if ( $is_active ) {
			$edges .= sprintf(
				'<circle cx="%s" cy="%s" r="3.5" fill="#FACC15"></circle>',
				( $centre_x + $x ) / 2,
				( $centre_y + $y ) / 2
			);
		}

		$fill        = $is_active ? '#FACC15' : '#171719';
		$node_stroke = $is_active ? '#FACC15' : '#343434';
		$label_fill  = $is_active ? '#000000' : '#FFFDF4';
		$count_fill  = $is_active ? '#0A0A0B' : '#77746C';

		$nodes .= sprintf(
			'<g transform="translate(%1$d,%2$d)"><rect x="-54" y="-22" width="108" height="44" fill="%3$s" stroke="%4$s" stroke-width="1.5"></rect><text x="0" y="-3" text-anchor="middle" fill="%5$s" class="hz-svg-label">%6$s</text><text x="0" y="12" text-anchor="middle" fill="%7$s" class="hz-svg-count">%8$d capabilities</text></g>',
			$x,
			$y,
			$fill,
			$node_stroke,
			$label_fill,
			esc_html( $domain['label'] ),
			$count_fill,
			count( $domain['skills'] )
		);
	}

	return sprintf(
		'<svg viewBox="0 0 520 420" class="hz-stack__svg" role="img" aria-label="%1$s"><ellipse cx="260" cy="210" rx="195" ry="148" fill="none" stroke="#262626" stroke-width="1" stroke-dasharray="4 4"></ellipse><circle cx="260" cy="210" r="75" fill="none" stroke="#171719" stroke-width="1"></circle>%2$s<g transform="translate(260,210)"><rect x="-56" y="-26" width="112" height="52" fill="#0A0A0B" stroke="#FACC15" stroke-width="1.5"></rect><text x="0" y="-3" text-anchor="middle" fill="#FFFDF4" class="hz-svg-name">MAULIK</text><text x="0" y="14" text-anchor="middle" fill="#FACC15" class="hz-svg-count">WP · PHP · CORE</text></g>%3$s</svg>',
		esc_attr__( 'Maulik at the centre, connected to WordPress, PHP, APIs, Databases, Security, Integrations and Tools', 'maulik-portfolio' ),
		$edges,
		$nodes
	);
}

?>
<!-- wp:group {"anchor":"technical-stack","ariaLabelledby":"heading-technical-stack","className":"is-style-section hz-section hz-stack is-style-surface-dark-grid","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section hz-section hz-stack is-style-surface-dark-grid" id="technical-stack" aria-labelledby="heading-technical-stack">
	<!-- wp:group {"className":"hz-section__head","layout":{"type":"default"}} -->
	<div class="wp-block-group hz-section__head">
		<!-- wp:group {"className":"hz-section__head-main","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-section__head-main">
			<!-- wp:paragraph {"className":"hz-eyebrow"} -->
			<p class="hz-eyebrow"><?php echo esc_html__( '03 · Interactive Technical Ecosystem', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2,"anchor":"heading-technical-stack","className":"hz-section__title"} -->
			<h2 class="wp-block-heading hz-section__title" id="heading-technical-stack"><?php echo esc_html__( 'Verified Technical Stack', 'maulik-portfolio' ); ?></h2>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->
		<!-- wp:paragraph {"className":"hz-section__head-lede"} -->
		<p class="hz-section__head-lede"><?php echo esc_html__( 'Explore the interconnected technologies I use to build WordPress plugins, PHP backend systems, REST integrations and cryptographic security workflows.', 'maulik-portfolio' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<!-- wp:group {"className":"hz-stack-8","layout":{"type":"default"}} -->
	<div class="wp-block-group hz-stack-8">
		<!-- wp:group {"className":"hz-body","layout":{"type":"default"}} -->
		<div class="wp-block-group hz-body">
			<!-- wp:group {"className":"hz-body__lead","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-body__lead">
				<!-- wp:group {"className":"hz-stack__map-head","layout":{"type":"default"}} -->
				<div class="wp-block-group hz-stack__map-head">
					<!-- wp:paragraph {"className":"hz-meta"} -->
					<p class="hz-meta"><?php echo esc_html__( 'System Topology · Select a Domain', 'maulik-portfolio' ); ?></p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"hz-label"} -->
					<p class="hz-label"><?php echo esc_html( $maulik_portfolio_active_domain['code'] ); ?> · <?php echo esc_html( $maulik_portfolio_active_domain['label'] ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
				<!-- wp:html -->
				<?php echo maulik_portfolio_node_map_svg( $maulik_portfolio_domains, $maulik_portfolio_node_positions ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<!-- /wp:html -->
				<div class="hz-stack__tabs">
					<?php foreach ( $maulik_portfolio_domains as $maulik_portfolio_domain_index => $maulik_portfolio_domain ) : ?>
						<?php
						$maulik_portfolio_tab_class     = 0 === $maulik_portfolio_domain_index ? 'hz-tab hz-tab-selected' : 'hz-tab';
						$maulik_portfolio_tab_current   = 0 === $maulik_portfolio_domain_index ? ' aria-current="true"' : '';
						$maulik_portfolio_domain_anchor = 'stack-' . $maulik_portfolio_domain['id'];
						?>
						<a class="<?php echo esc_html( $maulik_portfolio_tab_class ); ?>" href="#<?php echo esc_html( $maulik_portfolio_domain_anchor ); ?>"<?php echo $maulik_portfolio_tab_current; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $maulik_portfolio_domain['label'] ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
			<!-- /wp:group -->
			<!-- wp:group {"className":"hz-body__main hz-panel hz-panel-breakdown","layout":{"type":"default"}} -->
			<div class="wp-block-group hz-body__main hz-panel hz-panel-breakdown">
				<?php foreach ( $maulik_portfolio_domains as $maulik_portfolio_domain ) : ?>
					<!-- wp:group {"className":"hz-stack-4","layout":{"type":"default"}} -->
					<div class="wp-block-group hz-stack-4">
						<!-- wp:group {"className":"hz-row","layout":{"type":"default"}} -->
						<div class="wp-block-group hz-row">
							<!-- wp:paragraph {"className":"hz-label"} -->
							<p class="hz-label"><?php echo esc_html( $maulik_portfolio_domain['code'] ); ?> · <?php echo esc_html__( 'Verified Technical Domain', 'maulik-portfolio' ); ?></p>
							<!-- /wp:paragraph -->
							<!-- wp:heading {"level":3,"anchor":<?php echo wp_json_encode( 'stack-' . $maulik_portfolio_domain['id'] ); ?>,"className":"hz-domain__title"} -->
							<h3 class="wp-block-heading hz-domain__title" id="stack-<?php echo esc_html( $maulik_portfolio_domain['id'] ); ?>"><?php echo esc_html( $maulik_portfolio_domain['label'] ); ?></h3>
							<!-- /wp:heading -->
						</div>
						<!-- /wp:group -->
						<!-- wp:paragraph {"className":"hz-body-copy"} -->
						<p class="hz-body-copy"><?php echo esc_html( $maulik_portfolio_domain['description'] ); ?></p>
						<!-- /wp:paragraph -->
						<!-- wp:group {"className":"hz-skill-grid","layout":{"type":"default"}} -->
						<div class="wp-block-group hz-skill-grid">
							<?php foreach ( $maulik_portfolio_domain['skills'] as $maulik_portfolio_skill_index => $maulik_portfolio_skill ) : ?>
								<!-- wp:group {"className":"hz-skill","layout":{"type":"default"}} -->
								<div class="wp-block-group hz-skill">
									<!-- wp:group {"className":"hz-skill__head","layout":{"type":"default"}} -->
									<div class="wp-block-group hz-skill__head">
										<span class="hz-skill__name"><?php echo esc_html( $maulik_portfolio_skill[0] ); ?></span>
										<span class="hz-label-faint"><?php echo esc_html( $maulik_portfolio_domain['code'] ); ?>.<?php echo esc_html( sprintf( '%02d', $maulik_portfolio_skill_index + 1 ) ); ?></span>
									</div>
									<!-- /wp:group -->
									<!-- wp:paragraph {"className":"hz-skill__context"} -->
									<p class="hz-skill__context"><?php echo esc_html( $maulik_portfolio_skill[1] ); ?></p>
									<!-- /wp:paragraph -->
								</div>
								<!-- /wp:group -->
							<?php endforeach; ?>
						</div>
						<!-- /wp:group -->
						<!-- wp:group {"className":"hz-stack__proof","layout":{"type":"default"}} -->
						<div class="wp-block-group hz-stack__proof">
							<!-- wp:paragraph {"className":"hz-label-faint"} -->
							<p class="hz-label-faint"><?php echo esc_html__( 'Applied in Verified Work:', 'maulik-portfolio' ); ?></p>
							<!-- /wp:paragraph -->
							<!-- wp:paragraph {"className":"hz-meta"} -->
							<p class="hz-meta"><?php echo esc_html( $maulik_portfolio_domain['projects'] ); ?></p>
							<!-- /wp:paragraph -->
						</div>
						<!-- /wp:group -->
					</div>
					<!-- /wp:group -->
				<?php endforeach; ?>
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->