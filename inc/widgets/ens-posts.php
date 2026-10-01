<?php
defined( 'ABSPATH' ) || exit;

/** Latest Journal posts, using the shared card template. */
class ENS_Posts extends ENS_Widget {

	public function get_name() {
		return 'ens-posts';
	}

	// Per-request output (submission status / latest posts) — never serve from Elementor's element cache.
	protected function is_dynamic_content(): bool {
		return true;
	}

	public function get_title() {
		return __( 'ENS Journal Posts', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	protected function register_controls() {
		$this->section( __( 'Posts', 'elite-nail-studio' ) );
		$this->field( 'count', __( 'Number of posts', 'elite-nail-studio' ), 'number', 3 );
		$this->field( 'category', __( 'Category slug (optional)', 'elite-nail-studio' ), 'text', '' );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		$q = new WP_Query( [
			'posts_per_page'      => max( 1, (int) $s['count'] ),
			'category_name'       => sanitize_title( $s['category'] ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		] );
		echo '<div class="ens-cards ens-rail ens-stagger">';
		while ( $q->have_posts() ) {
			$q->the_post();
			get_template_part( 'template-parts/card' );
		}
		wp_reset_postdata();
		echo '</div>';
	}
}
