<?php
/**
 * Lightweight SEO (no SEO plugin): meta description, Open Graph, X card, archive canonicals.
 *
 * Description: the page/post excerpt (Page → Excerpt). Social image: the featured image, else the
 * front page's. URLs are printed absolute; a relative static export turns them root-relative and
 * cloudflare/finalize.mjs restores the public origin (docs/static-seo.md).
 * Also: author archives return 404 (they expose login names), head links to WordPress-only
 * endpoints (XML-RPC, REST, oEmbed, shortlink, feeds) are removed, as they break on a static host,
 * and the site name is exposed for brand protection from machine translation (motion.js).
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', fn() => add_post_type_support( 'page', 'excerpt' ) );

// The business name, for assets/js/motion.js: kept out of machine translation wherever it appears.
add_filter( 'language_attributes', fn( $attrs ) => is_admin() ? $attrs : $attrs . ' data-ens-brand="' . esc_attr( get_bloginfo( 'name' ) ) . '"' );

remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );

// Before redirect_canonical, so ?author=1 does not reveal the archive slug either.
add_action( 'template_redirect', function () {
	global $wp_query;
	if ( is_author() ) {
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}, 1 );
add_filter( 'wp_sitemaps_add_provider', fn( $provider, $name ) => 'users' === $name ? false : $provider, 10, 2 );

/** Title, description, canonical URL, image and type of the current view; null when it has none. */
function ens_seo_data() {
	$post = null;
	if ( is_singular() ) {
		$post = get_queried_object();
	} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
		$post = get_post( get_option( 'page_for_posts' ) );
	}

	if ( $post ) {
		$d = [
			'title' => is_front_page() ? wp_get_document_title() : get_the_title( $post ),
			'desc'  => has_excerpt( $post ) || 'post' === $post->post_type ? get_the_excerpt( $post ) : '',
			'url'   => is_singular() ? wp_get_canonical_url( $post ) : get_permalink( $post ),
			'img'   => get_post_thumbnail_id( $post ),
			'type'  => is_singular( 'post' ) ? 'article' : 'website',
		];
	} elseif ( is_category() || is_tag() ) {
		$term = get_queried_object();
		$d    = [
			'title' => $term->name,
			'desc'  => $term->description ?: sprintf(
				/* translators: 1: category name, 2: site name */
				__( 'Journal stories filed under %1$s, from %2$s.', 'elite-nail-studio' ),
				$term->name,
				get_bloginfo( 'name' )
			),
			'url'   => get_term_link( $term ),
			'img'   => 0,
			'type'  => 'website',
		];
	} else {
		return null; // 404, search: no social metadata.
	}

	$d['title'] = html_entity_decode( wp_strip_all_tags( $d['title'] ), ENT_QUOTES, 'UTF-8' );
	$d['desc']  = trim( html_entity_decode( wp_strip_all_tags( $d['desc'] ), ENT_QUOTES, 'UTF-8' ) );
	$d['img']   = $d['img'] ?: get_post_thumbnail_id( (int) get_option( 'page_on_front' ) );
	return $d;
}

add_action( 'wp_head', function () {
	// WordPress prints canonicals for single views only; add them for the Journal and its categories.
	if ( ( is_home() || is_category() || is_tag() ) && ! is_front_page() ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( get_pagenum_link( max( 1, (int) get_query_var( 'paged' ) ) ) ) );
	}

	$d = ens_seo_data();
	if ( ! $d ) {
		return;
	}
	$tags = [
		[ 'name', 'description', $d['desc'] ],
		[ 'property', 'og:type', $d['type'] ],
		[ 'property', 'og:site_name', get_bloginfo( 'name' ) ],
		[ 'property', 'og:locale', get_locale() ],
		[ 'property', 'og:title', $d['title'] ],
		[ 'property', 'og:description', $d['desc'] ],
		[ 'property', 'og:url', $d['url'] ],
	];
	$img = $d['img'] ? wp_get_attachment_image_src( $d['img'], '1536x1536' ) : false;
	if ( $img ) {
		$alt  = (string) get_post_meta( $d['img'], '_wp_attachment_image_alt', true );
		$tags = array_merge( $tags, [
			[ 'property', 'og:image', $img[0] ],
			[ 'property', 'og:image:width', $img[1] ],
			[ 'property', 'og:image:height', $img[2] ],
			[ 'property', 'og:image:alt', $alt ],
		] );
	}
	if ( 'article' === $d['type'] ) {
		$post = get_queried_object();
		$cat  = get_the_category( $post->ID );
		$tags = array_merge( $tags, [
			[ 'property', 'article:published_time', get_the_date( 'c', $post ) ],
			[ 'property', 'article:modified_time', get_the_modified_date( 'c', $post ) ],
			[ 'property', 'article:section', $cat ? $cat[0]->name : '' ],
		] );
	}
	$tags = array_merge( $tags, [
		[ 'name', 'twitter:card', $img ? 'summary_large_image' : 'summary' ],
		[ 'name', 'twitter:title', $d['title'] ],
		[ 'name', 'twitter:description', $d['desc'] ],
		[ 'name', 'twitter:image', $img ? $img[0] : '' ],
		[ 'name', 'twitter:image:alt', $img ? $alt : '' ],
	] );
	foreach ( $tags as [ $attr, $key, $value ] ) {
		if ( '' !== (string) $value ) {
			printf( '<meta %s="%s" content="%s">' . "\n", $attr, esc_attr( $key ), esc_attr( $value ) );
		}
	}
}, 5 );
