<?php
/**
 * Einstellungsseite „Prüfstelle“: News-Zeile, Preise, Google-Bewertung, Grundsätze, Footer.
 */

defined( 'ABSPATH' ) || exit;

/** Standardstruktur der Preise. */
function blatter_preis_sets(): array {
	return array(
		'standard' => 'Preise Freiburg & Bad Säckingen',
		'steinen'  => 'Preise Steinen',
	);
}

add_action(
	'admin_menu',
	function () {
		add_menu_page( 'Prüfstelle', 'Prüfstelle', 'edit_pages', 'blatter', 'blatter_render_settings', 'dashicons-car', 3 );
		add_submenu_page( 'blatter', 'Einstellungen', 'Einstellungen', 'edit_pages', 'blatter', 'blatter_render_settings' );
	}
);

/** Redakteure dürfen die Einstellungen speichern. */
add_filter( 'option_page_capability_blatter', fn() => 'edit_pages' );

add_action(
	'admin_init',
	function () {
		register_setting(
			'blatter',
			'blatter_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'blatter_sanitize_settings',
				'show_in_rest'      => false,
			)
		);
	}
);

function blatter_sanitize_settings( $in ): array {
	$in  = is_array( $in ) ? $in : array();
	$out = array();
	foreach ( array( 'news', 'news_link', 'google_schnitt', 'google_anzahl', 'google_link', 'grundsaetze_titel', 'grundsaetze_bild', 'fuss_text', 'fuss_karte', 'termin_shortcode' ) as $k ) {
		$out[ $k ] = sanitize_text_field( $in[ $k ] ?? '' );
	}
	$out['grundsaetze_punkte'] = sanitize_textarea_field( $in['grundsaetze_punkte'] ?? '' );
	$out['preise']             = array();
	foreach ( array_keys( blatter_preis_sets() ) as $set ) {
		foreach ( (array) ( $in['preise'][ $set ] ?? array() ) as $i => $card ) {
			$out['preise'][ $set ][ $i ] = array(
				'titel'   => sanitize_text_field( $card['titel'] ?? '' ),
				'prefix'  => sanitize_text_field( $card['prefix'] ?? '' ),
				'preis'   => sanitize_text_field( $card['preis'] ?? '' ),
				'hinweis' => sanitize_text_field( $card['hinweis'] ?? '' ),
				'icon'    => sanitize_key( $card['icon'] ?? 'car' ),
				'punkte'  => sanitize_textarea_field( $card['punkte'] ?? '' ),
			);
		}
	}
	return $out;
}

