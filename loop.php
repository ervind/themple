<?php
/* Loop for pages displaying single post */

if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if ( has_post_thumbnail() ) { ?><div class="featimage size-<?php echo tpl_get_loop_image_size(); ?>"><?php the_post_thumbnail( tpl_get_loop_image_size() ); ?></div><?php } ?>
		<h1<?php if ( !has_post_thumbnail() ) { echo ' class="nofeat"'; } ?>><?php the_title(); ?></h1>
		<?php the_content(); ?>
		<?php wp_link_pages(); ?>
		<?php if ( tpl_get_value ( 'source' ) ) {
			echo '<p>'. esc_html( tpl_get_value ( 'source_label' ) ) .' <a href="'. esc_url( tpl_get_value ( 'source' ) ) .'">'. esc_html( tpl_get_value ( 'source' ) ) .'</a></p>';
		} ?>
		<aside class="postmeta"><?php
			$author = get_the_author();
			$date = get_the_date( 'M j, Y' );
			printf ( __('Posted by %1$s on %2$s' ,'themple-starter'), $author, $date ); ?>
		</aside>
		<aside class="tags">
			<?php the_tags(); ?>
		</aside>
		<aside class="categories">
			<h6><?php _e( 'Categories', 'themple-starter' ); ?></h6>
			<?php the_category(); ?>
		</aside>
	</article>

<?php endwhile; else: ?>

	<p><?php _e( 'Sorry, no posts matched your criteria.', 'themple-starter' ); ?></p>

<?php endif;
