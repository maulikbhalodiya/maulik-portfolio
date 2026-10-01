<?php
/**
 * Title: Home Approach Section
 * Slug: maulik-portfolio/home-approach
 * Categories: maulik-portfolio-home
 * Description: Homepage section 06, the five engineering stages as one liners, on the 64px warm editorial surface.
 * Inserter: yes
 *
 * Section 06 of 08. The warm editorial surface at 64px, a second light surface
 * after the cream one and the last break in the dark run.
 *
 * FIVE STAGES: Understand, Design, Build, Verify, Improve. One sentence each.
 *
 * THE THIRTEEN QUESTIONS ARE GONE, AND THE LOSS IS DELIBERATE. The previous
 * version of this pattern asked thirteen verification questions across the five
 * stages. They are good questions, and "Can an unprivileged user bypass the
 * interface and call the endpoint directly?" is genuinely the most persuasive line
 * the site has. But the approved homepage states each stage in one line, and a
 * pattern whose job is to be the same section the approved homepage renders cannot
 * carry more text than the thing it mirrors. The Verify stage still keeps the
 * unprivileged user sentence, because the template keeps it too.
 *
 * There is no role="tablist" here and there never was. The prototype used it three
 * times without a matching role="tabpanel", which is invalid, and it is not
 * carried over. If the deep dive panel is ever built, the tablist and the tabpanel
 * arrive together.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The five engineering stages, each as a label and a one line description.
 */
$maulik_portfolio_approach_stages = array(
	array(
		'label' => __( 'Understand.', 'maulik-portfolio' ),
		'text'  => __( 'What the system actually has to do, what it must never do, and where the real constraint sits.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'Design.', 'maulik-portfolio' ),
		'text'  => __( 'Component boundaries, data flow and the failure cases, decided before any code is written.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'Build.', 'maulik-portfolio' ),
		'text'  => __( 'Small changes, each one reviewable on its own.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'Verify.', 'maulik-portfolio' ),
		'text'  => __( 'Test the failure path, not only the happy one. An unprivileged user should not be able to bypass the interface and call an endpoint directly.', 'maulik-portfolio' ),
	),
	array(
		'label' => __( 'Improve.', 'maulik-portfolio' ),
		'text'  => __( 'Simplify what the work made harder than it needed to be.', 'maulik-portfolio' ),
	),
);

?>
<!-- wp:group {"tagName":"section","className":"is-style-section is-style-surface-warm-editorial","anchor":"engineering-approach","ariaLabelledby":"heading-engineering-approach","layout":{"type":"constrained"}} -->
<section class="wp-block-group is-style-section is-style-surface-warm-editorial" id="engineering-approach" aria-labelledby="heading-engineering-approach">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '06 · Methodology and System Discipline', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-engineering-approach"} -->
	<h2 class="wp-block-heading" id="heading-engineering-approach"><?php echo esc_html__( 'How I approach engineering', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php echo esc_html__( 'Five stages, run in order and repeated whenever the requirement changes underneath the work.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:list {"className":"is-style-section-tight"} -->
	<ul class="wp-block-list is-style-section-tight">
		<?php foreach ( $maulik_portfolio_approach_stages as $maulik_portfolio_approach_stage ) : ?>
			<!-- wp:list-item -->
			<li><strong><?php echo esc_html( $maulik_portfolio_approach_stage['label'] ); ?></strong> <?php echo esc_html( $maulik_portfolio_approach_stage['text'] ); ?></li>
			<!-- /wp:list-item -->
		<?php endforeach; ?>
	</ul>
	<!-- /wp:list -->
</section>
<!-- /wp:group -->