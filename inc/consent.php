<?php
/**
 * Consent preferences (EDS standard, after the Elite Realty "EDS Consent" contract).
 *
 * Two truthful categories: Necessary (always on) and Preferences (translation).
 * Optional scripts are printed inert (`type="text/plain" data-eds-consent="…"
 * data-src="…"`) and started by assets/js/consent.js only after the visitor
 * allows their category, so GTranslate cannot contact GTranslate or Google first.
 * The choice lives in the visitor's browser (localStorage, see ens_consent_config()),
 * the HTML is identical for everyone, and nothing needs PHP at runtime: it works on
 * a static export. Stands down when the EDS Consent plugin is active.
 *
 * Reopen the panel from any `[data-eds-consent-open]` element or a link to `#eds-consent`.
 */

defined( 'ABSPATH' ) || exit;

/** Whether the theme's own consent layer runs. */
function ens_consent_active(): bool {
	return ! ( function_exists( 'eds_consent_enabled' ) && eds_consent_enabled() );
}

/** Browser configuration. Bump `version` (filter ens_consent_policy_version) after a material change. */
function ens_consent_config(): array {
	return [
		'key'        => 'eds-consent:elite-nail-studio',
		'version'    => (string) apply_filters( 'ens_consent_policy_version', '1' ),
		'maxAgeDays' => 180,
		// Removed when Preferences is withdrawn (scripts that already ran need a reload).
		'cleanup'    => [ 'localStorage' => [ '__GT_TRANSLATE_LANGS', 'gt_autoswitch' ], 'cookies' => [ 'googtrans' ] ],
		'labels'     => [
			'language'     => __( 'Language', 'elite-nail-studio' ),
			'languageHint' => __( 'Language: translation needs your permission. Open privacy preferences', 'elite-nail-studio' ),
		],
	];
}

/** GTranslate's widget scripts are optional (Preferences): print them inert. */
add_filter( 'script_loader_tag', function ( $tag, $handle ) {
	if ( ! ens_consent_active() || ! str_starts_with( $handle, 'gt_widget_script_' ) ) {
		return $tag;
	}
	return (string) preg_replace( '/<script(?=[^>]*\ssrc=)([^>]*)\ssrc=/', '<script type="text/plain" data-eds-consent="preferences"$1 data-src=', $tag );
}, 20, 2 );

add_action( 'wp_enqueue_scripts', function () {
	if ( ! ens_consent_active() ) {
		return;
	}
	wp_enqueue_script( 'ens-consent', ENS_URI . '/assets/js/consent.js', [], ENS_VERSION . '.' . filemtime( ENS_DIR . '/assets/js/consent.js' ), [ 'in_footer' => true, 'strategy' => 'defer' ] );
	wp_add_inline_script( 'ens-consent', 'window.ensConsentConfig = ' . wp_json_encode( ens_consent_config() ) . ';', 'before' );
}, 20 );

/** Published page URL by slug, or ''. */
function ens_consent_page_url( string $slug ): string {
	$page = get_page_by_path( $slug );
	return $page && 'publish' === $page->post_status ? (string) get_permalink( $page ) : '';
}

/** Links shown in the banner and the panel. */
function ens_consent_links(): string {
	$links = array_filter( [
		ens_consent_page_url( 'cookie-policy' ) => __( 'Cookie Policy', 'elite-nail-studio' ),
		(string) get_privacy_policy_url()       => __( 'Privacy Notice', 'elite-nail-studio' ),
		ens_consent_page_url( 'legal-notice' )  => __( 'Legal Notice', 'elite-nail-studio' ),
	], fn( $label, $url ) => '' !== $url, ARRAY_FILTER_USE_BOTH );
	$out = '';
	foreach ( $links as $url => $label ) {
		$out .= '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
	}
	return $out ? '<p class="ens-consent__links">' . $out . '</p>' : '';
}

