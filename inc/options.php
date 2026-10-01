<?php
/**
 * Studio details (Customizer → Studio Details), icons and small render helpers
 * shared by the header, footer, templates and widgets.
 */

defined( 'ABSPATH' ) || exit;

function ens_defaults() {
	return [
		'ens_promo'      => __( 'New season — Autumn Rituals now booking', 'elite-nail-studio' ),
		'ens_phone'      => '+1 (555) 014-2290',
		'ens_email'      => 'hello@maisonelise.example',
		'ens_address'    => "18 Rosewood Arcade\nOld Town, 10115",
		'ens_hours'      => "Tue – Fri · 10:00 – 20:00\nSat · 09:00 – 18:00\nSun – Mon · by appointment",
		'ens_instagram'  => 'https://instagram.com/',
		'ens_book_label' => __( 'Book appointment', 'elite-nail-studio' ),
		'ens_book_url'   => '/booking/',
	];
}

function ens_opt( $key ) {
	return get_theme_mod( $key, ens_defaults()[ $key ] ?? '' );
}

/** Booking URL, resolving site-relative paths. */
function ens_book_url() {
	$url = ens_opt( 'ens_book_url' );
	return str_starts_with( $url, '/' ) ? home_url( $url ) : $url;
}

add_action( 'customize_register', function ( WP_Customize_Manager $c ) {
	$c->add_section( 'ens_studio', [ 'title' => __( 'Studio Details', 'elite-nail-studio' ), 'priority' => 30 ] );
	$fields = [
		'ens_promo'      => [ __( 'Announcement bar', 'elite-nail-studio' ), 'text' ],
		'ens_phone'      => [ __( 'Phone', 'elite-nail-studio' ), 'text' ],
		'ens_email'      => [ __( 'Email', 'elite-nail-studio' ), 'email' ],
		'ens_address'    => [ __( 'Address', 'elite-nail-studio' ), 'textarea' ],
		'ens_hours'      => [ __( 'Opening hours', 'elite-nail-studio' ), 'textarea' ],
		'ens_instagram'  => [ __( 'Instagram URL', 'elite-nail-studio' ), 'url' ],
		'ens_book_label' => [ __( 'Header button label', 'elite-nail-studio' ), 'text' ],
		'ens_book_url'   => [ __( 'Header button link', 'elite-nail-studio' ), 'text' ],
	];
	foreach ( $fields as $id => [ $label, $type ] ) {
		$c->add_setting( $id, [
			'default'           => ens_defaults()[ $id ],
			'sanitize_callback' => match ( $type ) {
				'email'    => 'sanitize_email',
				'url'      => 'esc_url_raw',
				'textarea' => 'sanitize_textarea_field',
				default    => 'sanitize_text_field',
			},
		] );
		$c->add_control( $id, [ 'label' => $label, 'section' => 'ens_studio', 'type' => $type ] );
	}
} );

/** Inline SVG icons (stroke = currentColor). */
function ens_icon( $name ) {
	static $paths = [
		'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-up'  => '<path d="M7 17 17 7M8 7h9v9"/>',
		'arrow-l'   => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
		'plus'      => '<path d="M12 5v14M5 12h14"/>',
		'close'     => '<path d="M6 6l12 12M18 6 6 18"/>',
		'phone'     => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/>',
		'mail'      => '<rect x="3" y="5" width="18" height="14" rx="1"/><path d="m3 7 9 6 9-6"/>',
		'pin'       => '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
		'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".6" fill="currentColor"/>',
		'star'      => '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9z" fill="currentColor" stroke="none"/>',
	];
	return isset( $paths[ $name ] )
		? '<svg class="ens-icon ens-icon--' . $name . '" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>'
		: '';
}

/** Circular rotating text badge (hero, CTA band, ENS Badge widget). */
function ens_badge( $text, $url = '', $extra_class = '' ) {
	static $n = 0;
	$id   = 'ens-badge-path-' . ( ++$n );
	$tag  = $url ? 'a' : 'div';
	$href = $url ? ' href="' . esc_url( $url ) . '"' : '';
	$out  = "<{$tag} class=\"ens-badge {$extra_class}\"{$href}" . ( $url ? ' aria-label="' . esc_attr( $text ) . '"' : '' ) . '>';
	$out .= '<svg class="ens-badge__ring" viewBox="0 0 200 200" aria-hidden="true"><defs><path id="' . $id . '" d="M100,100 m-78,0 a78,78 0 1,1 156,0 a78,78 0 1,1 -156,0"/></defs>';
	$out .= '<text><textPath href="#' . $id . '" textLength="485">' . esc_html( $text ) . '</textPath></text></svg>';
	$out .= '<span class="ens-badge__core">' . ens_icon( 'arrow-up' ) . '</span>';
	return $out . "</{$tag}>";
}

/** Render an Elementor library template by slug (global footer, 404). Returns '' if missing. */
function ens_library_template( $slug ) {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return '';
	}
	$tpl = get_page_by_path( $slug, OBJECT, 'elementor_library' );
	return $tpl ? \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $tpl->ID, true ) : '';
}

/**
 * `sizes` for full-bleed cover images. On tall viewports a cover crop paints the image wider than
 * the screen (box height × image aspect), so "100vw" makes the browser pick a far-too-small file.
 * $vh / $min_px describe the painted box height (incl. headroom for content growth / parallax).
 * The leading media condition lets browsers without max() support skip to the plain 100vw entry.
 */
function ens_cover_sizes( $attachment_id, $vh, $min_px ) {
	$m = wp_get_attachment_metadata( $attachment_id );
	if ( empty( $m['width'] ) || empty( $m['height'] ) ) {
		return '100vw';
	}
	$a = $m['width'] / $m['height'];
	return sprintf( '(min-width: 1px) max(100vw, %.0fvh, %.0fpx), 100vw', $a * $vh, $a * $min_px );
}

/**
 * Core prefixes lazy images' sizes with "auto" (layout width), which would override the cover-crop
 * hint above. Images marked `ens-cover` keep their explicit sizes, at render and in the content pass.
 */
add_filter( 'wp_get_attachment_image_attributes', function ( $attr ) {
	if ( str_contains( $attr['class'] ?? '', 'ens-cover' ) && isset( $attr['sizes'] ) ) {
		$attr['sizes'] = preg_replace( '/^auto,\s*/', '', $attr['sizes'] );
	}
	return $attr;
} );
add_filter( 'wp_content_img_tag', fn( $img ) => str_contains( $img, 'ens-cover' ) ? str_replace( 'sizes="auto, ', 'sizes="', $img ) : $img );

/** Multi-line option → `<br>`-joined escaped HTML. */
function ens_lines( $text ) {
	return implode( '<br>', array_map( 'esc_html', preg_split( '/\R/', trim( $text ) ) ) );
}
