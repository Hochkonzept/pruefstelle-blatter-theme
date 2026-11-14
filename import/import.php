<?php
/**
 * Einmal-Import auf einer Kopie der Live-Seite (Staging).
 *
 *   wp eval-file wp-content/themes/pruefstelle-blatter/import/import.php
 *
 * - legt Standorte, Slider-Kacheln, Bewertungen und Einstellungen an
 * - schreibt die umgebauten Seiteninhalte (Blöcke + Shortcodes) in die Seiten
 *   (Elementor-Daten bleiben als Meta erhalten, werden aber nicht mehr gerendert)
 * - baut Haupt- und Footer-Menü
 *
 * Mehrfach ausführbar: vorhandene Einträge werden aktualisiert, nicht doppelt angelegt.
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( "Nur über WP-CLI ausführen.\n" );
}

$dir      = __DIR__;
$data     = json_decode( file_get_contents( $dir . '/data.json' ), true );
$manifest = json_decode( file_get_contents( $dir . '/manifest.json' ), true );
$log      = fn( $m ) => WP_CLI::log( $m );

/** Eintrag anhand Slug finden oder anlegen. */
function blatter_upsert( string $type, string $slug, array $postarr, array $meta = array() ): int {
	$found = get_posts( array( 'post_type' => $type, 'name' => $slug, 'post_status' => 'any', 'numberposts' => 1 ) );
	$postarr += array( 'post_type' => $type, 'post_name' => $slug, 'post_status' => 'publish' );
	if ( $found ) {
		$postarr['ID'] = $found[0]->ID;
		$id            = wp_update_post( $postarr, true );
	} else {
		$id = wp_insert_post( $postarr, true );
	}
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( $id->get_error_message() );
		return 0;
	}
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, $k, $v );
	}
	return (int) $id;
}

/* ---------- Standorte ---------- */
$order = array( 'freiburg' => 1, 'bad-saeckingen' => 2, 'steinen' => 3 );
foreach ( $data['standorte'] as $slug => $s ) {
	$meta = array(
		'titel'   => $s['titel'],
		'hinweis' => '',
		'strasse' => $s['strasse'],
		'ort'     => $s['ort'],
		'telefon' => $s['telefon'],
		'email'   => $s['email'],
		'karte'   => $s['karte'],
	);
	foreach ( $s['oeffnungszeiten'] as $z ) {
		$meta[ 'zeit_' . strtolower( $z['tag'] ) ] = $z['zeit'];
	}
	blatter_upsert( 'blatter_standort', $slug, array( 'post_title' => $s['name'], 'menu_order' => $order[ $slug ] ?? 9 ), $meta );
	$log( "Standort: {$s['name']}" );
}

/* ---------- Slider ---------- */
foreach ( $data['slides'] as $i => $sl ) {
	blatter_upsert(
		'blatter_slide',
		'slide-' . ( $i + 1 ),
		array( 'post_title' => $sl['titel'], 'post_content' => wp_strip_all_tags( str_replace( '&nbsp;', ' ', $sl['text'] ) ), 'menu_order' => $i + 1 ),
		array(
			'button_text' => $sl['button_text'],
			'button_link' => $sl['button_link'],
			'karte_oben'  => $sl['karte_oben'],
			'karte_unten' => $sl['karte_unten'],
			'bild'        => $sl['bild'],
		)
	);
}
$log( count( $data['slides'] ) . ' Slides' );

/* ---------- Bewertungen ---------- */
foreach ( $data['bewertungen'] as $i => $b ) {
	blatter_upsert( 'blatter_bewertung', sanitize_title( $b['name'] ), array( 'post_title' => $b['name'], 'post_content' => $b['text'], 'menu_order' => $i + 1 ), array( 'sterne' => (int) $b['sterne'] ) );
}
$log( count( $data['bewertungen'] ) . ' Bewertungen' );

/* ---------- Einstellungen ---------- */
$icons    = array( 'car', 'moto', 'trailer' );
$settings = get_option( 'blatter_settings', array() );
$settings = array_merge(
	$settings,
	array(
		'news'               => $data['kopf']['news'] ?? '',
		'news_link'          => $data['kopf']['news_link'] ?? '/',
		'google_schnitt'     => $data['google']['schnitt'] ?? '',
		'google_anzahl'      => $data['google']['anzahl'] ?? '',
		'google_link'        => $data['google']['link'] ?? '',
		'grundsaetze_titel'  => $data['grundsaetze']['titel'] ?? 'Das sind unsere Grundsätze',
		'grundsaetze_punkte' => implode( "\n", $data['grundsaetze']['punkte'] ?? array() ),
		'grundsaetze_bild'   => $data['grundsaetze']['bild'] ?? '',
		'fuss_text'          => $data['fuss']['text'] ?? '',
		'fuss_karte'         => 'Bötzinger Str. 12, 79111 Freiburg im Breisgau',
		'termin_shortcode'   => $settings['termin_shortcode'] ?? '[ssa_booking]',
	)
);
$settings['preise'] = array();
foreach ( $data['preise'] as $set => $cards ) {
	foreach ( $cards as $i => $c ) {
		$settings['preise'][ $set ][ $i ] = array(
			'titel'   => $c['titel'],
			'prefix'  => $c['prefix'],
			'preis'   => $c['preis'],
			'hinweis' => $c['hinweis'],
			'icon'    => $icons[ $i ] ?? 'car',
			'punkte'  => html_entity_decode( implode( "\n", $c['punkte'] ) ),
		);
	}
}
update_option( 'blatter_settings', $settings );
$log( 'Einstellungen gespeichert' );

