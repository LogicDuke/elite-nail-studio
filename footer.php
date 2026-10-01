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
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
