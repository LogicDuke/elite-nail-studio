<?php
/**
 * Base for Elite Nail Studio widgets: category, compact control helpers, render helpers.
 * Styling comes from the design system (assets/css/main.css); widgets expose content controls,
 * plus a few layout switches — colours and type are global (Site Settings).
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;

abstract class ENS_Widget extends \Elementor\Widget_Base {

	public function get_categories() {
		return [ 'elite-nail-studio' ];
	}

	public function get_keywords() {
		return [ 'elite', 'nail', 'ens' ];
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	/** Control types by short name. */
	private const TYPES = [
		'text'     => Controls_Manager::TEXT,
		'textarea' => Controls_Manager::TEXTAREA,
		'wysiwyg'  => Controls_Manager::WYSIWYG,
		'url'      => Controls_Manager::URL,
		'media'    => Controls_Manager::MEDIA,
		'gallery'  => Controls_Manager::GALLERY,
		'select'   => Controls_Manager::SELECT,
		'switch'   => Controls_Manager::SWITCHER,
		'number'   => Controls_Manager::NUMBER,
	];

	/** Control args from the short form. `$options` doubles as select options. */
	private static function args( $label, $type, $default, $options ) {
		$args = [ 'label' => $label, 'type' => self::TYPES[ $type ], 'label_block' => in_array( $type, [ 'text', 'textarea', 'url' ], true ) ];
		if ( 'select' === $type ) {
			$args['options'] = $options;
		}
		if ( 'switch' === $type ) {
			$args['return_value'] = 'yes';
		}
		if ( 'media' === $type && ! $default ) {
			$default = [ 'url' => \Elementor\Utils::get_placeholder_image_src() ];
		}
		if ( 'url' === $type && is_string( $default ) ) {
			$default = [ 'url' => $default ];
		}
		if ( in_array( $type, [ 'text', 'textarea', 'wysiwyg', 'url' ], true ) ) {
			$args['dynamic'] = [ 'active' => true ];
		}
		$args['default'] = $default;
		return $args;
	}

	protected function field( $id, $label, $type = 'text', $default = '', $options = [] ) {
		$this->add_control( $id, self::args( $label, $type, $default, $options ) );
	}

	/** Repeater: $fields = [ id => [ label, type, default, options? ] ], $defaults = rows. */
	protected function items( $id, $label, array $fields, array $defaults, $title_field ) {
		$r = new Repeater();
		foreach ( $fields as $fid => $f ) {
			$r->add_control( $fid, self::args( $f[0], $f[1], $f[2] ?? '', $f[3] ?? [] ) );
		}
		$this->add_control( $id, [
			'label'       => $label,
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $r->get_controls(),
			'default'     => $defaults,
			'title_field' => '{{{ ' . $title_field . ' }}}',
		] );
	}

	protected function section( $label, $tab = Controls_Manager::TAB_CONTENT ) {
		static $n = 0;
		$this->start_controls_section( 'ens_section_' . ( ++$n ), [ 'label' => $label, 'tab' => $tab ] );
	}

	/* ---------- render helpers ---------- */

	/** Image from a MEDIA control value. */
	protected static function img( $media, $size = 'large', $attr = [] ) {
		$attr += [ 'loading' => 'lazy', 'decoding' => 'async' ];
		if ( ! empty( $media['id'] ) ) {
			return wp_get_attachment_image( $media['id'], $size, false, $attr );
		}
		if ( ! empty( $media['url'] ) ) {
			$attr['src'] = $media['url'];
			$attr['alt'] = $attr['alt'] ?? '';
			return '<img ' . implode( ' ', array_map( fn( $k, $v ) => $k . '="' . esc_attr( $v ) . '"', array_keys( $attr ), $attr ) ) . '>';
		}
		return '';
	}

	/** Headline text: allows <em>, <br>, <strong>; newlines become line breaks. */
	protected static function rich( $text ) {
		return nl2br( wp_kses( $text, [ 'em' => [], 'br' => [], 'strong' => [] ] ), false );
	}

	/** href/target/rel attributes from a URL control value. */
	protected static function href( $link ) {
		$url = $link['url'] ?? '';
		if ( ! $url ) {
			return '';
		}
		$out = ' href="' . esc_url( $url ) . '"';
		if ( ! empty( $link['is_external'] ) ) {
			$out .= ' target="_blank" rel="noopener"';
		}
		return $out;
	}

	/** Section intro: eyebrow, heading, text — shared by several widgets. */
	protected static function intro( $s, $tag = 'h2', $class = '' ) {
		if ( empty( $s['eyebrow'] ) && empty( $s['title'] ) && empty( $s['text'] ) ) {
			return '';
		}
		$out = '<div class="ens-intro ' . esc_attr( $class ) . '">';
		if ( ! empty( $s['eyebrow'] ) ) {
			$out .= '<p class="ens-eyebrow ens-reveal">' . esc_html( $s['eyebrow'] ) . '</p>';
		}
		if ( ! empty( $s['title'] ) ) {
			$out .= "<{$tag} class=\"ens-title ens-split\">" . self::rich( $s['title'] ) . "</{$tag}>";
		}
		if ( ! empty( $s['text'] ) ) {
			$out .= '<p class="ens-lead ens-reveal">' . esc_html( $s['text'] ) . '</p>';
		}
		return $out . '</div>';
	}

	/** Adds eyebrow/title/text controls for intro(). */
	protected function intro_fields( $eyebrow = '', $title = '', $text = '' ) {
		$this->field( 'eyebrow', __( 'Eyebrow', 'elite-nail-studio' ), 'text', $eyebrow );
		$this->field( 'title', __( 'Title (use <em> for italic accent)', 'elite-nail-studio' ), 'textarea', $title );
		$this->field( 'text', __( 'Text', 'elite-nail-studio' ), 'textarea', $text );
	}
}
