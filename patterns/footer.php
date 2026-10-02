<?php
/**
 * Title: Footer
 * Slug: maulik-portfolio/footer
 * Categories: footer, maulik-portfolio-shell
 * Description: The site footer, with the asymmetric column grid and the copyright row.
 * Inserter: yes
 * Block Types: core/template-part/footer
 *
 * THE TRANSLATABLE SOURCE OF parts/footer.html.
 *
 * The part holds the 5/2/3/2 grid as columns and holds no words at all. Every
 * string in the footer is here, wrapped in esc_html__() with the maulik-portfolio
 * text domain, because a template part is parsed rather than executed and cannot
 * translate anything.
 *
 * THE GRID IS 5 / 2 / 3 / 2 AND THAT IS DELIBERATE. Identity takes five columns,
 * Explore two, Development three, Connect two. Do not balance it to three/three/
 * three/three. The asymmetry is in the design and it reads better than a balanced
 * four up, because the identity block genuinely has more to say than a list of
 * three links and the layout says so before you have read a word.
 *
 * Each column below carries its own class, and those classes are what the
 * asymmetric grid is written against. A loop that emitted four identical columns
 * would produce a balanced grid with the same markup, which is the specific bug
 * this array shape exists to prevent.
 *
 * THERE IS NO SEPARATOR BLOCK IN HERE, AND THAT IS A FIX. A core/separator was
 * authored between the grid and the legal row while the grid was still styled
 * without a border of its own. The grid rule in _site-footer.scss carries the
 * hairline under the columns, exactly as the design does with border-b on the
 * grid, so the authored separator was a second hairline in the same place. It
 * did not render, which is why it went unnoticed, and a separator whose absence
 * depends on something outside this file is not something to leave in place: the
 * day it does render, the footer has a double rule. The design has no separator
 * inside the footer grid, only the top border on the footer itself, and this now
 * matches that exactly.
 *
 * THE COLUMNS FOLLOW THE DESIGN'S INVENTORY, AND EVERY LINK RESOLVES TO A PAGE
 * THAT EXISTS. Explore is Home, About, Resume and Contact. Development is
 * RankKernel and All Development Work, which are the two of the design's four
 * targets that exist here: the design also points Payment Architecture and Data
 * Encryption at two nested project pages, and this site has no such pages. They
 * were not created, and no placeholder was linked in their place, because a
 * link that 404s is worse than a missing link. Connect is GitHub, LinkedIn and
 * Contact. The design's third Connect target is an email link, which this theme
 * does not have anywhere, so Contact takes the place and the two real external
 * profiles are kept, which leaves the footer with an actual off-site destination.
 *
 * The copyright year is generated, never hardcoded. A hardcoded year is not a
 * translation bug, it is a correctness bug, and it is the kind of thing that
 * survives for years because nothing breaks visibly.
 *
 * The name in the copyright is the author's name, not the site title, so it is
 * content and it is translated. The site title itself is not written anywhere in
 * this file: both the header wordmark and the footer wordmark are a core/site-title
 * block, so they read blogname from the admin and changing the site title there
 * updates both.
 *
 * PATTERNS RUN ON init. The conditional tags, the queried post accessor and the
 * loop are not available and are not used. Iterating this file's own arrays is.
 *
 * There is no email link, no mail link and no plaintext address, here or
 * anywhere else in the theme. There is no Writing or Blog link either.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Footer columns, each with its own column class, heading and link list.
 *
 * The five/two/three/two split is asymmetric on purpose. Do not balance it to
 * three/three/three/three.
 *
 * A link carries either a path, which is resolved against the home URL, or a
 * full url for the two external profiles. The external flag is what makes the
 * anchor open in a new tab with noopener and noreferrer, which an internal link
 * must not do.
 */
