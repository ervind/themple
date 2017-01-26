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

			<?php if ( $_GET["s"] != '' ) {
				$s = get_search_query();
			}
			else {
				$s = '(' . __( 'empty search term', 'themple-starter' ) . ')';
			}
			echo '<h2 class="tpl-searchterm">' . __( 'Searched for:', 'themple-starter' ) . ' <em>' . esc_html( $s ) . '</em></h2>';
			?>

			<?php get_template_part ( 'inc/loops/loop', 'blog' ); ?>

	    </main><!-- content -->

    	<?php get_sidebar(); ?>

	</div>

</div><!-- contentWrapper -->

<?php get_footer();