function blatter_render_settings(): void {
	$o   = get_option( 'blatter_settings', array() );
	$val = fn( $k ) => esc_attr( $o[ $k ] ?? '' );
	?>
	<div class="wrap">
		<h1>Prüfstelle – Einstellungen</h1>
		<p>Standorte, Slider-Kacheln und Bewertungen pflegst du links im Menü unter „Prüfstelle“.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'blatter' ); ?>

			<h2>Kopfzeile</h2>
			<table class="form-table" role="presentation">
				<tr><th><label for="b-news">News-Zeile</label></th><td><input class="large-text" id="b-news" name="blatter_settings[news]" value="<?php echo $val( 'news' ); ?>"><p class="description">Leer lassen blendet die Zeile aus.</p></td></tr>
				<tr><th><label for="b-newsl">News-Link</label></th><td><input class="regular-text" id="b-newsl" name="blatter_settings[news_link]" value="<?php echo $val( 'news_link' ); ?>"></td></tr>
			</table>

			<h2>Online-Termin</h2>
			<table class="form-table" role="presentation">
				<tr><th><label for="b-ssa">Shortcode Buchungskalender</label></th><td><input class="regular-text" id="b-ssa" name="blatter_settings[termin_shortcode]" value="<?php echo $val( 'termin_shortcode' ); ?>" placeholder="[ssa_booking]"><p class="description">Standard: <code>[ssa_booking]</code> (Simply Schedule Appointments).</p></td></tr>
			</table>

			<h2>Preise</h2>
			<?php foreach ( blatter_preis_sets() as $set => $label ) : ?>
				<h3><?php echo esc_html( $label ); ?></h3>
				<table class="widefat striped" style="max-width:1100px">
					<thead><tr><th>Titel</th><th>„ab“</th><th>Preis (€)</th><th>Hinweis</th><th>Icon</th><th>Punkte (eine Zeile pro Punkt)</th></tr></thead>
					<tbody>
					<?php for ( $i = 0; $i < 3; $i++ ) : $c = $o['preise'][ $set ][ $i ] ?? array(); $n = "blatter_settings[preise][{$set}][{$i}]"; ?>
						<tr>
							<td><input name="<?php echo esc_attr( $n ); ?>[titel]" value="<?php echo esc_attr( $c['titel'] ?? '' ); ?>"></td>
							<td><input size="3" name="<?php echo esc_attr( $n ); ?>[prefix]" value="<?php echo esc_attr( $c['prefix'] ?? '' ); ?>"></td>
							<td><input size="5" name="<?php echo esc_attr( $n ); ?>[preis]" value="<?php echo esc_attr( $c['preis'] ?? '' ); ?>"></td>
							<td><input size="12" name="<?php echo esc_attr( $n ); ?>[hinweis]" value="<?php echo esc_attr( $c['hinweis'] ?? '' ); ?>"></td>
							<td><select name="<?php echo esc_attr( $n ); ?>[icon]"><?php foreach ( array( 'car' => 'PKW', 'moto' => 'Motorrad', 'trailer' => 'Anhänger' ) as $k => $l ) : ?><option value="<?php echo esc_attr( $k ); ?>" <?php selected( $c['icon'] ?? '', $k ); ?>><?php echo esc_html( $l ); ?></option><?php endforeach; ?></select></td>
							<td><textarea rows="4" cols="40" name="<?php echo esc_attr( $n ); ?>[punkte]"><?php echo esc_textarea( $c['punkte'] ?? '' ); ?></textarea></td>
						</tr>
					<?php endfor; ?>
					</tbody>
				</table>
			<?php endforeach; ?>

			<h2>Google-Bewertung</h2>
			<table class="form-table" role="presentation">
				<tr><th><label for="b-gs">Schnitt</label></th><td><input size="4" id="b-gs" name="blatter_settings[google_schnitt]" value="<?php echo $val( 'google_schnitt' ); ?>"></td></tr>
				<tr><th><label for="b-ga">Anzahl</label></th><td><input size="6" id="b-ga" name="blatter_settings[google_anzahl]" value="<?php echo $val( 'google_anzahl' ); ?>"></td></tr>
				<tr><th><label for="b-gl">Link</label></th><td><input class="large-text" id="b-gl" name="blatter_settings[google_link]" value="<?php echo $val( 'google_link' ); ?>"></td></tr>
			</table>

			<h2>Grundsätze</h2>
			<table class="form-table" role="presentation">
				<tr><th><label for="b-gt">Überschrift</label></th><td><input class="regular-text" id="b-gt" name="blatter_settings[grundsaetze_titel]" value="<?php echo $val( 'grundsaetze_titel' ); ?>"></td></tr>
				<tr><th><label for="b-gp">Punkte</label></th><td><textarea rows="4" cols="40" id="b-gp" name="blatter_settings[grundsaetze_punkte]"><?php echo esc_textarea( $o['grundsaetze_punkte'] ?? '' ); ?></textarea></td></tr>
				<tr><th><label for="b-gb">Bild</label></th><td><input class="large-text" id="b-gb" name="blatter_settings[grundsaetze_bild]" value="<?php echo $val( 'grundsaetze_bild' ); ?>"></td></tr>
			</table>

			<h2>Footer</h2>
			<table class="form-table" role="presentation">
				<tr><th><label for="b-ft">Text unter dem Logo</label></th><td><input class="large-text" id="b-ft" name="blatter_settings[fuss_text]" value="<?php echo $val( 'fuss_text' ); ?>"></td></tr>
				<tr><th><label for="b-fk">Adresse für die Karte</label></th><td><input class="large-text" id="b-fk" name="blatter_settings[fuss_karte]" value="<?php echo $val( 'fuss_karte' ); ?>"></td></tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/** Preise eines Sets als Array. */
function blatter_preise( string $set ): array {
	$all = blatter_opt( 'preise', array() );
	$out = array();
	foreach ( (array) ( $all[ $set ] ?? $all['standard'] ?? array() ) as $c ) {
		if ( empty( $c['titel'] ) ) {
			continue;
		}
		$c['punkte'] = array_values( array_filter( array_map( 'trim', explode( "\n", (string) $c['punkte'] ) ) ) );
		$out[]       = $c;
	}
	return $out;
}
