<?php
defined( 'ABSPATH' ) || exit;

/** Filterable masonry gallery; opens in Elementor's built-in lightbox. */
class ENS_Gallery extends ENS_Widget {

	public function get_name() {
		return 'ens-gallery';
	}

	public function get_title() {
		return __( 'ENS Filter Gallery', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-gallery-masonry';
	}

	protected function register_controls() {
		$this->section( __( 'Gallery', 'elite-nail-studio' ) );
		$this->items( 'items', __( 'Images', 'elite-nail-studio' ), [
			'image'    => [ __( 'Image', 'elite-nail-studio' ), 'media' ],
			'category' => [ __( 'Category (filter)', 'elite-nail-studio' ), 'text', __( 'Gel', 'elite-nail-studio' ) ],
			'caption'  => [ __( 'Caption', 'elite-nail-studio' ), 'text', '' ],
		], [ [ 'caption' => 'Look' ] ], 'caption' );
		$this->field( 'all_label', __( '"All" label', 'elite-nail-studio' ), 'text', __( 'All looks', 'elite-nail-studio' ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$cats = array_values( array_unique( array_filter( array_map( fn( $i ) => trim( $i['category'] ), $s['items'] ) ) ) );
		$id   = 'ens-gallery-' . $this->get_id();
		?>
		<div class="ens-gallery" data-ens-gallery>
			<?php if ( count( $cats ) > 1 ) : ?>
				<ul class="ens-chips ens-reveal" aria-label="<?php esc_attr_e( 'Filter looks', 'elite-nail-studio' ); ?>">
					<li><button type="button" class="ens-chip is-active" data-ens-filter="*" aria-pressed="true"><?php echo esc_html( $s['all_label'] ); ?></button></li>
					<?php foreach ( $cats as $cat ) : ?>
						<li><button type="button" class="ens-chip" data-ens-filter="<?php echo esc_attr( sanitize_title( $cat ) ); ?>" aria-pressed="false"><?php echo esc_html( $cat ); ?></button></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<div class="ens-gallery__grid ens-stagger">
				<?php
				foreach ( $s['items'] as $item ) :
					$full = ! empty( $item['image']['id'] ) ? wp_get_attachment_image_url( $item['image']['id'], 'full' ) : ( $item['image']['url'] ?? '' );
					?>
					<figure class="ens-gallery__item" data-ens-cat="<?php echo esc_attr( sanitize_title( $item['category'] ) ); ?>">
						<a class="ens-zoom" href="<?php echo esc_url( $full ); ?>" data-elementor-open-lightbox="yes" data-elementor-lightbox-slideshow="<?php echo esc_attr( $id ); ?>" data-elementor-lightbox-title="<?php echo esc_attr( $item['caption'] ); ?>">
							<?php echo self::img( $item['image'], 'large', [ 'sizes' => '(max-width: 767px) 50vw, 33vw', 'alt' => $item['caption'] ] ); // phpcs:ignore ?>
						</a>
						<?php if ( $item['caption'] ) : ?>
							<figcaption><span><?php echo esc_html( $item['category'] ); ?></span><?php echo esc_html( $item['caption'] ); ?></figcaption>
						<?php endif; ?>
					</figure>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
