<?php
/**
 * Blogbeitrag.
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'hk-article hk-article--post' ); ?>>
		<header class="hk-posthead">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="hk-posthead__media"><?php the_post_thumbnail( 'large' ); ?></div>
			<?php endif; ?>
			<div class="hk-posthead__text">
				<p class="hk-posthead__meta"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
				<h1><?php the_title(); ?></h1>
			</div>
		</header>
		<div class="hk-content hk-content--prose">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
	get_template_part( 'parts/cta' );
endwhile;

get_footer();
