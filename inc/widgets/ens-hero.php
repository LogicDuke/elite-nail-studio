<?php
defined( 'ABSPATH' ) || exit;

/** Hero — home (full-screen cinematic), page (inner-page banner) or split (service detail). */
class ENS_Hero extends ENS_Widget {

	public function get_name() {
		return 'ens-hero';
	}

	public function get_title() {
		return __( 'ENS Hero', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-header';
	}

	protected function register_controls() {
		$this->section( __( 'Hero', 'elite-nail-studio' ) );
		$this->field( 'layout', __( 'Layout', 'elite-nail-studio' ), 'select', 'home', [
			'home'  => __( 'Home — full screen', 'elite-nail-studio' ),
			'page'  => __( 'Page banner', 'elite-nail-studio' ),
			'split' => __( 'Split — image right', 'elite-nail-studio' ),
		] );
		$this->field( 'image', __( 'Image', 'elite-nail-studio' ), 'media' );
		$this->field( 'focus', __( 'Mobile focal point (CSS object-position)', 'elite-nail-studio' ), 'text', '60% 50%' );
		$this->field( 'eyebrow', __( 'Eyebrow', 'elite-nail-studio' ), 'text', __( 'Maison Élise — Luxury Nail Atelier', 'elite-nail-studio' ) );
		$this->field( 'title', __( 'Title — one line per row, <em> for italic', 'elite-nail-studio' ), 'textarea', "The art\nof <em>perfect</em>\nnails" );
		$this->field( 'text', __( 'Text', 'elite-nail-studio' ), 'textarea', '' );
		$this->field( 'meta', __( 'Facts (split layout) — "Label | Value" per line', 'elite-nail-studio' ), 'textarea', '' );
		$this->field( 'btn1', __( 'Primary button', 'elite-nail-studio' ), 'text', __( 'Book appointment', 'elite-nail-studio' ) );
		$this->field( 'btn1_link', __( 'Primary link', 'elite-nail-studio' ), 'url', '/booking/' );
		$this->field( 'btn2', __( 'Secondary link', 'elite-nail-studio' ), 'text', '' );
		$this->field( 'btn2_link', __( 'Secondary link URL', 'elite-nail-studio' ), 'url', '' );
		$this->end_controls_section();

		$this->section( __( 'Extras', 'elite-nail-studio' ) );
		$this->field( 'inset', __( 'Arch inset image (home)', 'elite-nail-studio' ), 'media', [ 'url' => '' ] );
		$this->field( 'badge', __( 'Rotating badge text (empty = hidden)', 'elite-nail-studio' ), 'text', __( 'Book an appointment · Maison Élise · ', 'elite-nail-studio' ) );
		$this->field( 'badge_link', __( 'Badge link', 'elite-nail-studio' ), 'url', '/booking/' );
		$this->field( 'scroll_cue', __( 'Scroll cue', 'elite-nail-studio' ), 'switch', 'yes' );
		$this->end_controls_section();
	}

	protected function render() {
		$s      = $this->get_settings_for_display();
		$layout = $s['layout'];
		$is_h1  = 'home' === $layout ? 'ens-hero__title ens-hero__title--display' : 'ens-hero__title';
		$lines  = array_filter( array_map( 'trim', preg_split( '/\R/', $s['title'] ) ) );
		$style  = $s['focus'] ? ' style="--ens-focus:' . esc_attr( $s['focus'] ) . '"' : '';
		?>
		<section class="ens-hero ens-hero--<?php echo esc_attr( $layout ); ?>"<?php echo $style; // phpcs:ignore ?>>
			<div class="ens-hero__media<?php echo 'split' === $layout ? ' ens-mask' : ''; ?>">
				<?php echo self::img( $s['image'], 'full', [ 'class' => 'ens-hero__img', 'sizes' => 'split' === $layout ? '(max-width: 1024px) 100vw, 50vw' : '100vw', 'loading' => 'eager', 'fetchpriority' => 'high' ] ); // phpcs:ignore ?>
			</div>
			<div class="ens-hero__inner">
				<?php if ( $s['eyebrow'] ) : ?>
					<p class="ens-eyebrow ens-hero__eyebrow ens-reveal"><?php echo esc_html( $s['eyebrow'] ); ?></p>
				<?php endif; ?>
				<h1 class="<?php echo esc_attr( $is_h1 ); ?> ens-lines">
					<?php foreach ( $lines as $i => $line ) : ?>
						<span class="ens-line" style="--i:<?php echo (int) $i; ?>"><span><?php echo self::rich( $line ); // phpcs:ignore ?></span></span>
					<?php endforeach; ?>
				</h1>
				<?php if ( $s['text'] ) : ?>
					<p class="ens-hero__text ens-reveal"><?php echo esc_html( $s['text'] ); ?></p>
				<?php endif; ?>
				<?php if ( 'split' === $layout && $s['meta'] ) : ?>
					<dl class="ens-facts ens-reveal">
						<?php foreach ( preg_split( '/\R/', trim( $s['meta'] ) ) as $row ) : ?>
							<?php [ $k, $v ] = array_map( 'trim', explode( '|', $row . '|' ) ); ?>
							<div><dt><?php echo esc_html( $k ); ?></dt><dd><?php echo esc_html( $v ); ?></dd></div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
				<?php if ( $s['btn1'] || $s['btn2'] ) : ?>
					<p class="ens-hero__actions ens-reveal">
						<?php if ( $s['btn1'] ) : ?>
							<a class="ens-btn <?php echo 'split' === $layout ? '' : 'ens-btn--light'; ?>"<?php echo self::href( $s['btn1_link'] ); // phpcs:ignore ?>><span><?php echo esc_html( $s['btn1'] ); ?></span></a>
						<?php endif; ?>
						<?php if ( $s['btn2'] ) : ?>
							<a class="ens-link"<?php echo self::href( $s['btn2_link'] ); // phpcs:ignore ?>><?php echo esc_html( $s['btn2'] ); ?></a>
						<?php endif; ?>
					</p>
				<?php endif; ?>
			</div>
			<?php if ( 'home' === $layout && ! empty( $s['inset']['url'] ) ) : ?>
				<figure class="ens-hero__inset ens-mask"><?php echo self::img( $s['inset'], 'ens-card', [ 'sizes' => '22vw' ] ); // phpcs:ignore ?></figure>
			<?php endif; ?>
			<?php if ( 'page' !== $layout && $s['badge'] ) : ?>
				<?php echo ens_badge( $s['badge'], $s['badge_link']['url'] ?? '', 'ens-hero__badge' ); // phpcs:ignore ?>
			<?php endif; ?>
			<?php if ( 'home' === $layout && 'yes' === $s['scroll_cue'] ) : ?>
				<span class="ens-hero__cue" aria-hidden="true"><?php esc_html_e( 'Scroll', 'elite-nail-studio' ); ?></span>
			<?php endif; ?>
		</section>
		<?php
	}
}
