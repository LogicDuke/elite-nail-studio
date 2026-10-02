<?php
/**
 * Colour maths, palette derivation and the Nail Studio contrast pairs, shared by
 * bin/palette-build.php and bin/contrast-gate.php (derivation notes: palette-build.php).
 */

if ( PHP_SAPI !== 'cli' ) {
	exit;
}

defined( 'ENS_PALETTE_CLI' ) || define( 'ENS_PALETTE_CLI', true );
require __DIR__ . '/../inc/palette.php';

const ENSB_TEXT = 4.5;
const ENSB_UI   = 3.0;

/* Colour helpers --------------------------------------------------------- */

function ensb_rgb( string $hex ): array {
	return array_map( 'hexdec', str_split( ltrim( $hex, '#' ), 2 ) );
}

function ensb_hex( array $rgb ): string {
	return '#' . implode( '', array_map( static fn( $c ) => sprintf( '%02x', (int) max( 0, min( 255, round( $c ) ) ) ), $rgb ) );
}

/** Mix: t = share of $b. */
function ensb_mix( string $a, string $b, float $t ): string {
	$x = ensb_rgb( $a );
	$y = ensb_rgb( $b );
	return ensb_hex( [ $x[0] + ( $y[0] - $x[0] ) * $t, $x[1] + ( $y[1] - $x[1] ) * $t, $x[2] + ( $y[2] - $x[2] ) * $t ] );
}

function ensb_lum( string $hex ): float {
	$c = array_map(
		static function ( $v ) {
			$v /= 255;
			return $v <= 0.03928 ? $v / 12.92 : ( ( $v + 0.055 ) / 1.055 ) ** 2.4;
		},
		ensb_rgb( $hex )
	);
	return 0.2126 * $c[0] + 0.7152 * $c[1] + 0.0722 * $c[2];
}

function ensb_ratio( string $a, string $b ): float {
	$la = ensb_lum( $a );
	$lb = ensb_lum( $b );
	return ( max( $la, $lb ) + 0.05 ) / ( min( $la, $lb ) + 0.05 );
}

function ensb_reads( string $fg, array $bgs, float $min = ENSB_TEXT ): bool {
	foreach ( $bgs as $bg ) {
		if ( ensb_ratio( $fg, $bg ) + 1e-9 < $min ) {
			return false;
		}
	}
	return true;
}

/** Smallest push of $c toward each target in turn until $ok passes. [ colour, push ] (push -1 = impossible). */
function ensb_push( string $c, array $targets, callable $ok ): array {
	if ( $ok( $c ) ) {
		return [ $c, 0.0 ];
	}
	$from = $c;
	foreach ( $targets as $i => $target ) {
		for ( $t = 0.02; $t <= 1.0001; $t += 0.02 ) {
			$try = ensb_mix( $from, $target, $t );
			if ( $ok( $try ) ) {
				return [ $try, $i + $t ];
			}
		}
		$from = $target;
	}
	return [ $c, -1.0 ];
}

/** Largest share t of $b in mix($a,$b,t) that still passes. */
function ensb_dimmest( string $a, string $b, callable $ok ): string {
	for ( $t = 0.98; $t >= 0; $t -= 0.02 ) {
		$try = ensb_mix( $a, $b, $t );
		if ( $ok( $try ) ) {
			return $try;
		}
	}
	return $a;
}

function ensb_source( $spec ): string {
	return strtolower( is_array( $spec ) ? ensb_mix( $spec['mix'][0], $spec['mix'][1], (float) $spec['mix'][2] ) : $spec );
}

/* Derivation ------------------------------------------------------------- */

/** Dark-section surfaces of a palette (as ens_tones()['dark'] mixes them). */
function ensb_dark_set( string $espresso, string $ivory ): array {
	return [ $espresso, ensb_mix( $espresso, $ivory, 0.05 ), ensb_mix( $espresso, $ivory, 0.08 ) ];
}

/** Helper slots (also derived for Atelier). */
function ensb_helpers( array $s, callable $note ): array {
	$dark  = ensb_dark_set( $s['espresso'], $s['ivory'] );

	if ( ensb_reads( $s['espresso'], [ $s['rose'] ] ) ) {
		$on = $s['espresso'];
	} elseif ( ensb_reads( $s['ivory'], [ $s['rose'] ] ) ) {
		$on = $s['ivory'];
	} else {
		$dark_label = ensb_ratio( '#000000', $s['rose'] ) >= ensb_ratio( '#ffffff', $s['rose'] );
		list( $on, $p ) = ensb_push( $dark_label ? $s['espresso'] : $s['ivory'], [ $dark_label ? '#000000' : '#ffffff' ], static fn( $c ) => ensb_reads( $c, [ $s['rose'] ] ) );
		$note( 'on-rose', $p < 0 ? 1 : $p, 'label pushed to read on the accent' );
	}
	list( $glow, $p ) = ensb_push( $s['rose'], [ $s['ivory'], '#ffffff' ], static fn( $c ) => ensb_reads( $c, $dark ) );
	$note( 'rose-glow', $p, 'accent lightened for text on dark surfaces' );

	$card  = ensb_mix( $s['rose'], $on, 0.08 );
	$muted = ensb_dimmest( $on, $s['rose'], static fn( $c ) => ensb_reads( $c, [ $s['rose'], $card ] ) );
	return [ 'on-rose' => $on, 'rose-glow' => $glow, 'rose-card' => $card, 'rose-muted' => $muted ];
}

