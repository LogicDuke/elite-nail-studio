<?php
defined( 'ABSPATH' ) || exit;

/** Full-bleed image band with parallax image, centred call to action and optional badge. */
class ENS_Cta extends ENS_Widget {

	public function get_name() {
		return 'ens-cta';
	}

	public function get_title() {
		return __( 'ENS CTA Band', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	protected function register_controls() {
		$this->section( __( 'Call to action', 'elite-nail-studio' ) );
		$this->field( 'image', __( 'Background image (21:9)', 'elite-nail-studio' ), 'media' );
		$this->field( 'focus', __( 'Focal point (CSS object-position, e.g. 0% 50%; keep busy detail away from the centred text)', 'elite-nail-studio' ), 'text', '' );
		$this->intro_fields( __( 'Your hour of calm', 'elite-nail-studio' ), 'Reserve your <em>chair</em>', '' );
		$this->field( 'button', __( 'Button', 'elite-nail-studio' ), 'text', __( 'Book appointment', 'elite-nail-studio' ) );
		$this->field( 'link', __( 'Link', 'elite-nail-studio' ), 'url', '/booking/' );
		$this->field( 'badge', __( 'Badge text (empty = hidden)', 'elite-nail-studio' ), 'text', '' );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="ens-cta"<?php echo $s['focus'] ? ' style="--ens-focus:' . esc_attr( $s['focus'] ) . '"' : ''; ?>>
			<div class="ens-cta__media ens-parallax"><?php echo self::img( $s['image'], 'full', [ 'class' => 'ens-cover', 'sizes' => ens_cover_sizes( $s['image']['id'] ?? 0, 107, 702 ), 'alt' => '' ] ); // phpcs:ignore ?></div>
			<div class="ens-cta__inner">
				<?php echo self::intro( $s ); // phpcs:ignore ?>
				<?php if ( $s['button'] ) : ?>
					<p class="ens-reveal"><a class="ens-btn ens-btn--light"<?php echo self::href( $s['link'] ); // phpcs:ignore ?>><span><?php echo esc_html( $s['button'] ); ?></span></a></p>
				<?php endif; ?>
			</div>
			<?php if ( $s['badge'] ) : ?>
				<?php echo ens_badge( $s['badge'], $s['link']['url'] ?? '', 'ens-badge--light ens-cta__badge' ); // phpcs:ignore ?>
			<?php endif; ?>
		</section>
		<?php
	}
}
