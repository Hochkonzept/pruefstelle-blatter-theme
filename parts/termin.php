<?php
/**
 * Terminbuchung: Ortswappen, Überschrift, Buchungskalender (Simply Schedule Appointments).
 *
 * @var array $args titel, text, anker
 */

defined( 'ABSPATH' ) || exit;
$blatter_sc = blatter_opt( 'termin_shortcode', '[ssa_booking]' );
?>
<section class="hk-termin" id="<?php echo esc_attr( $args['anker'] ?: 'termin' ); ?>">
	<?php get_template_part( 'parts/ortswappen' ); ?>
	<?php if ( $args['titel'] ) : ?>
		<h2 class="hk-termin__title"><?php echo wp_kses_post( $args['titel'] ); ?></h2>
	<?php endif; ?>
	<?php if ( $args['text'] ) : ?>
		<p class="hk-termin__text"><?php echo wp_kses_post( $args['text'] ); ?></p>
	<?php endif; ?>
	<div class="hk-termin__booking">
		<?php echo do_shortcode( $blatter_sc ); ?>
	</div>
</section>
