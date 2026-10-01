<?php
defined( 'ABSPATH' ) || exit;

/**
 * Sticky editorial: images stay pinned while the steps scroll; the visible step swaps the image.
 * ≤1024px (and reduced motion) each step shows its own image inline.
 */
class ENS_Story extends ENS_Widget {

	public function get_name() {
		return 'ens-story';
	}

	public function get_title() {
		return __( 'ENS Sticky Story', 'elite-nail-studio' );
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	protected function register_controls() {
		$this->section( __( 'Story', 'elite-nail-studio' ) );
		$this->intro_fields( __( 'The ritual', 'elite-nail-studio' ), 'Three movements, <em>one</em> calm hour' );
		$this->items( 'steps', __( 'Steps', 'elite-nail-studio' ), [
			'image' => [ __( 'Image (4:5)', 'elite-nail-studio' ), 'media' ],
			'label' => [ __( 'Label', 'elite-nail-studio' ), 'text', __( 'Consultation', 'elite-nail-studio' ) ],
			'title' => [ __( 'Title', 'elite-nail-studio' ), 'text', '' ],
			'text'  => [ __( 'Text', 'elite-nail-studio' ), 'textarea', '' ],
		], [ [ 'label' => __( 'Consultation', 'elite-nail-studio' ) ] ], 'label' );
		$this->end_controls_section();
	}

	protected function render() {
		$s = $this->get_settings_for_display();
		?>
		<div class="ens-story" data-ens-story>
			<div class="ens-story__media" aria-hidden="true">
				<?php foreach ( $s['steps'] as $i => $step ) : ?>
					<figure class="ens-story__frame<?php echo 0 === $i ? ' is-active' : ''; ?>"><?php echo self::img( $step['image'], 'ens-card', [ 'sizes' => '(max-width: 1024px) 90vw, 45vw', 'alt' => '' ] ); // phpcs:ignore ?></figure>
				<?php endforeach; ?>
				<p class="ens-story__count"><span data-ens-story-index>01</span> / <?php echo esc_html( str_pad( (string) count( $s['steps'] ), 2, '0', STR_PAD_LEFT ) ); ?></p>
			</div>
			<div class="ens-story__content">
				<?php echo self::intro( $s ); // phpcs:ignore ?>
				<ol class="ens-story__steps">
					<?php foreach ( $s['steps'] as $i => $step ) : ?>
						<li class="ens-story__step<?php echo 0 === $i ? ' is-active' : ''; ?>" data-ens-step="<?php echo (int) $i; ?>">
							<figure class="ens-story__inline ens-mask"><?php echo self::img( $step['image'], 'ens-card', [ 'sizes' => '90vw' ] ); // phpcs:ignore ?></figure>
							<p class="ens-eyebrow"><span class="ens-story__n"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span> <?php echo esc_html( $step['label'] ); ?></p>
							<h3 class="ens-story__title"><?php echo esc_html( $step['title'] ); ?></h3>
							<p class="ens-story__text"><?php echo esc_html( $step['text'] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>
		</div>
		<?php
	}
}
