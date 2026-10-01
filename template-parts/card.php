<?php
/**
 * Journal card — used by the archive, related posts and the ENS Journal widget.
 * Expects to run inside the loop.
 */

defined( 'ABSPATH' ) || exit;
$cat = get_the_category();
?>
<article <?php post_class( 'ens-card' ); ?>>
	<a class="ens-card__media ens-zoom" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'large', [ 'loading' => 'lazy', 'sizes' => '(max-width: 767px) 90vw, (max-width: 1024px) 45vw, 30vw' ] );
		}
		?>
	</a>
	<div class="ens-card__body">
		<p class="ens-card__meta">
			<?php if ( $cat ) : ?>
				<span><?php echo esc_html( $cat[0]->name ); ?></span>
			<?php endif; ?>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</p>
		<h3 class="ens-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="ens-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
		<a class="ens-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read the story', 'elite-nail-studio' ); ?><span class="screen-reader-text">: <?php the_title(); ?></span></a>
	</div>
</article>
