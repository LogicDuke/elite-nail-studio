<?php
defined( 'ABSPATH' ) || exit;

/** Continuous typographic marquee (pure CSS; stops under reduced motion). */
class ENS_Marquee extends ENS_Widget {

	public function get_name() {
		return 'ens-marquee';
	}

	public function get_title() {
		return __( 'ENS Marquee', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-animation-text';
	}

	protected function register_controls() {
		$this->section( __( 'Marquee', 'elite-nail-studio' ) );
		$this->field( 'words', __( 'Words — one per line', 'elite-nail-studio' ), 'textarea', "Manicure\nGel couture\nNail art\nSculpted extensions\nSpa pedicure\nHand rituals" );
		$this->field( 'style', __( 'Style', 'elite-nail-studio' ), 'select', 'outline', [
			'outline' => __( 'Alternating outline', 'elite-nail-studio' ),
			'solid'   => __( 'Solid', 'elite-nail-studio' ),
		] );
		$this->field( 'speed', __( 'Seconds per loop', 'elite-nail-studio' ), 'number', 40 );
		$this->field( 'reverse', __( 'Reverse direction', 'elite-nail-studio' ), 'switch', '' );
		$this->end_controls_section();
	}

	protected function render() {
		$s     = $this->get_settings_for_display();
		$words = array_filter( array_map( 'trim', preg_split( '/\R/', $s['words'] ) ) );
		$items = implode( '', array_map( fn( $w ) => '<span class="ens-marquee__item">' . esc_html( $w ) . '</span><span class="ens-marquee__sep">✦</span>', $words ) );
		printf(
			'<div class="ens-marquee ens-marquee--%1$s%2$s" style="--ens-marquee-dur:%3$ds" role="marquee" aria-label="%4$s"><div class="ens-marquee__track"><div class="ens-marquee__group">%5$s</div><div class="ens-marquee__group" aria-hidden="true">%5$s</div></div></div>',
			esc_attr( $s['style'] ),
			'yes' === $s['reverse'] ? ' ens-marquee--reverse' : '',
			max( 8, (int) $s['speed'] ),
			esc_attr( implode( ', ', $words ) ),
			$items // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above.
		);
	}
}
