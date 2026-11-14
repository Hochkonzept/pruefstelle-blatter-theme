<?php
/**
 * Fuß: Logo, Text, Karte, Menü, Copyright.
 */

defined( 'ABSPATH' ) || exit;
$blatter_karte = blatter_opt( 'fuss_karte', 'Bötzinger Str. 12, 79111 Freiburg im Breisgau' );
?>
</main>

<footer class="hk-footer">
	<div class="hk-footer__top">
		<div class="hk-footer__brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hk-footer__logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<?php echo file_get_contents( BLATTER_DIR . '/assets/img/logo-footer.svg' ); // phpcs:ignore -- eigenes SVG. ?>
			</a>
			<p><?php echo esc_html( blatter_opt( 'fuss_text', 'Das Team von der KFZ-Prüfstelle Blatter freut sich auf deine nächste Hauptuntersuchung.' ) ); ?></p>
		</div>
		<div class="hk-footer__map">
			<iframe loading="lazy" title="<?php echo esc_attr( $blatter_karte ); ?>" src="https://maps.google.com/maps?q=<?php echo rawurlencode( $blatter_karte ); ?>&amp;t=m&amp;z=13&amp;output=embed&amp;iwloc=near"></iframe>
		</div>
	</div>
	<nav class="hk-footer__nav" aria-label="<?php esc_attr_e( 'Footer-Menü', 'blatter' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'footer',
				'container'      => false,
				'menu_class'     => 'hk-footer__list',
				'fallback_cb'    => false,
				'depth'          => 3,
			)
		);
		?>
	</nav>
	<div class="hk-footer__copy">
		<p>© All Rights Reserved. <?php echo esc_html( wp_date( 'Y' ) ); ?></p>
		<p>MADE WITH <span class="hk-heart" aria-label="Liebe">❤</span> BY <a href="https://hochkonzept.de" target="_blank" rel="noopener">Hochkonzept</a></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
