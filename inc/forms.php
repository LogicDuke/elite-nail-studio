<?php
/**
 * Form handling for the ENS Form widget (booking, quick booking, contact, newsletter).
 * Submissions are emailed to the studio address (Customizer → Studio Details → Email,
 * falling back to the admin email). Post/Redirect/Get with a status flag in the URL.
 */

defined( 'ABSPATH' ) || exit;

/** Field definitions per form type: name => [ label, type, required ]. */
function ens_form_fields( $type ) {
	$f = [
		'name'    => [ __( 'Full name', 'elite-nail-studio' ), 'text', true ],
		'email'   => [ __( 'Email', 'elite-nail-studio' ), 'email', true ],
		'phone'   => [ __( 'Phone', 'elite-nail-studio' ), 'tel', false ],
		'service' => [ __( 'Treatment', 'elite-nail-studio' ), 'select', true ],
		'artist'  => [ __( 'Preferred artist', 'elite-nail-studio' ), 'select', false ],
		'date'    => [ __( 'Preferred date', 'elite-nail-studio' ), 'date', true ],
		'time'    => [ __( 'Preferred time', 'elite-nail-studio' ), 'select', false ],
		'subject' => [ __( 'Subject', 'elite-nail-studio' ), 'text', false ],
		'message' => [ __( 'Message', 'elite-nail-studio' ), 'textarea', false ],
	];
	$sets = [
		'booking'    => [ 'name', 'phone', 'email', 'service', 'artist', 'date', 'time', 'message' ],
		'quick'      => [ 'name', 'phone', 'service', 'date' ],
		'contact'    => [ 'name', 'email', 'phone', 'subject', 'message' ],
		'newsletter' => [ 'email' ],
	];
	$fields = array_intersect_key( $f, array_flip( $sets[ $type ] ?? [] ) );
	if ( 'quick' === $type ) {
		$fields['phone'][2] = true; // Quick booking has no email, so phone is required.
	}
	if ( 'contact' === $type ) {
		$fields['message'][2] = true;
	}
	return $fields;
}

function ens_form_handle() {
	$type     = sanitize_key( $_POST['ens_type'] ?? '' );
	$back     = wp_validate_redirect( wp_unslash( $_POST['ens_back'] ?? '' ), home_url( '/' ) );
	$form_id  = sanitize_html_class( wp_unslash( $_POST['ens_form_id'] ?? 'ens-form' ) );
	$redirect = function ( $status ) use ( $back, $form_id ) {
		wp_safe_redirect( add_query_arg( [ 'ens_form' => $status, 'ens_id' => $form_id ], $back ) . '#' . $form_id );
		exit;
	};

	if ( ! wp_verify_nonce( $_POST['_ens_nonce'] ?? '', 'ens_form' ) || ! ens_form_fields( $type ) ) {
		$redirect( 'error' );
	}
	if ( ! empty( $_POST['ens_website'] ) ) { // Honeypot: pretend success.
		$redirect( 'sent' );
	}

	$lines = [];
	foreach ( ens_form_fields( $type ) as $key => [ $label, $input, $required ] ) {
		$raw   = wp_unslash( $_POST[ $key ] ?? '' );
		$value = match ( $input ) {
			'email'    => sanitize_email( $raw ),
			'textarea' => sanitize_textarea_field( $raw ),
			default    => sanitize_text_field( $raw ),
		};
		if ( ( $required && '' === $value ) || ( 'email' === $input && '' !== $raw && ! is_email( $value ) ) ) {
			$redirect( 'invalid' );
		}
		$lines[] = "{$label}: {$value}";
	}

	$to      = is_email( ens_opt( 'ens_email' ) ) && ! str_ends_with( ens_opt( 'ens_email' ), '.example' ) ? ens_opt( 'ens_email' ) : get_option( 'admin_email' );
	$subject = sprintf( '[%s] %s', get_bloginfo( 'name' ), ucfirst( $type ) );
	$headers = [];
	if ( ! empty( $_POST['email'] ) && is_email( wp_unslash( $_POST['email'] ) ) ) {
		$headers[] = 'Reply-To: ' . sanitize_email( wp_unslash( $_POST['email'] ) );
	}

	// ponytail: email only, no DB log; add a CPT store if the studio needs a submissions inbox.
	$sent = wp_mail( $to, $subject, implode( "\n", $lines ) . "\n\n" . $back, $headers );
	$redirect( $sent ? 'sent' : 'error' );
}
add_action( 'admin_post_ens_form', 'ens_form_handle' );
add_action( 'admin_post_nopriv_ens_form', 'ens_form_handle' );
