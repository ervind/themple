<?php
/* Loop for pages displaying the Page Builder layout */

if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

	<article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php if ( has_post_thumbnail() ) { ?><div class="tpl-featimage tpl-limg-size-<?php echo esc_attr( tpl_get_loop_image_size() ); ?>"><?php the_post_thumbnail( tpl_get_loop_image_size() ); ?></div><?php } ?>

		<div class="tpl-grid-row">
			<h1 class="tpl-grid-column-12<?php if ( !has_post_thumbnail() ) { echo ' tpl-nofeat'; } ?>"><?php the_title(); ?></h1>
		</div>

		<?php tpl_value('page_builder'); ?>

	</article>

<?php endwhile; else: ?>

	<p><?php _e( 'Sorry, no posts matched your criteria.', 'themple-starter' ); ?></p>

<?php endif;
