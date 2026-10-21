<?php
/**
 * Plugin-free enquiry form: a server-rendered block plus an admin-post
 * handler that emails the message. Nothing is written to the database.
 *
 * @package Arilia
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function arilia_register_blocks() {
	register_block_type( ARILIA_DIR . '/blocks/enquiry-form' );
}
add_action( 'init', 'arilia_register_blocks' );

function arilia_enquiry_interests() {
	return array(
		'retail'       => 'Retail / stockist',
		'distribution' => 'Distribution',
		'hospitality'  => 'Hospitality / events',
		'other'        => 'Something else',
	);
}

/**
 * Markup for the form. Called by blocks/enquiry-form/render.php.
 */
function arilia_enquiry_form_html( $attributes = array() ) {
	$status  = isset( $_GET['enquiry'] ) ? sanitize_key( wp_unslash( $_GET['enquiry'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$notice  = '';
	$heading = ! empty( $attributes['heading'] ) ? $attributes['heading'] : '';
	if ( 'sent' === $status ) {
		$notice = '<p class="arilia-form__notice arilia-form__notice--ok" role="status">Thank you. Your enquiry has been sent and we’ll be in touch shortly.</p>';
	} elseif ( 'error' === $status ) {
		$notice = '<p class="arilia-form__notice arilia-form__notice--error" role="alert">Sorry, something went wrong. Please check the fields and try again, or WhatsApp us using the button below.</p>';
	} elseif ( 'limit' === $status ) {
		$notice = '<p class="arilia-form__notice arilia-form__notice--error" role="alert">Too many messages in a short time. Please try again in a few minutes.</p>';
	}

	$opts = '';
	foreach ( arilia_enquiry_interests() as $key => $label ) {
		$opts .= '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
	}
	$issued = time();
	$token  = wp_hash( 'arilia_enquiry_' . $issued );

	ob_start();
	?>
	<div class="arilia-form" id="enquiry">
		<?php echo $heading ? '<h3 class="arilia-form__heading">' . esc_html( $heading ) . '</h3>' : ''; ?>
		<?php echo $notice; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="arilia-form__form">
			<input type="hidden" name="action" value="arilia_enquiry">
			<input type="hidden" name="arilia_issued" value="<?php echo esc_attr( $issued ); ?>">
			<input type="hidden" name="arilia_token" value="<?php echo esc_attr( $token ); ?>">
			<input type="hidden" name="arilia_return" value="<?php echo esc_url( get_permalink() ? get_permalink() : home_url( '/' ) ); ?>">
			<p class="arilia-form__hp" aria-hidden="true"><label>Leave this field empty <input type="text" name="arilia_website" tabindex="-1" autocomplete="off"></label></p>
			<div class="arilia-form__grid">
				<p class="arilia-form__field"><label for="aq-name">Your name <span>*</span></label><input id="aq-name" name="name" type="text" required maxlength="120" autocomplete="name"></p>
				<p class="arilia-form__field"><label for="aq-company">Business / company</label><input id="aq-company" name="company" type="text" maxlength="160" autocomplete="organization"></p>
				<p class="arilia-form__field"><label for="aq-email">Email <span>*</span></label><input id="aq-email" name="email" type="email" required maxlength="160" autocomplete="email"></p>
				<p class="arilia-form__field"><label for="aq-phone">Phone / WhatsApp</label><input id="aq-phone" name="phone" type="tel" maxlength="40" autocomplete="tel"></p>
				<p class="arilia-form__field"><label for="aq-city">City</label><input id="aq-city" name="city" type="text" maxlength="120" autocomplete="address-level2"></p>
				<p class="arilia-form__field"><label for="aq-interest">I’m interested in</label><select id="aq-interest" name="interest"><?php echo $opts; // phpcs:ignore WordPress.Security.EscapeOutput ?></select></p>
				<p class="arilia-form__field arilia-form__field--full"><label for="aq-message">Message <span>*</span></label><textarea id="aq-message" name="message" rows="5" required maxlength="3000" placeholder="Which flavours are you interested in, and roughly what volumes?"></textarea></p>
			</div>
			<p class="arilia-form__actions"><button type="submit" class="wp-block-button__link wp-element-button">Send enquiry</button> <a class="arilia-form__alt" href="<?php echo esc_url( arilia_whatsapp_href() ); ?>">or WhatsApp us</a></p>
			<p class="arilia-form__privacy">We use these details only to reply to your enquiry. Nothing is stored on this website.</p>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Handle submissions (logged-in and anonymous).
 */
function arilia_handle_enquiry() {
	$return = isset( $_POST['arilia_return'] ) ? esc_url_raw( wp_unslash( $_POST['arilia_return'] ) ) : home_url( '/' ); // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! $return || wp_parse_url( $return, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
		$return = home_url( '/' );
	}
	$back = function ( $status ) use ( $return ) {
		wp_safe_redirect( add_query_arg( 'enquiry', $status, $return ) . '#enquiry' );
		exit;
	};

	// phpcs:disable WordPress.Security.NonceVerification -- stateless token below replaces a nonce so caching never breaks the form.
	$issued = isset( $_POST['arilia_issued'] ) ? (int) $_POST['arilia_issued'] : 0;
	$token  = isset( $_POST['arilia_token'] ) ? sanitize_text_field( wp_unslash( $_POST['arilia_token'] ) ) : '';
	if ( ! $issued || ! hash_equals( wp_hash( 'arilia_enquiry_' . $issued ), $token ) || $issued > time() || $issued < time() - DAY_IN_SECONDS ) {
		$back( 'error' );
	}
	// Honeypot and "too fast to be human" check.
	if ( ! empty( $_POST['arilia_website'] ) || time() - $issued < 3 ) {
		$back( 'sent' ); // Pretend success to bots.
	}
	$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key  = 'arilia_rl_' . md5( $ip );
	$hits = (int) get_transient( $key );
	if ( $hits >= 3 ) {
		$back( 'limit' );
	}
	set_transient( $key, $hits + 1, 10 * MINUTE_IN_SECONDS );

	$name     = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$company  = sanitize_text_field( wp_unslash( $_POST['company'] ?? '' ) );
	$email    = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$phone    = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$city     = sanitize_text_field( wp_unslash( $_POST['city'] ?? '' ) );
	$interest = sanitize_key( wp_unslash( $_POST['interest'] ?? 'other' ) );
	$message  = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );
	// phpcs:enable

	$interests = arilia_enquiry_interests();
	if ( '' === $name || '' === $message || ! is_email( $email ) ) {
		$back( 'error' );
	}
	$interest_label = $interests[ $interest ] ?? $interests['other'];

	$lines = array(
		'New enquiry from the Arilia website',
		'',
		'Name:      ' . $name,
		'Company:   ' . ( $company ? $company : '—' ),
		'Email:     ' . $email,
		'Phone:     ' . ( $phone ? $phone : '—' ),
		'City:      ' . ( $city ? $city : '—' ),
		'Interest:  ' . $interest_label,
		'',
		'Message:',
		$message,
		'',
		'— Sent ' . gmdate( 'Y-m-d H:i' ) . ' UTC from ' . home_url( '/' ),
	);
	$to      = arilia_contact( 'email' );
	$subject = sprintf( '[Arilia enquiry] %s – %s', $interest_label, $name );
	$headers = array( 'Reply-To: ' . $name . ' <' . $email . '>' );
	$sent    = wp_mail( $to, $subject, implode( "\n", $lines ), $headers );
	$back( $sent ? 'sent' : 'error' );
}
add_action( 'admin_post_arilia_enquiry', 'arilia_handle_enquiry' );
add_action( 'admin_post_nopriv_arilia_enquiry', 'arilia_handle_enquiry' );
