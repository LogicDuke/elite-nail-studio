<?php
/**
 * EDS Demo Palette Switcher integration (optional plugin; inert without it).
 *
 * Registers every offered palette with its variable map (ens_palette_vars()).
 * The plugin re-points those --ens-c-* / --ens-a-* variables on
 * <html data-eds-demo-palette>, in the visitor's browser only; every token,
 * tone and section pattern follows through var(). The saved palette is the
 * default and the Reset target. Section patterns are not palettes and are not
 * registered.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	if ( ! function_exists( 'eds_dps_register_theme_palettes' ) ) {
		return;
	}
	$palettes = [];
	foreach ( ens_palettes() as $slug => $palette ) {
		$palettes[] = [
			'id'       => $slug,
			'name'     => $palette['name'],
			'swatches' => $palette['colors'],
			'vars'     => ens_palette_vars( $slug ),
		];
	}
	eds_dps_register_theme_palettes( [
		'integration_id'  => 'elite-nail-studio',
		'default_palette' => ens_palette_active_slug(),
		'palettes'        => $palettes,
	] );
} );
