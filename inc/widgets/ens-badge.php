<?php
defined( 'ABSPATH' ) || exit;

/** Slowly rotating circular text badge with a centre arrow. */
class ENS_Badge extends ENS_Widget {

	public function get_name() {
		return 'ens-badge';
	}

	public function get_title() {
		return __( 'ENS Rotating Badge', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-spinner';
	}

	protected function register_controls() {
		$this->section( __( 'Badge', 'elite-nail-studio' ) );
		$this->field( 'text', __( 'Circular text', 'elite-nail-studio' ), 'text', __( 'Book an appointment · Maison Élise · ', 'elite-nail-studio' ) );
		$this->field( 'link', __( 'Link', 'elite-nail-studio' ), 'url', '/booking/' );
		$this->field( 'tone', __( 'Tone', 'elite-nail-studio' ), 'select', 'dark', [
			'dark'  => __( 'Espresso', 'elite-nail-studio' ),
			'light' => __( 'Ivory (on images)', 'elite-nail-studio' ),
		] );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		echo ens_badge( $s['text'], $s['link']['url'] ?? '', 'ens-badge--' . sanitize_html_class( $s['tone'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