/** Nail Studio slots for one source map. */
function ensb_derive( array $map ): array {
	$notes = [];
	$note  = static function ( string $slot, float $push, string $what ) use ( &$notes ) {
		if ( 0.0 !== (float) $push ) {
			$notes[] = sprintf( '%s: %s (push %.2f)', $slot, $what, $push );
		}
	};
	$I = ensb_source( $map['ink'] );
	$D = ensb_source( $map['depth'] );
	$M = ensb_source( $map['metal'] );
	$S = ensb_source( $map['mist'] );
	$P = ensb_source( $map['paper'] );

	list( $espresso, $p ) = ensb_push( $I, [ '#000000' ], static fn( $c ) => ensb_lum( $c ) <= 0.04 );
	$note( 'espresso', $p, 'ink darkened to a dark base' );
	list( $ivory, $p ) = ensb_push( $P, [ '#ffffff' ], static fn( $c ) => ensb_lum( $c ) >= 0.78 );
	$note( 'ivory', $p, 'paper lightened to a light base' );
	$porcelain = ensb_mix( $ivory, '#ffffff', 0.6 );

	$nude = ensb_lum( $S ) >= 0.55 ? $S : ensb_mix( $ivory, $S, 0.12 );
	list( $nude, $p ) = ensb_push( $nude, [ $ivory, '#ffffff' ], static fn( $c ) => ensb_lum( $c ) >= 0.55 && ensb_reads( $espresso, [ $c ] ) );
	$note( 'nude', $p, 'lightened to hold espresso text' );
	$linen = ensb_mix( $ivory, $nude, 0.45 );
	$light = [ $ivory, $porcelain, $linen ];

	list( $cocoa, $p ) = ensb_push( $D, [ $espresso, '#000000' ], static fn( $c ) => ensb_reads( $c, $light ) );
	$note( 'cocoa', $p, 'depth darkened to read as text' );
	$taupe = ensb_dimmest( $cocoa, $ivory, static fn( $c ) => ensb_reads( $c, $light ) );

	$dark = ensb_dark_set( $espresso, $ivory );
	list( $rose_ink, $p ) = ensb_push( $M, [ $espresso, '#000000' ], static fn( $c ) => ensb_reads( $c, $light ) );
	$note( 'rose-ink', $p, 'accent darkened for text on light surfaces' );
	list( $champagne, $p ) = ensb_push( ensb_mix( $M, $P, 0.35 ), [ $ivory, '#ffffff' ], static fn( $c ) => ensb_reads( $c, $dark ) );
	$note( 'champagne', $p, 'lightened for text on dark surfaces' );
	list( $champagne_ink, $p ) = ensb_push( $champagne, [ $espresso, '#000000' ], static fn( $c ) => ensb_reads( $c, $light ) );
	$note( 'champagne-ink', $p, 'darkened for text on light surfaces' );

	$slots = [
		'ivory'         => $ivory,
		'porcelain'     => $porcelain,
		'linen'         => $linen,
		'nude'          => $nude,
		'rose'          => $M,
		'rose-ink'      => $rose_ink,
		'champagne'     => $champagne,
		'champagne-ink' => $champagne_ink,
		'espresso'      => $espresso,
		'cocoa'         => $cocoa,
		'taupe'         => $taupe,
	];
	return [ 'slots' => $slots + ensb_helpers( $slots, $note ), 'notes' => $notes ];
}

/* Gate ------------------------------------------------------------------- */

/** Hex of a tone reference (ens_tones()) over a backdrop (alpha composited). */
function ensb_resolve( $ref, array $slots, array $sources, string $over ): string {
	$hex = static fn( string $name ) => $slots[ $sources[ $name ] ?? $name ];
	if ( is_string( $ref ) ) {
		return $hex( $ref );
	}
	return ensb_mix( isset( $ref[2] ) ? $hex( $ref[2] ) : $over, $hex( $ref[0] ), $ref[1] / 100 );
}

/**
 * Nail Studio pairs: [ fg role, bg roles, minimum ]. Text 4.5:1, UI 3:1.
 * text/heading: body copy, headings, price badges (soft); muted/subtle: leads, labels,
 * meta, form labels; accent-ink/eyebrow: emphasis, eyebrows, links; on-strong: button
 * labels; on-accent: button hover label, badge icon; focus + strong: focus ring, button
 * and form underline against the section.
 */
function ensb_pairs(): array {
	$surfaces = [ 'bg', 'surface', 'surface-alt' ];
	return [
		[ 'text', array_merge( $surfaces, [ 'soft' ] ), ENSB_TEXT ],
		[ 'heading', array_merge( $surfaces, [ 'soft' ] ), ENSB_TEXT ],
		[ 'text-muted', $surfaces, ENSB_TEXT ],
		[ 'text-subtle', $surfaces, ENSB_TEXT ],
		[ 'accent-ink', $surfaces, ENSB_TEXT ],
		[ 'eyebrow', $surfaces, ENSB_TEXT ],
		[ 'on-strong', [ 'strong' ], ENSB_TEXT ],
		[ 'on-accent', [ 'accent' ], ENSB_TEXT ],
		[ 'focus', $surfaces, ENSB_UI ],
		[ 'strong', $surfaces, ENSB_UI ],
	];
}

/** Failures of one tone. */
function ensb_gate_tone( array $slots, array $sources, string $tone ): array {
	$roles = ens_tones()[ $tone ];
	$fail  = [];
	foreach ( ensb_pairs() as list( $fg, $bgs, $min ) ) {
		foreach ( $bgs as $bg ) {
			$back = ensb_resolve( $roles[ $bg ], $slots, $sources, $slots['ivory'] );
			$r    = ensb_ratio( ensb_resolve( $roles[ $fg ], $slots, $sources, $back ), $back );
			if ( $r + 1e-9 < $min ) {
				$fail[] = sprintf( '%s %s on %s %.2f:1 (min %.1f)', $tone, $fg, $bg, $r, $min );
			}
		}
	}
	return $fail;
}
