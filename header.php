<?php
/**
 * Kopf: Info-Leiste, Logo, Navigation, Lesefortschritt.
 */

defined( 'ABSPATH' ) || exit;
$blatter_news = blatter_opt( 'news' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="hk-skip" href="#inhalt"><?php esc_html_e( 'Zum Inhalt springen', 'blatter' ); ?></a>

<div class="hk-topbar">
	<div class="hk-topbar__inner">
		<div class="hk-topbar__news">
			<?php if ( $blatter_news ) : ?>
				<a href="<?php echo esc_url( blatter_url( blatter_opt( 'news_link', '/' ) ) ); ?>"><?php echo esc_html( $blatter_news ); ?></a>
			<?php endif; ?>
		</div>
		<div class="hk-topbar__logo">
			<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2019/01/küs_logo.png' ) ); ?>" alt="KÜS" width="40" height="22">
		</div>
		<ul class="hk-topbar__phones">
			<?php foreach ( blatter_standorte() as $blatter_s ) : ?>
				<li><a href="<?php echo esc_attr( blatter_tel( $blatter_s['telefon'] ) ); ?>"><?php echo blatter_icon( 'phone' ); ?><?php echo esc_html( $blatter_s['name'] . ': ' . str_replace( '/', '-', $blatter_s['telefon'] ) ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

<header class="hk-header" id="kopf">
	<div class="hk-header__inner">
		<a class="hk-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php
			if ( has_custom_logo() ) {
				echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'medium', false, array( 'alt' => get_bloginfo( 'name' ) ) );
			} else {
				printf( '<img src="%s" alt="%s" width="190" height="42">', esc_url( BLATTER_URI . '/assets/img/logo.png' ), esc_attr( get_bloginfo( 'name' ) ) );
			}
			?>
		</a>
		<button class="hk-burger" type="button" aria-expanded="false" aria-controls="hk-nav" aria-label="<?php esc_attr_e( 'Menü', 'blatter' ); ?>">
			<?php echo blatter_icon( 'menu', 'hk-burger__open' ) . blatter_icon( 'close', 'hk-burger__close' ); ?>
		</button>
		<nav class="hk-nav" id="hk-nav" aria-label="<?php esc_attr_e( 'Hauptmenü', 'blatter' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'hk-nav__list',
					'fallback_cb'    => false,
					'depth'          => 2,
				)
			);
			?>
		</nav>
	</div>
	<div class="hk-progress" aria-hidden="true"><span></span></div>
</header>

<main id="inhalt" class="hk-main">
