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
 * THE COPYRIGHT YEAR IS GENERATED, NEVER HARDCODED. A hardcoded year is not a
 * translation bug, it is a correctness bug, and it is the kind of thing that
 * survives for years because nothing breaks visibly.
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
				'label' => __( 'Projects', 'maulik-portfolio' ),
				'path'  => '/projects/',
			),
			array(
				'label' => __( 'RankKernel', 'maulik-portfolio' ),
				'path'  => '/rankkernel/',
			),
			array(
				'label' => __( 'About', 'maulik-portfolio' ),
				'path'  => '/about/',
			),
		),
	),
	array(
		'class'   => 'site-footer__col--development',
		'heading' => __( 'Development', 'maulik-portfolio' ),
		'links'   => array(
			array(
				'label' => __( 'WordPress Engineering', 'maulik-portfolio' ),
				'path'  => '/projects/',
			),
			array(
				'label' => __( 'PHP Backend', 'maulik-portfolio' ),
				'path'  => '/projects/',
			),
			array(
				'label' => __( 'Security', 'maulik-portfolio' ),
				'path'  => '/projects/',
			),
		),
	),
	array(
		'class'   => 'site-footer__col--connect',
		'heading' => __( 'Connect', 'maulik-portfolio' ),
		'links'   => array(
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

			<!-- wp:paragraph {"className":"site-footer__identity-line"} -->
			<p class="site-footer__identity-line"><?php echo esc_html__( 'WordPress Developer at Qrolic Technologies', 'maulik-portfolio' ); ?></p>
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
					<!-- wp:paragraph {"className":"site-footer__link"} -->
					<p class="site-footer__link"><a href="<?php echo esc_url( get_home_url( null, $maulik_portfolio_footer_link['path'] ) ); ?>"><?php echo esc_html( $maulik_portfolio_footer_link['label'] ); ?></a></p>
					<!-- /wp:paragraph -->
				<?php endforeach; ?>
			</div>
			<!-- /wp:group -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:group -->

	<!-- wp:separator {"className":"site-footer__rule"} /-->

	<!-- wp:group {"tagName":"div","className":"site-footer__legal","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group site-footer__legal">
		<!-- wp:paragraph {"className":"site-footer__copyright","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
		<p class="site-footer__copyright"><?php echo esc_html( sprintf( /* translators: %s: the current year. */ __( '© %s Maulik Bhalodiya', 'maulik-portfolio' ), wp_date( 'Y' ) ) ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"className":"site-footer__technical","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
		<p class="site-footer__technical"><?php echo esc_html__( 'WordPress Block Theme Ready Architecture · Structured Data Prototype', 'maulik-portfolio' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</footer>
<!-- /wp:group -->
