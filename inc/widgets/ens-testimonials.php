<?php
defined( 'ABSPATH' ) || exit;

/** Editorial quote slider: native scroll-snap track, prev/next buttons, no autoplay. */
class ENS_Testimonials extends ENS_Widget {

	public function get_name() {
		return 'ens-testimonials';
	}

	public function get_title() {
		return __( 'ENS Testimonials', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	protected function register_controls() {
		$this->section( __( 'Testimonials', 'elite-nail-studio' ) );
		$this->field( 'eyebrow', __( 'Eyebrow', 'elite-nail-studio' ), 'text', __( 'Kind words', 'elite-nail-studio' ) );
		$this->items( 'items', __( 'Quotes', 'elite-nail-studio' ), [
			'quote'  => [ __( 'Quote', 'elite-nail-studio' ), 'textarea', '' ],
			'name'   => [ __( 'Name', 'elite-nail-studio' ), 'text', '' ],
			'detail' => [ __( 'Detail', 'elite-nail-studio' ), 'text', '' ],
		], [ [ 'name' => 'Client' ] ], 'name' );
		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$count = count( $s['items'] );
		?>
		<div class="ens-quotes" data-ens-slider>
			<div class="ens-quotes__head">
				<?php if ( $s['eyebrow'] ) : ?>
					<p class="ens-eyebrow ens-reveal"><?php echo esc_html( $s['eyebrow'] ); ?></p>
				<?php endif; ?>
				<p class="ens-quotes__stars" aria-label="<?php esc_attr_e( 'Rated 5 out of 5', 'elite-nail-studio' ); ?>"><?php echo str_repeat( ens_icon( 'star' ), 5 ); // phpcs:ignore ?></p>
			</div>
			<div class="ens-quotes__track" data-ens-slider-track tabindex="0" aria-label="<?php esc_attr_e( 'Testimonials', 'elite-nail-studio' ); ?>">
				<?php foreach ( $s['items'] as $item ) : ?>
					<figure class="ens-quote">
						<blockquote class="ens-quote__text"><p><?php echo esc_html( $item['quote'] ); ?></p></blockquote>
						<figcaption class="ens-quote__by"><strong><?php echo esc_html( $item['name'] ); ?></strong><span><?php echo esc_html( $item['detail'] ); ?></span></figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
			<?php if ( $count > 1 ) : ?>
				<div class="ens-quotes__nav">
					<button type="button" class="ens-round" data-ens-slider-prev aria-label="<?php esc_attr_e( 'Previous', 'elite-nail-studio' ); ?>"><?php echo ens_icon( 'arrow-l' ); // phpcs:ignore ?></button>
					<p class="ens-quotes__count"><span data-ens-slider-index>01</span> / <?php echo esc_html( str_pad( (string) $count, 2, '0', STR_PAD_LEFT ) ); ?></p>
					<button type="button" class="ens-round" data-ens-slider-next aria-label="<?php esc_attr_e( 'Next', 'elite-nail-studio' ); ?>"><?php echo ens_icon( 'arrow' ); // phpcs:ignore ?></button>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
