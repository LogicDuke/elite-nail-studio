<?php
defined( 'ABSPATH' ) || exit;

/**
 * Horizontal lookbook: on desktop, vertical scroll drives the rail sideways (sticky stage).
 * Tablet/mobile, reduced motion and the editor get a native swipe rail.
 */
class ENS_Lookbook extends ENS_Widget {

	public function get_name() {
		return 'ens-lookbook';
	}

	public function get_title() {
		return __( 'ENS Horizontal Lookbook', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
	}

	protected function register_controls() {
		$this->section( __( 'Lookbook', 'elite-nail-studio' ) );
		$this->intro_fields( __( 'Lookbook', 'elite-nail-studio' ), 'The season, <em>in detail</em>', '' );
		$this->items( 'items', __( 'Looks', 'elite-nail-studio' ), [
			'image'   => [ __( 'Image (portrait)', 'elite-nail-studio' ), 'media' ],
			'caption' => [ __( 'Caption', 'elite-nail-studio' ), 'text', __( 'Milk & honey French', 'elite-nail-studio' ) ],
			'tag'     => [ __( 'Tag', 'elite-nail-studio' ), 'text', __( 'Gel couture', 'elite-nail-studio' ) ],
		], [ [ 'caption' => __( 'Milk & honey French', 'elite-nail-studio' ) ] ], 'caption' );
		$this->field( 'cta', __( 'End card label', 'elite-nail-studio' ), 'text', __( 'View the full lookbook', 'elite-nail-studio' ) );
		$this->field( 'cta_link', __( 'End card link', 'elite-nail-studio' ), 'url', '/lookbook/' );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<section class="ens-lookbook" data-ens-lookbook>
			<div class="ens-lookbook__stage">
				<?php echo self::intro( $s, 'h2', 'ens-lookbook__intro' ); // phpcs:ignore ?>
				<div class="ens-lookbook__viewport">
					<ul class="ens-lookbook__track" data-ens-lookbook-track>
						<?php foreach ( $s['items'] as $i => $item ) : ?>
							<li class="ens-look<?php echo $i % 2 ? ' ens-look--low' : ''; ?>">
								<figure class="ens-look__media ens-zoom"><?php echo self::img( $item['image'], 'ens-card', [ 'sizes' => '(max-width: 767px) 72vw, 28vw' ] ); // phpcs:ignore ?></figure>
								<p class="ens-look__caption"><span><?php echo esc_html( $item['tag'] ); ?></span><?php echo esc_html( $item['caption'] ); ?></p>
							</li>
						<?php endforeach; ?>
						<?php if ( $s['cta'] ) : ?>
							<li class="ens-look ens-look--end">
								<a class="ens-look__end"<?php echo self::href( $s['cta_link'] ); // phpcs:ignore ?>>
									<span><?php echo esc_html( $s['cta'] ); ?></span><?php echo ens_icon( 'arrow' ); // phpcs:ignore ?>
								</a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
				<div class="ens-lookbook__progress" aria-hidden="true"><span data-ens-lookbook-bar></span></div>
			</div>
		</section>
		<?php
	}
}
