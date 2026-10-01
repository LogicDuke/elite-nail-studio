<?php
defined( 'ABSPATH' ) || exit;

/** Treatments grid: 3 / 2 columns, horizontal snap rail on mobile, staggered entrance. */
class ENS_Services extends ENS_Widget {

	public function get_name() {
		return 'ens-services';
	}

	public function get_title() {
		return __( 'ENS Services Grid', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	protected function register_controls() {
		$this->section( __( 'Treatments', 'elite-nail-studio' ) );
		$this->items( 'items', __( 'Treatments', 'elite-nail-studio' ), [
			'image'    => [ __( 'Image (4:5)', 'elite-nail-studio' ), 'media' ],
			'title'    => [ __( 'Title', 'elite-nail-studio' ), 'text', __( 'Signature Manicure', 'elite-nail-studio' ) ],
			'text'     => [ __( 'Description', 'elite-nail-studio' ), 'textarea', '' ],
			'price'    => [ __( 'Price', 'elite-nail-studio' ), 'text', __( 'from $55', 'elite-nail-studio' ) ],
			'duration' => [ __( 'Duration', 'elite-nail-studio' ), 'text', __( '60 min', 'elite-nail-studio' ) ],
			'link'     => [ __( 'Link', 'elite-nail-studio' ), 'url', '' ],
		], [ [ 'title' => __( 'Signature Manicure', 'elite-nail-studio' ) ] ], 'title' );
		$this->field( 'columns', __( 'Columns (desktop)', 'elite-nail-studio' ), 'select', '3', [ '2' => '2', '3' => '3' ] );
		$this->field( 'link_label', __( 'Link label', 'elite-nail-studio' ), 'text', __( 'Discover', 'elite-nail-studio' ) );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<div class="ens-services ens-rail ens-stagger" style="--ens-cols:' . (int) $s['columns'] . '">';
		foreach ( $s['items'] as $i => $item ) {
			$href = self::href( $item['link'] );
			$tag  = $href ? 'a' : 'div';
			?>
			<article class="ens-service">
				<<?php echo $tag . $href; // phpcs:ignore ?> class="ens-service__link">
					<figure class="ens-service__media ens-zoom">
						<?php echo self::img( $item['image'], 'ens-card', [ 'sizes' => '(max-width: 767px) 78vw, (max-width: 1024px) 45vw, 30vw' ] ); // phpcs:ignore ?>
						<span class="ens-service__num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					</figure>
					<div class="ens-service__body">
						<h3 class="ens-service__title"><?php echo esc_html( $item['title'] ); ?></h3>
						<?php if ( $item['text'] ) : ?>
							<p class="ens-service__text"><?php echo esc_html( $item['text'] ); ?></p>
						<?php endif; ?>
						<p class="ens-service__meta">
							<span><?php echo esc_html( $item['price'] ); ?></span>
							<span><?php echo esc_html( $item['duration'] ); ?></span>
						</p>
						<?php if ( $href && $s['link_label'] ) : ?>
							<span class="ens-link" aria-hidden="true"><?php echo esc_html( $s['link_label'] ); ?></span>
						<?php endif; ?>
					</div>
				</<?php echo $tag; // phpcs:ignore ?>>
			</article>
			<?php
		}
		echo '</div>';
	}
}
