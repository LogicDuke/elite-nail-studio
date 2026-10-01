<?php
defined( 'ABSPATH' ) || exit;

/** Booking / quick booking / contact / newsletter form. Handled by inc/forms.php (no plugin needed). */
class ENS_Form extends ENS_Widget {

	public function get_name() {
		return 'ens-form';
	}

	// Per-request output (submission status / latest posts) — never serve from Elementor's element cache.
	protected function is_dynamic_content(): bool {
		return true;
	}

	public function get_title() {
		return __( 'ENS Form', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	protected function register_controls() {
		$this->section( __( 'Form', 'elite-nail-studio' ) );
		$this->field( 'type', __( 'Form type', 'elite-nail-studio' ), 'select', 'booking', [
			'booking'    => __( 'Booking', 'elite-nail-studio' ),
			'quick'      => __( 'Quick booking card', 'elite-nail-studio' ),
			'contact'    => __( 'Contact', 'elite-nail-studio' ),
			'newsletter' => __( 'Newsletter', 'elite-nail-studio' ),
		] );
		$this->field( 'heading', __( 'Heading', 'elite-nail-studio' ), 'text', '' );
		$this->field( 'intro', __( 'Intro', 'elite-nail-studio' ), 'textarea', '' );
		$this->field( 'services', __( 'Treatment options — one per line', 'elite-nail-studio' ), 'textarea', "Signature Manicure\nGel Couture\nNail Art Atelier\nSculpted Extensions\nSpa Pedicure\nHand Ritual" );
		$this->field( 'artists', __( 'Artist options — one per line', 'elite-nail-studio' ), 'textarea', __( 'No preference', 'elite-nail-studio' ) );
		$this->field( 'times', __( 'Time options — one per line', 'elite-nail-studio' ), 'textarea', "Morning (10:00 – 12:00)\nMidday (12:00 – 15:00)\nAfternoon (15:00 – 18:00)\nEvening (18:00 – 20:00)" );
		$this->field( 'submit', __( 'Button label', 'elite-nail-studio' ), 'text', __( 'Request appointment', 'elite-nail-studio' ) );
		$this->field( 'success', __( 'Success message', 'elite-nail-studio' ), 'textarea', __( 'Thank you — we will confirm your appointment within one working day.', 'elite-nail-studio' ) );
		$this->field( 'tone', __( 'Tone', 'elite-nail-studio' ), 'select', 'light', [
			'light' => __( 'Light card', 'elite-nail-studio' ),
			'plain' => __( 'No card', 'elite-nail-studio' ),
			'dark'  => __( 'On dark background', 'elite-nail-studio' ),
		] );
		$this->end_controls_section();
	}

	private static function options( $lines ) {
		return array_filter( array_map( 'trim', preg_split( '/\R/', (string) $lines ) ) );
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$type   = $s['type'];
		$id     = 'ens-form-' . $this->get_id();
		// phpcs:ignore WordPress.Security.NonceVerification -- display-only status flags from our own redirect.
		$status = ( $_GET['ens_id'] ?? '' ) === $id ? sanitize_key( $_GET['ens_form'] ?? '' ) : '';
		$back   = remove_query_arg( [ 'ens_form', 'ens_id' ], ( is_ssl() ? 'https://' : 'http://' ) . ( $_SERVER['HTTP_HOST'] ?? '' ) . ( $_SERVER['REQUEST_URI'] ?? '' ) ); // phpcs:ignore
		$opts   = [ 'service' => self::options( $s['services'] ), 'artist' => self::options( $s['artists'] ), 'time' => self::options( $s['times'] ) ];
		$msgs   = [
			'sent'    => $s['success'],
			'invalid' => __( 'Please complete the required fields with a valid email address.', 'elite-nail-studio' ),
			'error'   => __( 'Sorry, something went wrong. Please call us or try again.', 'elite-nail-studio' ),
		];
		?>
		<div class="ens-form ens-form--<?php echo esc_attr( $type . ' ens-form--' . $s['tone'] ); ?>" id="<?php echo esc_attr( $id ); ?>">
			<?php if ( $s['heading'] ) : ?>
				<h3 class="ens-form__heading"><?php echo esc_html( $s['heading'] ); ?></h3>
			<?php endif; ?>
			<?php if ( $s['intro'] ) : ?>
				<p class="ens-form__intro"><?php echo esc_html( $s['intro'] ); ?></p>
			<?php endif; ?>
			<?php if ( isset( $msgs[ $status ] ) ) : ?>
				<p class="ens-form__status ens-form__status--<?php echo esc_attr( $status ); ?>" role="status"><?php echo esc_html( $msgs[ $status ] ); ?></p>
			<?php endif; ?>
			<form class="ens-form__fields" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="ens_form">
				<input type="hidden" name="ens_type" value="<?php echo esc_attr( $type ); ?>">
				<input type="hidden" name="ens_form_id" value="<?php echo esc_attr( $id ); ?>">
				<input type="hidden" name="ens_back" value="<?php echo esc_url( $back ); ?>">
				<?php wp_nonce_field( 'ens_form', '_ens_nonce', false ); ?>
				<p class="ens-hp" aria-hidden="true"><label>Website <input type="text" name="ens_website" tabindex="-1" autocomplete="off"></label></p>
				<?php
				foreach ( ens_form_fields( $type ) as $key => [ $label, $input, $required ] ) {
					$fid  = "{$id}-{$key}";
					$req  = $required ? ' required' : '';
					$wide = in_array( $input, [ 'textarea' ], true ) || 'newsletter' === $type ? ' ens-field--wide' : '';
					echo '<p class="ens-field ens-field--' . esc_attr( $key . $wide ) . '">';
					echo '<label for="' . esc_attr( $fid ) . '">' . esc_html( $label ) . ( $required ? ' <span aria-hidden="true">*</span>' : '' ) . '</label>';
					$auto = [ 'name' => 'name', 'email' => 'email', 'phone' => 'tel' ][ $key ] ?? 'off';
					if ( 'select' === $input ) {
						echo '<select id="' . esc_attr( $fid ) . '" name="' . esc_attr( $key ) . '"' . $req . '><option value="">' . esc_html__( 'Select…', 'elite-nail-studio' ) . '</option>'; // phpcs:ignore
						foreach ( $opts[ $key ] as $o ) {
							echo '<option>' . esc_html( $o ) . '</option>';
						}
						echo '</select>';
					} elseif ( 'textarea' === $input ) {
						echo '<textarea id="' . esc_attr( $fid ) . '" name="' . esc_attr( $key ) . '" rows="4"' . $req . '></textarea>'; // phpcs:ignore
					} else {
						$min = 'date' === $input ? ' min="' . esc_attr( wp_date( 'Y-m-d' ) ) . '"' : '';
						echo '<input id="' . esc_attr( $fid ) . '" type="' . esc_attr( $input ) . '" name="' . esc_attr( $key ) . '" autocomplete="' . esc_attr( $auto ) . '"' . $req . $min . '>'; // phpcs:ignore
					}
					echo '</p>';
				}
				?>
				<p class="ens-form__submit">
					<button class="ens-btn<?php echo 'dark' === $s['tone'] ? ' ens-btn--light' : ''; ?>" type="submit"><span><?php echo esc_html( $s['submit'] ); ?></span></button>
				</p>
			</form>
		</div>
		<?php
	}
}