/** First layer: early in the document so keyboard users meet it first. Hidden until the script decides. */
add_action( 'wp_body_open', function () {
	if ( ! ens_consent_active() ) {
		return;
	}
	?>
	<section class="ens-consent" aria-labelledby="ens-consent-title" data-ens-consent-banner hidden>
		<h2 class="ens-consent__title" id="ens-consent-title"><?php esc_html_e( 'Your privacy', 'elite-nail-studio' ); ?></h2>
		<p class="ens-consent__text"><?php esc_html_e( 'This site uses only the storage it needs to work. With your permission, the language selector can also translate pages through GTranslate and Google, which then receive the page text and your IP address.', 'elite-nail-studio' ); ?></p>
		<?php echo ens_consent_links(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in ens_consent_links(). ?>
		<div class="ens-consent__actions">
			<button type="button" class="ens-btn ens-btn--sm" data-eds-consent-action="reject"><span><?php esc_html_e( 'Reject optional', 'elite-nail-studio' ); ?></span></button>
			<button type="button" class="ens-btn ens-btn--sm" data-eds-consent-action="accept"><span><?php esc_html_e( 'Accept optional', 'elite-nail-studio' ); ?></span></button>
			<button type="button" class="ens-consent__manage" data-eds-consent-action="manage"><?php esc_html_e( 'Manage preferences', 'elite-nail-studio' ); ?></button>
		</div>
	</section>
	<?php
}, 5 );

/** Preferences panel: a native modal dialog (focus containment, Escape, top layer). */
add_action( 'wp_footer', function () {
	if ( ! ens_consent_active() ) {
		return;
	}
	?>
	<dialog class="ens-consent-panel" aria-labelledby="ens-consent-panel-title" data-ens-consent-panel>
		<button type="button" class="ens-consent-panel__close" data-eds-consent-action="close" aria-label="<?php esc_attr_e( 'Close without changes', 'elite-nail-studio' ); ?>">&times;</button>
		<h2 class="ens-consent__title" id="ens-consent-panel-title"><?php esc_html_e( 'Privacy preferences', 'elite-nail-studio' ); ?></h2>
		<p class="ens-consent__text"><?php esc_html_e( 'Choose what you allow. Nothing optional is switched on until you choose it, and you can change your mind at any time under “Cookie preferences” in the footer.', 'elite-nail-studio' ); ?></p>
		<ul class="ens-consent__categories">
			<li class="ens-consent__category">
				<div>
					<h3 class="ens-consent__category-title"><label for="ens-consent-necessary"><?php esc_html_e( 'Necessary', 'elite-nail-studio' ); ?></label></h3>
					<p class="ens-consent__category-text" id="ens-consent-necessary-desc"><?php esc_html_e( 'Keeps the site working and remembers this choice. On demonstration sites it also remembers the colour palette you preview. Always active.', 'elite-nail-studio' ); ?></p>
				</div>
				<input class="ens-consent__switch" type="checkbox" role="switch" id="ens-consent-necessary" checked disabled aria-describedby="ens-consent-necessary-desc">
			</li>
			<li class="ens-consent__category">
				<div>
					<h3 class="ens-consent__category-title"><label for="ens-consent-preferences"><?php esc_html_e( 'Preferences: translation', 'elite-nail-studio' ); ?></label></h3>
					<p class="ens-consent__category-text" id="ens-consent-preferences-desc"><?php esc_html_e( 'Lets the language selector load GTranslate, send the page text to Google’s translation service and remember the language you choose.', 'elite-nail-studio' ); ?></p>
				</div>
				<input class="ens-consent__switch" type="checkbox" role="switch" id="ens-consent-preferences" data-eds-consent-category="preferences" aria-describedby="ens-consent-preferences-desc">
			</li>
		</ul>
		<?php echo ens_consent_links(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in ens_consent_links(). ?>
		<div class="ens-consent__actions">
			<button type="button" class="ens-btn ens-btn--sm" data-eds-consent-action="reject"><span><?php esc_html_e( 'Reject optional', 'elite-nail-studio' ); ?></span></button>
			<button type="button" class="ens-btn ens-btn--sm" data-eds-consent-action="accept"><span><?php esc_html_e( 'Accept optional', 'elite-nail-studio' ); ?></span></button>
			<button type="button" class="ens-btn ens-btn--sm ens-btn--outline" data-eds-consent-action="save"><span><?php esc_html_e( 'Save preferences', 'elite-nail-studio' ); ?></span></button>
		</div>
	</dialog>
	<?php
}, 5 );
