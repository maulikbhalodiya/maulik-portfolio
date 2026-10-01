<?php
/**
 * Footer pattern.
 *
 * Patterns are PHP, but they run on init, not at render time. That means
 * is_single(), get_post(), the loop and the_title() are all unavailable here.
 * Only static data, esc_html_e() style translation calls and
 * get_theme_file_uri() work. Anything that depends on the current query has to
 * be a template part or a block render callback instead.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

?>
<!-- wp:group {"tagName":"footer","className":"site-footer","layout":{"type":"constrained"}} -->
<footer class="wp-block-group site-footer">
	<!-- wp:paragraph {"align":"center"} -->
	<p class="has-text-align-center"><?php echo esc_html( get_bloginfo( 'name', 'display' ) ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph {"align":"center"} -->
	<p class="has-text-align-center"><?php echo esc_html( get_bloginfo( 'description', 'display' ) ); ?></p>
	<!-- /wp:paragraph -->
</footer>
<!-- /wp:group -->
