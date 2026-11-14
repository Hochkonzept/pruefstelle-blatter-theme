<?php
/**
 * Inhaltstypen: Standorte, Slider-Kacheln, Bewertungen.
 *
 * Bewusst ohne ACF: einfache Metaboxen, alle Felder per REST lesbar/schreibbar
 * (z. B. für ein späteres Update per Messenger/n8n).
 */

defined( 'ABSPATH' ) || exit;

/** Felddefinitionen je Inhaltstyp. */
function blatter_fields(): array {
	$tage = array( 'montag' => 'Montag', 'dienstag' => 'Dienstag', 'mittwoch' => 'Mittwoch', 'donnerstag' => 'Donnerstag', 'freitag' => 'Freitag', 'samstag' => 'Samstag', 'sonntag' => 'Sonntag' );
	$standort = array(
		'titel'   => array( 'label' => 'Überschrift in der Kachel', 'type' => 'text', 'help' => 'z. B. „Hauptuntersuchung Freiburg“' ),
		'hinweis' => array( 'label' => 'Aktueller Hinweis', 'type' => 'textarea', 'help' => 'Erscheint in der Standort-Kachel unter der Überschrift, z. B. geänderte Öffnungszeiten. Leer lassen = kein Hinweis.' ),
		'strasse' => array( 'label' => 'Straße', 'type' => 'text' ),
		'ort'     => array( 'label' => 'PLZ und Ort', 'type' => 'text' ),
		'telefon' => array( 'label' => 'Telefon', 'type' => 'text' ),
		'email'   => array( 'label' => 'E-Mail', 'type' => 'email' ),
		'karte'   => array( 'label' => 'Adresse für Google Maps', 'type' => 'text' ),
	);
	foreach ( $tage as $key => $label ) {
		$standort[ 'zeit_' . $key ] = array( 'label' => 'Öffnungszeit ' . $label, 'type' => 'text', 'help' => '' );
	}

	return array(
		'blatter_standort'  => $standort,
		'blatter_slide'     => array(
			'button_text' => array( 'label' => 'Button-Text', 'type' => 'text' ),
			'button_link' => array( 'label' => 'Button-Link', 'type' => 'text', 'help' => 'Ganze URL oder Sprungmarke, z. B. #termin' ),
			'karte_oben'  => array( 'label' => 'Bildkachel: kleine Zeile', 'type' => 'text' ),
			'karte_unten' => array( 'label' => 'Bildkachel: große Zeile', 'type' => 'text' ),
			'bild'        => array( 'label' => 'Bild (URL oder Mediathek-ID)', 'type' => 'text', 'help' => 'Alternativ einfach das Beitragsbild setzen.' ),
		),
		'blatter_bewertung' => array(
			'sterne' => array( 'label' => 'Sterne (1–5)', 'type' => 'number' ),
		),
	);
}

