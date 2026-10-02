<?php
/**
 * WebP delivery for photographs (EDS standard, as Elite Realty).
 *
 * Every upload keeps its JPEG source and JPEG derivatives; an optimised WebP
 * sibling is written beside each file ("name-768x960.webp" next to
 * "name-768x960.jpg") and the front end serves it in src/srcset (and the gallery
 * lightbox) when it exists. Attachment IDs, metadata and Elementor references
 * never change, and the JPEG URL stays canonical for anything outside <img>
 * (feeds, social cards, structured data). Siblings are made on upload, removed
 * with their JPEG, and can be (re)built with `wp ens webp`.
 * To revert: remove this file's require (and, optionally, the .webp files).
 */

defined( 'ABSPATH' ) || exit;

/** Lossy WebP quality: at 86 every photograph measured SSIM >= .978 against its approved JPEG. */
const ENS_WEBP_QUALITY = 86;

/** A sibling is kept only when it saves at least this share of the JPEG's bytes. */
const ENS_WEBP_MIN_SAVING = 0.10;

/** The WebP sibling of an uploads URL when that file exists, else the URL itself. */
function ens_webp_url( string $url ): string {
	static $uploads = null, $seen = [];
	if ( isset( $seen[ $url ] ) ) {
		return $seen[ $url ];
	}
	$uploads = $uploads ?? wp_get_upload_dir();
	$webp    = preg_replace( '/\.jpe?g$/i', '.webp', $url );
	if ( $webp === $url || ! str_starts_with( $url, $uploads['baseurl'] ) ) {
		return $seen[ $url ] = $url;
	}
	return $seen[ $url ] = file_exists( $uploads['basedir'] . substr( $webp, strlen( $uploads['baseurl'] ) ) ) ? $webp : $url;
}

/** Swap every URL of a srcset string. */
function ens_webp_srcset( string $srcset ): string {
	return (string) preg_replace_callback( '/(\S+)(\s+\d+[wx])/', static fn( $m ) => ens_webp_url( $m[1] ) . $m[2], $srcset );
}

/** Theme, widget and Elementor <img> output (wp_get_attachment_image). */
add_filter( 'wp_get_attachment_image_attributes', function ( array $attr ): array {
	if ( is_admin() ) {
		return $attr;
	}
	if ( ! empty( $attr['src'] ) ) {
		$attr['src'] = ens_webp_url( $attr['src'] );
	}
	if ( ! empty( $attr['srcset'] ) ) {
		$attr['srcset'] = ens_webp_srcset( $attr['srcset'] );
	}
	return $attr;
}, 20 );

/** Images inside post content (srcset is added before this filter runs). */
add_filter( 'wp_content_img_tag', function ( string $img ): string {
	return (string) preg_replace_callback(
		'/\s(src|srcset)="([^"]+)"/',
		static fn( $m ) => ' ' . $m[1] . '="' . ( 'src' === $m[1] ? ens_webp_url( $m[2] ) : ens_webp_srcset( $m[2] ) ) . '"',
		$img
	);
}, 20 );

/**
 * Write WebP siblings for an attachment's source and derivatives.
 *
 * @return array{made:int,skipped:int,bytes:int} Siblings written, files left JPEG-only, bytes of the siblings.
 */
