<?php
/**
 * Title: Home Approach Section
 * Slug: maulik-portfolio/home-approach
 * Categories: maulik-portfolio-home
 * Description: Homepage section 06, the five engineering stages, on the dark-grid surface.
 * Inserter: yes
 *
 * Section 06 of 08. Dark-grid, and the rhythm returns to the grid texture before
 * the light editorial break.
 *
 * FIVE STAGES: Understand, Design, Build, Verify, Improve. Each one below carries
 * a real engineering question taken from the verified approach content. Questions
 * are the point. "Can an unprivileged user bypass the UI and call the AJAX or REST
 * endpoint directly?" demonstrates more competence than any score out of ten, and
 * unlike a score it can be checked.
 *
 * There is no role="tablist" here. The prototype uses it three times without a
 * matching role="tabpanel", which is invalid, and it is not carried over. If the
 * deep dive panel is ever built, the tablist and the tabpanel arrive together.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * The five engineering stages with their verification questions.
 */
$maulik_portfolio_stages = array(
	array(
		'stage'     => '01',
		'title'     => __( 'Understand', 'maulik-portfolio' ),
		'questions' => array(
			__( 'What is the actual technical requirement, stated without assuming the solution?', 'maulik-portfolio' ),
			__( 'Who is authorised to do this, and where is that enforced?', 'maulik-portfolio' ),
			__( 'What breaks if this is only half finished?', 'maulik-portfolio' ),
		),
	),
	array(
		'stage'     => '02',
		'title'     => __( 'Design', 'maulik-portfolio' ),
		'questions' => array(
			__( 'Where does this responsibility belong, on this system or on another one?', 'maulik-portfolio' ),
			__( 'What is the contract between the parts, and what happens when it times out?', 'maulik-portfolio' ),
			__( 'Which decision is expensive to reverse later?', 'maulik-portfolio' ),
		),
	),
	array(
		'stage'     => '03',
		'title'     => __( 'Build', 'maulik-portfolio' ),
		'questions' => array(
			__( 'Is this extending WordPress through its own APIs, or working around them?', 'maulik-portfolio' ),
			__( 'Can the next maintainer read this without me in the room?', 'maulik-portfolio' ),
		),
	),
	array(
		'stage'     => '04',
		'title'     => __( 'Verify', 'maulik-portfolio' ),
		'questions' => array(
			__( 'Can an unprivileged user bypass the interface and call the endpoint directly?', 'maulik-portfolio' ),
			__( 'Does the signature check fail when a single payload field is altered?', 'maulik-portfolio' ),
			__( 'What happens on the retry, and on the duplicate callback?', 'maulik-portfolio' ),
		),
	),
	array(
		'stage'     => '05',
		'title'     => __( 'Improve', 'maulik-portfolio' ),
		'questions' => array(
			__( 'What is the slow query, and is it slow for everyone or only at scale?', 'maulik-portfolio' ),
			__( 'What would I remove if I rebuilt this today?', 'maulik-portfolio' ),
		),
	),
);

?>
<!-- wp:group {"tagName":"section","className":"section section-approach dark-grid","anchor":"engineering-approach","ariaLabelledby":"heading-engineering-approach","layout":{"type":"constrained"}} -->
<section class="wp-block-group section section-approach dark-grid" id="engineering-approach" aria-labelledby="heading-engineering-approach">
	<!-- wp:paragraph {"className":"eyebrow","typography":{"fontFamily":"var:preset|font-family|mono"}} -->
	<p class="eyebrow"><span class="w-2 h-2 bg-accent" aria-hidden="true"></span> <span class="eyebrow-text"><?php echo esc_html__( '06 · Methodology and System Discipline', 'maulik-portfolio' ); ?></span></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"level":2,"anchor":"heading-engineering-approach"} -->
	<h2 class="wp-block-heading" id="heading-engineering-approach"><?php echo esc_html__( 'How I approach engineering', 'maulik-portfolio' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"className":"section-lede"} -->
	<p class="section-lede"><?php echo esc_html__( 'Five stages. The questions below are the ones I ask at each one.', 'maulik-portfolio' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:group {"tagName":"ol","className":"stage-list","layout":{"type":"constrained"}} -->
	<ol class="wp-block-group stage-list">
		<?php foreach ( $maulik_portfolio_stages as $maulik_portfolio_stage ) : ?>
			<!-- wp:group {"tagName":"li","className":"stage","layout":{"type":"constrained"}} -->
			<li class="wp-block-group stage">
				<!-- wp:heading {"level":3,"className":"stage__title"} -->
				<h3 class="wp-block-heading stage__title"><span class="stage__number" aria-hidden="true"><?php echo esc_html( $maulik_portfolio_stage['stage'] ); ?></span> <?php echo esc_html( $maulik_portfolio_stage['title'] ); ?></h3>
				<!-- /wp:heading -->

				<!-- wp:list {"className":"stage__questions"} -->
				<ul class="wp-block-list stage__questions">
					<?php foreach ( $maulik_portfolio_stage['questions'] as $maulik_portfolio_stage_question ) : ?>
						<!-- wp:list-item -->
						<li><?php echo esc_html( $maulik_portfolio_stage_question ); ?></li>
						<!-- /wp:list-item -->
					<?php endforeach; ?>
				</ul>
				<!-- /wp:list -->
			</li>
			<!-- /wp:group -->
		<?php endforeach; ?>
	</ol>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
