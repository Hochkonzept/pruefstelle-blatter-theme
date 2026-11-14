<?php
/**
 * 404.
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="hk-404">
	<h1>Seite nicht gefunden</h1>
	<p>Die Seite gibt es nicht (mehr). Hier geht’s weiter:</p>
	<div class="wp-block-buttons">
		<div class="wp-block-button"><a class="wp-block-button__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">Zur Startseite</a></div>
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link" href="<?php echo esc_url( home_url( '/#termin' ) ); ?>">Termin buchen</a></div>
	</div>
</section>
<?php
get_footer();