add_action(
	'init',
	function () {
		$common = array(
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'blatter',
			'show_in_rest'        => true,
			'exclude_from_search' => true,
			'capability_type'     => 'page',
			'map_meta_cap'        => true,
		);

		register_post_type(
			'blatter_standort',
			$common + array(
				'labels'   => array( 'name' => 'Standorte', 'singular_name' => 'Standort', 'add_new_item' => 'Standort hinzufügen', 'edit_item' => 'Standort bearbeiten' ),
				'supports' => array( 'title', 'page-attributes' ),
			)
		);
		register_post_type(
			'blatter_slide',
			$common + array(
				'labels'   => array( 'name' => 'Startseiten-Slider', 'singular_name' => 'Slide', 'add_new_item' => 'Slide hinzufügen', 'edit_item' => 'Slide bearbeiten' ),
				'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			)
		);
		register_post_type(
			'blatter_bewertung',
			$common + array(
				'labels'   => array( 'name' => 'Bewertungen', 'singular_name' => 'Bewertung', 'add_new_item' => 'Bewertung hinzufügen', 'edit_item' => 'Bewertung bearbeiten' ),
				'supports' => array( 'title', 'editor', 'page-attributes' ),
			)
		);

		foreach ( blatter_fields() as $type => $fields ) {
			foreach ( $fields as $key => $f ) {
				register_post_meta(
					$type,
					$key,
					array(
						'type'              => 'number' === $f['type'] ? 'integer' : 'string',
						'single'            => true,
						'show_in_rest'      => true,
						'sanitize_callback' => 'textarea' === $f['type'] ? 'sanitize_textarea_field' : ( 'number' === $f['type'] ? 'absint' : 'sanitize_text_field' ),
						'auth_callback'     => fn() => current_user_can( 'edit_pages' ),
					)
				);
			}
		}
	}
);

/** Slider und Bewertungen ohne Gutenberg – schneller zu pflegen. */
add_filter( 'use_block_editor_for_post_type', fn( $use, $type ) => in_array( $type, array( 'blatter_slide', 'blatter_bewertung', 'blatter_standort' ), true ) ? false : $use, 10, 2 );

add_action(
	'add_meta_boxes',
	function () {
		foreach ( array_keys( blatter_fields() ) as $type ) {
			add_meta_box( 'blatter_fields', __( 'Angaben', 'blatter' ), 'blatter_render_metabox', $type, 'normal', 'high' );
		}
	}
);

function blatter_render_metabox( WP_Post $post ): void {
	$fields = blatter_fields()[ $post->post_type ] ?? array();
	wp_nonce_field( 'blatter_save_fields', 'blatter_fields_nonce' );
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $fields as $key => $f ) {
		$val = get_post_meta( $post->ID, $key, true );
		$id  = 'blatter_' . $key;
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $f['label'] ) . '</label></th><td>';
		if ( 'textarea' === $f['type'] ) {
			echo '<textarea class="large-text" rows="3" id="' . esc_attr( $id ) . '" name="blatter[' . esc_attr( $key ) . ']">' . esc_textarea( $val ) . '</textarea>';
		} else {
			printf( '<input class="regular-text" type="%1$s" id="%2$s" name="blatter[%3$s]" value="%4$s"%5$s>', esc_attr( $f['type'] ), esc_attr( $id ), esc_attr( $key ), esc_attr( $val ), 'number' === $f['type'] ? ' min="1" max="5"' : '' );
		}
		if ( ! empty( $f['help'] ) ) {
			echo '<p class="description">' . esc_html( $f['help'] ) . '</p>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

add_action(
	'save_post',
	function ( $post_id, $post ) {
		if ( ! isset( $_POST['blatter_fields_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['blatter_fields_nonce'] ), 'blatter_save_fields' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$fields = blatter_fields()[ $post->post_type ] ?? array();
		$input  = isset( $_POST['blatter'] ) ? (array) wp_unslash( $_POST['blatter'] ) : array();
		foreach ( $fields as $key => $f ) {
			$raw = $input[ $key ] ?? '';
			$val = 'textarea' === $f['type'] ? sanitize_textarea_field( $raw ) : ( 'number' === $f['type'] ? absint( $raw ) : sanitize_text_field( $raw ) );
			update_post_meta( $post_id, $key, $val );
		}
	},
	10,
	2
);

/** Spalte „Reihenfolge“ in den Listen. */
foreach ( array( 'blatter_standort', 'blatter_slide', 'blatter_bewertung' ) as $blatter_type ) {
	add_filter( "manage_{$blatter_type}_posts_columns", fn( $c ) => $c + array( 'menu_order' => __( 'Reihenfolge', 'blatter' ) ) );
	add_action( "manage_{$blatter_type}_posts_custom_column", function ( $col, $id ) { if ( 'menu_order' === $col ) { echo (int) get_post_field( 'menu_order', $id ); } }, 10, 2 );
}

/** Standorte holen (sortiert), Schlüssel = Slug. */
function blatter_standorte(): array {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	$cache = array();
	$posts = get_posts( array( 'post_type' => 'blatter_standort', 'numberposts' => -1, 'orderby' => 'menu_order', 'order' => 'ASC', 'post_status' => 'publish' ) );
	$tage  = array( 'montag' => 'Montag', 'dienstag' => 'Dienstag', 'mittwoch' => 'Mittwoch', 'donnerstag' => 'Donnerstag', 'freitag' => 'Freitag', 'samstag' => 'Samstag', 'sonntag' => 'Sonntag' );
	foreach ( $posts as $p ) {
		$m     = fn( $k ) => (string) get_post_meta( $p->ID, $k, true );
		$zeiten = array();
		foreach ( $tage as $k => $label ) {
			$zeiten[ $label ] = $m( 'zeit_' . $k );
		}
		$cache[ $p->post_name ] = array(
			'name'    => get_the_title( $p ),
			'titel'   => $m( 'titel' ),
			'hinweis' => $m( 'hinweis' ),
			'strasse' => $m( 'strasse' ),
			'ort'     => $m( 'ort' ),
			'telefon' => $m( 'telefon' ),
			'email'   => $m( 'email' ),
			'karte'   => $m( 'karte' ),
			'zeiten'  => $zeiten,
		);
	}
	return $cache;
}
