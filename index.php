<?php
/**
 * Fallback: Beitragsliste (Archive, Suche).
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="hk-pagehead">
	<h1><?php echo is_search() ? esc_html( sprintf( 'Suche: %s', get_search_query() ) ) : wp_kses_post( get_the_archive_title() ?: 'Blog' ); ?></h1>
</div>
<div class="hk-posts">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			get_template_part( 'parts/beitrag-kachel' );
		endwhile;
	else :
		echo '<p>' . esc_html__( 'Keine Beiträge gefunden.', 'blatter' ) . '</p>';
	endif;
	?>
</div>
<div class="hk-pagination"><?php the_posts_pagination(); ?></div>
<?php
get_footer();
