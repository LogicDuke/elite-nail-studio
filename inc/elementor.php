<?php
/**
 * Elementor integration: widget category, self-hosted font group, widget base + registration.
 * Widgets live in inc/widgets/ — one class per file, auto-registered.
 */

defined( 'ABSPATH' ) || exit;

// Theme fonts appear in Elementor's font picker under "Elite Nail Studio" and are never fetched from Google.
add_filter( 'elementor/fonts/groups', fn( $groups ) => [ 'ens' => __( 'Elite Nail Studio', 'elite-nail-studio' ) ] + $groups );
add_filter( 'elementor/fonts/additional_fonts', fn( $fonts ) => $fonts + [ 'Cormorant Garamond' => 'ens', 'Jost' => 'ens' ] );

add_action( 'elementor/elements/categories_registered', function ( $manager ) {
	$manager->add_category( 'elite-nail-studio', [ 'title' => __( 'Elite Nail Studio', 'elite-nail-studio' ), 'icon' => 'eicon-star-o' ] );
} );

add_action( 'elementor/widgets/register', function ( $widgets ) {
	require_once ENS_DIR . '/inc/widgets/class-ens-widget.php';
	foreach ( glob( ENS_DIR . '/inc/widgets/ens-*.php' ) as $file ) {
		require_once $file;
		$class = 'ENS_' . str_replace( '-', '_', ucwords( substr( basename( $file, '.php' ), 4 ), '-' ) );
		$widgets->register( new $class() );
	}
} );

// Editor preview: skip `ens-js`, so pre-reveal states never hide content while editing.
add_action( 'wp_head', function () {
	if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		echo "<script>document.documentElement.classList.remove('ens-js')</script>\n";
	}
}, 2 );
