<?php
/**
 * Standalone WCAG gate over the generated registry (inc/palette-registry.php):
 * every offered palette, in every tone a section can take — Light, Dark (also
 * the footer and dark chrome) and the Accent tone it actually renders (its own,
 * or the light fallback when its Accent did not pass). Text 4.5:1, UI 3:1.
 *
 * Usage:  php bin/contrast-gate.php          one line per palette, exit 1 on any failure
 *         php bin/contrast-gate.php atelier  every pair of one palette
 */

if ( PHP_SAPI !== 'cli' ) {
	exit;
}

require __DIR__ . '/palette-lib.php';

$ensg_only     = $argv[1] ?? '';
$ensg_failures = 0;

foreach ( ens_palette_registry() as $ensg_slug => $ensg_p ) {
	if ( empty( $ensg_p['selectable'] ) || ( $ensg_only && $ensg_only !== $ensg_slug ) ) {
		continue;
	}
	$ensg_sources = ens_palette_accent_sources( ! empty( $ensg_p['sections']['accent'] ) );
	$ensg_min     = [];
	$ensg_fail    = [];
	foreach ( array_keys( ens_tones() ) as $ensg_tone ) {
		$ensg_roles = ens_tones()[ $ensg_tone ];
		foreach ( ensb_pairs() as list( $ensg_fg, $ensg_bgs, $ensg_need ) ) {
			foreach ( $ensg_bgs as $ensg_bg ) {
				$ensg_back = ensb_resolve( $ensg_roles[ $ensg_bg ], $ensg_p['slots'], $ensg_sources, $ensg_p['slots']['ivory'] );
				$ensg_r    = ensb_ratio( ensb_resolve( $ensg_roles[ $ensg_fg ], $ensg_p['slots'], $ensg_sources, $ensg_back ), $ensg_back );
				$ensg_ok   = $ensg_r + 1e-9 >= $ensg_need;
				$ensg_key  = $ensg_tone . ( ENSB_UI === $ensg_need ? ' ui' : ' text' );
				$ensg_min[ $ensg_key ] = min( $ensg_min[ $ensg_key ] ?? 99, $ensg_r );
				if ( ! $ensg_ok ) {
					$ensg_fail[] = sprintf( '%s %s on %s %.2f:1 (min %.1f)', $ensg_tone, $ensg_fg, $ensg_bg, $ensg_r, $ensg_need );
				}
				if ( $ensg_only ) {
					printf( "%-4s %-6s %-12s on %-11s %5.2f:1 (min %.1f)\n", $ensg_ok ? 'ok' : 'FAIL', $ensg_tone, $ensg_fg, $ensg_bg, $ensg_r, $ensg_need );
				}
			}
		}
	}
	$ensg_failures += count( $ensg_fail );
	if ( ! $ensg_only ) {
		printf( "%-4s %-24s %s\n", $ensg_fail ? 'FAIL' : 'ok', $ensg_p['name'], implode( '  ', array_map( static fn( $k, $v ) => sprintf( '%s %.2f', $k, $v ), array_keys( $ensg_min ), $ensg_min ) ) );
		foreach ( $ensg_fail as $ensg_line ) {
			echo "       {$ensg_line}\n";
		}
	}
}

echo $ensg_failures ? "\n{$ensg_failures} pair(s) failed.\n" : "\nAll pairs pass in every tone (minimum ratios shown).\n";
exit( $ensg_failures ? 1 : 0 );
