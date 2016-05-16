			<div class="clearfix"></div>

		</div><!-- wrapper -->

	<footer>
		<div class="wrapper row">

			<div class="column-3">
				<?php dynamic_sidebar( 'footer-sidebar-1' ); ?>
			</div>
			<div class="column-3">
				<?php dynamic_sidebar( 'footer-sidebar-2' ); ?>
			</div>
			<div class="column-3">
				<?php dynamic_sidebar( 'footer-sidebar-3' ); ?>
			</div>
			<div class="column-3">
				<?php dynamic_sidebar( 'footer-sidebar-4' ); ?>
			</div>

		</div>

		<div class="clearfix"></div>

	</footer>

	<section class="copyright">
		<aside class="wrapper row">
			<p class="column-12"><?php echo tpl_get_value( 'copyright_text' ); ?></p>
		</aside>
	</section>

	<?php wp_footer(); ?>
	</body>
</html>
