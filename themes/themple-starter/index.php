<?php
get_header();

$layout = tpl_get_layout();
if ( $layout == 'full' ) {
	$columns = 12;
}
else {
	$columns = 8;
}
?>

<div id="contentWrapper">

	<div class="tpl-grid-row">

	    <main id="content" class="blog content tpl-grid-column-<?php echo $columns ?>">

			<?php get_template_part ( 'inc/loops/loop', 'blog' ); ?>

			<aside class="pagination">
				<?php next_posts_link( tpl_get_value ( 'olderposts' ) ); ?>
				<?php previous_posts_link( tpl_get_value ( 'newerposts' ) ); ?>
			</aside>

	    </main><!-- content -->

		<?php get_sidebar(); ?>

	</div>

</div><!-- contentWrapper -->

<?php get_footer();
