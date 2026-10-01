<?php
defined( 'ABSPATH' ) || exit;

/** FAQ accordion on native <details> (exclusive via `name`), with FAQPage structured data. */
class ENS_Faq extends ENS_Widget {

	public function get_name() {
		return 'ens-faq';
	}

	public function get_title() {
		return __( 'ENS FAQ', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-accordion';
	}

	protected function register_controls() {
		$this->section( __( 'Questions', 'elite-nail-studio' ) );
		$this->field( 'group', __( 'Group label (optional)', 'elite-nail-studio' ), 'text', '' );
		$this->items( 'items', __( 'Questions', 'elite-nail-studio' ), [
			'q' => [ __( 'Question', 'elite-nail-studio' ), 'text', '' ],
			'a' => [ __( 'Answer', 'elite-nail-studio' ), 'wysiwyg', '' ],
		], [ [ 'q' => __( 'How long does a manicure last?', 'elite-nail-studio' ) ] ], 'q' );
		$this->field( 'first_open', __( 'Open the first question', 'elite-nail-studio' ), 'switch', '' );
		$this->field( 'schema', __( 'Output FAQ structured data', 'elite-nail-studio' ), 'switch', 'yes' );
		$this->end_controls_section();
	}

	protected function render() {
		$s    = $this->get_settings_for_display();
		$name = 'ens-faq-' . $this->get_id();
		echo '<div class="ens-faq ens-stagger">';
		if ( $s['group'] ) {
			echo '<h3 class="ens-faq__group">' . esc_html( $s['group'] ) . '</h3>';
		}
		foreach ( $s['items'] as $i => $item ) {
			printf(
				'<details class="ens-faq__item" name="%s"%s><summary class="ens-faq__q"><span>%s</span><i class="ens-faq__icon" aria-hidden="true"></i></summary><div class="ens-faq__a">%s</div></details>',
				esc_attr( $name ),
				0 === $i && 'yes' === $s['first_open'] ? ' open' : '',
				esc_html( $item['q'] ),
				wp_kses_post( $item['a'] )
			);
		}
		echo '</div>';

		if ( 'yes' === $s['schema'] && ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$schema = [
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => array_map( fn( $i ) => [
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( $i['q'] ),
					'acceptedAnswer' => [ '@type' => 'Answer', 'text' => wp_strip_all_tags( $i['a'] ) ],
				], $s['items'] ),
			];
			echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
		}
	}
}
