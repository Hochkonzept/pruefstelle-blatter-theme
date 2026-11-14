<?php
/**
 * Bewertungs-Karussell + Google-Badge.
 */

defined( 'ABSPATH' ) || exit;
$blatter_reviews = get_posts( array( 'post_type' => 'blatter_bewertung', 'numberposts' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
if ( ! $blatter_reviews ) {
	return;
}
$blatter_schnitt = blatter_opt( 'google_schnitt', '4.9' );
$blatter_anzahl  = blatter_opt( 'google_anzahl' );
$blatter_link    = blatter_opt( 'google_link' );
?>
<div class="hk-carousel" data-hk-carousel>
	<button type="button" class="hk-carousel__nav hk-carousel__prev" aria-label="Vorherige Bewertung"><?php echo blatter_icon( 'chev-l' ); ?></button>
	<div class="hk-carousel__viewport">
		<ul class="hk-carousel__track">
			<?php foreach ( $blatter_reviews as $blatter_r ) : ?>
				<?php $blatter_st = max( 1, min( 5, (int) get_post_meta( $blatter_r->ID, 'sterne', true ) ?: 5 ) ); ?>
				<li class="hk-review">
					<div class="hk-review__stars" role="img" aria-label="<?php echo esc_attr( 'Bewertet mit ' . $blatter_st . ' von 5' ); ?>">
						<?php echo str_repeat( blatter_icon( 'star' ), $blatter_st ); // phpcs:ignore ?>
					</div>
					<blockquote><?php echo esc_html( wp_strip_all_tags( $blatter_r->post_content ) ); ?></blockquote>
					<p class="hk-review__name"><?php echo esc_html( get_the_title( $blatter_r ) ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<button type="button" class="hk-carousel__nav hk-carousel__next" aria-label="Nächste Bewertung"><?php echo blatter_icon( 'chev-r' ); ?></button>
</div>
<?php if ( $blatter_anzahl ) : ?>
	<a class="hk-google" href="<?php echo esc_url( $blatter_link ); ?>" target="_blank" rel="noopener nofollow">
		<?php echo blatter_icon( 'google' ); ?>
		<strong><?php echo esc_html( $blatter_schnitt ); ?></strong>
		<span class="hk-google__stars"><?php echo str_repeat( blatter_icon( 'star' ), 5 ); // phpcs:ignore ?></span>
		<span><?php echo esc_html( $blatter_anzahl ); ?> Bewertungen</span>
	</a>
<?php endif; ?>
