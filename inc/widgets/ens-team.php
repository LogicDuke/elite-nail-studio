<?php
defined( 'ABSPATH' ) || exit;

/** Artists: 3:4 portraits with an overlapping info panel; swipe rail on mobile. */
class ENS_Team extends ENS_Widget {

	public function get_name() {
		return 'ens-team';
	}

	public function get_title() {
		return __( 'ENS Team', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	protected function register_controls() {
		$this->section( __( 'Artists', 'elite-nail-studio' ) );
		$this->items( 'items', __( 'Artists', 'elite-nail-studio' ), [
			'image'     => [ __( 'Portrait (3:4)', 'elite-nail-studio' ), 'media' ],
			'name'      => [ __( 'Name', 'elite-nail-studio' ), 'text', '' ],
			'role'      => [ __( 'Role', 'elite-nail-studio' ), 'text', '' ],
			'bio'       => [ __( 'Short bio', 'elite-nail-studio' ), 'textarea', '' ],
			'handle'    => [ __( 'Instagram handle', 'elite-nail-studio' ), 'text', '' ],
			'instagram' => [ __( 'Instagram URL', 'elite-nail-studio' ), 'url', '' ],
		], [ [ 'name' => 'Artist' ] ], 'name' );
		$this->field( 'columns', __( 'Columns (desktop)', 'elite-nail-studio' ), 'select', '4', [ '3' => '3', '4' => '4' ] );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo '<div class="ens-team ens-rail ens-stagger" style="--ens-cols:' . (int) $s['columns'] . '">';
		foreach ( $s['items'] as $item ) {
			?>
			<article class="ens-artist">
				<figure class="ens-artist__media"><?php echo self::img( $item['image'], 'large', [ 'sizes' => '(max-width: 767px) 72vw, (max-width: 1024px) 45vw, 24vw', 'alt' => $item['name'] ] ); // phpcs:ignore ?></figure>
				<div class="ens-artist__body">
					<p class="ens-eyebrow"><?php echo esc_html( $item['role'] ); ?></p>
					<h3 class="ens-artist__name"><?php echo esc_html( $item['name'] ); ?></h3>
					<p class="ens-artist__bio"><?php echo esc_html( $item['bio'] ); ?></p>
					<?php if ( $item['handle'] ) : ?>
						<a class="ens-artist__ig"<?php echo self::href( $item['instagram'] ); // phpcs:ignore ?>><?php echo ens_icon( 'instagram' ) . esc_html( $item['handle'] ); // phpcs:ignore ?></a>
					<?php endif; ?>
				</div>
			</article>
			<?php
		}
		echo '</div>';
	}
}
