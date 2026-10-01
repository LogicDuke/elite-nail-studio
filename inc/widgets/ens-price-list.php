<?php
defined( 'ABSPATH' ) || exit;

/** Menu-style price list with dotted leaders; rows reveal progressively. */
class ENS_Price_List extends ENS_Widget {

	public function get_name() {
		return 'ens-price-list';
	}

	public function get_title() {
		return __( 'ENS Price List', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-price-list';
	}

	protected function register_controls() {
		$this->section( __( 'Price list', 'elite-nail-studio' ) );
		$this->field( 'title', __( 'Category', 'elite-nail-studio' ), 'text', __( 'Manicure', 'elite-nail-studio' ) );
		$this->field( 'note', __( 'Note', 'elite-nail-studio' ), 'textarea', '' );
		$this->items( 'items', __( 'Treatments', 'elite-nail-studio' ), [
			'name'     => [ __( 'Name', 'elite-nail-studio' ), 'text', __( 'Signature Manicure', 'elite-nail-studio' ) ],
			'text'     => [ __( 'Description', 'elite-nail-studio' ), 'text', '' ],
			'duration' => [ __( 'Duration', 'elite-nail-studio' ), 'text', '45 min' ],
			'price'    => [ __( 'Price', 'elite-nail-studio' ), 'text', '$55' ],
			'badge'    => [ __( 'Badge (optional)', 'elite-nail-studio' ), 'text', '' ],
		], [ [ 'name' => __( 'Signature Manicure', 'elite-nail-studio' ) ] ], 'name' );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="ens-prices">
			<?php if ( $s['title'] ) : ?>
				<h3 class="ens-prices__title ens-reveal"><?php echo esc_html( $s['title'] ); ?></h3>
			<?php endif; ?>
			<?php if ( $s['note'] ) : ?>
				<p class="ens-prices__note ens-reveal"><?php echo esc_html( $s['note'] ); ?></p>
			<?php endif; ?>
			<ul class="ens-prices__list ens-stagger">
				<?php foreach ( $s['items'] as $item ) : ?>
					<li class="ens-price">
						<p class="ens-price__row">
							<span class="ens-price__name"><?php echo esc_html( $item['name'] ); ?>
								<?php if ( $item['badge'] ) : ?>
									<em class="ens-price__badge"><?php echo esc_html( $item['badge'] ); ?></em>
								<?php endif; ?>
							</span>
							<span class="ens-price__dots" aria-hidden="true"></span>
							<span class="ens-price__amount"><?php echo esc_html( $item['price'] ); ?></span>
						</p>
						<p class="ens-price__desc"><?php echo esc_html( $item['text'] ); ?><?php if ( $item['duration'] ) : ?> <span><?php echo esc_html( $item['duration'] ); ?></span><?php endif; ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}
