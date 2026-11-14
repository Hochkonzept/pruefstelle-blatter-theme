<?php
/**
 * Standort-Kacheln: roter Kopf mit Pin, Kontakt, Öffnungszeiten, Karte.
 *
 * @var array $args orte (Slugs, Komma), anker
 */

defined( 'ABSPATH' ) || exit;
$blatter_all  = blatter_standorte();
$blatter_want = array_filter( array_map( 'trim', explode( ',', (string) $args['orte'] ) ) );
$blatter_list = $blatter_want ? array_intersect_key( $blatter_all, array_flip( $blatter_want ) ) : $blatter_all;
if ( $blatter_want ) {
	$blatter_list = array_replace( array_flip( array_intersect( $blatter_want, array_keys( $blatter_list ) ) ), $blatter_list );
}
if ( ! $blatter_list ) {
	return;
}
?>
<section class="hk-standorte hk-standorte--<?php echo (int) count( $blatter_list ); ?>" id="<?php echo esc_attr( $args['anker'] ?: 'Kontakt' ); ?>">
	<span id="about" class="hk-anchor"></span>
	<div class="hk-standorte__grid">
		<?php foreach ( $blatter_list as $blatter_s ) : ?>
			<article class="hk-standort" data-hk-reveal>
				<header class="hk-standort__head">
					<?php echo blatter_icon( 'pin' ); ?>
					<h2><?php echo esc_html( $blatter_s['name'] ); ?></h2>
				</header>
				<div class="hk-standort__body">
					<h3 class="hk-standort__title"><?php echo esc_html( $blatter_s['titel'] ?: 'Hauptuntersuchung ' . $blatter_s['name'] ); ?></h3>
					<?php if ( $blatter_s['hinweis'] ) : ?>
						<p class="hk-standort__hinweis"><?php echo nl2br( esc_html( $blatter_s['hinweis'] ) ); ?></p>
					<?php endif; ?>

					<h4>Kontakt</h4>
					<p class="hk-standort__kontakt">
						<?php echo esc_html( $blatter_s['strasse'] ); ?><br>
						<?php echo esc_html( $blatter_s['ort'] ); ?><br>
						Tel: <a href="<?php echo esc_attr( blatter_tel( $blatter_s['telefon'] ) ); ?>"><?php echo esc_html( $blatter_s['telefon'] ); ?></a><br>
						Email: <a href="mailto:<?php echo esc_attr( $blatter_s['email'] ); ?>"><?php echo esc_html( $blatter_s['email'] ); ?></a>
					</p>

					<h4>Öffnungszeiten</h4>
					<dl class="hk-zeiten">
						<?php foreach ( $blatter_s['zeiten'] as $blatter_tag => $blatter_zeit ) : ?>
							<div><dt><?php echo esc_html( $blatter_tag ); ?></dt><dd><?php echo esc_html( $blatter_zeit ?: 'Geschlossen' ); ?></dd></div>
						<?php endforeach; ?>
					</dl>

					<h4>Standort</h4>
					<div class="hk-standort__map">
						<iframe loading="lazy" title="<?php echo esc_attr( $blatter_s['karte'] ); ?>" src="https://maps.google.com/maps?q=<?php echo rawurlencode( $blatter_s['karte'] ?: $blatter_s['strasse'] . ' ' . $blatter_s['ort'] ); ?>&amp;t=m&amp;z=10&amp;output=embed&amp;iwloc=near"></iframe>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>
