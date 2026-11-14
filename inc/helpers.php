<?php
/**
 * Kleine Helfer.
 */

defined( 'ABSPATH' ) || exit;

/** Theme-Option lesen (alle Einstellungen liegen in einer Option). */
function blatter_opt( string $key, $default = '' ) {
	$opts = get_option( 'blatter_settings', array() );
	return ( isset( $opts[ $key ] ) && '' !== $opts[ $key ] ) ? $opts[ $key ] : $default;
}

/** Inline-SVG-Icons (ersetzt Font Awesome). */
function blatter_icon( string $name, string $class = '' ): string {
	$icons = array(
		'check'    => '<path d="M20.3 5.3a1 1 0 0 1 0 1.4l-10.5 10.5a1 1 0 0 1-1.4 0L3.7 12.5a1 1 0 1 1 1.4-1.4l4 4 9.8-9.8a1 1 0 0 1 1.4 0z"/>',
		'phone'    => '<path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z"/>',
		'calendar' => '<path d="M7 2a1 1 0 0 1 1 1v1h8V3a1 1 0 1 1 2 0v1h1a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h1V3a1 1 0 0 1 1-1zm13 8H4v9a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-9zM8 12v4h4v-4H8z"/>',
		'pin'      => '<path d="M12 1.5a6.5 6.5 0 0 0-1.2 12.9V22a1.2 1.2 0 0 0 2.4 0v-7.6A6.5 6.5 0 0 0 12 1.5zm-1.6 3.1a2 2 0 1 1-1.8 1.8 2 2 0 0 1 1.8-1.8z"/>',
		'star'     => '<path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4 6.1 20.5l1.2-6.5L2.5 9.4l6.6-.9z"/>',
		'chev-l'   => '<path d="M15.4 4.6a1 1 0 0 1 0 1.4L9.4 12l6 6a1 1 0 1 1-1.4 1.4l-6.7-6.7a1 1 0 0 1 0-1.4l6.7-6.7a1 1 0 0 1 1.4 0z"/>',
		'chev-r'   => '<path d="M8.6 4.6a1 1 0 0 1 1.4 0l6.7 6.7a1 1 0 0 1 0 1.4L10 19.4A1 1 0 1 1 8.6 18l6-6-6-6a1 1 0 0 1 0-1.4z"/>',
		'chev-d'   => '<path d="M4.6 8.6a1 1 0 0 1 1.4 0l6 6 6-6a1 1 0 1 1 1.4 1.4l-6.7 6.7a1 1 0 0 1-1.4 0L4.6 10a1 1 0 0 1 0-1.4z"/>',
		'car'      => '<path d="M5.5 5h13l2.3 5.4A2 2 0 0 1 22 12v5a1 1 0 0 1-1 1h-1a2.5 2.5 0 0 1-5 0H9a2.5 2.5 0 0 1-5 0H3a1 1 0 0 1-1-1v-5a2 2 0 0 1 1.2-1.6zm1.3 2-1.6 3.8h13.6L17.2 7zM6.5 13a1.3 1.3 0 1 0 0 2.6 1.3 1.3 0 0 0 0-2.6zm11 0a1.3 1.3 0 1 0 0 2.6 1.3 1.3 0 0 0 0-2.6z"/>',
		'moto'     => '<path d="M14.5 5h2.2l1.5 3.6A4.5 4.5 0 1 1 15 12.7l-.8.8h-4.4A4.5 4.5 0 1 1 7.6 9l1-1h4.3l-.9-1.5h-1.5V5zm-9 6a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zm13 0a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zm-2.6-1.2 1 2.4h1.1l-1-2.6z"/>',
		'trailer'  => '<path d="M2 5h16v9h4v2h-4.2a2.5 2.5 0 0 1-4.6 0H2zm2 2v5h2V7zm4 0v5h2V7zm4 0v5h2V7zM15.5 14.5a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>',
		'google'   => '<path fill="#4285F4" d="M22.6 12.2c0-.8-.1-1.5-.2-2.2H12v4.2h6a5 5 0 0 1-2.2 3.3v2.7h3.6c2-1.9 3.2-4.7 3.2-8z"/><path fill="#34A853" d="M12 23c3 0 5.5-1 7.4-2.7l-3.6-2.8c-1 .7-2.3 1.1-3.8 1.1-2.9 0-5.4-2-6.3-4.6H2v2.9A11 11 0 0 0 12 23z"/><path fill="#FBBC05" d="M5.7 14a6.6 6.6 0 0 1 0-4.1V7H2a11 11 0 0 0 0 9.9z"/><path fill="#EA4335" d="M12 5.4c1.6 0 3.1.6 4.3 1.7l3.2-3.2A11 11 0 0 0 2 7l3.7 2.9C6.6 7.3 9.1 5.4 12 5.4z"/>',
		'menu'     => '<path d="M3 6h18v2H3zm0 5h18v2H3zm0 5h18v2H3z"/>',
		'close'    => '<path d="M6.4 5l5.6 5.6L17.6 5 19 6.4 13.4 12l5.6 5.6-1.4 1.4-5.6-5.6L6.4 19 5 17.6l5.6-5.6L5 6.4z"/>',
	);
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}
	return sprintf( '<svg class="hk-icon hk-icon--%1$s %2$s" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">%3$s</svg>', esc_attr( $name ), esc_attr( $class ), $icons[ $name ] );
}

/** Telefonnummer als tel:-Link. */
function blatter_tel( string $number ): string {
	$digits = preg_replace( '/[^0-9+]/', '', $number );
	if ( str_starts_with( $digits, '0' ) ) {
		$digits = '+49' . substr( $digits, 1 );
	}
	return 'tel:' . $digits;
}

/** Interne Links relativ zur aktuellen Installation (Staging/Live). */
function blatter_url( string $url ): string {
	if ( str_starts_with( $url, '/' ) ) {
		return home_url( $url );
	}
	return $url;
}

/** Bild-URL aus der Mediathek (relativ gespeichert). */
function blatter_img( string $path ): string {
	if ( '' === $path ) {
		return '';
	}
	if ( is_numeric( $path ) ) {
		return (string) wp_get_attachment_image_url( (int) $path, 'large' );
	}
	return blatter_url( $path );
}
