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

			<h1 class="tpl-nofeat"><?php _e( '404 Error', 'themple-starter' ); ?></h1>
			<?php get_template_part ( 'inc/loops/loop' ); ?>

    	</main><!-- content -->

		<?php get_sidebar(); ?>

	</div>

</div><!-- contentWrapper -->

<?php get_footer();
