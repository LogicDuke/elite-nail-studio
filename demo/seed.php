<?php
/**
 * Demo content installer: `wp ens seed --images=<dir>`
 *
 * Idempotent: pages/posts/templates are matched by slug and updated in place; images are matched
 * by filename. Builds Elementor data from the arrays below, so the demo is reproducible and the
 * resulting pages are ordinary Elementor pages a customer edits in the editor.
 */

defined( 'ABSPATH' ) || exit;

class ENS_Seed {

	private $img  = [];
	private $n    = 0;
	private $page = '';

	/**
	 * Install or refresh the demo.
	 *
	 * ## OPTIONS
	 *
	 * [--images=<dir>]
	 * : Folder with ens-<slot>.jpg files (see demo/build-images.py).
	 */
	public function seed( $args, $assoc ) {
		$this->import_images( $assoc['images'] ?? '' );
		$this->kit();
		$this->posts();

		$pages = [
			'home'     => [ 'Home', $this->home() ],
			'about'    => [ 'About', $this->about() ],
			'services' => [ 'Treatments', $this->services() ],
			'pricing'  => [ 'Pricing', $this->pricing() ],
			'lookbook' => [ 'Lookbook', $this->lookbook() ],
			'artists'  => [ 'Artists', $this->artists() ],
			'booking'  => [ 'Book', $this->booking() ],
			'faq'      => [ 'FAQ', $this->faq() ],
			'contact'  => [ 'Contact', $this->contact() ],
		];
		$ids = [];
		foreach ( $pages as $slug => [ $title, $data ] ) {
			$ids[ $slug ] = $this->save( 'page', $slug, $title, $data );
		}
		foreach ( $this->treatments() as $i => $t ) {
			$this->page = $t['slug'];
			$this->n    = 0;
			$ids[ $t['slug'] ] = $this->save( 'page', $t['slug'], $t['title'], $this->treatment_page( $t, $i ), [ 'post_parent' => $ids['services'] ] );
		}
		$ids['journal'] = $this->save( 'page', 'journal', 'Journal', null, [
			'post_excerpt' => 'Notes on colour, care and craft from the Maison Élise atelier.',
			'thumb'        => 'journal-hero',
		] );

		// Legal notice: plain block content (editable in WordPress), from demo/legal-notice.html.
		$ids['legal-notice'] = $this->save( 'page', 'legal-notice', 'Legal Notice', null, [
			'post_content' => (string) file_get_contents( __DIR__ . '/legal-notice.html' ),
		] );

		$this->save( 'elementor_library', 'ens-footer', 'Global Footer', $this->footer() );
		$this->save( 'elementor_library', 'ens-404', '404 Page', $this->not_found() );

		$this->menus( $ids );
		$this->settings( $ids );
		\Elementor\Plugin::$instance->files_manager->clear_cache();
		WP_CLI::success( 'Elite Nail Studio demo installed.' );
	}

	/* ------------------------------------------------------------------ */
	/* Infrastructure                                                       */
	/* ------------------------------------------------------------------ */

	private function import_images( $dir ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		$slots = json_decode( file_get_contents( __DIR__ . '/images.json' ), true );
		foreach ( $slots as $slot ) {
			$name     = 'ens-' . $slot['id'];
			$existing = get_posts( [ 'post_type' => 'attachment', 'name' => $name, 'posts_per_page' => 1, 'fields' => 'ids', 'post_status' => 'inherit' ] );
			if ( $existing ) {
				$this->img[ $slot['id'] ] = $existing[0];
				continue;
			}
			$file = trailingslashit( $dir ) . $name . '.jpg';
			if ( ! $dir || ! file_exists( $file ) ) {
				WP_CLI::error( "Missing image {$file}. Pass --images=<dir> (python demo/build-images.py <dir>)." );
			}
			$tmp = wp_tempnam( $name );
			copy( $file, $tmp );
			$id = media_handle_sideload( [ 'name' => $name . '.jpg', 'tmp_name' => $tmp ], 0, $slot['subject'] );
			if ( is_wp_error( $id ) ) {
				WP_CLI::error( $id->get_error_message() );
			}
			wp_update_post( [ 'ID' => $id, 'post_name' => $name ] );
			update_post_meta( $id, '_wp_attachment_image_alt', $slot['subject'] );
			update_post_meta( $id, '_ens_slot', $slot['id'] );
			$this->img[ $slot['id'] ] = $id;
		}
		WP_CLI::log( count( $this->img ) . ' image slots ready.' );
	}

	/** Elementor Kit: global colours/fonts mirror tokens.css; schemes off so the theme styles widgets. */
	private function kit() {
		$kit_id = (int) get_option( 'elementor_active_kit' );
		$colors = [
			'ens_ivory' => [ 'Ivory', '#F8F4F0' ], 'ens_porcelain' => [ 'Porcelain', '#FFFDFB' ], 'ens_linen' => [ 'Linen', '#F1E6DF' ],
			'ens_nude' => [ 'Nude', '#E9D5CC' ], 'ens_rose' => [ 'Dusty Rose', '#C99091' ], 'ens_rose_ink' => [ 'Rose Ink', '#8F585A' ],
			'ens_champagne' => [ 'Champagne', '#B99A72' ], 'ens_champagne_ink' => [ 'Champagne Ink', '#85683F' ],
			'ens_espresso' => [ 'Espresso', '#271D1B' ], 'ens_cocoa' => [ 'Cocoa', '#5E4B45' ], 'ens_taupe' => [ 'Taupe', '#6F5C56' ],
		];
		$settings = (array) get_post_meta( $kit_id, '_elementor_page_settings', true );
		$settings = array_merge( $settings, [
			'system_colors'     => [
				[ '_id' => 'primary', 'title' => 'Primary', 'color' => '#271D1B' ],
				[ '_id' => 'secondary', 'title' => 'Secondary', 'color' => '#5E4B45' ],
				[ '_id' => 'text', 'title' => 'Text', 'color' => '#5E4B45' ],
				[ '_id' => 'accent', 'title' => 'Accent', 'color' => '#C99091' ],
			],
			'custom_colors'     => array_map( fn( $id, $c ) => [ '_id' => $id, 'title' => $c[0], 'color' => $c[1] ], array_keys( $colors ), $colors ),
			'system_typography' => [
				[ '_id' => 'primary', 'title' => 'Display', 'typography_typography' => 'custom', 'typography_font_family' => 'Cormorant Garamond', 'typography_font_weight' => '300' ],
				[ '_id' => 'secondary', 'title' => 'Heading', 'typography_typography' => 'custom', 'typography_font_family' => 'Cormorant Garamond', 'typography_font_weight' => '400' ],
				[ '_id' => 'text', 'title' => 'Body', 'typography_typography' => 'custom', 'typography_font_family' => 'Jost', 'typography_font_weight' => '400' ],
				[ '_id' => 'accent', 'title' => 'Label', 'typography_typography' => 'custom', 'typography_font_family' => 'Jost', 'typography_font_weight' => '500' ],
			],
			'container_width'   => [ 'unit' => 'px', 'size' => 1320 ],
			'site_name'         => 'Maison Élise',
			'site_description'  => 'Luxury Nail Atelier',
		] );
		update_post_meta( $kit_id, '_elementor_page_settings', $settings );
		foreach ( [
			'elementor_disable_color_schemes'      => 'yes',
			'elementor_disable_typography_schemes' => 'yes',
			'elementor_google_font'                => '0',
			'elementor_font_display'               => 'swap',
			'elementor_load_fa4_shim'              => '',
			'elementor_cpt_support'                => [ 'page', 'post' ],
		] as $k => $v ) {
			update_option( $k, $v );
		}
	}

	private function id() {
		return substr( md5( $this->page . '-' . ( ++$this->n ) ), 0, 7 );
	}

	/** Section (top-level container). */
	private function sec( array $children, $classes = '', array $s = [] ) {
		return [
			'id'       => $this->id(),
			'elType'   => 'container',
			'isInner'  => false,
			'settings' => $s + [ 'content_width' => 'boxed', 'flex_direction' => 'column', 'css_classes' => $classes ],
			'elements' => $children,
		];
	}

	/** Grid section: $cols like '5fr 7fr'; collapses to 1 column on tablet unless $tablet given. */
	private function grid( array $children, $cols, $classes = '', $tablet = '1fr', array $s = [] ) {
		$inner = ! empty( $s['isInner'] );
		unset( $s['isInner'] );
		$el = $this->sec( $children, $classes, $s + [
			'container_type'          => 'grid',
			'grid_columns_grid'       => [ 'unit' => 'custom', 'size' => $cols ],
			'grid_columns_grid_tablet' => [ 'unit' => 'custom', 'size' => $tablet ],
			'grid_columns_grid_mobile' => [ 'unit' => 'custom', 'size' => '1fr' ],
			'grid_rows_grid'          => [ 'unit' => 'custom', 'size' => 'auto' ],
			'grid_rows_grid_tablet'   => [ 'unit' => 'custom', 'size' => 'auto' ],
			'grid_rows_grid_mobile'   => [ 'unit' => 'custom', 'size' => 'auto' ],
			'grid_gaps'               => [ 'column' => '96', 'row' => '56', 'unit' => 'px', 'isLinked' => false ],
			'grid_gaps_tablet'        => [ 'column' => '48', 'row' => '48', 'unit' => 'px', 'isLinked' => true ],
			'grid_align_items'        => 'center',
		] );
		if ( $inner ) {
			$el['isInner']                   = true;
			$el['settings']['content_width'] = 'full';
		}
		return $el;
	}

	/** Inner column container. */
	private function col( array $children, $classes = '', array $s = [] ) {
		return [
			'id'       => $this->id(),
			'elType'   => 'container',
			'isInner'  => true,
			'settings' => $s + [ 'content_width' => 'full', 'flex_direction' => 'column', 'css_classes' => $classes ],
			'elements' => $children,
		];
	}

