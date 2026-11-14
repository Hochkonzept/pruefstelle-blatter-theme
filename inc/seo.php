<?php
/**
 * Strukturierte Daten für die drei Standorte (AutomotiveBusiness).
 * Titel/Meta-Descriptions bleiben bei Yoast SEO.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_head',
	function () {
		if ( ! is_front_page() && ! is_page( 'hauptuntersuchung' ) && ! is_page() ) {
			return;
		}
		$map  = array( 'Montag' => 'Mo', 'Dienstag' => 'Tu', 'Mittwoch' => 'We', 'Donnerstag' => 'Th', 'Freitag' => 'Fr', 'Samstag' => 'Sa', 'Sonntag' => 'Su' );
		$data = array();
		foreach ( blatter_standorte() as $s ) {
			$hours = array();
			foreach ( $s['zeiten'] as $tag => $zeit ) {
				if ( preg_match_all( '/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/', $zeit, $m, PREG_SET_ORDER ) ) {
					foreach ( $m as $r ) {
						$hours[] = sprintf( '%s %s-%s', $map[ $tag ], str_pad( $r[1], 5, '0', STR_PAD_LEFT ), str_pad( $r[2], 5, '0', STR_PAD_LEFT ) );
					}
				}
			}
			$plz_ort = explode( ' ', $s['ort'], 2 );
			$data[]  = array(
				'@type'        => 'AutomotiveBusiness',
				'name'         => 'KÜS KFZ-Prüfstelle Blatter ' . $s['name'],
				'url'          => home_url( '/' ),
				'telephone'    => $s['telefon'],
				'email'        => $s['email'],
				'address'      => array(
					'@type'           => 'PostalAddress',
					'streetAddress'   => $s['strasse'],
					'postalCode'      => $plz_ort[0] ?? '',
					'addressLocality' => $plz_ort[1] ?? '',
					'addressCountry'  => 'DE',
				),
				'openingHours' => $hours,
			);
		}
		if ( ! $data ) {
			return;
		}
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@graph' => $data ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "</script>\n";
	},
	30
);
