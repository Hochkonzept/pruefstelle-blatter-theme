<?php
/**
 * Startseiten-Slider: roter Textkasten links, Bildkacheln rechts.
 * Inhalte: Prüfstelle → Startseiten-Slider (Reihenfolge = Feld „Reihenfolge“).
 */

defined( 'ABSPATH' ) || exit;

$blatter_slides = get_posts( array( 'post_type' => 'blatter_slide', 'numberposts' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
if ( ! $blatter_slides ) {
	return;
}
$blatter_bg = blatter_img( (string) get_post_meta( $blatter_slides[0]->ID, 'bild', true ) );
?>
<section class="hk-slider" data-hk-slider aria-roledescription="carousel" aria-label="Aktuelles">
	<div class="hk-slider__bg" aria-hidden="true">
		<?php foreach ( $blatter_slides as $blatter_i => $blatter_s ) : ?>
			<?php $blatter_img = has_post_thumbnail( $blatter_s ) ? get_the_post_thumbnail_url( $blatter_s, 'full' ) : blatter_img( (string) get_post_meta( $blatter_s->ID, 'bild', true ) ); ?>
			<div class="hk-slider__bgimg<?php echo 0 === $blatter_i ? ' is-active' : ''; ?>" style="background-image:url('<?php echo esc_url( $blatter_img ); ?>')"></div>
		<?php endforeach; ?>
	</div>

	<div class="hk-slider__box">
		<div class="hk-slider__texts" aria-live="polite">
			<?php foreach ( $blatter_slides as $blatter_i => $blatter_s ) : ?>
				<?php
				$blatter_btn  = (string) get_post_meta( $blatter_s->ID, 'button_text', true );
				$blatter_link = (string) get_post_meta( $blatter_s->ID, 'button_link', true );
				?>
				<div class="hk-slider__text<?php echo 0 === $blatter_i ? ' is-active' : ''; ?>" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( ( $blatter_i + 1 ) . ' von ' . count( $blatter_slides ) ); ?>"<?php echo 0 === $blatter_i ? '' : ' hidden'; ?>>
					<h2><?php echo esc_html( get_the_title( $blatter_s ) ); ?></h2>
					<div class="hk-slider__copy"><?php echo wp_kses_post( wpautop( $blatter_s->post_content ) ); ?></div>
					<?php if ( $blatter_btn ) : ?>
						<a class="hk-btn hk-btn--ghost" href="<?php echo esc_url( blatter_url( $blatter_link ) ); ?>"<?php echo str_starts_with( $blatter_link, 'http' ) && ! str_contains( $blatter_link, wp_parse_url( home_url(), PHP_URL_HOST ) ) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $blatter_btn ); ?></a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="hk-slider__controls">
			<button type="button" class="hk-slider__prev" aria-label="Vorheriges"><?php echo blatter_icon( 'chev-l' ); ?></button>
			<button type="button" class="hk-slider__next" aria-label="Nächstes"><?php echo blatter_icon( 'chev-r' ); ?></button>
		</div>
	</div>

	<div class="hk-slider__cards" aria-hidden="true">
		<div class="hk-slider__track">
			<?php foreach ( $blatter_slides as $blatter_i => $blatter_s ) : ?>
				<?php $blatter_img = has_post_thumbnail( $blatter_s ) ? get_the_post_thumbnail_url( $blatter_s, 'medium_large' ) : blatter_img( (string) get_post_meta( $blatter_s->ID, 'bild', true ) ); ?>
				<button type="button" class="hk-slider__card<?php echo 0 === $blatter_i ? ' is-active' : ''; ?>" data-index="<?php echo (int) $blatter_i; ?>" tabindex="-1" style="background-image:url('<?php echo esc_url( $blatter_img ); ?>')">
					<span class="hk-slider__card-top"><?php echo esc_html( (string) get_post_meta( $blatter_s->ID, 'karte_oben', true ) ); ?></span>
					<span class="hk-slider__card-main"><?php echo esc_html( (string) get_post_meta( $blatter_s->ID, 'karte_unten', true ) ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>
	</div>

	<img class="hk-slider__kues" src="<?php echo esc_url( home_url( '/wp-content/uploads/2025/03/KUES-Logo-weiss-weiss-5534fe.svg' ) ); ?>" alt="KÜS" width="90" height="40">
</section>