function ens_webp_make( int $attachment_id, bool $force = false, ?array $meta = null ): array {
	$result = [ 'made' => 0, 'skipped' => 0, 'bytes' => 0 ];
	$file   = get_attached_file( $attachment_id );
	$meta   = $meta ?? wp_get_attachment_metadata( $attachment_id );
	if ( ! $file || ! is_array( $meta ) || ! preg_match( '/\.jpe?g$/i', $file ) ) {
		return $result;
	}
	$dir   = dirname( $file );
	$files = array_unique( array_merge( [ basename( $file ) ], array_column( $meta['sizes'] ?? [], 'file' ) ) );
	foreach ( $files as $name ) {
		$jpeg = "{$dir}/{$name}";
		$webp = (string) preg_replace( '/\.jpe?g$/i', '.webp', $jpeg );
		if ( ! file_exists( $jpeg ) || ( ! $force && file_exists( $webp ) ) ) {
			$result['bytes'] += file_exists( $webp ) ? filesize( $webp ) : 0;
			$result['made']  += file_exists( $webp ) ? 1 : 0;
			continue;
		}
		$editor = wp_get_image_editor( $jpeg );
		if ( is_wp_error( $editor ) ) {
			++$result['skipped'];
			continue;
		}
		$editor->set_quality( ENS_WEBP_QUALITY );
		$saved = $editor->save( $webp, 'image/webp' );
		clearstatcache();
		if ( is_wp_error( $saved ) || ! file_exists( $webp ) || filesize( $webp ) > filesize( $jpeg ) * ( 1 - ENS_WEBP_MIN_SAVING ) ) {
			wp_delete_file( $webp );
			++$result['skipped'];
			continue;
		}
		++$result['made'];
		$result['bytes'] += filesize( $webp );
	}
	return $result;
}

add_filter( 'wp_generate_attachment_metadata', function ( $meta, $attachment_id ) {
	ens_webp_make( (int) $attachment_id, true, is_array( $meta ) ? $meta : null ); // Metadata is not saved yet.
	return $meta;
}, 20, 2 );

/** A JPEG leaving the uploads folder takes its WebP sibling with it (no stale siblings). */
add_filter( 'wp_delete_file', function ( $file ) {
	if ( is_string( $file ) && preg_match( '/\.jpe?g$/i', $file ) ) {
		$webp = preg_replace( '/\.jpe?g$/i', '.webp', $file );
		if ( file_exists( $webp ) ) {
			@unlink( $webp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
		}
	}
	return $file;
} );

/** Deleting an attachment removes every sibling it owns, even where core leaves a size file behind. */
add_action( 'delete_attachment', function ( $attachment_id ) {
	$file = get_attached_file( $attachment_id );
	$meta = wp_get_attachment_metadata( $attachment_id );
	if ( ! $file ) {
		return;
	}
	foreach ( array_merge( [ basename( $file ) ], array_column( is_array( $meta ) ? ( $meta['sizes'] ?? [] ) : [], 'file' ) ) as $name ) {
		$webp = dirname( $file ) . '/' . preg_replace( '/\.jpe?g$/i', '.webp', $name );
		if ( str_ends_with( $webp, '.webp' ) && file_exists( $webp ) ) {
			@unlink( $webp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
		}
	}
} );

/** The 4:5 card crop is only requested for portrait and square images (cards, lookbook, story). */
add_filter( 'intermediate_image_sizes_advanced', function ( $sizes, $meta ) {
	if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) && $meta['width'] > $meta['height'] ) {
		unset( $sizes['ens-card'] );
	}
	return $sizes;
}, 10, 2 );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * Build WebP siblings for every JPEG attachment.
	 *
	 * [--force]
	 * : Rebuild siblings that already exist.
	 */
	WP_CLI::add_command( 'ens webp', function ( $args, $assoc ) {
		$total = [ 'made' => 0, 'skipped' => 0, 'bytes' => 0 ];
		$ids   = get_posts( [ 'post_type' => 'attachment', 'post_mime_type' => 'image/jpeg', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ] );
		foreach ( $ids as $id ) {
			$r = ens_webp_make( (int) $id, ! empty( $assoc['force'] ) );
			foreach ( $total as $k => $v ) {
				$total[ $k ] = $v + $r[ $k ];
			}
		}
		WP_CLI::success( sprintf( '%d attachments: %d WebP siblings (%s), %d files kept JPEG-only (WebP not %d%% smaller).', count( $ids ), $total['made'], size_format( $total['bytes'], 1 ), $total['skipped'], ENS_WEBP_MIN_SAVING * 100 ) );
	} );
}