	private function w( $type, array $s, $classes = '' ) {
		if ( $classes ) {
			$s['_css_classes'] = $classes;
		}
		return [ 'id' => $this->id(), 'elType' => 'widget', 'widgetType' => $type, 'settings' => $s, 'elements' => [] ];
	}

	private function media( $slot ) {
		$id = $this->img[ $slot ];
		return [ 'id' => $id, 'url' => wp_get_attachment_url( $id ), 'source' => 'library' ];
	}

	private static function link( $url ) {
		return [ 'url' => $url, 'is_external' => '', 'nofollow' => '' ];
	}

	/* Native-widget shorthands */
	private function eyebrow( $text, $align = '' ) {
		return $this->w( 'heading', [ 'title' => $text, 'header_size' => 'p', 'align' => $align ], 'ens-eyebrow ens-reveal' );
	}

	private function h( $text, $tag = 'h2', $align = '', $classes = 'ens-split' ) {
		return $this->w( 'heading', [ 'title' => $text, 'header_size' => $tag, 'align' => $align ], $classes );
	}

	private function text( $html, $classes = 'ens-reveal', $align = '' ) {
		return $this->w( 'text-editor', [ 'editor' => $html, 'align' => $align ], $classes );
	}

	private function button( $label, $url, $classes = 'ens-reveal', $align = '' ) {
		return $this->w( 'button', [ 'text' => $label, 'link' => self::link( $url ), 'align' => $align ], $classes );
	}

