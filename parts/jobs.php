<?php
/**
 * Offene Stellen (Karriere) – Beiträge per Pfad oder Kategorie.
 *
 * @var array $args pfade, kategorie
 */

defined( 'ABSPATH' ) || exit;
$blatter_ids = array();
foreach ( array_filter( array_map( 'trim', explode( ',', (string) $args['pfade'] ) ) ) as $blatter_p ) {
	$blatter_id = url_to_postid( home_url( $blatter_p ) );
	if ( $blatter_id ) {
		$blatter_ids[] = $blatter_id;
	}
}
$blatter_query = array( 'post_type' => 'post', 'posts_per_page' => 12, 'ignore_sticky_posts' => true );
if ( $blatter_ids ) {
	$blatter_query['post__in'] = $blatter_ids;
	$blatter_query['orderby']  = 'post__in';
} elseif ( $args['kategorie'] ) {
	$blatter_query['category_name'] = $args['kategorie'];
} else {
	return;
}
$blatter_q = new WP_Query( $blatter_query );
?>
<div class="hk-posts hk-posts--jobs">
	<?php
	while ( $blatter_q->have_posts() ) :
		$blatter_q->the_post();
		?>
		<article class="hk-postcard">
			<a class="hk-postcard__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); ?></a>
			<div class="hk-postcard__body">
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<a class="hk-btn" href="<?php the_permalink(); ?>">Jetzt bewerben</a>
			</div>
		</article>
		<?php
	endwhile;
	wp_reset_postdata();
	?>
</div>
