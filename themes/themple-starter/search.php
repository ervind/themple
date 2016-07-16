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
    <main id="content" class="content column-8">

		<?php if ( $_GET["s"] != '' ) {
			$s = get_search_query();
		}
		else {
			$s = '(' . __( 'empty search term', 'themple-starter' ) . ')';
		}
		echo '<h2 class="searchterm">' . __( 'Searched for:', 'themple-starter' ) . ' <em>' . $s . '</em></h2>';
		?>

		<?php get_template_part ( 'inc/loops/loop', 'blog' ); ?>

    </main><!-- content -->

    <?php get_sidebar(); ?>

</div><!-- contentWrapper -->

<?php get_footer();
