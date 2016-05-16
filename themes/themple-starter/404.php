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

<div id="contentWrapper" class="row">
    <main id="content" class="content column-<?php echo $columns; ?>">

		<?php get_template_part ( 'loop' ); ?>

    </main><!-- content -->

	<?php get_sidebar(); ?>

</div><!-- contentWrapper -->

<?php get_footer();
