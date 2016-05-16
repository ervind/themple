<?php if ( tpl_get_layout() != 'full' ) { ?>
<div class="mobile-only clearfix column-12">
	<hr>
</div>

<section id="<?php echo tpl_get_layout(); ?>-sidebar" class="sidebar column-3">

	<?php dynamic_sidebar( 'right-sidebar' ); ?>

</section>
<?php }
