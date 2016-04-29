<?php
/* Loop for pages displaying static pages */

if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

	<article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if ( has_post_thumbnail() ) { ?><div class="featimage size-<?php echo tpl_get_loop_image_size(); ?>"><?php the_post_thumbnail( tpl_get_loop_image_size() ); ?></div><?php } ?>
		<h1<?php if ( !has_post_thumbnail() ) { echo ' class="nofeat"'; } ?>><?php the_title(); ?></h1>
		<?php the_content(); ?>
		<?php wp_link_pages(); ?>
	</article>

<?php endwhile; else: ?>

	<p><?php _e( 'Sorry, no posts matched your criteria.', 'themple-starter' ); ?></p>

<?php endif;
