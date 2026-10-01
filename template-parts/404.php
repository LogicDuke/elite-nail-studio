<?php
/**
 * 404 — renders the Elementor template "ens-404" (editable), with a minimal fallback.
 */

defined( 'ABSPATH' ) || exit;

$content = ens_library_template( 'ens-404' );
if ( $content ) {
	echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- Elementor output.
	return;
}
?>
<section class="ens-hero ens-hero--page">
	<div class="ens-hero__inner">
		<p class="ens-eyebrow">404</p>
		<h1 class="ens-hero__title"><?php esc_html_e( 'This page has slipped away', 'elite-nail-studio' ); ?></h1>
		<p class="ens-hero__actions"><a class="ens-btn ens-btn--light" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span><?php esc_html_e( 'Return home', 'elite-nail-studio' ); ?></span></a></p>
	</div>
</section>
