<?php
/**
 * Form handling for the ENS Form widget (booking, quick booking, contact, newsletter).
 *
 * WordPress: the form posts to admin-post.php; submissions are emailed to the studio
 * address (Customizer → Studio Details → Email). Without a real address (empty or a
 * reserved .example demo address) nothing is sent and the visitor sees the demo
 * message: there is no fallback to any other mailbox. Post/Redirect/Get with a status
 * flag in the URL.
 *
 * Static export (Simply Static): each form is rewritten to post to the Cloudflare Pages
 * Function in cloudflare/functions/api/form.js (assets/js/forms.js submits it), which
 * validates the same fields. See docs/static-forms.md. Keep both field lists in sync.
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

	$to = ens_opt( 'ens_email' );
	if ( ! is_email( $to ) || str_ends_with( $to, '.example' ) ) {
		$redirect( 'demo' ); // No real recipient: never fall back to another mailbox.
	}
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

/** Path of the static form endpoint (Cloudflare Pages Function). */
function ens_static_form_endpoint(): string {
	return (string) apply_filters( 'ens_static_form_endpoint', '/api/form' );
}

/** Simply Static keeps excluded (wp-admin) form actions untouched; let admin-post.php reach the rewrite below. */
add_filter( 'simply_static_preserve_form_action', function ( $preserve, $action ) {
	return str_contains( (string) wp_parse_url( (string) $action, PHP_URL_PATH ), '/admin-post.php' ) ? false : $preserve;
}, 10, 2 );

/**
 * Simply Static export: point every ENS form at the static endpoint and drop the
 * WordPress-only fields (admin-post action, nonce, absolute return URL).
 */
add_filter( 'ss_after_replace_urls_in_html', function ( $html ) {
	if ( ! is_string( $html ) || ! str_contains( $html, 'ens-form__fields' ) ) {
		return $html;
	}
	$html = (string) preg_replace(
		'/(<form class="ens-form__fields" method="post") action="[^"]*admin-post\.php"/',
		'$1 action="' . esc_attr( ens_static_form_endpoint() ) . '" data-ens-static',
		$html
	);
	return (string) preg_replace( '/\s*<input type="hidden" (?:id="_ens_nonce" )?name="(?:action|_ens_nonce|ens_back)" [^>]*>/', '', $html );
}, 10, 1 );