	/** Centred section intro (eyebrow, title, lead). */
	private function intro( $eyebrow, $title, $lead = '' ) {
		$out = [ $this->eyebrow( $eyebrow, 'center' ), $this->h( $title, 'h2', 'center' ) ];
		if ( $lead ) {
			$out[] = $this->text( "<p>{$lead}</p>", 'ens-lead ens-reveal', 'center' );
		}
		return $this->col( $out, 'ens-center ens-intro-block', [ 'flex_gap' => [ 'column' => '0', 'row' => '20', 'unit' => 'px', 'isLinked' => false ], 'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '64', 'left' => '0', 'isLinked' => false ] ] );
	}

	private function save( $type, $slug, $title, $data, array $extra = [] ) {
		$found = get_posts( [ 'post_type' => $type, 'name' => $slug, 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ] );
		$post  = [
			'ID'           => $found[0] ?? 0,
			'post_type'    => $type,
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_status'  => 'publish',
			'post_parent'  => $extra['post_parent'] ?? 0,
			'post_excerpt' => $extra['post_excerpt'] ?? '',
			'post_content' => $extra['post_content'] ?? '',
		];
		$id = wp_insert_post( wp_slash( $post ), true );
		if ( is_wp_error( $id ) ) {
			WP_CLI::error( $id->get_error_message() );
		}
		if ( ! empty( $extra['thumb'] ) ) {
			set_post_thumbnail( $id, $this->img[ $extra['thumb'] ] );
		}
		if ( null !== $data ) {
			update_post_meta( $id, '_elementor_edit_mode', 'builder' );
			update_post_meta( $id, '_elementor_version', ELEMENTOR_VERSION );
			update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
			if ( 'page' === $type ) {
				update_post_meta( $id, '_wp_page_template', 'elementor_header_footer' );
			} else {
				update_post_meta( $id, '_elementor_template_type', 'page' );
				wp_set_object_terms( $id, 'page', 'elementor_library_type' );
			}
		}
		WP_CLI::log( "  {$type}: {$slug} (#{$id})" );
		return $id;
	}

	/* ------------------------------------------------------------------ */
	/* Shared content                                                       */
	/* ------------------------------------------------------------------ */

	private function treatments() {
		return [
			[
				'slug' => 'signature-manicure', 'title' => 'Signature Manicure', 'img' => 'service-manicure',
				'short' => 'Shape, cuticle care and a flawless lacquer finish, closed with a warm-oil hand massage.',
				'price' => 'from $55', 'duration' => '50 min', 'lasts' => '7–10 days',
				'lead' => 'Our foundation treatment and the clearest expression of how we work: meticulous preparation, a perfectly balanced shape and a finish that looks freshly lacquered for days.',
				'steps' => [
					[ 'Consultation', 'Shape, length and shade chosen together, with an honest read of your nail health.' ],
					[ 'Dry preparation', 'Glass-file shaping and gentle cuticle work — no soaking, no over-cutting.' ],
					[ 'Lacquer, three coats', 'Base, two thin colour coats and a high-shine top coat, each applied to the free edge.' ],
					[ 'Hand massage', 'Warm jojoba oil and a slow pressure-point massage while your polish sets.' ],
				],
				'menu' => [ [ 'Signature Manicure', 'Classic lacquer finish', '50 min', '$55' ], [ 'Express Manicure', 'Shape, tidy and polish', '30 min', '$38' ], [ 'French Manicure', 'Hand-painted smile line', '60 min', '$65' ] ],
				'faq'  => [ [ 'How long does lacquer last?', 'Typically seven to ten days. Wearing gloves for housework and applying cuticle oil nightly makes a noticeable difference.' ], [ 'Can I bring my own polish?', 'Of course — though most guests find something they love in our library of 140 shades.' ], [ 'Is this suitable for bitten nails?', 'Yes. We will shape conservatively and suggest a short, soft square that grows out gracefully.' ] ],
			],
			[
				'slug' => 'gel-couture', 'title' => 'Gel Couture', 'img' => 'service-gel',
				'short' => 'Chip-resistant gel from our curated colour library, cured to a mirror gloss that lasts three weeks.',
				'price' => 'from $75', 'duration' => '70 min', 'lasts' => '3 weeks',
				'lead' => 'Long-wear colour with the depth of a glass finish. We use a breathable, HEMA-free gel system and remove it the same careful way we apply it.',
				'steps' => [
					[ 'Safe removal', 'Previous gel is lifted with a gentle soak-and-push, never scraped.' ],
					[ 'Structure', 'A thin builder layer balances the apex so colour sits evenly and resists lifting.' ],
					[ 'Colour', 'Two pigment coats cured under low-heat LED for comfort.' ],
					[ 'Mirror gloss', 'A non-wipe top coat and cuticle oil to finish.' ],
				],
				'menu' => [ [ 'Gel Couture', 'Colour of your choice', '70 min', '$75' ], [ 'Gel Couture + French', 'Fine smile line', '80 min', '$85' ], [ 'Gel Removal', 'Gentle, nail-safe', '20 min', '$20' ] ],
				'faq'  => [ [ 'Will gel damage my nails?', 'Not when it is applied and removed correctly. Damage almost always comes from peeling or aggressive removal — which we never do.' ], [ 'Is your gel HEMA-free?', 'Yes. Our system is HEMA-free, which significantly reduces the risk of sensitivity.' ], [ 'How often should I take a break?', 'With proper removal there is no need for breaks, but we are happy to alternate with a Hand Ritual.' ] ],
			],
			[
				'slug' => 'nail-art-atelier', 'title' => 'Nail Art Atelier', 'img' => 'service-art',
				'short' => 'Hand-painted detail, chrome and fine lines — from a single accent to a full editorial set.',
				'price' => 'from $25', 'duration' => '+30 min', 'lasts' => 'with your gel',
				'lead' => 'Our artists trained in illustration and it shows. Bring a reference, a mood or nothing at all — every set is painted freehand, never stamped.',
				'steps' => [
					[ 'Brief', 'Share a reference or a feeling; we sketch options on a swatch tip.' ],
					[ 'Base', 'Applied over Gel Couture or Sculpted Extensions for longevity.' ],
					[ 'Freehand detail', 'Fine liners, micro-dots, chrome dust or foil, layered by hand.' ],
					[ 'Seal', 'Encapsulated under top coat so the art wears as long as the colour.' ],
				],
				'menu' => [ [ 'Accent Art', 'One to two nails', '15 min', '$25' ], [ 'Full Set Art', 'All ten nails', '45 min', '$60' ], [ 'Chrome & Glazed', 'Powder finish', '15 min', '$20' ] ],
				'faq'  => [ [ 'Can you copy a design from Instagram?', 'We take inspiration gladly and always reinterpret, so your set is yours — and the original artist is respected.' ], [ 'How long does detailed art take?', 'Allow an extra 30 to 60 minutes depending on complexity; we will estimate at consultation.' ], [ 'Can art be done on natural nails?', 'Yes, over a gel base for durability.' ] ],
			],
			[
				'slug' => 'sculpted-extensions', 'title' => 'Sculpted Extensions', 'img' => 'service-extensions',
				'short' => 'Builder-gel extensions sculpted to your ideal length and shape — light as a natural nail.',
				'price' => 'from $110', 'duration' => '120 min', 'lasts' => '3–4 weeks',
				'lead' => 'Length without heaviness. Each extension is sculpted on a form and filed to an apex that suits your hand, so the result looks and feels like the nails you were born with — only longer.',
				'steps' => [
					[ 'Shape design', 'Almond, oval, squoval or ballerina — chosen for the proportions of your fingers.' ],
					[ 'Sculpting', 'Builder gel is sculpted on forms, never glued tips.' ],
					[ 'Refinement', 'Filed thin at the free edge with a strong, balanced apex.' ],
					[ 'Finish', 'Colour, milky sheer or art of your choice.' ],
				],
				'menu' => [ [ 'Full Set', 'Sculpted, any length', '120 min', '$110' ], [ 'Infill', 'Three to four weeks', '90 min', '$85' ], [ 'Single Repair', 'Per nail', '10 min', '$8' ] ],
				'faq'  => [ [ 'Are extensions bad for my natural nails?', 'Sculpted builder gel is the gentlest extension method we know; with regular infills your nails grow underneath unharmed.' ], [ 'How long can I go?', 'As long as your nail bed can support comfortably — we will advise honestly.' ], [ 'Can I remove them myself?', 'Please don’t. A professional removal takes twenty minutes and protects your nails.' ] ],
			],
			[
				'slug' => 'spa-pedicure', 'title' => 'Spa Pedicure', 'img' => 'service-pedicure',
				'short' => 'A mineral soak, exfoliation, callus care and polish, finished with a long massage.',
				'price' => 'from $80', 'duration' => '75 min', 'lasts' => '3–4 weeks',
				'lead' => 'An unhurried pedicure in a reclining chair, with a warm mineral soak, thorough but gentle callus care and a long lower-leg massage.',
				'steps' => [
					[ 'Mineral soak', 'Warm water, magnesium salts and rose petals.' ],
					[ 'Exfoliation', 'A sugar-and-oil scrub and gentle callus refinement.' ],
					[ 'Nail care', 'Shaping, cuticle care and lacquer or gel.' ],
					[ 'Massage', 'Fifteen minutes of lower-leg and foot massage with warm balm.' ],
				],
				'menu' => [ [ 'Spa Pedicure', 'With lacquer', '75 min', '$80' ], [ 'Spa Pedicure Gel', 'With gel colour', '85 min', '$95' ], [ 'Express Pedicure', 'Shape and polish', '40 min', '$50' ] ],
				'faq'  => [ [ 'Do you use razors on calluses?', 'Never. We refine with files and enzyme softeners only — safer and longer-lasting.' ], [ 'Are the foot basins shared?', 'Each basin is lined with a single-use liner and disinfected between every guest.' ], [ 'Can I have a pedicure while pregnant?', 'Yes — we adapt the massage and avoid certain pressure points. Please let us know when booking.' ] ],
			],
			[
				'slug' => 'hand-ritual', 'title' => 'Hand Ritual', 'img' => 'service-ritual',
				'short' => 'Paraffin, enzyme exfoliation and lymphatic massage — the most restorative hour for your hands.',
				'price' => 'from $65', 'duration' => '45 min', 'lasts' => 'a feeling',
				'lead' => 'Pure care, no polish required. A treatment for hands that work hard: enzyme exfoliation, a warm paraffin wrap and a slow lymphatic massage.',
				'steps' => [
					[ 'Cleanse', 'A warm towel compress and gentle enzyme exfoliation.' ],
					[ 'Mask', 'A rose-clay mask to soften and brighten.' ],
					[ 'Paraffin wrap', 'Warm paraffin and linen to deeply hydrate.' ],
					[ 'Massage', 'Lymphatic drainage technique for hands and forearms.' ],
				],
				'menu' => [ [ 'Hand Ritual', 'Full treatment', '45 min', '$65' ], [ 'Ritual + Manicure', 'Combined', '90 min', '$105' ], [ 'Paraffin Add-on', 'With any service', '15 min', '$18' ] ],
				'faq'  => [ [ 'Is the Hand Ritual good for dry skin?', 'It is designed for it — the paraffin and clay combination is especially helpful in winter.' ], [ 'Can I add it to a manicure?', 'Yes, as the combined Ritual + Manicure.' ], [ 'Does it include polish?', 'No, but you can add a lacquer finish for $15.' ] ],
			],
		];
	}

	private function services_grid() {
		return $this->w( 'ens-services', [
			'columns'    => '3',
			'link_label' => 'Discover',
			'items'      => array_map( fn( $t ) => [
				'_id'      => substr( md5( $t['slug'] ), 0, 7 ),
				'image'    => $this->media( $t['img'] ),
				'title'    => $t['title'],
				'text'     => $t['short'],
				'price'    => $t['price'],
				'duration' => $t['duration'],
				'link'     => self::link( '/services/' . $t['slug'] . '/' ),
			], $this->treatments() ),
		] );
	}

	private function marquee( $classes = 'ens-flush' ) {
		return $this->sec( [ $this->w( 'ens-marquee', [ 'words' => "Manicure\nGel couture\nNail art\nSculpted extensions\nSpa pedicure\nHand rituals", 'style' => 'outline', 'speed' => 46 ] ) ], $classes, [ 'content_width' => 'full' ] );
	}

	private function quotes( $classes = 'ens-sec-linen' ) {
		return $this->sec( [ $this->w( 'ens-testimonials', [
			'eyebrow' => 'Kind words',
			'items'   => [
				[ '_id' => 'q1', 'quote' => 'The calmest hour of my month. My gel lasted a full three weeks without a single chip — and my nails have never been healthier.', 'name' => 'Amara O.', 'detail' => 'Gel Couture, every three weeks' ],
				[ '_id' => 'q2', 'quote' => 'Inès painted the most delicate line art for my wedding. Guests kept asking which atelier did them.', 'name' => 'Charlotte D.', 'detail' => 'Bridal set' ],
				[ '_id' => 'q3', 'quote' => 'Finally a studio that refuses to rush. Spotless, quiet and genuinely skilled.', 'name' => 'Priya S.', 'detail' => 'Signature Manicure' ],
				[ '_id' => 'q4', 'quote' => 'The Hand Ritual is a revelation in winter. I book it for my mother every December now.', 'name' => 'Laura B.', 'detail' => 'Hand Ritual' ],
			],
		] ) ], $classes );
	}

	private function cta( $title = 'Reserve your <em>chair</em>', $eyebrow = 'Your hour of calm', $text = 'Appointments open four weeks ahead. Same-week openings are released every Monday morning.' ) {
		return $this->sec( [ $this->w( 'ens-cta', [
			'image'   => $this->media( 'cta-band' ),
			'focus'   => '0% 50%',
			'eyebrow' => $eyebrow,
			'title'   => $title,
			'text'    => $text,
			'button'  => 'Book appointment',
			'link'    => self::link( '/booking/' ),
			'badge'   => 'Book an appointment · Maison Élise · ',
		] ) ], 'ens-flush', [ 'content_width' => 'full' ] );
	}

	private function page_hero( $slot, $eyebrow, $title, $text = '', $focus = '', $classes = '' ) {
		return $this->sec( [ $this->w( 'ens-hero', [
			'layout'  => 'page',
			'focus'   => $focus,
			'focus_tablet' => $focus ? '0% 50%' : '',
			'image'   => $this->media( $slot ),
			'eyebrow' => $eyebrow,
			'title'   => $title,
			'text'    => $text,
			'btn1'    => '',
			'badge'   => '',
		] ) ], trim( 'ens-flush ' . $classes ), [ 'content_width' => 'full' ] );
	}

	private function faq_items( array $rows ) {
		return array_map( fn( $r, $i ) => [ '_id' => 'f' . $i, 'q' => $r[0], 'a' => '<p>' . $r[1] . '</p>' ], $rows, array_keys( $rows ) );
	}

	private function price_items( array $rows ) {
		return array_map( fn( $r, $i ) => [ '_id' => 'p' . $i, 'name' => $r[0], 'text' => $r[1], 'duration' => $r[2], 'price' => $r[3], 'badge' => $r[4] ?? '' ], $rows, array_keys( $rows ) );
	}

	/* ------------------------------------------------------------------ */
	/* Pages                                                                */
	/* ------------------------------------------------------------------ */

	private function home() {
		$this->page = 'home';
		$this->n    = 0;
		return [
			$this->sec( [ $this->w( 'ens-hero', [
				'layout'     => 'home',
				'image'      => $this->media( 'home-hero' ),
				'inset'      => $this->media( 'home-hero-detail' ),
				'focus'      => '62% 50%',
				'focus_tablet' => '45% 50%',
				'eyebrow'    => 'Maison Élise — Luxury Nail Atelier',
				'title'      => "The art\nof <em>perfect</em>\nnails",
				'text'       => 'An appointment-only atelier for manicure, gel couture and nail art — unhurried, meticulous and entirely yours.',
				'btn1'       => 'Book appointment',
				'btn1_link'  => self::link( '/booking/' ),
				'btn2'       => 'Explore treatments',
				'btn2_link'  => self::link( '/services/' ),
				'badge'      => 'Book an appointment · Maison Élise · ',
				'badge_link' => self::link( '/booking/' ),
			] ) ], 'ens-flush', [ 'content_width' => 'full' ] ),

			// Welcome + quick booking card overlapping the hero edge.
			$this->grid( [
				$this->col( [
					$this->eyebrow( 'Welcome to the atelier' ),
					$this->h( 'Where precision meets <em>quiet luxury</em>' ),
					$this->text( '<p>Maison Élise is a small, appointment-only nail atelier. One artist per guest, never double-booked, with hospital-grade hygiene and a colour library curated for real life — from milky sheers to deep espresso gloss.</p>', 'ens-lead ens-reveal' ),
					$this->grid( [
						$this->w( 'counter', [ 'starting_number' => 0, 'ending_number' => 12, 'title' => 'Years of craft', 'duration' => 1800 ] ),
						$this->w( 'counter', [ 'starting_number' => 0, 'ending_number' => 9400, 'suffix' => '+', 'thousand_separator' => 'yes', 'title' => 'Appointments', 'duration' => 1800 ] ),
					], '1fr 1fr', 'ens-reveal', '1fr 1fr', [ 'isInner' => true, 'grid_columns_grid_mobile' => [ 'unit' => 'custom', 'size' => '1fr 1fr' ] ] ),
				], '', [ 'flex_gap' => [ 'column' => '0', 'row' => '24', 'unit' => 'px', 'isLinked' => false ] ] ),
				$this->col( [ $this->w( 'ens-form', [
					'type'    => 'quick',
					'heading' => 'Reserve in a minute',
					'intro'   => 'Tell us what you would like and when — we confirm by phone within the day.',
					'submit'  => 'Request my time',
					'success' => 'Thank you — we will call to confirm your appointment today.',
					'tone'    => 'light',
				] ) ], 'ens-overlap-up ens-first-tablet' ),
			], '7fr 5fr', 'ens-welcome' ),

			$this->marquee(),

			$this->sec( [
				$this->intro( 'Treatments', 'Six rituals, <em>one</em> standard', 'Every treatment begins with a consultation and ends with cuticle oil and a moment to admire your hands.' ),
				$this->services_grid(),
				$this->button( 'Full menu & prices', '/pricing/', 'ens-reveal ens-btn-outline', 'center' ),
			], '', [ 'flex_gap' => [ 'column' => '0', 'row' => '56', 'unit' => 'px', 'isLinked' => false ] ] ),

			$this->sec( [ $this->w( 'ens-feature', [
				'image'      => $this->media( 'home-intro' ),
				'image2'     => $this->media( 'home-intro-detail' ),
				'side'       => 'left',
				'arch'       => 'yes',
				'block'      => 'yes',
				'stat'       => '1:1',
				'stat_label' => 'One artist, one guest',
				'eyebrow'    => 'Why Maison Élise',
				'title'      => 'Quiet hands, <em>precise</em> work',
				'text'       => 'We built the atelier we wanted to visit: calm, spotless and honest about what healthy nails need.',
				'points'     => [
					[ '_id' => 'pt1', 'title' => 'Hospital-grade hygiene', 'text' => 'Metal tools are autoclave-sterilised; files and buffers are single-use, always.' ],
					[ '_id' => 'pt2', 'title' => 'Healthy-nail first', 'text' => 'Gentle e-file technique, no over-buffing, and HEMA-free gel as standard.' ],
					[ '_id' => 'pt3', 'title' => 'Time, not turnover', 'text' => 'Appointments run at your pace — we schedule generous buffers between guests.' ],
				],
				'button'     => 'Our story',
				'link'       => self::link( '/about/' ),
			] ) ], 'ens-sec-porcelain' ),

			$this->sec( [ $this->w( 'ens-story', [
				'eyebrow' => 'The ritual',
				'title'   => 'Three movements, <em>one</em> calm hour',
				'text'    => '',
				'steps'   => [
					[ '_id' => 's1', 'image' => $this->media( 'story-1' ), 'label' => 'Consultation', 'title' => 'We begin by listening', 'text' => 'Shape, length, lifestyle and shade — chosen together at the swatch table, with an honest read of your nail health.' ],
					[ '_id' => 's2', 'image' => $this->media( 'story-2' ), 'label' => 'Preparation', 'title' => 'The invisible work', 'text' => 'Dry, gentle cuticle care and a balanced shape. This is where a manicure is won — and where we take our time.' ],
					[ '_id' => 's3', 'image' => $this->media( 'story-3' ), 'label' => 'Finish', 'title' => 'A glass-like finish', 'text' => 'Thin, even coats sealed to the free edge, warm oil and a moment to admire the result over a fresh espresso.' ],
				],
			] ) ] ),

			$this->sec( [ $this->w( 'ens-lookbook', [
				'eyebrow'  => 'Lookbook',
				'title'    => 'The season, <em>in detail</em>',
				'text'     => 'Recent sets from the atelier — milky sheers, chrome glaze and hand-painted line work.',
				'items'    => [
					[ '_id' => 'l1', 'image' => $this->media( 'lookbook-1' ), 'caption' => 'Milk & honey French', 'tag' => 'Minimal' ],
					[ '_id' => 'l2', 'image' => $this->media( 'lookbook-2' ), 'caption' => 'Glazed pearl chrome', 'tag' => 'Couture' ],
					[ '_id' => 'l3', 'image' => $this->media( 'lookbook-3' ), 'caption' => 'Peony ombré', 'tag' => 'Couture' ],
					[ '_id' => 'l4', 'image' => $this->media( 'lookbook-4' ), 'caption' => 'Espresso & gold', 'tag' => 'Art' ],
					[ '_id' => 'l5', 'image' => $this->media( 'lookbook-5' ), 'caption' => 'Negative-space line', 'tag' => 'Art' ],
					[ '_id' => 'l6', 'image' => $this->media( 'lookbook-6' ), 'caption' => 'Pearl bridal', 'tag' => 'Bridal' ],
				],
				'cta'      => 'View the full lookbook',
				'cta_link' => self::link( '/lookbook/' ),
			] ) ], 'ens-flush ens-sec-linen', [ 'content_width' => 'full' ] ),

			$this->grid( [
				$this->col( [
					$this->eyebrow( 'The menu' ),
					$this->h( 'Honest prices, <em>no</em> surprises' ),
					$this->text( '<p>Every price includes consultation, removal of your previous lacquer and cuticle oil to take home. Gel removal is complimentary when you rebook.</p>', 'ens-lead ens-reveal' ),
					$this->button( 'See the full menu', '/pricing/', 'ens-reveal ens-btn-outline' ),
				], 'ens-sticky-col', [ 'flex_gap' => [ 'column' => '0', 'row' => '24', 'unit' => 'px', 'isLinked' => false ] ] ),
				$this->col( [
					$this->w( 'ens-price-list', [ 'title' => 'Hands', 'items' => $this->price_items( [ [ 'Signature Manicure', 'Lacquer finish and hand massage', '50 min', '$55', 'Signature' ], [ 'Gel Couture', 'Three-week gloss', '70 min', '$75' ], [ 'Sculpted Extensions', 'Builder gel, any length', '120 min', '$110' ] ] ) ] ),
					$this->w( 'ens-price-list', [ 'title' => 'Feet & rituals', 'items' => $this->price_items( [ [ 'Spa Pedicure', 'Mineral soak and massage', '75 min', '$80' ], [ 'Hand Ritual', 'Paraffin and lymphatic massage', '45 min', '$65' ] ] ) ] ),
				], '', [ 'flex_gap' => [ 'column' => '0', 'row' => '64', 'unit' => 'px', 'isLinked' => false ] ] ),
			], '5fr 7fr', '', '1fr', [ 'grid_align_items' => 'start' ] ),

			$this->quotes(),
			$this->cta(),

			$this->sec( [
				$this->intro( 'The Journal', 'Notes on colour, <em>care</em> & craft' ),
				$this->w( 'ens-posts', [ 'count' => 3 ] ),
			] ),
		];
	}

	private function about() {
		$this->page = 'about';
		$this->n    = 0;
		return [
			$this->page_hero( 'about-hero', 'Our story', 'An atelier built on <em>patience</em>', 'Founded in 2014 by Élise Marchand with one chair, one lamp and a stubborn belief that a manicure should never be rushed.', '12% 50%' ),
			$this->sec( [ $this->w( 'ens-feature', [
				'image'   => $this->media( 'about-studio' ),
				'image2'  => $this->media( 'about-detail' ),
				'side'    => 'left',
				'arch'    => 'yes',
				'block'   => 'yes',
				'eyebrow' => 'Since 2014',
				'title'   => 'Small <em>by design</em>',
				'text'    => '',
				'body'    => '<p>We have never wanted to be the biggest salon in town. Six artists, eight chairs and a waiting list we are quietly proud of — because the only way to do this work well is to give it time.</p><p>Every artist trains with us for three months before taking a guest, and every product on our shelves has earned its place by being kinder to nails than the alternative.</p>',
				'button'  => 'Meet the artists',
				'link'    => self::link( '/artists/' ),
			] ) ] ),
			$this->sec( [
				$this->intro( 'What we stand for', 'Craft, care & <em>calm</em>' ),
				$this->grid( array_map( fn( $v ) => $this->col( [
					$this->w( 'heading', [ 'title' => $v[0], 'header_size' => 'p' ], 'ens-eyebrow' ),
					$this->w( 'heading', [ 'title' => $v[1], 'header_size' => 'h3' ] ),
					$this->text( '<p>' . $v[2] . '</p>', '' ),
				], '', [ 'flex_gap' => [ 'column' => '0', 'row' => '16', 'unit' => 'px', 'isLinked' => false ] ] ), [
					[ 'I.', 'Craft', 'Freehand art, sculpted structure and clean preparation are skills we practise every single week.' ],
					[ 'II.', 'Care', 'We protect your natural nail first. If a treatment is not right for you today, we will say so.' ],
					[ 'III.', 'Calm', 'Soft light, no background chatter and an espresso when you arrive. The hour is yours.' ],
				] ), '1fr 1fr 1fr', 'ens-stagger', '1fr', [ 'isInner' => true, 'grid_align_items' => 'start', 'grid_gaps' => [ 'column' => '56', 'row' => '48', 'unit' => 'px', 'isLinked' => false ] ] ),
			], 'ens-sec-dark' ),
			$this->grid( [
				$this->w( 'counter', [ 'starting_number' => 0, 'ending_number' => 12, 'title' => 'Years of craft' ] ),
				$this->w( 'counter', [ 'starting_number' => 0, 'ending_number' => 9400, 'suffix' => '+', 'thousand_separator' => 'yes', 'title' => 'Appointments' ] ),
				$this->w( 'counter', [ 'starting_number' => 0, 'ending_number' => 6, 'title' => 'Resident artists' ] ),
				$this->w( 'counter', [ 'starting_number' => 0, 'ending_number' => 140, 'title' => 'Shades in our library' ] ),
			], '1fr 1fr 1fr 1fr', 'ens-tight ens-stagger', '1fr 1fr', [ 'grid_columns_grid_mobile' => [ 'unit' => 'custom', 'size' => '1fr 1fr' ] ] ),
			$this->sec( [ $this->w( 'ens-feature', [
				'image'   => $this->media( 'founder' ),
				'side'    => 'right',
				'block'   => 'yes',
				'eyebrow' => 'A note from the founder',
				'title'   => '“Your hands tell the story of <em>your days</em>.”',
				'body'    => '<p>I opened Maison Élise because I was tired of leaving salons feeling hurried. Nails are small, but the care we give them says something about how we treat ourselves.</p><p>Here, we slow down. We listen. And we do the invisible work properly — so you leave with hands that feel as good as they look.</p><p><em>— Élise Marchand, founder</em></p>',
			] ) ], 'ens-sec-linen' ),
			$this->quotes( '' ),
			$this->cta(),
		];
	}

	private function services() {
		$this->page = 'services';
		$this->n    = 0;
		return [
			$this->page_hero( 'services-hero', 'Treatments', 'Rituals for <em>hands</em> & feet', 'Six treatments, each refined over a decade. Every one begins with a consultation and ends with a moment to admire your hands.' ),
			$this->grid( [
				$this->col( [ $this->eyebrow( 'Our approach' ), $this->h( 'Fewer treatments, <em>done beautifully</em>' ) ], '', [ 'flex_gap' => [ 'column' => '0', 'row' => '8', 'unit' => 'px', 'isLinked' => false ] ] ),
				$this->col( [ $this->text( '<p>Rather than a menu of forty options, we offer six treatments we know inside out — then tailor each to you. Not sure where to start? Book a Signature Manicure; your artist will guide you from there.</p>', 'ens-lead ens-reveal' ) ] ),
			], '6fr 6fr', 'ens-tight', '1fr', [ 'grid_align_items' => 'end' ] ),
			$this->sec( [ $this->services_grid() ], 'ens-flush-top' ),
			$this->marquee(),
			$this->sec( [ $this->w( 'ens-feature', [
				'image'      => $this->media( 'home-intro' ),
				'image2'     => $this->media( 'home-intro-detail' ),
				'side'       => 'right',
				'block'      => 'yes',
				'stat'       => '0',
				'stat_label' => 'Reused files, ever',
				'eyebrow'    => 'Our standards',
				'title'      => 'What every visit <em>includes</em>',
				'points'     => [
					[ '_id' => 'a', 'title' => 'A proper consultation', 'text' => 'Five unhurried minutes to agree shape, shade and what your nails need.' ],
					[ '_id' => 'b', 'title' => 'Sterile, single-use tools', 'text' => 'Autoclaved metal instruments and fresh files for every guest.' ],
					[ '_id' => 'c', 'title' => 'Aftercare to take home', 'text' => 'A travel cuticle oil and written care notes for your treatment.' ],
					[ '_id' => 'd', 'title' => 'A seven-day promise', 'text' => 'If anything chips or lifts within a week, we fix it at no charge.' ],
				],
			] ) ] ),
			$this->grid( [
				$this->col( [ $this->eyebrow( 'Good to know' ), $this->h( 'Treatment <em>questions</em>' ), $this->button( 'All questions', '/faq/', 'ens-reveal ens-btn-outline' ) ], 'ens-sticky-col', [ 'flex_gap' => [ 'column' => '0', 'row' => '24', 'unit' => 'px', 'isLinked' => false ] ] ),
				$this->w( 'ens-faq', [ 'items' => $this->faq_items( [
					[ 'Which treatment should I choose?', 'If you want natural nails to look their best for a week, choose the Signature Manicure. For three weeks of colour, Gel Couture. For length, Sculpted Extensions.' ],
					[ 'Do you work on natural nails only?', 'We work on natural nails and our own sculpted extensions. We do not infill other salons’ acrylics, but will remove them gently.' ],
					[ 'What products do you use?', 'HEMA-free gels, ten-free lacquers and cold-pressed oils. Full ingredient lists are available at reception.' ],
				] ), 'first_open' => 'yes' ] ),
			], '5fr 7fr', 'ens-sec-linen', '1fr', [ 'grid_align_items' => 'start' ] ),
			$this->cta(),
		];
	}

	private function treatment_page( array $t, $i ) {
		return [
			$this->sec( [ $this->w( 'ens-hero', [
				'layout'     => 'split',
				'image'      => $this->media( $t['img'] ),
				'eyebrow'    => sprintf( 'Treatment %02d', $i + 1 ),
				'title'      => $t['title'],
				'text'       => $t['lead'],
				'meta'       => "Duration | {$t['duration']}\nPrice | {$t['price']}\nLasts | {$t['lasts']}",
				'btn1'       => 'Book this treatment',
				'btn1_link'  => self::link( '/booking/' ),
				'btn2'       => 'All treatments',
				'btn2_link'  => self::link( '/services/' ),
				'badge'      => '',
			] ) ], 'ens-flush', [ 'content_width' => 'full' ] ),
			$this->grid( [
				$this->col( [ $this->eyebrow( 'How it unfolds' ), $this->h( 'Step by <em>step</em>' ), $this->text( '<p>' . esc_html( $t['short'] ) . '</p>', 'ens-lead ens-reveal' ) ], 'ens-sticky-col', [ 'flex_gap' => [ 'column' => '0', 'row' => '20', 'unit' => 'px', 'isLinked' => false ] ] ),
				$this->w( 'ens-feature', [
					'image'   => [ 'url' => '' ],
					'points'  => array_map( fn( $s, $k ) => [ '_id' => 'st' . $k, 'title' => $s[0], 'text' => $s[1] ], $t['steps'], array_keys( $t['steps'] ) ),
					'eyebrow' => '',
					'title'   => '',
					'text'    => '',
				] ),
			], '5fr 7fr', 'ens-sec-porcelain', '1fr', [ 'grid_align_items' => 'start' ] ),
			$this->grid( [
				$this->w( 'ens-price-list', [ 'title' => 'Options', 'note' => 'Prices include consultation, previous-lacquer removal and take-home cuticle oil.', 'items' => $this->price_items( $t['menu'] ) ] ),
				$this->w( 'ens-faq', [ 'group' => 'Questions', 'items' => $this->faq_items( $t['faq'] ) ] ),
			], '1fr 1fr', '', '1fr', [ 'grid_align_items' => 'start' ] ),
			$this->sec( [
				$this->intro( 'Pairs well with', 'Complete the <em>ritual</em>' ),
				$this->services_grid(),
			], 'ens-sec-linen' ),
			$this->cta( 'Ready when <em>you</em> are' ),
		];
	}

	private function pricing() {
		$this->page = 'pricing';
		$this->n    = 0;
		$list       = fn( $title, $note, $rows ) => $this->w( 'ens-price-list', [ 'title' => $title, 'note' => $note, 'items' => $this->price_items( $rows ) ] );
		return [
			$this->page_hero( 'pricing-hero', 'Menu', 'Considered prices, <em>clearly</em> shown', 'Everything is included: consultation, removal of previous lacquer and a cuticle oil to take home.', '', 'ens-hero-calm' ),
			$this->grid( [
				$this->col( [
					$list( 'Manicure', 'Lacquer finishes on natural nails.', [ [ 'Signature Manicure', 'Shape, cuticle care, lacquer, massage', '50 min', '$55', 'Signature' ], [ 'Express Manicure', 'Shape, tidy and polish', '30 min', '$38' ], [ 'French Manicure', 'Hand-painted smile line', '60 min', '$65' ], [ 'Gentleman’s Grooming', 'Shape, buff, matte finish', '30 min', '$40' ] ] ),
					$list( 'Gel & extensions', 'HEMA-free gel, cured under low-heat LED.', [ [ 'Gel Couture', 'Colour of your choice', '70 min', '$75' ], [ 'Gel Couture + French', 'Fine smile line', '80 min', '$85' ], [ 'Sculpted Extensions', 'Full set, any length', '120 min', '$110' ], [ 'Extension Infill', 'Three to four weeks', '90 min', '$85' ], [ 'Gel Removal', 'Complimentary when rebooking', '20 min', '$20' ] ] ),
				], '', [ 'flex_gap' => [ 'column' => '0', 'row' => '72', 'unit' => 'px', 'isLinked' => false ] ] ),
				$this->col( [ $this->w( 'image', [ 'image' => $this->media( 'pricing-side' ), 'image_size' => 'large' ], 'ens-mask ens-arch' ) ], 'ens-sticky-col' ),
			], '7fr 5fr', '', '1fr', [ 'grid_align_items' => 'start' ] ),
			$this->grid( [
				$list( 'Feet', 'In reclining chairs with single-use basin liners.', [ [ 'Spa Pedicure', 'Mineral soak, callus care, massage', '75 min', '$80' ], [ 'Spa Pedicure Gel', 'With gel colour', '85 min', '$95' ], [ 'Express Pedicure', 'Shape and polish', '40 min', '$50' ] ] ),
				$list( 'Art & rituals', 'Add art to any gel or extension service.', [ [ 'Accent Art', 'One to two nails', '15 min', '$25' ], [ 'Full Set Art', 'All ten nails', '45 min', '$60' ], [ 'Chrome & Glazed', 'Powder finish', '15 min', '$20' ], [ 'Hand Ritual', 'Paraffin and lymphatic massage', '45 min', '$65', 'Restorative' ], [ 'Paraffin Add-on', 'With any service', '15 min', '$18' ] ] ),
			], '1fr 1fr', 'ens-sec-porcelain', '1fr', [ 'grid_align_items' => 'start' ] ),
			$this->sec( [
				$this->intro( 'Good to know', 'Our <em>policies</em>' ),
				$this->grid( array_map( fn( $p ) => $this->col( [ $this->w( 'heading', [ 'title' => $p[0], 'header_size' => 'h3' ] ), $this->text( '<p>' . $p[1] . '</p>', '' ) ], '', [ 'flex_gap' => [ 'column' => '0', 'row' => '12', 'unit' => 'px', 'isLinked' => false ] ] ), [
					[ 'Reservations', 'A card secures your booking; nothing is charged unless you miss your appointment.' ],
					[ 'Cancellations', 'Free up to 24 hours before. Later cancellations are charged at 50%.' ],
					[ 'Seven-day promise', 'If anything chips or lifts within a week, we repair it at no charge.' ],
				] ), '1fr 1fr 1fr', 'ens-stagger', '1fr', [ 'isInner' => true, 'grid_align_items' => 'start', 'grid_gaps' => [ 'column' => '56', 'row' => '40', 'unit' => 'px', 'isLinked' => false ] ] ),
			], 'ens-sec-dark' ),
			$this->cta(),
		];
	}

	private function lookbook() {
		$this->page = 'lookbook';
		$this->n    = 0;
		$looks      = [
			[ 'lookbook-1', 'Minimal', 'Milk & honey French' ], [ 'lookbook-2', 'Couture', 'Glazed pearl chrome' ], [ 'lookbook-7', 'Minimal', 'Matte nude, short square' ],
			[ 'lookbook-3', 'Couture', 'Peony ombré' ], [ 'lookbook-4', 'Art', 'Espresso & gold' ], [ 'lookbook-8', 'Couture', 'Champagne cat-eye' ],
			[ 'lookbook-5', 'Art', 'Negative-space line' ], [ 'lookbook-9', 'Minimal', 'Linen pedicure' ], [ 'lookbook-10', 'Couture', 'Burgundy velvet' ],
			[ 'lookbook-6', 'Bridal', 'Pearl bridal' ], [ 'lookbook-11', 'Art', 'Rose & champagne swirl' ], [ 'lookbook-12', 'Bridal', 'Colour story' ],
		];
		return [
			$this->page_hero( 'gallery-hero', 'Lookbook', 'The season, <em>in detail</em>', 'A living archive of recent sets from the atelier. Tap any look to see it larger — and bring it to your consultation.' ),
			$this->sec( [ $this->w( 'ens-gallery', [
				'all_label' => 'All looks',
				'items'     => array_map( fn( $l, $k ) => [ '_id' => 'g' . $k, 'image' => $this->media( $l[0] ), 'category' => $l[1], 'caption' => $l[2] ], $looks, array_keys( $looks ) ),
			] ) ] ),
			$this->marquee( 'ens-flush ens-sec-linen' ),
			$this->cta( 'Bring a <em>reference</em>', 'Inspired?', 'Screenshot a look, book a consultation and your artist will adapt it to your hands.' ),
		];
	}

	private function artists() {
		$this->page = 'artists';
		$this->n    = 0;
		return [
			$this->page_hero( 'team-hero', 'Artists', 'The hands <em>behind</em> the work', 'Every artist at Maison Élise trains with us for three months before taking a single guest.' ),
			$this->sec( [
				$this->intro( 'Resident artists', 'Meet the <em>atelier</em>', 'Request an artist when you book — or let us match you with the specialist for your treatment.' ),
				$this->w( 'ens-team', [ 'columns' => '4', 'items' => [
					[ '_id' => 't1', 'image' => $this->media( 'team-1' ), 'name' => 'Élise Marchand', 'role' => 'Founder & creative director', 'bio' => 'Fifteen years behind the lamp; still books two mornings a week.', 'handle' => '@elise.atelier', 'instagram' => self::link( 'https://instagram.com/' ) ],
					[ '_id' => 't2', 'image' => $this->media( 'team-2' ), 'name' => 'Inès Laurent', 'role' => 'Senior nail artist', 'bio' => 'Trained illustrator; our go-to for freehand line art and bridal sets.', 'handle' => '@ines.paints', 'instagram' => self::link( 'https://instagram.com/' ) ],
					[ '_id' => 't3', 'image' => $this->media( 'team-3' ), 'name' => 'Maya Okafor', 'role' => 'Extensions specialist', 'bio' => 'Sculpts builder-gel structure so fine you forget it is there.', 'handle' => '@maya.sculpts', 'instagram' => self::link( 'https://instagram.com/' ) ],
					[ '_id' => 't4', 'image' => $this->media( 'team-4' ), 'name' => 'Sofia Reyes', 'role' => 'Pedicure & wellness', 'bio' => 'Former spa therapist; her Hand Ritual has a waiting list.', 'handle' => '@sofia.rituals', 'instagram' => self::link( 'https://instagram.com/' ) ],
				] ] ),
			] ),
			$this->sec( [ $this->w( 'ens-feature', [
				'image'      => $this->media( 'founder' ),
				'side'       => 'left',
				'block'      => 'yes',
				'stat'       => '3',
				'stat_label' => 'Months of training',
				'eyebrow'    => 'Join the atelier',
				'title'      => 'We are always looking for <em>steady hands</em>',
				'body'       => '<p>Experienced or newly qualified, if you care about craft and calm, we would love to hear from you. We offer paid training, a four-day week and products we are proud of.</p>',
				'button'     => 'Get in touch',
				'link'       => self::link( '/contact/' ),
			] ) ], 'ens-sec-linen' ),
			$this->quotes( '' ),
			$this->cta( 'Request your <em>artist</em>' ),
		];
	}

	private function booking() {
		$this->page = 'booking';
		$this->n    = 0;
		$hours      = implode( '<br>', array_map( 'esc_html', explode( "\n", ens_defaults()['ens_hours'] ) ) );
		$address    = implode( '<br>', array_map( 'esc_html', explode( "\n", ens_defaults()['ens_address'] ) ) );
		return [
			$this->page_hero( 'booking-hero', 'Book', 'Reserve your <em>hour</em>', 'Send a request and we will confirm by phone or email within one working day.', '', 'ens-hero-calm' ),
			$this->grid( [
				$this->col( [
					$this->eyebrow( 'Appointments' ),
					$this->h( 'Request an <em>appointment</em>' ),
					$this->text( '<p>Choose your treatment and a preferred time. If you would like a particular artist, let us know — otherwise we will match you with the specialist for your treatment.</p>', 'ens-lead ens-reveal' ),
					$this->text( "<p><strong>Opening hours</strong><br>{$hours}</p><p><strong>The atelier</strong><br>{$address}</p><p><strong>Prefer to call?</strong><br>" . esc_html( ens_defaults()['ens_phone'] ) . '</p>' ),
				], 'ens-sticky-col', [ 'flex_gap' => [ 'column' => '0', 'row' => '24', 'unit' => 'px', 'isLinked' => false ] ] ),
				$this->col( [ $this->w( 'ens-form', [
					'type'    => 'booking',
					'heading' => 'Your details',
					'intro'   => 'Fields marked * are required.',
					'artists' => "No preference\nÉlise Marchand\nInès Laurent\nMaya Okafor\nSofia Reyes",
					'submit'  => 'Request appointment',
					'tone'    => 'light',
				] ) ] ),
			], '5fr 7fr', '', '1fr', [ 'grid_align_items' => 'start' ] ),
			$this->sec( [
				$this->intro( 'Before your visit', 'A few <em>gentle</em> notes' ),
				$this->grid( array_map( fn( $p ) => $this->col( [ $this->w( 'heading', [ 'title' => $p[0], 'header_size' => 'h3' ] ), $this->text( '<p>' . $p[1] . '</p>', '' ) ], '', [ 'flex_gap' => [ 'column' => '0', 'row' => '12', 'unit' => 'px', 'isLinked' => false ] ] ), [
					[ 'Arrive bare', 'If you are wearing gel from another salon, please mention it so we can allow time for removal.' ],
					[ 'Bring inspiration', 'Screenshots are welcome. Your artist will adapt any look to suit your hands.' ],
					[ 'Allow time', 'We never rush the finish — leave ten minutes after lacquer services before handling bags.' ],
				] ), '1fr 1fr 1fr', 'ens-stagger', '1fr', [ 'isInner' => true, 'grid_align_items' => 'start', 'grid_gaps' => [ 'column' => '56', 'row' => '40', 'unit' => 'px', 'isLinked' => false ] ] ),
			], 'ens-sec-linen' ),
			$this->grid( [
				$this->col( [ $this->eyebrow( 'Booking questions' ), $this->h( 'Before you <em>reserve</em>' ) ], 'ens-sticky-col', [ 'flex_gap' => [ 'column' => '0', 'row' => '8', 'unit' => 'px', 'isLinked' => false ] ] ),
				$this->w( 'ens-faq', [ 'items' => $this->faq_items( [
					[ 'How far ahead can I book?', 'Four weeks ahead. Same-week openings are released every Monday at 9:00.' ],
					[ 'Do you take walk-ins?', 'We are appointment-only so that nobody is rushed, but do call — we can sometimes fit in a quick repair.' ],
					[ 'Can I book for a group?', 'Yes, for up to four guests side by side. Bridal parties can reserve the whole atelier on Sunday mornings.' ],
					[ 'What is your cancellation policy?', 'Free up to 24 hours before your appointment; later cancellations are charged at 50% of the treatment.' ],
				] ), 'first_open' => 'yes' ] ),
			], '5fr 7fr', '', '1fr', [ 'grid_align_items' => 'start' ] ),
		];
	}

	private function faq() {
		$this->page = 'faq';
		$this->n    = 0;
		return [
			$this->page_hero( 'faq-hero', 'Questions', 'Everything you <em>might</em> ask', 'Can’t find your answer? We reply to every message within one working day.' ),
			$this->grid( [
				$this->col( [
					$this->eyebrow( 'Help' ),
					$this->h( 'Frequently asked' ),
					$this->text( '<p>Answers on booking, treatments and aftercare. Still curious? Call us or send a note.</p>', 'ens-lead ens-reveal' ),
					$this->button( 'Contact the atelier', '/contact/', 'ens-reveal ens-btn-outline' ),
				], 'ens-sticky-col', [ 'flex_gap' => [ 'column' => '0', 'row' => '24', 'unit' => 'px', 'isLinked' => false ] ] ),
				$this->col( [
					$this->w( 'ens-faq', [ 'group' => 'Appointments', 'first_open' => 'yes', 'items' => $this->faq_items( [
						[ 'How far ahead can I book?', 'Four weeks ahead. Same-week openings are released every Monday at 9:00.' ],
						[ 'Do you take walk-ins?', 'We are appointment-only so nobody is rushed, but call us — small repairs can often be fitted in.' ],
						[ 'What is your cancellation policy?', 'Free up to 24 hours before; later cancellations are charged at 50%.' ],
					] ) ] ),
					$this->w( 'ens-faq', [ 'group' => 'Treatments', 'items' => $this->faq_items( [
						[ 'Is gel bad for my nails?', 'Not when applied and removed correctly. We use HEMA-free gel and never scrape or peel.' ],
						[ 'How long does each treatment last?', 'Lacquer seven to ten days, gel around three weeks, extensions three to four weeks between infills.' ],
						[ 'Are your tools sterilised?', 'Metal tools are autoclave-sterilised in sealed pouches opened in front of you; files are single-use.' ],
					] ) ] ),
					$this->w( 'ens-faq', [ 'group' => 'Care & aftercare', 'items' => $this->faq_items( [
						[ 'How do I make my manicure last?', 'Cuticle oil every night, gloves for cleaning, and avoid using nails as tools.' ],
						[ 'What if something chips?', 'Our seven-day promise covers any chip or lift — just call and we will repair it.' ],
						[ 'Can I remove gel at home?', 'We recommend not to. Professional removal takes twenty minutes and protects your nail plate.' ],
					] ) ] ),
				], '', [ 'flex_gap' => [ 'column' => '0', 'row' => '56', 'unit' => 'px', 'isLinked' => false ] ] ),
			], '5fr 7fr', '', '1fr', [ 'grid_align_items' => 'start' ] ),
			$this->cta(),
		];
	}

	private function contact() {
		$this->page = 'contact';
		$this->n    = 0;
		$d          = ens_defaults();
		$lines      = fn( $s ) => implode( '<br>', array_map( 'esc_html', explode( "\n", $s ) ) );
		return [
			$this->page_hero( 'contact-hero', 'Contact', 'Come and <em>visit</em>', 'Find us in the old arcade, behind the ivory awning. Coffee is always on.', '12% 50%' ),
			$this->grid( [
				$this->col( [
					$this->w( 'image', [ 'image' => $this->media( 'contact-side' ), 'image_size' => 'large' ], 'ens-mask' ),
					$this->grid( [
						$this->col( [ $this->w( 'heading', [ 'title' => 'The atelier', 'header_size' => 'p' ], 'ens-eyebrow' ), $this->text( '<p>' . $lines( $d['ens_address'] ) . '</p>', '' ) ] ),
						$this->col( [ $this->w( 'heading', [ 'title' => 'Hours', 'header_size' => 'p' ], 'ens-eyebrow' ), $this->text( '<p>' . $lines( $d['ens_hours'] ) . '</p>', '' ) ] ),
						$this->col( [ $this->w( 'heading', [ 'title' => 'Call', 'header_size' => 'p' ], 'ens-eyebrow' ), $this->text( '<p><a href="tel:+15550142290">' . esc_html( $d['ens_phone'] ) . '</a></p>', '' ) ] ),
						$this->col( [ $this->w( 'heading', [ 'title' => 'Write', 'header_size' => 'p' ], 'ens-eyebrow' ), $this->text( '<p><a href="mailto:' . esc_attr( $d['ens_email'] ) . '">' . esc_html( $d['ens_email'] ) . '</a></p>', '' ) ] ),
					], '1fr 1fr', 'ens-stagger', '1fr 1fr', [ 'isInner' => true, 'grid_align_items' => 'start', 'grid_gaps' => [ 'column' => '32', 'row' => '32', 'unit' => 'px', 'isLinked' => true ] ] ),
				], '', [ 'flex_gap' => [ 'column' => '0', 'row' => '48', 'unit' => 'px', 'isLinked' => false ] ] ),
				$this->col( [ $this->w( 'ens-form', [
					'type'    => 'contact',
					'heading' => 'Send us a note',
					'intro'   => 'For appointments, please use the booking form — it reaches us faster.',
					'submit'  => 'Send message',
					'success' => 'Thank you — we reply to every message within one working day.',
					'tone'    => 'light',
				] ) ] ),
			], '5fr 7fr', '', '1fr', [ 'grid_align_items' => 'start' ] ),
			$this->sec( [
				$this->intro( 'Getting here', 'Easy to <em>find</em>' ),
				$this->grid( array_map( fn( $p ) => $this->col( [ $this->w( 'heading', [ 'title' => $p[0], 'header_size' => 'h3' ] ), $this->text( '<p>' . $p[1] . '</p>', '' ) ], '', [ 'flex_gap' => [ 'column' => '0', 'row' => '12', 'unit' => 'px', 'isLinked' => false ] ] ), [
					[ 'By train', 'Old Town station is a four-minute walk; take the arcade exit and look for the ivory awning.' ],
					[ 'By car', 'Rosewood car park is directly opposite. We validate two hours of parking.' ],
					[ 'Accessibility', 'Step-free entrance, an accessible restroom and a height-adjustable treatment chair.' ],
				] ), '1fr 1fr 1fr', 'ens-stagger', '1fr', [ 'isInner' => true, 'grid_align_items' => 'start', 'grid_gaps' => [ 'column' => '56', 'row' => '40', 'unit' => 'px', 'isLinked' => false ] ] ),
			], 'ens-sec-linen' ),
		];
	}

	private function footer() {
		$this->page = 'ens-footer';
		$this->n    = 0;
		$d          = ens_defaults();
		$lines      = fn( $s ) => implode( '<br>', array_map( 'esc_html', explode( "\n", $s ) ) );
		$links      = fn( $rows ) => $this->w( 'icon-list', [ 'icon_list' => array_map( fn( $r, $k ) => [ '_id' => 'i' . $k, 'text' => $r[0], 'link' => self::link( $r[1] ), 'selected_icon' => [ 'value' => '', 'library' => '' ] ], $rows, array_keys( $rows ) ) ] );
		return [
			$this->grid( [
				$this->col( [ $this->eyebrow( 'The newsletter' ), $this->h( 'Letters from the <em>atelier</em>' ), $this->text( '<p>Seasonal colour edits, care notes and first access to new appointments. Once a month, never more.</p>', 'ens-lead ens-reveal' ) ], '', [ 'flex_gap' => [ 'column' => '0', 'row' => '16', 'unit' => 'px', 'isLinked' => false ] ] ),
				$this->col( [ $this->w( 'ens-form', [ 'type' => 'newsletter', 'submit' => 'Subscribe', 'success' => 'Welcome — your first letter arrives next month.', 'tone' => 'dark' ] ) ] ),
			], '7fr 5fr', 'ens-sec-dark ens-footer__news', '1fr', [ 'grid_align_items' => 'end' ] ),
			$this->grid( [
				$this->col( [ $this->w( 'heading', [ 'title' => 'Maison Élise', 'header_size' => 'h3' ] ), $this->text( '<p>An appointment-only nail atelier for manicure, gel couture and nail art.</p>', '' ) ], '', [ 'flex_gap' => [ 'column' => '0', 'row' => '12', 'unit' => 'px', 'isLinked' => false ] ] ),
				$this->col( [ $this->w( 'heading', [ 'title' => 'Visit', 'header_size' => 'p' ], 'ens-eyebrow' ), $this->text( '<p>' . $lines( $d['ens_address'] ) . '</p><p><a href="tel:+15550142290">' . esc_html( $d['ens_phone'] ) . '</a><br><a href="mailto:' . esc_attr( $d['ens_email'] ) . '">' . esc_html( $d['ens_email'] ) . '</a></p>', '' ) ] ),
				$this->col( [ $this->w( 'heading', [ 'title' => 'Hours', 'header_size' => 'p' ], 'ens-eyebrow' ), $this->text( '<p>' . $lines( $d['ens_hours'] ) . '</p>', '' ) ] ),
				$this->col( [ $this->w( 'heading', [ 'title' => 'Explore', 'header_size' => 'p' ], 'ens-eyebrow' ), $links( [ [ 'Treatments', '/services/' ], [ 'Pricing', '/pricing/' ], [ 'Lookbook', '/lookbook/' ], [ 'Artists', '/artists/' ], [ 'Journal', '/journal/' ], [ 'Instagram', 'https://instagram.com/' ] ] ) ] ),
			], '2fr 1fr 1fr 1fr', 'ens-sec-dark ens-tight', '1fr 1fr', [ 'grid_align_items' => 'start', 'grid_gaps' => [ 'column' => '48', 'row' => '40', 'unit' => 'px', 'isLinked' => false ] ] ),
			$this->sec( [ $this->w( 'heading', [ 'title' => 'Maison Élise', 'header_size' => 'p', 'align' => 'center' ], 'ens-wordmark ens-reveal' ) ], 'ens-sec-dark ens-flush-top', [ 'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '24', 'left' => '0', 'isLinked' => false ], 'content_width' => 'full' ] ),
		];
	}

	private function not_found() {
		$this->page = 'ens-404';
		$this->n    = 0;
		return [
			$this->sec( [ $this->w( 'ens-hero', [
				'layout'    => 'page',
				'image'     => $this->media( 'notfound' ),
				'eyebrow'   => 'Error 404',
				'title'     => 'This page has <em>slipped</em> away',
				'text'      => 'The link may be old or the page has moved. Let us take you somewhere lovely instead.',
				'btn1'      => 'Return home',
				'btn1_link' => self::link( '/' ),
				'btn2'      => 'Book appointment',
				'btn2_link' => self::link( '/booking/' ),
				'badge'     => '',
			] ) ], 'ens-flush', [ 'content_width' => 'full' ] ),
			$this->sec( [
				$this->intro( 'Perhaps you were looking for', 'Our <em>treatments</em>' ),
				$this->services_grid(),
			] ),
		];
	}

	/* ------------------------------------------------------------------ */
	/* Posts, menus, settings                                               */
	/* ------------------------------------------------------------------ */

	private function posts() {
		$cats = [];
		foreach ( [ 'Trends', 'Care', 'Studio' ] as $c ) {
			$term       = term_exists( $c, 'category' ) ?: wp_insert_term( $c, 'category' );
			$cats[ $c ] = (int) $term['term_id'];
		}
		$p    = fn( $t ) => "<!-- wp:paragraph -->\n<p>{$t}</p>\n<!-- /wp:paragraph -->\n\n";
		$h2   = fn( $t ) => "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->\n\n";
		$q    = fn( $t ) => "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>{$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->\n\n";
		$ul   = fn( $items ) => "<!-- wp:list -->\n<ul class=\"wp-block-list\">" . implode( '', array_map( fn( $i ) => "<!-- wp:list-item -->\n<li>{$i}</li>\n<!-- /wp:list-item -->", $items ) ) . "</ul>\n<!-- /wp:list -->\n\n";
		$posts = [
			[ 'the-autumn-edit', 'The autumn edit: five shades we can’t stop wearing', 'Trends', 'post-1', '-2 days', 'From milky mocha to a glossy black cherry, the colours our artists are reaching for this season — and how to wear them.',
				$p( 'Every September the swatch table at the atelier quietly rearranges itself. The sorbet pinks drift to the back, and the deeper, warmer tones move forward. This year the shift has been especially elegant: less goth, more cashmere.' )
				. $h2( '1. Milky mocha' ) . $p( 'A sheer, creamy brown that reads as polished nude from across the room and as colour up close. It flatters almost every skin tone and grows out invisibly.' )
				. $h2( '2. Black cherry gloss' ) . $p( 'The autumn classic, refined. We layer two thin coats for depth rather than one heavy one, so the colour glows rather than sits flat.' )
				. $h2( '3. Olive smoke' ) . $p( 'Surprisingly wearable on short, square nails. Pair it with gold jewellery and it looks entirely intentional.' )
				. $h2( '4. Champagne chrome' ) . $p( 'A whisper of chrome powder over a sheer pink base. The most requested finish of the season by far.' )
				. $h2( '5. Espresso' ) . $p( 'Deep brown is the new black. It is softer against the skin, and it photographs beautifully.' )
				. $q( 'Autumn colour should feel like a good coat: warm, considered and easy to live in.' )
				. $p( 'Ask your artist to hold any of these shades up against your hand at consultation — it is the quickest way to know.' ),
			],
			[ 'cuticle-care-properly', 'Cuticle care, properly: a two-minute nightly ritual', 'Care', 'post-2', '-9 days', 'The single habit that makes every manicure last longer — and why less cutting is always more.',
				$p( 'If we could give every guest one piece of advice, it would be this: oil your cuticles every night. Not occasionally. Every night. It takes two minutes and it changes everything.' )
				. $h2( 'Why it matters' ) . $p( 'Healthy cuticles form a seal that protects the nail matrix. Dry, cracked cuticles lift, snag and invite you to pick at them — which is where most damage begins.' )
				. $h2( 'The ritual' ) . $ul( [ 'One drop of oil at the base of each nail.', 'Massage in small circles for ten seconds per finger.', 'Gently push back softened skin with a towel — never cut at home.', 'Finish with hand cream, working it into the knuckles.' ] )
				. $q( 'Cutting live tissue makes cuticles grow back thicker. Oil and patience make them disappear.' )
				. $p( 'Every treatment at Maison Élise ends with a travel-size bottle of our cold-pressed jojoba oil. Keep it by your bed.' ),
			],
			[ 'bridal-nails-planning', 'Bridal nails: planning your set from engagement to aisle', 'Trends', 'post-3', '-17 days', 'A gentle timeline for the most photographed hands of your life — including the trial most brides skip.',
				$p( 'Your hands will be in more photographs on your wedding day than your shoes. Ring shots, bouquet shots, the first-dance close-ups. A little planning goes a long way.' )
				. $h2( 'Three months before' ) . $p( 'Begin regular manicures to bring your natural nails into their best condition. If you are considering extensions, now is the time to try them.' )
				. $h2( 'Six weeks before: the trial' ) . $p( 'Book a full trial with your chosen artist. Bring photographs of your dress, flowers and ring. We will try two options on alternate hands so you can compare in daylight.' )
				. $h2( 'Two days before' ) . $p( 'Your final set. Gel or sculpted extensions are ideal — fully cured, nothing to smudge, perfect for the honeymoon.' )
				. $q( 'The most beautiful bridal nails are the ones that make the ring look even better.' )
				. $p( 'Bridal parties can reserve the whole atelier on Sunday mornings — with champagne, of course.' ),
			],
			[ 'gel-or-builder-gel', 'Gel or builder gel? A plain-English guide', 'Care', 'post-4', '-26 days', 'Two of the most requested services, explained without jargon — so you can choose with confidence.',
				$p( 'We are asked this at least once a day, so here is the honest answer.' )
				. $h2( 'Gel colour' ) . $p( 'A thin, flexible coloured gel applied like polish and cured under a lamp. It lasts around three weeks and suits nails that are already reasonably strong.' )
				. $h2( 'Builder gel' ) . $p( 'A thicker, self-levelling gel that adds structure. It creates an apex that protects weak or bendy nails, and it can be used to sculpt extra length.' )
				. $ul( [ 'Short, strong nails: gel colour is perfect.', 'Thin, peeling or bendy nails: builder gel underneath your colour.', 'Want length: sculpted builder gel extensions.' ] )
				. $p( 'Both are removed the same gentle way — never peeled, never scraped. That is the real secret to healthy nails under gel.' ),
			],
			[ 'the-case-for-minimal-nail-art', 'The case for minimal nail art', 'Trends', 'post-5', '-34 days', 'Why a single fine line can say more than a full set of embellishment — and how to wear it.',
				$p( 'There is a particular pleasure in noticing a detail only when someone is close. That is what minimal nail art does best.' )
				. $h2( 'Less, but better' ) . $p( 'A micro French tip in champagne. A single gold dot at the cuticle. A negative-space crescent. These are small decisions, carefully executed, and they work with everything you own.' )
				. $q( 'Minimal does not mean simple. A perfectly straight two-millimetre line is the hardest thing we paint.' )
				. $h2( 'How to start' ) . $p( 'Choose one accent nail and one element. Ask your artist to sketch it on a swatch tip first. If you love it, repeat it on all ten next time.' ),
			],
			[ 'inside-the-hand-ritual', 'Inside the hand ritual: why we slow everything down', 'Studio', 'post-6', '-45 days', 'A closer look at our most restorative treatment — and the thinking behind the forty-five quiet minutes.',
				$p( 'The Hand Ritual began as a staff treat. After long days at the lamp, our artists would take turns giving each other paraffin wraps. Guests started asking what smelled so good.' )
				. $h2( 'What happens' ) . $ul( [ 'A warm towel compress and gentle enzyme exfoliation.', 'A rose-clay mask to brighten and soften.', 'A warm paraffin wrap, sealed in linen.', 'Fifteen minutes of lymphatic massage for hands and forearms.' ] )
				. $h2( 'Why it works' ) . $p( 'Warmth opens the skin to the oils that follow, and slow massage reduces puffiness and tension. Most guests report their rings fit more easily afterwards.' )
				. $q( 'We do not play music during the ritual. Most people fall quiet within five minutes — it is the nicest sound in the atelier.' ),
			],
		];
		foreach ( $posts as [ $slug, $title, $cat, $img, $when, $excerpt, $content ] ) {
			$found = get_posts( [ 'post_type' => 'post', 'name' => $slug, 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ] );
			$id    = wp_insert_post( wp_slash( [
				'ID'            => $found[0] ?? 0,
				'post_type'     => 'post',
				'post_name'     => $slug,
				'post_title'    => $title,
				'post_excerpt'  => $excerpt,
				'post_content'  => $content,
				'post_status'   => 'publish',
				'post_date'     => wp_date( 'Y-m-d 09:30:00', strtotime( $when ) ),
				'post_category' => [ $cats[ $cat ] ],
			] ), true );
			set_post_thumbnail( $id, $this->img[ $img ] );
			WP_CLI::log( "  post: {$slug} (#{$id})" );
		}
		// Retire WordPress sample content (trash, recoverable).
		foreach ( [ [ 'post', 'hello-world' ], [ 'page', 'sample-page' ] ] as [ $type, $slug ] ) {
			$sample = get_page_by_path( $slug, OBJECT, $type );
			if ( $sample && 'trash' !== $sample->post_status ) {
				wp_trash_post( $sample->ID );
			}
		}
		$uncat = get_category_by_slug( 'uncategorized' );
		if ( $uncat ) {
			update_option( 'default_category', $cats['Studio'] );
		}
	}

	private function menus( array $ids ) {
		$make = function ( $name, $location, array $items ) {
			$menu = wp_get_nav_menu_object( $name );
			$id   = $menu ? $menu->term_id : wp_create_nav_menu( $name );
			foreach ( wp_get_nav_menu_items( $id ) ?: [] as $old ) {
				wp_delete_post( $old->ID, true );
			}
			foreach ( $items as [ $title, $page, $children ] ) {
				$parent = wp_update_nav_menu_item( $id, 0, [ 'menu-item-title' => $title, 'menu-item-object' => 'page', 'menu-item-object-id' => $page, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ] );
				foreach ( $children as [ $ct, $cp ] ) {
					wp_update_nav_menu_item( $id, 0, [ 'menu-item-title' => $ct, 'menu-item-object' => 'page', 'menu-item-object-id' => $cp, 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $parent, 'menu-item-status' => 'publish' ] );
				}
			}
			$locations              = get_theme_mod( 'nav_menu_locations', [] );
			$locations[ $location ] = $id;
			set_theme_mod( 'nav_menu_locations', $locations );
		};
		$treat = array_map( fn( $t ) => [ $t['title'], $ids[ $t['slug'] ] ], $this->treatments() );
		$make( 'Primary', 'menu-1', [
			[ 'Treatments', $ids['services'], array_merge( $treat, [ [ 'Pricing', $ids['pricing'] ] ] ) ],
			[ 'Lookbook', $ids['lookbook'], [] ],
			[ 'Atelier', $ids['about'], [ [ 'Our story', $ids['about'] ], [ 'Artists', $ids['artists'] ], [ 'FAQ', $ids['faq'] ] ] ],
			[ 'Journal', $ids['journal'], [] ],
			[ 'Contact', $ids['contact'], [] ],
		] );
		$privacy = (int) get_option( 'wp_page_for_privacy_policy' );
		if ( $privacy ) {
			wp_update_post( [ 'ID' => $privacy, 'post_status' => 'publish' ] );
		}
		$make( 'Footer legal', 'footer', array_filter( [ $privacy ? [ 'Privacy', $privacy, [] ] : null, [ 'FAQ', $ids['faq'], [] ], [ 'Contact', $ids['contact'], [] ], [ 'Legal notice', $ids['legal-notice'], [] ] ] ) );
	}

	private function settings( array $ids ) {
		update_option( 'blogname', 'Maison Élise' );
		update_option( 'blogdescription', 'Nail Atelier' );
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
		update_option( 'page_for_posts', $ids['journal'] );
		update_option( 'posts_per_page', 9 );
	}
}

WP_CLI::add_command( 'ens', 'ENS_Seed' );
