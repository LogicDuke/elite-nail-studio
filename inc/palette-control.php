<?php
/**
 * Palette control: one radio swatch card per palette (its five library colours,
 * name, the section tones it offers), the section pattern directly under the
 * selected card, and a reset to the Maison Élise default. Customizer only.
 */

defined( 'ABSPATH' ) || exit;

/** Swatch cards + section pattern (settings: 'default' = ens_palette, 'pattern' = ens_section_pattern). */
class ENS_Palette_Control extends WP_Customize_Control {

	/** @var string */
	public $type = 'ens-palette';

	public function render_content() {
		$current = $this->value();
		$names   = [
			'dark'   => __( 'Dark', 'elite-nail-studio' ),
			'light'  => __( 'Light', 'elite-nail-studio' ),
			'accent' => __( 'Accent', 'elite-nail-studio' ),
		];
		?>
		<span class="customize-control-title" aria-hidden="true"><?php echo esc_html( $this->label ); ?></span>
		<?php if ( $this->description ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>
		<p class="ens-style-reset">
			<button type="button" class="button-link" data-ens-style-reset><?php esc_html_e( 'Restore the Maison Élise default (Atelier, Original)', 'elite-nail-studio' ); ?></button>
		</p>
		<fieldset class="ens-palette-cards">
			<legend class="screen-reader-text"><?php echo esc_html( $this->label ); ?></legend>
			<?php
			foreach ( ens_palettes() as $slug => $palette ) :
				$roles = array_intersect_key( $names, array_flip( ens_palette_roles( $slug ) ) );
				?>
				<label class="ens-palette-card" data-roles="<?php echo esc_attr( implode( ' ', array_keys( $roles ) ) ); ?>">
					<input type="radio" name="<?php echo esc_attr( '_customize-radio-' . $this->id ); ?>" value="<?php echo esc_attr( $slug ); ?>" <?php $this->link(); ?> <?php checked( $current, $slug ); ?>>
					<span class="ens-palette-card__swatches" aria-hidden="true">
						<?php foreach ( $palette['colors'] as $color ) : ?>
							<span style="background:<?php echo esc_attr( $color ); ?>"></span>
						<?php endforeach; ?>
					</span>
					<span class="ens-palette-card__name"><?php echo esc_html( $palette['name'] ); ?><?php echo $palette['default'] ? ' <em>' . esc_html__( '(default)', 'elite-nail-studio' ) . '</em>' : ''; ?></span>
					<span class="ens-palette-card__roles"><?php echo esc_html( __( 'Sections:', 'elite-nail-studio' ) . ' ' . implode( ' · ', $roles ) ); ?></span>
				</label>
				<?php
				if ( $slug === $current ) {
					$this->render_pattern();
				}
			endforeach;
			if ( ! isset( ens_palettes()[ $current ] ) ) {
				$this->render_pattern();
			}
			?>
		</fieldset>
		<?php
	}

	/** The section pattern (linked to the 'pattern' setting). */
	private function render_pattern() {
		$pattern = $this->value( 'pattern' );
		?>
		<fieldset class="ens-pattern" id="ens-pattern">
			<legend class="ens-pattern__title"><?php esc_html_e( 'Section pattern', 'elite-nail-studio' ); ?></legend>
			<p class="ens-pattern__description"><?php esc_html_e( 'Sets the colour of every page section by its position. Photographic sections (hero, call-to-action band) keep their design; give a container the class ens-tone-dark, ens-tone-light or ens-tone-accent to set it on its own.', 'elite-nail-studio' ); ?></p>
			<?php foreach ( ens_patterns() as $key => $definition ) : ?>
				<label class="ens-pattern__option">
					<input type="radio" name="_customize-radio-ens_section_pattern" value="<?php echo esc_attr( $key ); ?>" <?php $this->link( 'pattern' ); ?> <?php checked( $pattern, $key ); ?>>
					<?php echo esc_html( $definition['label'] ); ?>
				</label>
			<?php endforeach; ?>
			<p class="ens-pattern__note" aria-live="polite" hidden></p>
		</fieldset>
		<?php
	}
}

add_action( 'customize_controls_print_styles', function () {
	?>
	<style>
		.ens-style-reset{margin:4px 0 8px}
		.ens-palette-cards{display:grid;gap:8px;min-width:0;margin:8px 0 0;padding:0;border:0}
		.ens-palette-card{position:relative;display:grid;gap:6px;padding:8px;border:1px solid #c3c4c7;border-radius:6px;background:#fff;cursor:pointer}
		.ens-palette-card:has(input:checked){border-color:#2271b1;box-shadow:0 0 0 1px #2271b1}
		.ens-palette-card:has(input:focus-visible){outline:2px solid #2271b1;outline-offset:2px}
		.ens-palette-card input{position:absolute;opacity:0;pointer-events:none}
		.ens-palette-card__swatches{display:grid;grid-auto-flow:column;grid-auto-columns:1fr;height:28px;border-radius:4px;overflow:hidden;box-shadow:inset 0 0 0 1px rgba(0,0,0,.12)}
		.ens-palette-card__name{font-weight:600}
		.ens-palette-card__name em{font-weight:400;color:#646970}
		.ens-palette-card__roles{font-size:12px;color:#646970}
		.ens-pattern{min-width:0;margin:-2px 0 4px;padding:8px 10px 10px;border:1px solid #2271b1;border-radius:6px;background:#f6f7f7}
		.ens-pattern__title{float:left;width:100%;margin:0 0 2px;padding:0;font-size:14px;font-weight:600;line-height:1.5}
		.ens-pattern__description{clear:both;margin:0 0 6px;font-size:12px;font-style:italic;color:#646970}
		.ens-pattern__option{display:flex;align-items:center;gap:8px;padding:4px 0;cursor:pointer}
		.ens-pattern__option input{margin:0}
		.ens-pattern__note{margin:6px 0 0;font-size:12px;color:#646970}
	</style>
	<?php
} );

/** Keep the pattern group under the selected card, note skipped tones, wire the reset. */
add_action( 'customize_controls_print_footer_scripts', function () {
	/* translators: %s: section tone names, e.g. "Accent". */
	$text = [ 'skip' => __( 'This palette has no readable %s sections, so the pattern skips them.', 'elite-nail-studio' ) ];
	?>
	<script>
	wp.customize( 'ens_palette', 'ens_section_pattern', function ( palette, pattern ) {
		var text = <?php echo wp_json_encode( $text ); ?>;
		function sync() {
			var group = document.getElementById( 'ens-pattern' );
			var input = document.querySelector( '.ens-palette-card input[value="' + palette.get() + '"]' );
			if ( ! group || ! input ) {
				return;
			}
			var card = input.closest( '.ens-palette-card' );
			var roles = card.dataset.roles.split( ' ' );
			var pat = pattern.get();
			var missing = 'original' === pat ? [] : [ 'Accent' ].filter( function ( role ) { return pat.indexOf( role.toLowerCase() ) > -1 && roles.indexOf( role.toLowerCase() ) < 0; } );
			var note = group.querySelector( '.ens-pattern__note' );
			note.textContent = missing.length ? text.skip.replace( '%s', missing.join( ' / ' ) ) : '';
			note.hidden = ! note.textContent;
			if ( card.nextElementSibling !== group ) {
				var pane = card.closest( '.wp-full-overlay-sidebar-content' );
				var top = card.getBoundingClientRect().top;
				card.after( group );
				if ( pane ) {
					pane.scrollTop += card.getBoundingClientRect().top - top;
				}
			}
		}
		palette.bind( sync );
		pattern.bind( sync );
		wp.customize.control( 'ens_palette', function ( control ) {
			control.deferred.embedded.done( function () {
				sync();
				var reset = control.container[0].querySelector( '[data-ens-style-reset]' );
				if ( reset ) {
					reset.addEventListener( 'click', function () {
						palette.set( 'atelier' );
						pattern.set( 'original' );
					} );
				}
			} );
		} );
		wp.customize.section( 'ens_colours', function ( section ) {
			section.expanded.bind( sync );
		} );
	} );
	</script>
	<?php
} );
