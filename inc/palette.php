<?php
/**
 * Colour palettes and section tones — the single source of colour.
 *
 * A palette is a set of colour slots (ivory, rose, espresso, …). Atelier, the
 * approved Maison Élise colours, is the fixed default; the other EDS palettes
 * are derived and contrast-gated by bin/palette-build.php into
 * inc/palette-registry.php. Slots are printed once, on :root, as --ens-c-{slot}
 * (ens_palette_vars()); tokens.css turns them into --ens-{slot}, and every role
 * token (--ens-text, --ens-bg, …) of every tone only references those. Re-pointing
 * the --ens-c-* map on <html> therefore recolours the whole page — which is what
 * the optional EDS Demo Palette Switcher does (inc/demo-palette-switcher.php).
 *
 * Kept free of WordPress calls (bar the guarded theme_mod read) so the bin/
 * scripts can load it from the command line.
 */

defined( 'ABSPATH' ) || defined( 'ENS_PALETTE_CLI' ) || exit;

/** Atelier: the approved Maison Élise colours, byte for byte (also tokens.css fallbacks). */
function ens_palette_atelier(): array {
	return [
		'ivory'         => '#f8f4f0',
		'porcelain'     => '#fffdfb',
		'linen'         => '#f1e6df',
		'nude'          => '#e9d5cc',
		'rose'          => '#c99091',
		'rose-ink'      => '#8f585a',
		'champagne'     => '#b99a72',
		'champagne-ink' => '#85683f',
		'espresso'      => '#271d1b',
		'cocoa'         => '#5e4b45',
		'taupe'         => '#6f5c56',
	];
}

/**
 * Slot labels. The first eleven are the Elementor Global Colours (ens_{slot});
 * the helper slots only feed role tokens.
 */
function ens_palette_labels(): array {
	return [
		'ivory'         => 'Ivory',
		'porcelain'     => 'Porcelain',
		'linen'         => 'Linen',
		'nude'          => 'Nude',
		'rose'          => 'Rose',
		'rose-ink'      => 'Rose Ink',
		'champagne'     => 'Champagne',
		'champagne-ink' => 'Champagne Ink',
		'espresso'      => 'Espresso',
		'cocoa'         => 'Cocoa',
		'taupe'         => 'Taupe',
	];
}

/** The generated registry (bin/palette-build.php). */
function ens_palette_registry(): array {
	static $registry = null;
	if ( null === $registry ) {
		$file     = __DIR__ . '/palette-registry.php';
		$registry = is_file( $file ) ? require $file : [];
	}
	return $registry;
}

/** Palettes offered (registry order: Atelier first). */
function ens_palettes(): array {
	return array_filter( ens_palette_registry(), static fn( $p ) => ! empty( $p['selectable'] ) );
}

/** Slug of the saved palette (Customizer theme_mod; Atelier by default). */
function ens_palette_active_slug(): string {
	$slug = function_exists( 'get_theme_mod' ) ? (string) get_theme_mod( 'ens_palette', 'atelier' ) : 'atelier';
	return isset( ens_palettes()[ $slug ] ) ? $slug : 'atelier';
}

/** Colour slots of a palette (slot => hex), helper slots included. */
function ens_palette_slots( ?string $slug = null ): array {
	return ens_palette_registry()[ $slug ?? ens_palette_active_slug() ]['slots'] ?? ens_palette_registry()['atelier']['slots'] ?? ens_palette_atelier();
}

/**
 * Section tones: every role token as a colour reference.
 *   'slot'                  var(--ens-slot)
 *   [ 'slot', 72 ]          slot at 72% over transparent
 *   [ 'slot', 92, 'slot2' ] 92% slot mixed with slot2
 * `light` is the page default (:root); `dark` is .ens-sec-dark / .ens-tone-dark;
 * `accent` (the palette's accent as a fill) reads the --ens-a-* sources, which
 * fall back to light slots for a palette whose Accent did not pass the gate.
 */
