<?php
/**
 * Global header: announcement bar, centred wordmark, split navigation, overlay menu ≤1024px.
 * Menu: Appearance → Menus → "Primary". Details: Customizer → Studio Details.
 */

defined( 'ABSPATH' ) || exit;
$promo = ens_opt( 'ens_promo' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'elite-nail-studio' ); ?></a>

<header class="ens-header" data-ens-header>
	<?php if ( $promo ) : ?>
		<div class="ens-header__bar">
			<p class="ens-header__promo"><?php echo esc_html( $promo ); ?></p>
			<p class="ens-header__contact">
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', ens_opt( 'ens_phone' ) ) ); ?>"><?php echo esc_html( ens_opt( 'ens_phone' ) ); ?></a>
				<a href="mailto:<?php echo esc_attr( ens_opt( 'ens_email' ) ); ?>"><?php echo esc_html( ens_opt( 'ens_email' ) ); ?></a>
			</p>
		</div>
	<?php endif; ?>

	<div class="ens-header__main">
		<button class="ens-burger" type="button" aria-expanded="false" aria-controls="ens-menu" data-ens-menu-toggle>
			<span class="ens-burger__lines" aria-hidden="true"></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'elite-nail-studio' ); ?></span>
		</button>

		<nav class="ens-nav" aria-label="<?php esc_attr_e( 'Primary', 'elite-nail-studio' ); ?>">
			<?php
			wp_nav_menu( [
				'theme_location' => 'menu-1',
				'container'      => false,
				'menu_class'     => 'ens-nav__list',
				'depth'          => 2,
				'fallback_cb'    => 'ens_menu_fallback',
			] );
			?>
		</nav>

		<a class="ens-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php if ( has_custom_logo() ) : ?>
				<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'medium', false, [ 'class' => 'ens-logo__img', 'alt' => get_bloginfo( 'name' ) ] ); ?>
			<?php else : ?>
				<span class="ens-logo__name"><?php bloginfo( 'name' ); ?></span>
				<span class="ens-logo__tag"><?php bloginfo( 'description' ); ?></span>
			<?php endif; ?>
		</a>

		<div class="ens-header__actions">
			<a class="ens-header__ig" href="<?php echo esc_url( ens_opt( 'ens_instagram' ) ); ?>" target="_blank" rel="noopener">
				<?php echo ens_icon( 'instagram' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Instagram', 'elite-nail-studio' ); ?></span>
			</a>
			<a class="ens-btn ens-btn--sm ens-header__cta" href="<?php echo esc_url( ens_book_url() ); ?>">
				<span><?php echo esc_html( ens_opt( 'ens_book_label' ) ); ?></span>
			</a>
		</div>
	</div>
</header>

<div class="ens-menu" id="ens-menu" data-ens-menu hidden>
	<nav class="ens-menu__nav" aria-label="<?php esc_attr_e( 'Mobile', 'elite-nail-studio' ); ?>">
		<?php
		wp_nav_menu( [
			'theme_location' => 'menu-1',
			'container'      => false,
			'menu_class'     => 'ens-menu__list',
			'depth'          => 2,
			'fallback_cb'    => 'ens_menu_fallback',
		] );
		?>
	</nav>
	<div class="ens-menu__foot">
		<a class="ens-btn ens-btn--light" href="<?php echo esc_url( ens_book_url() ); ?>"><span><?php echo esc_html( ens_opt( 'ens_book_label' ) ); ?></span></a>
		<p><?php echo ens_lines( ens_opt( 'ens_address' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
		<p><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', ens_opt( 'ens_phone' ) ) ); ?>"><?php echo esc_html( ens_opt( 'ens_phone' ) ); ?></a></p>
	</div>
</div>

<main id="content" class="ens-main">
