<?php
/**
 * Beitragskachel.
 */

defined( 'ABSPATH' ) || exit;
?>
<article class="hk-postcard">
	<a class="hk-postcard__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); } ?>
	</a>
	<div class="hk-postcard__body">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<p class="hk-postcard__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
		<a class="hk-postcard__more" href="<?php the_permalink(); ?>">Zum Artikel »</a>
	</div>
</article>
