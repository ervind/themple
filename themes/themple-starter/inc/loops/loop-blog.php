<?php
/* Loop for pages displaying multiple posts */

$count = 1;
if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if ( has_post_thumbnail() ) { ?><div class="tpl-featimage tpl-limg-size-<?php echo esc_attr( tpl_get_loop_image_size() ); ?>"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( tpl_get_loop_image_size() ); ?></a></div><?php } ?>
		<h1<?php if ( !has_post_thumbnail() ) { echo ' class="tpl-nofeat"'; } ?>><a href="<?php the_permalink(); ?>"><?php
			if ( get_the_title() != '' ) {
				the_title();
			}
			else {
				_e( '(Untitled)', 'themple-starter' );
			} ?></a></h1>
		<?php tpl_show_post_in_loop( $post, tpl_get_value( 'excerpt_format' ) ); ?>
		<?php if ( tpl_get_value ( 'source' ) ) {
			echo '<p>'. esc_html( tpl_get_value ( 'source_label' ) ) .' <a href="'. esc_url( tpl_get_value ( 'source' ) ) .'">'. esc_html( tpl_get_value ( 'source' ) ) .'</a></p>';
		} ?>
		<aside class="postmeta"><?php
			$author = get_the_author();
			$date = get_the_date( 'M j, Y' );
			printf ( __('Posted by %1$s on %2$s' ,'themple-starter'), $author, $date ); ?></aside>
	</article>

	<?php $count++;
		if ( $count <= $wp_query->post_count ) {
			echo '<hr>';
		}
	?>

<?php endwhile; else: ?>

	<p><?php _e( 'Sorry, no posts matched your criteria.', 'themple-starter' ); ?></p>

<?php endif;
