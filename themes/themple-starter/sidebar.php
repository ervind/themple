<?php if ( tpl_get_layout() != 'full' ) { ?>
<div class="tpl-mobile-only clearfix tpl-grid-column-12">
	<hr>
</div>

<section id="<?php echo esc_attr( tpl_get_layout() ); ?>-sidebar" class="sidebar tpl-grid-column-3">

	<?php dynamic_sidebar( 'right-sidebar' ); ?>

</section>
<?php }
