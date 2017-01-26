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

	    <main id="content" class="content tpl-grid-column-<?php echo $columns ?>">

			<?php get_template_part ( 'inc/loops/loop' ); ?>

			<?php if ( comments_open() ) {
				comments_template();
			} ?>

    	</main><!-- content -->

		<?php get_sidebar(); ?>

	</div>

</div><!-- contentWrapper -->

<?php get_footer();
