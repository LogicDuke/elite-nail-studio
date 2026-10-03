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

require ENS_DIR . '/inc/palette.php';
require ENS_DIR . '/inc/palette-customizer.php';
require ENS_DIR . '/inc/demo-palette-switcher.php';
require ENS_DIR . '/inc/options.php';
require ENS_DIR . '/inc/webp.php';
require ENS_DIR . '/inc/consent.php';
require ENS_DIR . '/inc/forms.php';
require ENS_DIR . '/inc/seo.php';
require ENS_DIR . '/inc/elementor.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require ENS_DIR . '/demo/seed.php';
}

// Hello Elementor: keep its reset, drop its theme + header/footer styles (we ship our own).
add_filter( 'hello_elementor_enqueue_theme_style', '__return_false' );
add_filter( 'hello_elementor_header_footer', '__return_false' );
add_filter( 'hello_elementor_page_title', '__return_false' );
add_filter( 'hello_elementor_description_meta_tag', '__return_false' );

// Simply Static: inline styles have their own url() pass; as a plain URL attribute too, a value like
// "--ens-cols:3" is taken for a relative path and exported as "--ens-cols:3/" (invalid, layout lost).
add_filter( 'ss_match_tags', function ( $tags ) {
	return array_map( fn( $attrs ) => array_values( array_diff( (array) $attrs, [ 'style' ] ) ), (array) $tags );
} );

add_action( 'after_setup_theme', function () {
	register_nav_menus( [
		'menu-1' => __( 'Primary', 'elite-nail-studio' ),
		'footer' => __( 'Footer legal', 'elite-nail-studio' ),
	] );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
	add_image_size( 'ens-card', 900, 1125, true );   // 4:5, portrait/square sources only (inc/webp.php)
}, 20 );

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
	wp_add_inline_style( 'ens-tokens', ens_palette_css() ); // Palette, tones, section pattern (inc/palette.php).
	wp_enqueue_style( 'ens-main', ENS_URI . '/assets/css/main.css', array_merge( [ 'ens-tokens' ], $deps ), $ver( '/assets/css/main.css' ) );
	wp_enqueue_script( 'ens-motion', ENS_URI . '/assets/js/motion.js', [], $ver( '/assets/js/motion.js' ), [ 'in_footer' => true, 'strategy' => 'defer' ] );
	wp_enqueue_script( 'ens-forms', ENS_URI . '/assets/js/forms.js', [], $ver( '/assets/js/forms.js' ), [ 'in_footer' => true, 'strategy' => 'defer' ] ); // Static-export forms (inc/forms.php).
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

/**
 * Desktop dropdowns: each child link carries its page's featured image, shown in the panel's image
 * column while the item is hovered or focused (main.css §4; loaded on the panel's first opening).
 */
add_filter( 'walker_nav_menu_start_el', function ( $output, $item, $depth, $args ) {
	if ( 1 !== $depth || 'ens-nav__list' !== ( $args->menu_class ?? '' ) || 'post_type' !== $item->type ) {
		return $output;
	}
	$img = get_post_thumbnail_id( (int) $item->object_id );
	return $img ? $output . '<span class="ens-nav__thumb" aria-hidden="true">' . wp_get_attachment_image( $img, 'medium_large', false, [ 'alt' => '', 'loading' => 'lazy', 'sizes' => '200px' ] ) . '</span>' : $output;
}, 10, 4 );

/** Archive/blog hero image: the Journal page's featured image, else none. */
function ens_journal_hero_id() {
	$page = (int) get_option( 'page_for_posts' );
	return $page ? get_post_thumbnail_id( $page ) : 0;
}
