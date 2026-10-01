<?php
defined( 'ABSPATH' ) || exit;

/**
 * Editorial split: layered imagery (main + overlapping detail, offset colour block,
 * floating stat card, optional arch) beside an intro, numbered points and a link.
 */
class ENS_Feature extends ENS_Widget {

	public function get_name() {
		return 'ens-feature';
	}

	public function get_title() {
		return __( 'ENS Editorial Split', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	protected function register_controls() {
		$this->section( __( 'Imagery', 'elite-nail-studio' ) );
		$this->field( 'image', __( 'Main image (4:5) — remove for a text-only block', 'elite-nail-studio' ), 'media' );
		$this->field( 'image2', __( 'Detail image (optional, 1:1)', 'elite-nail-studio' ), 'media', [ 'url' => '' ] );
		$this->field( 'side', __( 'Image side', 'elite-nail-studio' ), 'select', 'left', [ 'left' => __( 'Left', 'elite-nail-studio' ), 'right' => __( 'Right', 'elite-nail-studio' ) ] );
		$this->field( 'arch', __( 'Arch-shaped main image', 'elite-nail-studio' ), 'switch', '' );
		$this->field( 'block', __( 'Offset colour block', 'elite-nail-studio' ), 'switch', 'yes' );
		$this->field( 'stat', __( 'Stat number (optional)', 'elite-nail-studio' ), 'text', '' );
		$this->field( 'stat_label', __( 'Stat label', 'elite-nail-studio' ), 'text', '' );
		$this->end_controls_section();

		$this->section( __( 'Content', 'elite-nail-studio' ) );
		$this->intro_fields( __( 'The atelier', 'elite-nail-studio' ), 'Quiet hands, <em>precise</em> work', '' );
		$this->field( 'body', __( 'Body', 'elite-nail-studio' ), 'wysiwyg', '' );
		$this->items( 'points', __( 'Points', 'elite-nail-studio' ), [
			'title' => [ __( 'Title', 'elite-nail-studio' ), 'text', '' ],
			'text'  => [ __( 'Text', 'elite-nail-studio' ), 'textarea', '' ],
		], [], 'title' );
		$this->field( 'button', __( 'Link label', 'elite-nail-studio' ), 'text', '' );
		$this->field( 'link', __( 'Link', 'elite-nail-studio' ), 'url', '' );
		$this->end_controls_section();
	}

	protected function render() {
		$s       = $this->get_settings_for_display();
		$visual  = ! empty( $s['image']['url'] );
		$classes = 'ens-feature ens-feature--' . $s['side'] . ( 'yes' === $s['block'] ? ' ens-feature--block' : '' ) . ( $visual ? '' : ' ens-feature--text' );
		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<?php if ( $visual ) : ?>
			<div class="ens-feature__visual">
				<figure class="ens-feature__main ens-mask<?php echo 'yes' === $s['arch'] ? ' ens-arch' : ''; ?>">
					<?php echo self::img( $s['image'], 'large', [ 'sizes' => '(max-width: 1024px) 90vw, 45vw' ] ); // phpcs:ignore ?>
				</figure>
				<?php if ( ! empty( $s['image2']['url'] ) ) : ?>
					<figure class="ens-feature__detail ens-mask-x"><?php echo self::img( $s['image2'], 'medium_large', [ 'sizes' => '(max-width: 767px) 45vw, 20vw' ] ); // phpcs:ignore ?></figure>
				<?php endif; ?>
				<?php if ( $s['stat'] ) : ?>
					<p class="ens-feature__stat ens-reveal"><strong><?php echo esc_html( $s['stat'] ); ?></strong><span><?php echo esc_html( $s['stat_label'] ); ?></span></p>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<div class="ens-feature__content">
				<?php echo self::intro( $s ); // phpcs:ignore ?>
				<?php if ( $s['body'] ) : ?>
					<div class="ens-feature__body ens-prose ens-reveal"><?php echo wp_kses_post( $s['body'] ); ?></div>
				<?php endif; ?>
				<?php if ( $s['points'] ) : ?>
					<ol class="ens-points ens-stagger">
						<?php foreach ( $s['points'] as $p ) : ?>
							<li><h3><?php echo esc_html( $p['title'] ); ?></h3><p><?php echo esc_html( $p['text'] ); ?></p></li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>
				<?php if ( $s['button'] ) : ?>
					<p class="ens-reveal"><a class="ens-link"<?php echo self::href( $s['link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['button'] ); ?></a></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
