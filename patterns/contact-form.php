<?php
/**
 * Title: Contact Form
 * Slug: maulik-portfolio/contact-form
 * Categories: maulik-portfolio-contact
 * Description: The contact page form section, four labelled fields, an intent dropdown and an honest note that nothing submits yet.
 * Inserter: yes
 *
 * A DELIBERATE PLACEHOLDER. The owner asked for a contact form and asked for an
 * intent dropdown on it. What is delivered here is the markup, and only the
 * markup: the form is real, valid HTML with real labels, real autocomplete
 * tokens and a real required contract, and it posts to the page it is rendered
 * on. It cannot deliver a message to anybody, because there is no handler for
 * it to post to.
 *
 * WHY THERE IS NO HANDLER. WordPress 7.1.2 ships no core/form block,
 * wp-includes/blocks/form.php is not present in this install, and nothing in
 * this theme or on this site filters one in or otherwise processes a request.
 * That was checked, not assumed.
 *
 * WHY NOTHING FAKES THE SUBMISSION. There is no script on this site that
 * intercepts this form, and there is no success message in this pattern. A
 * reader who fills this in, presses submit and is told it worked, when nothing
 * was sent anywhere, is worse off than a reader who can see that this form is
 * inert. So the form is honest and the note says so in as many words. The
 * submit button is not disabled either: a disabled button is hidden from
 * assistive technology in several screen readers, which would hide the state of
 * the page from exactly the people a placeholder form most needs to be honest
 * to.
 *
 * WHY IT IS A PATTERN AND NOT A BLOCK. Core auto-registers every PHP file in a
 * block theme's patterns/ directory, and functions.php requires only inc/*.php.
 * A dynamic block would have meant adding a require_once line to functions.php,
 * which is a shared file. A pattern needs no edit there.
 *
 * WHY THE FORM IS RAW MARKUP INSIDE A GROUP. The theme has no block that holds
 * a form control, and docs/ARCHITECTURE.md 2.4 bans the raw HTML block, block
 * level shortcodes and pasted markup in page content. Page content here is one
 * pattern reference and nothing else, which is what that rule is protecting. The
 * markup lives in a pattern, which is a supported location, not in post_content.
 *
 * THE INTENT OPTIONS ARE NOT INVENTED. Every option string is taken from
 * wording this site already publishes, and the four sources are recorded in the
 * comments beside each option below.
 *
 * THERE IS NO EMAIL ADDRESS IN THIS FILE AND THERE IS NO MAILTO. A field
 * labelled Email is not an address, and the address this site once published was
 * spam listed inside 48 hours.
 *
 * @package Maulik_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Where the form posts to. The current page, so the markup is a real form and
 * not a fake one. The contact page is the only page this pattern belongs on,
 * so that path is the fallback when there is no permalink in scope, which is
 * the case when the pattern is rendered outside a loop.
 */
$maulik_portfolio_contact_form_action = get_permalink();
if ( ! $maulik_portfolio_contact_form_action ) {
	$maulik_portfolio_contact_form_action = home_url( '/contact/' );
}

/*
 * The intent options, each one lifted from copy this site already publishes.
 *
 * "Professional opportunities", "technical conversations" and "questions about
 * my development work" are the three intents the contact page hero lede
 * already names, in that order, in content/pages/contact.html. Nothing was
 * added to that set. The leading empty value option is the prompt, so no
 * option is a valid answer pretending to be a placeholder.
 */
$maulik_portfolio_contact_intents = array(
	__( 'Professional opportunities', 'maulik-portfolio' ),
	__( 'Technical conversations', 'maulik-portfolio' ),
	__( 'Questions about my development work', 'maulik-portfolio' ),
);

$maulik_portfolio_contact_form_group = array(
	'className' => 'is-style-section contact-form-section',
	'layout'    => array(
		'type' => 'default',
	),
);

?>
<!-- wp:group <?php echo wp_json_encode( $maulik_portfolio_contact_form_group ); ?> -->
<section class="wp-block-group is-style-section contact-form-section" aria-label="<?php echo esc_attr__( 'Contact form section', 'maulik-portfolio' ); ?>">
	<div class="contact-form">
		<h2 class="wp-block-heading contact-form-title" id="heading-contact-form"><?php echo esc_html__( 'Send a message', 'maulik-portfolio' ); ?></h2>
		<p class="contact-form-note" id="contact-form-note"><?php echo esc_html__( 'This form is a placeholder. Nothing on this site processes a submission yet, so pressing send delivers no message and shows no confirmation. Use one of the published channels until a real handler is connected.', 'maulik-portfolio' ); ?></p>
		<p class="contact-form-required-note" id="contact-form-required-note"><?php echo esc_html__( 'Fields marked with an asterisk are required.', 'maulik-portfolio' ); ?></p>
		<form class="contact-form-fields" method="post" action="<?php echo esc_url( $maulik_portfolio_contact_form_action ); ?>" aria-label="<?php echo esc_attr__( 'Contact form', 'maulik-portfolio' ); ?>">
			<p class="contact-form-field">
				<label class="contact-form-label" for="contact-form-name"><?php echo esc_html__( 'Name', 'maulik-portfolio' ); ?><span class="contact-form-required" aria-hidden="true">*</span></label>
				<input class="contact-form-control" type="text" id="contact-form-name" name="name" autocomplete="name" required aria-describedby="contact-form-required-note">
			</p>
			<p class="contact-form-field">
				<label class="contact-form-label" for="contact-form-email"><?php echo esc_html__( 'Email', 'maulik-portfolio' ); ?><span class="contact-form-required" aria-hidden="true">*</span></label>
				<input class="contact-form-control" type="email" id="contact-form-email" name="email" autocomplete="email" required aria-describedby="contact-form-required-note">
			</p>
			<p class="contact-form-field">
				<label class="contact-form-label" for="contact-form-intent"><?php echo esc_html__( 'Intent', 'maulik-portfolio' ); ?><span class="contact-form-required" aria-hidden="true">*</span></label>
				<select class="contact-form-control" id="contact-form-intent" name="intent" required aria-describedby="contact-form-required-note">
					<option value=""><?php echo esc_html__( 'Select one', 'maulik-portfolio' ); ?></option>
					<?php foreach ( $maulik_portfolio_contact_intents as $maulik_portfolio_contact_intent ) : ?>
						<option value="<?php echo esc_attr( $maulik_portfolio_contact_intent ); ?>"><?php echo esc_html( $maulik_portfolio_contact_intent ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			<p class="contact-form-field">
				<label class="contact-form-label" for="contact-form-message"><?php echo esc_html__( 'Message', 'maulik-portfolio' ); ?><span class="contact-form-required" aria-hidden="true">*</span></label>
				<textarea class="contact-form-control contact-form-textarea" id="contact-form-message" name="message" rows="6" required aria-describedby="contact-form-required-note"></textarea>
			</p>
			<div class="wp-block-button contact-btn contact-btn-amber">
				<button class="wp-block-button__link wp-element-button" type="submit"><?php echo esc_html__( 'Send message', 'maulik-portfolio' ); ?></button>
			</div>
		</form>
	</div>
</section>
<!-- /wp:group -->