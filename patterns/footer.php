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
 * THE FOOTER CARRIES NO backgroundColor BLOCK ATTRIBUTE, AND IT USED TO CARRY
 * backgroundColor "black". That attribute is removed on purpose and must not be
 * put back, because it was fighting the stylesheet.
 *
 * The footer's ground is the base token, #0A0A0B, opaque, and it is set in
 * _site-footer.scss as:
 *
 *   .wp-block-group.site-footer { background-color: var(--wp--preset--color--base) !important; }
 *
 * Core turns the black backgroundColor attribute into the class
 * has-black-background-color, and Core prints that class as
 *
 *   .has-black-background-color { background-color: var(--wp--preset--color--black) !important; }
 *
 * so the footer had two competing !important background declarations: ours at
 * #0A0A0B and Core's at the pure black #000000. That is not a cascade an author
 * can win by specificity, because !important against !important is decided by
 * specificity and then by source order, and Core's global styles are printed
 * late. The result is that the footer ground depended on which stylesheet
 * happened to win, which is exactly the kind of silent visual dependency that
 * survives review.
 *
 * With the attribute gone, Core no longer prints has-black-background-color at
 * all, there is one declaration, and it is ours.
 *
 * has-background WAS REMOVED ALONGSIDE IT, and that is not tidying. Without a
 * backgroundColor attribute the class is meaningless to Core, and Core drops it
 * on the next save of the template part, which would then leave the rendered
 * markup disagreeing with the stored markup. The two must change together.
 *
 * THE COLUMNS FOLLOW THE DESIGN'S INVENTORY, AND EVERY LINK RESOLVES TO SOMETHING
 * THAT EXISTS. Explore is Home, About, Resume and Contact, which is the design's
 * four exactly. Development is the design's four, in the design's order:
 * RankKernel, All Development Work, Payment Architecture and Data Encryption.
 * The design points the last two at two nested project pages, and this site has
 * no such pages and cannot have them, because there are no custom post types and
 * /projects/{slug}/ does not resolve. They were not created, and the links were
 * not pointed at anything invented: both cards live on /projects/ and both carry
 * an id, so the two links resolve as in page anchors to those ids. See the note
 * at the entries themselves for which ids, and for what to re check first if a
 * case study is ever renumbered.
 *
 * Connect is the design's three, in the design's order: GitHub, LinkedIn and
 * Email. The Email entry was previously omitted, on the grounds recorded below.
 * THAT GROUNDS IS REVERSED BY THE OWNER, EXPLICITLY AND DELIBERATELY, so this
 * paragraph now contradicts nothing and the code stands on its own.
 *
 * HISTORY, KEPT BECAUSE THE REVERSAL NEEDS ITS REASON. The third Connect
 * target is an email address, and this theme previously carried no email link,
 * no mailto and no plaintext address anywhere, at all, ever: CI grepped for
 * both patterns and the address had been spam-listed in production once. An
 * in-page Contact link was tried in that slot and taken back out, because
 * Explore already carries Contact pointing at /contact/, and the same label
 * pointing at the same URL in two columns of one footer reads as a duplicate
 * that looks like a mistake.
 *
 * THE OWNER'S RULE FOR THIS PROJECT IS THAT FOR ANYTHING DESIGN OR COLOUR
 * RELATED, USE EXACTLY WHAT THE DEMO DESIGN USES, WITH NO SUBSTITUTIONS. The
 * design's footer has an Email link in the Connect column and the design's
 * section 08 has an Email Me button, and this theme substituted a /contact/
 * link for both. That was the substitution, so the design wins. The owner was
 * told the address is spam-listed in production and ruled anyway. That is
 * settled: do not re-open it, do not re-litigate the spam listing, and do not
 * delete these two entries on the strength of the history above. If you are
 * reading this while tempted to remove the mailto, the answer is no.
 *
 * The address is written out literally below rather than assembled or filtered,
 * because a partially assembled address that fails to render is a silently
 * dead link in a footer, and the footer is the one place a visitor is told how
 * to reach the author. CI no longer blocks it: the mailto and raw-address
 * gates in .github/workflows/ were narrowed rather than removed, so they still
 * reject any OTHER address while exempting the design's one.
 *
 * The copyright year is generated, never hardcoded. A hardcoded year is not a
 * translation bug, it is a correctness bug, and it is the kind of thing that
 * survives for years because nothing breaks visibly.
 *
 * The name in the copyright is the author's name, not the site title, so it is
 * content and it is translated. The site title is never written as a literal.
 * Both wordmarks read the blog name from the admin through get_bloginfo(), so
 * changing the site title there updates both. The header keeps its
 * core/site-title block. The footer does not, and the reason is a measured
 * defect rather than a preference: core/site-title renders its anchor as
 * target="_self" rel="home", which on the live site came out as
 *
 *   <a href="https://maulik-dev.duckdns.org" target="_self" rel="home" ...>
 *
 * Both attributes are wrong for this link. target="_self" is the default and
 * says nothing, and rel="home" is a retired HTML4 keyword that nothing reads
 * back. The design, at Footer.tsx line 27, is a bare anchor to the root with no
 * target and no rel, so the footer wordmark is a core/heading wrapping an anchor
 * instead. It carries the same site-footer__brand class on the heading, so every
 * type rule that already applied to it still applies, and it still reads
 * blogname from the admin.
 *
 * PATTERNS RUN ON init. The conditional tags, the queried post accessor and the
 * loop are not available and are not used. Iterating this file's own arrays is.
 *
 * There is one email link, the design's own, in the Connect column. There is
 * no plaintext address anywhere, and no mail link in any other part of the
 * theme. There is no Writing or Blog link either.
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
 * A link carries a path, which is resolved against the home URL, a full url
 * for the two external profiles, or a mailto, which is passed through exactly
 * as written. The external flag is what makes the anchor open in a new tab with
 * noopener and noreferrer, which an internal link must not do and which a
 * mailto must not do either, because the design's email anchor carries neither
 * attribute.
 *
 * ESC_ATTR() IS NOT USED ON ANYTHING THAT GOES INTO A BLOCK COMMENT, AND IT USED
 * TO BE. That was a live defect on the live site, measured by reading the
 * attributes back off the rendered HTML rather than the source. The footer
 * external links came out as
 *
 *   target=&quot;_blank&quot;   rel=&quot;noopener noreferrer&quot;
 *
 * The cause was a raw attribute fragment, ' target="_blank" rel="noopener
 * noreferrer"', built into a variable and then passed through esc_attr().
 * esc_attr() turns every double quote into the entity &quot;, and the entity
 * survives into the output as literal text inside the attribute value, so the
 * browser parsed a target of quote underscore blank quote and a rel that was
 * truncated at the first space.
 *
 * Two rules follow, and the second is the one people forget.
 *   One: never run an escaping function over a fragment that already contains
 *   quotes. A value and its delimiters are separate jobs. The target and rel
 *   below are written literally inside the anchor and nothing escapes them,
 *   because there is nothing there that needs it.
 *   Two: inside a block comment, use wp_json_encode() and nothing else. esc_attr()
 *   emits &quot;, which is not valid JSON, and a block whose JSON does not parse
 *   fails silently: no error, no markup, no attribute. wp_json_encode() emits
 *   the quotes as quotes, so the JSON parses. That is the pattern
 *   patterns/home-capabilities.php, patterns/home-approach.php and
 *   patterns/home-technical-stack.php already use, and this file now matches it.
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
				'label'  => __( 'Resume', 'maulik-portfolio' ),
				'path'   => '/resume/',
				'accent' => true,
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

			/*
			 * The design's third and fourth Development links, restored as in
			 * page anchors rather than nested routes. The design points them at
			 * /projects/cross-domain-payment-architecture/ and
			 * /projects/wordpress-data-encryption/, and this site has no such
			 * routes: there are no custom post types and /projects/{slug}/ does
			 * not resolve. The cards themselves do exist on /projects/ and
			 * carry ids, so the links point at those ids. An anchor that
			 * resolves to nothing is worse than a missing link, because it
			 * looks complete, and every id below was read off
			 * content/pages/projects.html before it was written here.
			 *
			 *   Cross Domain Payment Architecture  ->  #case-study-01
			 *   Secure WordPress Data Encryption  ->  #case-study-04
			 *
			 * The design's own slugs, cross-domain-payment-architecture and
			 * wordpress-data-encryption, are not ids anywhere in that file. If
			 * the case study anchors are ever renumbered, these two paths are
			 * the two that break and they break silently.
			 */
			array(
				'label' => __( 'Payment Architecture', 'maulik-portfolio' ),
				'path'  => '/projects/#case-study-01',
			),
			array(
				'label' => __( 'Data Encryption', 'maulik-portfolio' ),
				'path'  => '/projects/#case-study-04',
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
				'url'   => 'https://www.linkedin.com/in/maulik-bhalodiya-',
			),

			/*
			 * THE DESIGN'S THIRD CONNECT LINK, RESTORED BY OWNER DECISION.
			 *
			 * Footer.tsx lines 162 to 169 read:
			 *
			 *   <a href={PROFILE_DATA.links.email}
			 *      className="text-[#D8D5CA] hover:text-[#FACC15] transition-colors">
			 *     Email
			 *   </a>
			 *
			 * which is the SAME treatment as GitHub and LinkedIn, and that is
			 * the reason this entry carries no accent flag. It is deliberately
			 * NOT the Resume treatment. The design's one accent footer link is
			 * Resume in Explore, at text-[#FACC15] with hover:text-[#FDE047] and
			 * font-medium, and the design's Email is not it: it is
			 * text-[#D8D5CA] with hover:text-[#FACC15], which is exactly what
			 * the plain site-footer__link class already resolves to, and which
			 * is why no new class and no new SCSS rule are involved.
			 *
			 * NO ICON. The design's anchor wraps the bare word Email and
			 * nothing else, so this one ships with no glyph before or after
			 * it. The Mail icon in the design belongs to the section 08
			 * button, not to this link, and it does not travel here.
			 *
			 * NO target AND NO rel. The design's email anchor carries neither,
			 * because a mailto opens a local client rather than a browsing
			 * context, so the external-attributes branch below must not run
			 * for it. It is declared with its own key rather than through
			 * 'url' for that reason, and the 'mailto' key is what tells the
			 * renderer to pass the value through untouched instead of
			 * resolving it against the home URL.
			 */
			array(
				'label'  => __( 'Email', 'maulik-portfolio' ),
				'mailto' => 'mailto:maulikbhalodiya9999@gmail.com',
			),
		),
	),
);

