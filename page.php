<?php
/**
 * Seiten: Inhalt aus dem Block-Editor (Sektionen + Shortcodes).
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$blatter_hero = blatter_page_has_hero();
	?>
	<article <?php post_class( $blatter_hero ? 'hk-article hk-article--sections' : 'hk-article hk-article--text' ); ?>>
		<?php if ( ! $blatter_hero ) : ?>
			<header class="hk-pagehead"><h1><?php the_title(); ?></h1></header>
		<?php endif; ?>
		<div class="hk-content">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
