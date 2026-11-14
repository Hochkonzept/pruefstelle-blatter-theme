<?php
/**
 * Reihe der Ortswappen (Lörrach, Freiburg, Waldshut, Säckingen, Emmendingen).
 */

defined( 'ABSPATH' ) || exit;
$blatter_wappen = array( 'LOe_ohneschrif', 'FR_ohne-schrift', 'WT_ohneschrift', 'saek_ohneschrif', 'EM_ohneschrif' );
?>
<div class="hk-wappen" aria-hidden="true">
	<?php foreach ( $blatter_wappen as $blatter_w ) : ?>
		<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2023/11/' . $blatter_w . '.svg' ) ); ?>" alt="" width="64" height="64" loading="lazy">
	<?php endforeach; ?>
</div>