?>
<!-- wp:group {"tagName":"footer","className":"site-footer no-print","layout":{"type":"constrained"}} -->
<footer class="wp-block-group site-footer no-print">
	<!-- wp:group {"tagName":"div","className":"site-footer__grid","layout":{"type":"constrained"}} -->
	<div class="wp-block-group site-footer__grid">
		<!-- wp:group {"tagName":"div","className":"site-footer__col site-footer__col--identity","layout":{"type":"constrained"}} -->
		<div class="wp-block-group site-footer__col site-footer__col--identity">
			<!-- wp:heading {"level":2,"className":"site-footer__brand"} -->
			<h2 class="wp-block-heading site-footer__brand"><a href="<?php echo esc_url( get_home_url() ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"site-footer__identity-line"} -->
			<p class="site-footer__identity-line"><?php echo esc_html__( 'WordPress Developer | PHP Engineer | Custom Plugin Developer', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"className":"site-footer__summary"} -->
			<p class="site-footer__summary"><?php echo esc_html__( 'Custom WordPress plugins, PHP backend systems, third party integrations, payment flows and field level encryption.', 'maulik-portfolio' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<?php foreach ( $maulik_portfolio_footer_columns as $maulik_portfolio_footer_column ) : ?>
			<?php
			/*
			 * The column's full class string is built once and then encoded twice,
			 * for two different consumers. wp_json_encode() writes the block
			 * comment's className attribute, and esc_attr() writes the same string
			 * into the rendered class attribute. They are not interchangeable and
			 * must not be swapped: see the note above $maulik_portfolio_footer_
			 * columns for why esc_attr() inside a block comment produces a block
			 * that silently fails to parse.
			 */
			$maulik_portfolio_footer_class = 'site-footer__col ' . $maulik_portfolio_footer_column['class'];
			?>
			<!-- wp:group {"tagName":"div","className":<?php echo wp_json_encode( $maulik_portfolio_footer_class ); ?>,"layout":{"type":"constrained"}} -->
			<div class="wp-block-group <?php echo esc_attr( $maulik_portfolio_footer_class ); ?>">
				<!-- wp:heading {"level":2,"className":"site-footer__heading"} -->
				<h2 class="wp-block-heading site-footer__heading"><?php echo esc_html( $maulik_portfolio_footer_column['heading'] ); ?></h2>
				<!-- /wp:heading -->

				<?php foreach ( $maulik_portfolio_footer_column['links'] as $maulik_portfolio_footer_link ) : ?>
					<?php
					/*
					 * Three link shapes, and the branch order matters.
					 *
					 *   The mailto key: the scheme and its address are passed
					 *     through exactly as written. It is never resolved
					 *     against the home URL, which would turn the whole
					 *     thing into a broken path on this origin.
					 *   url:    an off-site profile, used as written.
					 *   path:   an internal route, resolved against the home
					 *     URL.
					 *
					 * The mailto branch is tested first because isset() on
					 * 'url' would otherwise claim it, and the design's email
					 * anchor carries neither target nor rel.
					 */
					$maulik_portfolio_footer_external = isset( $maulik_portfolio_footer_link['url'] );

					if ( isset( $maulik_portfolio_footer_link['mailto'] ) ) {
						$maulik_portfolio_footer_external = false;
						$maulik_portfolio_footer_url      = $maulik_portfolio_footer_link['mailto'];
					} elseif ( $maulik_portfolio_footer_external ) {
						$maulik_portfolio_footer_url = $maulik_portfolio_footer_link['url'];
					} else {
						$maulik_portfolio_footer_url = get_home_url( null, $maulik_portfolio_footer_link['path'] );
					}

					/*
					 * THE EXTERNAL ATTRIBUTES, AS LITERALS, DELIBERATELY NOT ESCAPED.
					 *
					 * See the note above $maulik_portfolio_footer_columns. The
					 * previous version of this line built this same fragment into a
					 * variable and then ran it through esc_attr(), which emitted
					 * &quot; for each quote. The entity survived into the
					 * attribute value, so the browser read a target of quote
					 * underscore blank quote and a rel cut off at the first
					 * space. This string holds no input: it is a fixed literal,
					 * and there is nothing in it to escape.
					 */
					$maulik_portfolio_footer_attrs = $maulik_portfolio_footer_external
						? ' target="_blank" rel="noopener noreferrer"'
						: '';

					/*
					 * THE RESUME LINK IS THE DESIGN'S ONE ACCENT LINK IN THE FOOTER.
					 *
					 * Footer.tsx line 69 states it as text-[#FACC15] with
					 * hover:text-[#FDE047] and font-medium, while every other link
					 * in the footer is text-[#D8D5CA] with hover:text-[#FACC15].
					 * Measured at 1440 the design's Resume link computes to
					 * rgb(250, 204, 21) at weight 500 and our copy of the same
					 * array computed to rgb(216, 213, 202) at weight 400.
					 *
					 * The class is built from a flag rather than typed into the
					 * markup, and the block comment's className is written with
					 * wp_json_encode() for the reason given above the columns
					 * array: a JSON class name has to be JSON encoded, and
					 * esc_attr() would put &quot; in it and the block would be
					 * dropped by Core without an error.
					 */
					$maulik_portfolio_footer_link_class = 'site-footer__link'
						. ( ! empty( $maulik_portfolio_footer_link['accent'] ) ? ' site-footer__link--accent' : '' );
					?>
					<!-- wp:paragraph {"className":<?php echo wp_json_encode( $maulik_portfolio_footer_link_class ); ?>} -->
					<p class="<?php echo esc_attr( $maulik_portfolio_footer_link_class ); ?>"><a href="<?php echo esc_url( $maulik_portfolio_footer_url ); ?>"<?php echo $maulik_portfolio_footer_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- A fixed literal with no input in it. See the note above. ?>><?php echo esc_html( $maulik_portfolio_footer_link['label'] ); ?></a></p>
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