function ens_tones(): array {
	return [
		'light'  => [
			'bg'          => 'ivory',
			'surface'     => 'porcelain',
			'surface-alt' => 'linen',
			'soft'        => 'nude',
			'text'        => 'espresso',
			'heading'     => 'espresso',
			'text-muted'  => 'cocoa',
			'text-subtle' => 'taupe',
			'accent'      => 'rose',
			'on-accent'   => 'on-rose',
			'accent-ink'  => 'rose-ink',
			'eyebrow'     => 'rose-ink',
			'detail'      => 'champagne',
			'ornament'    => 'rose',
			'strong'      => 'espresso',
			'on-strong'   => 'ivory',
			'focus'       => 'champagne-ink',
			'line'        => [ 'espresso', 12 ],
			'line-strong' => [ 'espresso', 24 ],
		],
		'dark'   => [
			'bg'          => 'espresso',
			'surface'     => [ 'espresso', 95, 'ivory' ],
			'surface-alt' => [ 'espresso', 92, 'ivory' ],
			'soft'        => [ 'espresso', 88, 'ivory' ],
			'text'        => 'ivory',
			'heading'     => 'ivory',
			'text-muted'  => [ 'ivory', 72 ],
			'text-subtle' => [ 'ivory', 72 ],
			'accent'      => 'rose',
			'on-accent'   => 'on-rose',
			'accent-ink'  => 'rose-glow',
			'eyebrow'     => 'champagne',
			'detail'      => 'champagne',
			'ornament'    => 'rose-glow',
			'strong'      => 'ivory',
			'on-strong'   => 'espresso',
			'focus'       => 'champagne',
			'line'        => [ 'ivory', 22 ],
			'line-strong' => [ 'ivory', 22 ],
		],
		'accent' => [
			'bg'          => 'a-bg',
			'surface'     => 'a-card',
			'surface-alt' => 'a-card',
			'soft'        => 'a-card',
			'text'        => 'a-ink',
			'heading'     => 'a-ink',
			'text-muted'  => 'a-muted',
			'text-subtle' => 'a-muted',
			'accent'      => 'a-wash',
			'on-accent'   => 'a-on-wash',
			'accent-ink'  => 'a-ink',
			'eyebrow'     => 'a-ink',
			'detail'      => 'a-ink',
			'ornament'    => 'a-ink',
			'strong'      => 'a-ink',
			'on-strong'   => 'a-bg',
			'focus'       => 'a-ink',
			'line'        => [ 'a-ink', 22 ],
			'line-strong' => [ 'a-ink', 32 ],
		],
	];
}

/** Accent-tone sources: the palette's own accent slots, or light slots when its Accent failed the gate. */
function ens_palette_accent_sources( bool $accent ): array {
	return $accent
		? [ 'a-bg' => 'rose', 'a-card' => 'rose-card', 'a-ink' => 'on-rose', 'a-muted' => 'rose-muted', 'a-wash' => 'ivory', 'a-on-wash' => 'espresso' ]
		: [ 'a-bg' => 'ivory', 'a-card' => 'porcelain', 'a-ink' => 'espresso', 'a-muted' => 'cocoa', 'a-wash' => 'rose', 'a-on-wash' => 'on-rose' ];
}

/**
 * Every palette-specific value as one flat custom-property map, printed once on
 * :root and registered with the demo switcher.
 */
function ens_palette_vars( ?string $slug = null ): array {
	$slug = $slug ?? ens_palette_active_slug();
	$vars = [];
	foreach ( ens_palette_slots( $slug ) as $key => $hex ) {
		$vars[ "--ens-c-{$key}" ] = $hex;
	}
	foreach ( ens_palette_accent_sources( ! empty( ens_palette_registry()[ $slug ]['sections']['accent'] ) ) as $key => $slot ) {
		$vars[ "--ens-{$key}" ] = "var(--ens-c-{$slot})";
	}
	return $vars;
}

/** CSS value of a tone reference (see ens_tones()). */
function ens_tone_value( $ref ): string {
	if ( is_string( $ref ) ) {
		return "var(--ens-{$ref})";
	}
	return sprintf( 'color-mix(in srgb, var(--ens-%s) %d%%, %s)', $ref[0], $ref[1], isset( $ref[2] ) ? "var(--ens-{$ref[2]})" : 'transparent' );
}

/** Role-token declarations of one tone. */
function ens_tone_decl( string $tone ): string {
	$decl = '';
	foreach ( ens_tones()[ $tone ] as $role => $ref ) {
		$decl .= "--ens-{$role}:" . ens_tone_value( $ref ) . ';';
	}
	return $decl;
}

