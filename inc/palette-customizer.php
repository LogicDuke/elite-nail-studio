<?php
/**
 * Maison Élise Colours (Appearance → Customize): palette and section pattern.
 *
 * Two allowlisted theme_mods, `ens_palette` (a slug from the contrast-gated
 * registry) and `ens_section_pattern`. Everything they change is the one inline
 * style after tokens.css (#ens-tokens-inline-css, ens_palette_css()); the
 * preview re-renders it through a selective-refresh partial. Saving also writes
 * the palette into the Elementor kit's Global Colours (one way: the theme never
 * reads them back).
 */

defined( 'ABSPATH' ) || exit;

/** Register the section, settings, control and live-preview partial. */
add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {
	require_once __DIR__ . '/palette-control.php';

	$wp_customize->add_section( 'ens_colours', [
		'title'       => __( 'Maison Élise Colours', 'elite-nail-studio' ),
		'description' => __( 'Colours for the whole site. Every palette is checked for readable contrast (WCAG AA).', 'elite-nail-studio' ),
		'priority'    => 25,
	] );
	$wp_customize->add_setting( 'ens_palette', [
		'type'              => 'theme_mod',
		'default'           => 'atelier',
		'transport'         => 'postMessage',
		'sanitize_callback' => fn( $slug ) => isset( ens_palettes()[ $slug ] ) ? $slug : 'atelier',
	] );
	$wp_customize->add_setting( 'ens_section_pattern', [
		'type'              => 'theme_mod',
		'default'           => 'original',
		'transport'         => 'postMessage',
		'sanitize_callback' => fn( $pattern ) => isset( ens_patterns()[ $pattern ] ) ? $pattern : 'original',
	] );
	$wp_customize->add_control( new ENS_Palette_Control( $wp_customize, 'ens_palette', [
		'label'       => __( 'Colour palette', 'elite-nail-studio' ),
		'description' => __( 'Choose a palette and a section pattern; the preview updates with your real pages.', 'elite-nail-studio' ),
		'section'     => 'ens_colours',
		'settings'    => [ 'default' => 'ens_palette', 'pattern' => 'ens_section_pattern' ],
	] ) );
	$wp_customize->selective_refresh->add_partial( 'ens_colours', [
		'selector'            => '#ens-tokens-inline-css',
		'settings'            => [ 'ens_palette', 'ens_section_pattern' ],
		'container_inclusive' => false,
		'fallback_refresh'    => true,
		'render_callback'     => fn() => ens_palette_css(),
	] );
} );

/**
 * Write the saved palette into the active Elementor kit (Global Colours ens_* and
 * the four system colours), so the editor's swatches match the site.
 */
function ens_palette_sync_kit(): void {
	$kit_id = (int) get_option( 'elementor_active_kit' );
	$kit    = $kit_id ? get_post_meta( $kit_id, '_elementor_page_settings', true ) : null;
	if ( ! is_array( $kit ) ) {
		return;
	}
	$slots  = ens_palette_slots();
	$system = [ 'primary' => 'espresso', 'secondary' => 'cocoa', 'text' => 'cocoa', 'accent' => 'rose' ];
	$before = $kit;
	foreach ( [ 'custom_colors', 'system_colors' ] as $group ) {
		foreach ( $kit[ $group ] ?? [] as $i => $color ) {
			$id   = (string) ( $color['_id'] ?? '' );
			if ( 'system_colors' === $group ) {
				$slot = $system[ $id ] ?? '';
			} else {
				$slot = str_starts_with( $id, 'ens_' ) ? str_replace( '_', '-', substr( $id, 4 ) ) : '';
			}
			if ( isset( $slots[ $slot ] ) ) {
				$kit[ $group ][ $i ]['color'] = strtoupper( $slots[ $slot ] );
			}
		}
	}
	if ( $kit !== $before ) {
		update_post_meta( $kit_id, '_elementor_page_settings', $kit );
		if ( class_exists( '\Elementor\Plugin' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	}
}
add_action( 'customize_save_after', 'ens_palette_sync_kit' );
