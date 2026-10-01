<?php
/**
 * Elite Nail Studio — child theme bootstrap.
 *
 * Theme layer: tokens, components, header/footer, motion, forms and custom Elementor widgets.
 * Page content lives in Elementor (see docs/README.md).
 */

defined( 'ABSPATH' ) || exit;

define( 'ENS_VERSION', '1.0.0' );
define( 'ENS_DIR', get_stylesheet_directory() );
define( 'ENS_URI', get_stylesheet_directory_uri() );

require ENS_DIR . '/inc/options.php';
require ENS_DIR . '/inc/forms.php';
require ENS_DIR . '/inc/elementor.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require ENS_DIR . '/demo/seed.php';
}

// Hello Elementor: keep its reset, drop its theme + header/footer styles (we ship our own).
add_filter( 'hello_elementor_enqueue_theme_style', '__return_false' );
add_filter( 'hello_elementor_header_footer', '__return_false' );
add_filter( 'hello_elementor_page_title', '__return_false' );
add_filter( 'hello_elementor_description_meta_tag', '__return_false' );

add_action( 'after_setup_theme', function () {
	register_nav_menus( [
		'menu-1' => __( 'Primary', 'elite-nail-studio' ),
		'footer' => __( 'Footer legal', 'elite-nail-studio' ),
	] );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
	add_image_size( 'ens-card', 900, 1125, true );   // 4:5
	add_image_size( 'ens-wide', 1600, 0 );
}, 20 );

/** `<html data-ens-palette>` — alternative palettes are a tokens.css block + this filter. */
add_filter( 'language_attributes', function ( $output ) {
	$palette = apply_filters( 'ens_palette', '' );
	return $palette ? $output . ' data-ens-palette="' . esc_attr( $palette ) . '"' : $output;
} );

add_action( 'wp_head', function () {
	// Pre-reveal states only apply under .ens-js, so content stays visible without JS.
	echo "<script>document.documentElement.classList.add('ens-js')</script>\n";
	foreach ( [ 'cormorant-garamond', 'jost' ] as $font ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( ENS_URI . "/assets/fonts/{$font}.woff2" ) );
	}
}, 1 );

add_action( 'wp_enqueue_scripts', function () {
	$deps = wp_style_is( 'elementor-frontend', 'registered' ) ? [ 'elementor-frontend' ] : [];
	$ver  = fn( $file ) => ENS_VERSION . '.' . filemtime( ENS_DIR . $file );
	wp_enqueue_style( 'ens-tokens', ENS_URI . '/assets/css/tokens.css', [], $ver( '/assets/css/tokens.css' ) );
	wp_enqueue_style( 'ens-main', ENS_URI . '/assets/css/main.css', array_merge( [ 'ens-tokens' ], $deps ), $ver( '/assets/css/main.css' ) );
	wp_enqueue_script( 'ens-motion', ENS_URI . '/assets/js/motion.js', [], $ver( '/assets/js/motion.js' ), [ 'in_footer' => true, 'strategy' => 'defer' ] );
	if ( is_singular( 'post' ) && comments_open() ) {
		wp_enqueue_script( 'comment-reply' );
	}
}, 20 );

/** True when the page opens with a full-bleed hero, so the header starts transparent. */
function ens_has_hero() {
	if ( is_home() || is_archive() || is_search() || is_404() || is_singular( 'post' ) ) {
		return true;
	}
	if ( is_singular() ) {
		$data = json_decode( (string) get_post_meta( get_queried_object_id(), '_elementor_data', true ), true );
		$el   = $data[0] ?? null;
		while ( $el && 'widget' !== ( $el['elType'] ?? '' ) ) {
			$el = $el['elements'][0] ?? null; // Descend the first container chain to its first widget.
		}
		return $el && 'ens-hero' === ( $el['widgetType'] ?? '' ) && 'split' !== ( $el['settings']['layout'] ?? 'home' );
	}
	return false;
}

add_filter( 'body_class', function ( $classes ) {
	$classes[] = ens_has_hero() ? 'ens-has-hero' : 'ens-no-hero';
	return $classes;
} );

// Archive heroes read "Care", not "Category: Care" — the eyebrow already says "The Journal".
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/** Fallback for the primary menu before one is assigned. */
function ens_menu_fallback() {
	echo '<ul class="ens-nav__list">';
	wp_list_pages( [ 'title_li' => '', 'depth' => 1 ] );
	echo '</ul>';
}

/** Archive/blog hero image: the Journal page's featured image, else none. */
function ens_journal_hero_id() {
	$page = (int) get_option( 'page_for_posts' );
	return $page ? get_post_thumbnail_id( $page ) : 0;
}
