<?php
/**
 * Grundsätze (falls als Shortcode genutzt; auf den Seiten liegt die Sektion als Blockgruppe).
 */

defined( 'ABSPATH' ) || exit;
$blatter_punkte = array_filter( array_map( 'trim', explode( "\n", (string) blatter_opt( 'grundsaetze_punkte', "Unkompliziert\nFreundlich\nSchnell" ) ) ) );
?>
<section class="wp-block-group hk-split">
	<figure class="wp-block-image hk-split__media"><img src="<?php echo esc_url( blatter_img( blatter_opt( 'grundsaetze_bild', '/wp-content/uploads/2024/02/eddi-schluesseluebergabe.jpg' ) ) ); ?>" alt="Schlüsselübergabe an der Prüfstelle" loading="lazy"></figure>
	<div class="wp-block-group hk-split__content">
		<h2 class="wp-block-heading"><?php echo esc_html( blatter_opt( 'grundsaetze_titel', 'Das sind unsere Grundsätze' ) ); ?></h2>
		<ul class="wp-block-list is-style-check">
			<?php foreach ( $blatter_punkte as $blatter_p ) : ?><li><?php echo esc_html( $blatter_p ); ?></li><?php endforeach; ?>
		</ul>
		<div class="wp-block-buttons"><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#termin">Termin</a></div></div>
	</div>
</section>
