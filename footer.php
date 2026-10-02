<?php
/**
 * Global footer. The upper footer is the Elementor template "ens-footer"
 * (Templates → Saved Templates → Global Footer), so its content is edited in Elementor.
 * The legal bar below is theme-rendered (dynamic year + "Footer legal" menu).
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer class="ens-footer">
	<?php echo ens_library_template( 'ens-footer' ); // phpcs:ignore WordPress.Security.EscapeOutput -- Elementor output. ?>
	<div class="ens-footer__legal">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . get_bloginfo( 'name' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'elite-nail-studio' ); ?></p>
		<?php
		wp_nav_menu( [
			'theme_location' => 'footer',
			'container'      => false,
			'menu_class'     => 'ens-footer__menu',
			'depth'          => 1,
			'fallback_cb'    => false,
		] );
		?>
		<p class="ens-footer__credit"><?php echo ens_eds( esc_html__( 'Crafted by Elite Digital Solutions', 'elite-nail-studio' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped, then wrapped. ?></p>
	</div>
	<?php if ( ens_opt( 'ens_disclosure' ) ) : // Demo sites: Customizer → Studio Details → Footer disclosure. ?>
		<p class="ens-footer__disclosure"><?php echo ens_eds( esc_html( ens_opt( 'ens_disclosure' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped, then wrapped. ?></p>
	<?php endif; ?>
</footer>

<?php wp_footer(); ?>
</body>
</html>
