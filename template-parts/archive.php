<?php
/**
 * Journal archive (posts page, categories, tags, dates).
 * Hero image = featured image of the page set as "Posts page" (Settings → Reading).
 */

defined( 'ABSPATH' ) || exit;

$posts_page = (int) get_option( 'page_for_posts' );
$title      = is_home() ? ( $posts_page ? get_the_title( $posts_page ) : __( 'Journal', 'elite-nail-studio' ) ) : wp_strip_all_tags( get_the_archive_title() );
$intro      = is_home() ? ( $posts_page ? get_the_excerpt( $posts_page ) : '' ) : wp_strip_all_tags( get_the_archive_description() );
$current    = is_category() ? get_queried_object_id() : 0;
?>
<section class="ens-hero ens-hero--page">
	<div class="ens-hero__media">
		<?php echo wp_get_attachment_image( ens_journal_hero_id(), 'full', false, [ 'class' => 'ens-hero__img', 'sizes' => ens_cover_sizes( ens_journal_hero_id(), 70, 520 ), 'fetchpriority' => 'high' ] ); ?>
	</div>
	<div class="ens-hero__inner">
		<p class="ens-eyebrow ens-reveal"><?php esc_html_e( 'The Journal', 'elite-nail-studio' ); ?></p>
		<h1 class="ens-hero__title ens-split"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $intro ) : ?>
			<p class="ens-hero__text ens-reveal"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="ens-archive">
	<div class="ens-container">
		<ul class="ens-chips ens-reveal" aria-label="<?php esc_attr_e( 'Categories', 'elite-nail-studio' ); ?>">
			<li><a class="ens-chip<?php echo $current ? '' : ' is-active'; ?>" href="<?php echo esc_url( $posts_page ? get_permalink( $posts_page ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'All stories', 'elite-nail-studio' ); ?></a></li>
			<?php foreach ( get_categories( [ 'hide_empty' => true ] ) as $cat ) : ?>
				<li><a class="ens-chip<?php echo $current === $cat->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a></li>
			<?php endforeach; ?>
		</ul>

		<?php if ( have_posts() ) : ?>
			<div class="ens-cards ens-stagger">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/card' );
				}
				?>
			</div>
			<?php
			the_posts_pagination( [
				'mid_size'  => 1,
				'prev_text' => ens_icon( 'arrow-l' ) . '<span class="screen-reader-text">' . __( 'Previous', 'elite-nail-studio' ) . '</span>',
				'next_text' => ens_icon( 'arrow' ) . '<span class="screen-reader-text">' . __( 'Next', 'elite-nail-studio' ) . '</span>',
			] );
			?>
		<?php else : ?>
			<p class="ens-empty"><?php esc_html_e( 'No stories here yet.', 'elite-nail-studio' ); ?></p>
		<?php endif; ?>
	</div>
</section>