/** Section patterns: label and repeating tone cycle (as Elite Realty). */
function ens_patterns(): array {
	return [
		'original'          => [ 'label' => 'Original — the approved Maison Élise design', 'cycle' => [] ],
		'dark-light'        => [ 'label' => 'Dark / Light alternating', 'cycle' => [ 'dark', 'light' ] ],
		'light-dark'        => [ 'label' => 'Light / Dark alternating', 'cycle' => [ 'light', 'dark' ] ],
		'dark-light-accent' => [ 'label' => 'Dark / Light / Accent', 'cycle' => [ 'dark', 'light', 'accent' ] ],
		'light-dark-accent' => [ 'label' => 'Light / Dark / Accent', 'cycle' => [ 'light', 'dark', 'accent' ] ],
		'mostly-light'      => [ 'label' => 'Mostly light', 'cycle' => [ 'light', 'light', 'light', 'dark' ] ],
		'mostly-dark'       => [ 'label' => 'Mostly dark', 'cycle' => [ 'dark', 'dark', 'dark', 'light' ] ],
	];
}

/** Saved section pattern (Original by default). */
function ens_pattern_active(): string {
	$saved = function_exists( 'get_theme_mod' ) ? (string) get_theme_mod( 'ens_section_pattern', 'original' ) : 'original';
	return isset( ens_patterns()[ $saved ] ) ? $saved : 'original';
}

/** Tones a palette offers: Dark and Light always, Accent when it passed the gate. */
function ens_palette_roles( string $slug ): array {
	return array_keys( array_filter( ens_palette_registry()[ $slug ]['sections'] ?? [ 'dark' => true, 'light' => true ] ) );
}

/** The pattern's cycle without tones the palette does not offer. */
function ens_pattern_sequence( string $slug, string $pattern ): array {
	return array_values( array_intersect( ens_patterns()[ $pattern ]['cycle'] ?? [], ens_palette_roles( $slug ) ) );
}

/**
 * Every palette-dependent style: palette variables, role tokens of each tone,
 * the per-section override classes and the section pattern. One inline style
 * after tokens.css; values are only var() references to the --ens-c-* map.
 *
 * Patterned sections: top-level Elementor containers of the page content,
 * except photographic ones (hero, CTA band) and containers with their own
 * `ens-tone-{dark|light|accent}` class. Original adds nothing.
 */
function ens_palette_css( ?string $slug = null, ?string $pattern = null ): string {
	$slug    = $slug ?? ens_palette_active_slug();
	$pattern = $pattern ?? ens_pattern_active();
	$paint   = 'background-color:var(--ens-bg);color:var(--ens-text);';

	$root = '';
	foreach ( ens_palette_vars( $slug ) as $name => $value ) {
		$root .= "{$name}:{$value};";
	}
	$css  = ":root{{$root}}";
	$css .= ':root,.ens-tone-light{' . ens_tone_decl( 'light' ) . '}';
	$css .= '.ens-sec-dark,.ens-tone-dark{' . ens_tone_decl( 'dark' ) . '}';
	$css .= '.ens-tone-accent{' . ens_tone_decl( 'accent' ) . '}';
	$css .= ".ens-tone-light,.ens-tone-dark,.ens-tone-accent{{$paint}}";

	// Hello's reset greys figcaptions (#333); on dark and accent sections they follow the section.
	$captions = [ '.ens-sec-dark figcaption', '.ens-tone-dark figcaption', '.ens-tone-accent figcaption' ];
	$sequence = ens_pattern_sequence( $slug, $pattern );
	if ( $sequence ) {
		$section = '.e-con.e-parent:not(:has(.ens-hero,.ens-cta))';
		foreach ( array_unique( $sequence ) as $tone ) {
			$selectors = [];
			foreach ( array_keys( $sequence, $tone, true ) as $i ) {
				$selectors[] = sprintf( '.ens-main>.elementor>:is(%1$s):not([class*="ens-tone-"]):nth-child(%2$dn+%3$d of %1$s)', $section, count( $sequence ), $i + 1 );
			}
			$css .= implode( ',', $selectors ) . '{' . ens_tone_decl( $tone ) . $paint . '}';
			if ( 'light' !== $tone ) {
				$captions = array_merge( $captions, array_map( static fn( $s ) => "{$s} figcaption", $selectors ) );
			}
		}
	}
	$css .= implode( ',', $captions ) . '{color:inherit}';
	return $css;
}
