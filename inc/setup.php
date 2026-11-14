<?php
/**
 * Theme-Setup, Assets, Menüs, Editor.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'blatter', BLATTER_DIR . '/languages' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 300, 'flex-height' => true, 'flex-width' => true ) );
		remove_theme_support( 'core-block-patterns' );
		add_editor_style( 'assets/css/main.css' );

		register_nav_menus(
			array(
				'primary' => __( 'Hauptmenü', 'blatter' ),
				'footer'  => __( 'Footer-Menü', 'blatter' ),
			)
		);
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$css = BLATTER_DIR . '/assets/css/main.css';
		$js  = BLATTER_DIR . '/assets/js/main.js';
		wp_enqueue_style( 'blatter', BLATTER_URI . '/assets/css/main.css', array(), BLATTER_VERSION . '.' . filemtime( $css ) );
		wp_enqueue_script( 'blatter', BLATTER_URI . '/assets/js/main.js', array(), BLATTER_VERSION . '.' . filemtime( $js ), array( 'strategy' => 'defer', 'in_footer' => true ) );

		// Unnötigen Ballast entfernen.
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'global-styles' );
	},
	20
);

/** Fonts früh laden. */
add_action(
	'wp_head',
	function () {
		printf( '<link rel="preload" href="%s" as="font" type="font/ttf" crossorigin>' . "\n", esc_url( BLATTER_URI . '/assets/fonts/Gabarito.ttf' ) );
	},
	1
);

/** Block-Stile für Listen (Häkchen) und Buttons. */
add_action(
	'init',
	function () {
		register_block_style( 'core/list', array( 'name' => 'check', 'label' => __( 'Häkchen rot', 'blatter' ) ) );
		register_block_style( 'core/button', array( 'name' => 'outline', 'label' => __( 'Umriss', 'blatter' ) ) );
		register_block_style( 'core/group', array( 'name' => 'card', 'label' => __( 'Karte mit Schatten', 'blatter' ) ) );
	}
);

/** Emojis und oEmbed-Discovery raus – schneller Kopf. */
add_action(
	'init',
	function () {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
	}
);

/** Body-Klassen. */
add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_singular( 'page' ) && ! is_front_page() ) {
			$classes[] = 'hk-page';
		}
		return $classes;
	}
);

/** Seiten ohne Titelzeile: Inhalt beginnt mit Hero (Seiten-Editor zeigt Titel trotzdem). */
function blatter_page_has_hero( $post = null ): bool {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}
	return str_contains( $post->post_content, 'hk-hero' ) || str_contains( $post->post_content, '[blatter_slider' );
}

/** Walker-loses Menü mit Dropdown-Klassen. */
add_filter(
	'nav_menu_css_class',
	function ( $classes, $item ) {
		if ( in_array( 'menu-item-has-children', $classes, true ) ) {
			$classes[] = 'has-dropdown';
		}
		// Sprungmarken (/#Preise, /#Kontakt) nie als „aktuell“ markieren.
		if ( 'custom' === $item->type && str_contains( (string) $item->url, '#' ) ) {
			$classes = array_diff( $classes, array( 'current-menu-item', 'current_page_item', 'current-menu-ancestor', 'current-page-ancestor' ) );
		}
		return $classes;
	},
	10,
	2
);

add_filter(
	'walker_nav_menu_start_el',
	function ( $output, $item, $depth, $args ) {
		if ( 'primary' === ( $args->theme_location ?? '' ) && in_array( 'menu-item-has-children', (array) $item->classes, true ) && 0 === $depth ) {
			$output .= '<button class="hk-nav__toggle" type="button" aria-expanded="false" aria-label="' . esc_attr__( 'Untermenü öffnen', 'blatter' ) . '">' . blatter_icon( 'chev-d' ) . '</button>';
		}
		return $output;
	},
	10,
	4
);

/** Auszugslänge Blog-Kacheln. */
add_filter( 'excerpt_length', fn() => 22 );
add_filter( 'excerpt_more', fn() => ' …' );
