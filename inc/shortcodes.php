<?php
/**
 * Wiederkehrende Sektionen als Shortcodes – Inhalte kommen aus Standorten,
 * Slider, Bewertungen und den Einstellungen.
 */

defined( 'ABSPATH' ) || exit;

/** Template-Teil puffern. */
function blatter_part( string $slug, array $args = array() ): string {
	ob_start();
	get_template_part( 'parts/' . $slug, null, $args );
	return (string) ob_get_clean();
}

add_shortcode( 'blatter_slider', fn() => blatter_part( 'slider' ) );
add_shortcode( 'blatter_bewertungen', fn() => blatter_part( 'bewertungen' ) );
add_shortcode( 'blatter_ortswappen', fn() => blatter_part( 'ortswappen' ) );

add_shortcode(
	'blatter_termin',
	function ( $atts ) {
		$a = shortcode_atts( array( 'titel' => '', 'text' => '', 'anker' => 'termin' ), $atts );
		return blatter_part( 'termin', $a );
	}
);

add_shortcode(
	'blatter_preise',
	function ( $atts ) {
		$a = shortcode_atts( array( 'set' => 'standard', 'buttons' => '', 'titel' => 'Preise' ), $atts );
		return blatter_part( 'preise', $a );
	}
);

add_shortcode(
	'blatter_standorte',
	function ( $atts ) {
		$a = shortcode_atts( array( 'orte' => '', 'anker' => 'Kontakt' ), $atts );
		return blatter_part( 'standorte', $a );
	}
);

add_shortcode(
	'blatter_grundsaetze',
	fn() => blatter_part( 'grundsaetze' )
);

add_shortcode(
	'blatter_beitraege',
	function ( $atts ) {
		$a = shortcode_atts( array( 'anzahl' => 9 ), $atts );
		return blatter_part( 'beitraege', $a );
	}
);

add_shortcode(
	'blatter_jobs',
	function ( $atts ) {
		$a = shortcode_atts( array( 'pfade' => '', 'kategorie' => '' ), $atts );
		return blatter_part( 'jobs', $a );
	}
);

/** Shortcode-Blöcke nicht in <p> wickeln. */
add_filter( 'the_content', 'shortcode_unautop', 11 );
