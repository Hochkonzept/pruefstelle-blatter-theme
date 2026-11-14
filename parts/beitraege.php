<?php
/**
 * Blog-Übersicht (Shortcode auf der Seite „Blog“).
 *
 * @var array $args anzahl
 */

defined( 'ABSPATH' ) || exit;
$blatter_paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$blatter_q     = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => (int) $args['anzahl'],
		'paged'          => $blatter_paged,
		'ignore_sticky_posts' => true,
	)
);
?>
<div class="hk-posts">
	<?php
	while ( $blatter_q->have_posts() ) :
		$blatter_q->the_post();
		get_template_part( 'parts/beitrag-kachel' );
	endwhile;
	wp_reset_postdata();
	?>
</div>
<?php if ( $blatter_q->max_num_pages > 1 ) : ?>
	<nav class="hk-pagination"><?php echo paginate_links( array( 'total' => $blatter_q->max_num_pages, 'current' => $blatter_paged ) ); // phpcs:ignore ?></nav>
<?php endif; ?>