$maulik_portfolio_footer_columns = array(
	array(
		'class'   => 'site-footer__col--explore',
		'heading' => __( 'Explore', 'maulik-portfolio' ),
		'links'   => array(
			array(
				'label' => __( 'Home', 'maulik-portfolio' ),
				'path'  => '/',
			),
			array(
				'label' => __( 'About', 'maulik-portfolio' ),
				'path'  => '/about/',
			),
			array(
				'label' => __( 'Resume', 'maulik-portfolio' ),
				'path'  => '/resume/',
			),
			array(
				'label' => __( 'Contact', 'maulik-portfolio' ),
				'path'  => '/contact/',
			),
		),
	),
	array(
		'class'   => 'site-footer__col--development',
		'heading' => __( 'Development', 'maulik-portfolio' ),
		'links'   => array(
			array(
				'label' => __( 'RankKernel (Open Source)', 'maulik-portfolio' ),
				'path'  => '/rankkernel/',
			),
			array(
				'label' => __( 'All Development Work', 'maulik-portfolio' ),
				'path'  => '/projects/',
			),
		),
	),
	array(
		'class'   => 'site-footer__col--connect',
		'heading' => __( 'Connect', 'maulik-portfolio' ),
		'links'   => array(
			array(
				'label' => __( 'GitHub', 'maulik-portfolio' ),
				'url'   => 'https://github.com/maulikbhalodiya',
			),
			array(
				'label' => __( 'LinkedIn', 'maulik-portfolio' ),
				'url'   => 'https://www.linkedin.com/in/maulikbhalodiya/',
			),
			array(
				'label' => __( 'Contact', 'maulik-portfolio' ),
				'path'  => '/contact/',
			),
		),
	),
);

?>
<!-- wp:group {"tagName":"footer","className":"site-footer no-print has-black-background-color has-background","backgroundColor":"black","layout":{"type":"constrained"}} -->
<footer class="wp-block-group site-footer no-print has-black-background-color has-background">
	<!-- wp:group {"tagName":"div","className":"site-footer__grid","layout":{"type":"constrained"}} -->
	<div class="wp-block-group site-footer__grid">
		<!-- wp:group {"tagName":"div","className":"site-footer__col site-footer__col--identity","layout":{"type":"constrained"}} -->
		<div class="wp-block-group site-footer__col site-footer__col--identity">
			<!-- wp:site-title {"level":2,"className":"site-footer__brand"} /-->

			<!-- wp:paragraph {"className":"site-footer__identity-line"} -->
			<p class="site-footer__identity-line"><?php echo esc_html__( 'WordPress Developer | PHP Engineer | Custom Plugin Developer', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"className":"site-footer__summary"} -->
			<p class="site-footer__summary"><?php echo esc_html__( 'Custom WordPress plugins, PHP backend systems, third party integrations, payment flows and field level encryption.', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<?php foreach ( $maulik_portfolio_footer_columns as $maulik_portfolio_footer_column ) : ?>
			<!-- wp:group {"tagName":"div","className":"site-footer__col <?php echo esc_attr( $maulik_portfolio_footer_column['class'] ); ?>","layout":{"type":"constrained"}} -->
			<div class="wp-block-group site-footer__col <?php echo esc_attr( $maulik_portfolio_footer_column['class'] ); ?>">
				<!-- wp:heading {"level":2,"className":"site-footer__heading"} -->
				<h2 class="wp-block-heading site-footer__heading"><?php echo esc_html( $maulik_portfolio_footer_column['heading'] ); ?></h2>
				<!-- /wp:heading -->

				<?php foreach ( $maulik_portfolio_footer_column['links'] as $maulik_portfolio_footer_link ) : ?>
					<?php
					$maulik_portfolio_footer_external = isset( $maulik_portfolio_footer_link['url'] );
					$maulik_portfolio_footer_url      = $maulik_portfolio_footer_external
						? $maulik_portfolio_footer_link['url']
						: get_home_url( null, $maulik_portfolio_footer_link['path'] );
					$maulik_portfolio_footer_rel      = $maulik_portfolio_footer_external ? ' target="_blank" rel="noopener noreferrer"' : '';
					?>
					<!-- wp:paragraph {"className":"site-footer__link"} -->
					<p class="site-footer__link"><a href="<?php echo esc_url( $maulik_portfolio_footer_url ); ?>"<?php echo esc_attr( $maulik_portfolio_footer_rel ); ?>><?php echo esc_html( $maulik_portfolio_footer_link['label'] ); ?></a></p>
					<!-- /wp:paragraph -->
				<?php endforeach; ?>
			</div>
			<!-- /wp:group -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"tagName":"div","className":"site-footer__legal","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group site-footer__legal">
		<!-- wp:paragraph {"className":"site-footer__copyright"} -->
		<p class="site-footer__copyright"><?php echo esc_html( sprintf( /* translators: 1: the current year, 2: the site author. */ __( '© %1$s %2$s. All rights reserved.', 'maulik-portfolio' ), wp_date( 'Y' ), 'Maulik Bhalodiya' ) ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"className":"site-footer__technical","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
		<p class="site-footer__technical"><?php echo esc_html__( 'WordPress Block Theme Ready Architecture · Structured Data Prototype', 'maulik-portfolio' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</footer>
<!-- /wp:group -->
