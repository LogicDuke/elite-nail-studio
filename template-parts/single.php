<?php
/**
 * Single post (editorial hero + overlapping article card) and plain, non-Elementor pages.
 */

defined( 'ABSPATH' ) || exit;

while ( have_posts() ) :
	the_post();

	if ( ! is_singular( 'post' ) ) :
		?>
		<article class="ens-page ens-container ens-container--narrow">
			<h1 class="ens-page__title"><?php the_title(); ?></h1>
			<div class="ens-prose"><?php the_content(); ?></div>
		</article>
		<?php
		continue;
	endif;

	$cat = get_the_category();
	?>
	<section class="ens-hero ens-hero--page ens-hero--post">
		<div class="ens-hero__media">
			<?php the_post_thumbnail( 'full', [ 'class' => 'ens-hero__img', 'sizes' => '100vw', 'fetchpriority' => 'high' ] ); ?>
		</div>
		<div class="ens-hero__inner">
			<?php if ( $cat ) : ?>
				<a class="ens-eyebrow ens-reveal" href="<?php echo esc_url( get_category_link( $cat[0] ) ); ?>"><?php echo esc_html( $cat[0]->name ); ?></a>
			<?php endif; ?>
			<h1 class="ens-hero__title ens-split"><?php the_title(); ?></h1>
			<p class="ens-hero__meta ens-reveal">
				<?php the_author(); ?> · <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
				· <?php
				/* translators: %d: minutes */
				printf( esc_html__( '%d min read', 'elite-nail-studio' ), max( 1, (int) round( str_word_count( wp_strip_all_tags( get_the_content() ) ) / 220 ) ) );
				?>
			</p>
		</div>
	</section>

	<article <?php post_class( 'ens-article' ); ?>>
		<div class="ens-article__card">
			<?php if ( has_excerpt() ) : ?>
				<p class="ens-article__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
			<div class="ens-prose"><?php the_content(); ?></div>

			<footer class="ens-article__foot">
				<?php the_tags( '<p class="ens-article__tags">', '', '</p>' ); ?>
				<p class="ens-share">
					<span><?php esc_html_e( 'Share', 'elite-nail-studio' ); ?></span>
					<?php
					$u = rawurlencode( get_permalink() );
					$t = rawurlencode( get_the_title() );
					$share = [
						'Pinterest' => "https://pinterest.com/pin/create/button/?url={$u}&description={$t}",
						'Facebook'  => "https://www.facebook.com/sharer/sharer.php?u={$u}",
						'Email'     => "mailto:?subject={$t}&body={$u}",
					];
					foreach ( $share as $label => $href ) {
						printf( '<a href="%s" target="_blank" rel="noopener">%s</a>', esc_url( $href ), esc_html( $label ) );
					}
					?>
				</p>
			</footer>
		</div>

		<nav class="ens-article__nav" aria-label="<?php esc_attr_e( 'More stories', 'elite-nail-studio' ); ?>">
			<?php
			the_post_navigation( [
				'prev_text' => '<span class="ens-eyebrow">' . __( 'Previous', 'elite-nail-studio' ) . '</span><span class="ens-article__nav-title">%title</span>',
				'next_text' => '<span class="ens-eyebrow">' . __( 'Next', 'elite-nail-studio' ) . '</span><span class="ens-article__nav-title">%title</span>',
			] );
			?>
		</nav>

		<?php
		if ( comments_open() || get_comments_number() ) {
			echo '<div class="ens-comments">';
			comments_template();
			echo '</div>';
		}
		?>
	</article>

	<?php
	$related = new WP_Query( [
		'post__not_in'        => [ get_the_ID() ],
		'category__in'        => wp_list_pluck( $cat, 'term_id' ),
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	] );
	if ( $related->have_posts() ) :
		?>
		<section class="ens-related">
			<div class="ens-container">
				<p class="ens-eyebrow"><?php esc_html_e( 'Keep reading', 'elite-nail-studio' ); ?></p>
				<h2 class="ens-related__title ens-split"><?php esc_html_e( 'More from the Journal', 'elite-nail-studio' ); ?></h2>
				<div class="ens-cards ens-stagger">
					<?php
					while ( $related->have_posts() ) {
						$related->the_post();
						get_template_part( 'template-parts/card' );
					}
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>
<?php endwhile; ?>
