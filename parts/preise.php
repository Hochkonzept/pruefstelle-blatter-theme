<?php
/**
 * Preiskarten mit hochzählender Zahl.
 *
 * @var array $args set, buttons, titel
 */

defined( 'ABSPATH' ) || exit;
$blatter_cards = blatter_preise( $args['set'] );
if ( ! $blatter_cards ) {
	return;
}
?>
<section class="hk-preise" id="preise">
	<span id="Preise" class="hk-anchor"></span>
	<h2><?php echo esc_html( $args['titel'] ); ?></h2>
	<div class="hk-preise__grid">
		<?php foreach ( $blatter_cards as $blatter_c ) : ?>
			<article class="hk-preis" data-hk-reveal>
				<header class="hk-preis__head">
					<h3><?php echo esc_html( $blatter_c['titel'] ); ?></h3>
					<?php echo blatter_icon( $blatter_c['icon'] ?: 'car', 'hk-preis__icon' ); ?>
				</header>
				<div class="hk-preis__body">
					<p class="hk-preis__price">
						<?php if ( $blatter_c['prefix'] ) : ?><span class="hk-preis__prefix"><?php echo esc_html( $blatter_c['prefix'] ); ?></span><?php endif; ?>
						<span class="hk-preis__num" data-hk-count="<?php echo esc_attr( $blatter_c['preis'] ); ?>"><?php echo esc_html( $blatter_c['preis'] ); ?></span><span class="hk-preis__cur">€</span>
					</p>
					<p class="hk-preis__note"><?php echo esc_html( $blatter_c['hinweis'] ); ?></p>
					<hr>
					<ul class="hk-check">
						<?php foreach ( $blatter_c['punkte'] as $blatter_p ) : ?>
							<li><?php echo blatter_icon( 'check' ); ?><span><?php echo esc_html( $blatter_p ); ?></span></li>
						<?php endforeach; ?>
					</ul>
					<?php if ( $args['buttons'] ) : ?>
						<a class="hk-btn" href="#termin"><?php echo blatter_icon( 'calendar' ); ?>Termin buchen</a>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>