/* ---------- Seiten ---------- */
foreach ( $manifest as $m ) {
	if ( ! empty( $m['skipped'] ) ) {
		continue;
	}
	$page = get_post( (int) $m['id'] );
	if ( ! $page || 'page' !== $page->post_type || $page->post_name !== $m['slug'] ) {
		$page = '/' === $m['link'] ? get_post( (int) get_option( 'page_on_front' ) ) : get_page_by_path( trim( $m['link'], '/' ) );
	}
	if ( ! $page ) {
		WP_CLI::warning( "Seite nicht gefunden: {$m['link']}" );
		continue;
	}
	$content = file_get_contents( $dir . '/pages/' . $m['file'] );
	if ( ! get_post_meta( $page->ID, '_blatter_backup_content', true ) ) {
		update_post_meta( $page->ID, '_blatter_backup_content', $page->post_content );
	}
	wp_update_post( array( 'ID' => $page->ID, 'post_content' => wp_slash( $content ) ) );
	delete_post_meta( $page->ID, '_elementor_edit_mode' );
	update_post_meta( $page->ID, '_wp_page_template', 'default' );
	$log( "Seite: {$m['link']}" );
}

/* ---------- Menüs ---------- */
function blatter_menu( string $name, string $location, array $items ): void {
	$menu = wp_get_nav_menu_object( $name );
	if ( $menu ) {
		foreach ( (array) wp_get_nav_menu_items( $menu->term_id ) as $it ) {
			wp_delete_post( $it->ID, true );
		}
		$menu_id = $menu->term_id;
	} else {
		$menu_id = wp_create_nav_menu( $name );
	}
	$add = function ( array $items, int $parent = 0 ) use ( &$add, $menu_id ) {
		foreach ( $items as $pos => $it ) {
			$args = array( 'menu-item-title' => $it[0], 'menu-item-status' => 'publish', 'menu-item-parent-id' => $parent, 'menu-item-position' => $pos + 1 );
			$page = str_starts_with( $it[1], '/' ) && ! str_contains( $it[1], '#' ) ? get_page_by_path( trim( $it[1], '/' ) ) : null;
			if ( '/' === $it[1] ) {
				$page = get_post( (int) get_option( 'page_on_front' ) );
			}
			if ( $page ) {
				$args += array( 'menu-item-object' => 'page', 'menu-item-object-id' => $page->ID, 'menu-item-type' => 'post_type' );
			} else {
				$args += array( 'menu-item-type' => 'custom', 'menu-item-url' => home_url( $it[1] ) );
			}
			$id = wp_update_nav_menu_item( $menu_id, 0, $args );
			if ( ! empty( $it[2] ) ) {
				$add( $it[2], (int) $id );
			}
		}
	};
	$add( $items );
	$locs              = get_theme_mod( 'nav_menu_locations', array() );
	$locs[ $location ] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locs );
}

blatter_menu(
	'Hauptmenü',
	'primary',
	array(
		array( 'Startseite', '/' ),
		array( 'Standorte', '/hauptuntersuchung/', array( array( 'Freiburg', '/hauptuntersuchung/freiburg/' ), array( 'Bad Säckingen', '/hauptuntersuchung/bad-saeckingen/' ), array( 'Steinen', '/hauptuntersuchung/steinen/' ) ) ),
		array( 'Preise', '/#Preise' ),
		array( 'Karriere', '/karriere/' ),
		array( 'Blog', '/blog/' ),
		array( 'Kontakt', '/#Kontakt' ),
	)
);
blatter_menu(
	'Footer',
	'footer',
	array(
		array( 'Startseite', '/' ),
		array( 'Standorte', '/hauptuntersuchung/' ),
		array( 'Impressum', '/impressum/' ),
		array( 'Datenschutzerklärung', '/datenschutzerklaerung/' ),
	)
);
$log( 'Menüs angelegt' );

flush_rewrite_rules();
WP_CLI::success( 'Import fertig. Jetzt das Theme aktivieren: wp theme activate pruefstelle-blatter' );
