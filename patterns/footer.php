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
 * The copyright year is generated with wp_date( 'Y' ), never hardcoded. A
 * hardcoded year is not a translation bug, it is a correctness bug, and it is
 * the kind of thing that survives for years because nothing breaks visibly.
 *
 * PATTERNS RUN ON init. The conditional tags, the queried post accessor and the
 * loop are not available and are not used. Iterating this file's own arrays is.
 *
 * There is no email link, no mailto and no plaintext address, in this pattern or
 * anywhere else in the theme. There is no Writing or Blog link either.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Footer columns, each with its own translated heading and its own link list.
 *
 * The five/two/three/two split is asymmetric on purpose. Do not balance it to
 * three/three/three/three.
 *
 * @var array<int, array{heading: string, links: array<int, array{label: string, path: string}>}> $maulik_portfolio_footer_columns Footer columns and their links.
 */
$maulik_portfolio_footer_columns = array(
	array(
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
		),
	),
	array(
		'heading' => __( 'Development', 'maulik-portfolio' ),
		'links'   => array(
			array(
				'label' => __( 'RankKernel', 'maulik-portfolio' ),
				'path'  => '/rankkernel/',
			),
			array(
				'label' => __( 'Projects', 'maulik-portfolio' ),
				'path'  => '/projects/',
			),
		),
	),
	array(
		'heading' => __( 'Connect', 'maulik-portfolio' ),
		'links'   => array(
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
			<p class="site-footer__identity-line"><?php echo esc_html__( 'WordPress Developer and PHP Engineer', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<?php foreach ( $maulik_portfolio_footer_columns as $maulik_portfolio_footer_column ) : ?>
			<!-- wp:group {"tagName":"div","className":"site-footer__col","layout":{"type":"constrained"}} -->
			<div class="wp-block-group site-footer__col">
				<!-- wp:heading {"level":2,"className":"site-footer__heading"} -->
				<h2 class="wp-block-heading site-footer__heading"><?php echo esc_html( $maulik_portfolio_footer_column['heading'] ); ?></h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"site-footer__nav"} -->
				<p class="site-footer__nav"><?php echo esc_html__( 'Navigation', 'maulik-portfolio' ); ?></p>
				<!-- /wp:paragraph -->

				<?php foreach ( $maulik_portfolio_footer_column['links'] as $maulik_portfolio_footer_link ) : ?>
					<!-- wp:paragraph {"className":"site-footer__link"} -->
					<p class="site-footer__link"><a href="<?php echo esc_url( get_home_url( $maulik_portfolio_footer_link['path'] ) ); ?>"><?php echo esc_html( $maulik_portfolio_footer_link['label'] ); ?></a></p>
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
		<p class="site-footer__copyright"><?php echo esc_html( sprintf( /* translators: %s: the current year. */ __( 'Copyright %s Maulik Bhalodiya', 'maulik-portfolio' ), wp_date( 'Y' ) ) ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"className":"site-footer__technical","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
		<p class="site-footer__technical"><?php echo esc_html__( 'WordPress Block Theme Ready Architecture', 'maulik-portfolio' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</footer>
<!-- /wp:group -->