			<div class="clearfix"></div>

		</div><!-- wrapper -->

	<footer id="footer">
		<div class="tpl-wrapper tpl-grid-row">

			<div class="tpl-grid-column-3">
				<?php dynamic_sidebar( 'footer-sidebar-1' ); ?>
			</div>
			<div class="tpl-grid-column-3">
				<?php dynamic_sidebar( 'footer-sidebar-2' ); ?>
			</div>
			<div class="tpl-grid-column-3">
				<?php dynamic_sidebar( 'footer-sidebar-3' ); ?>
			</div>
			<div class="tpl-grid-column-3">
				<?php dynamic_sidebar( 'footer-sidebar-4' ); ?>
			</div>

		</div>

		<div class="clearfix"></div>

	</footer>

	<section id="copyright" class="copyright">
		<aside class="tpl-wrapper tpl-grid-row">
			<p class="tpl-grid-column-12"><?php tpl_value( 'copyright_text' ); ?></p>
		</aside>
	</section>

	<?php wp_footer(); ?>
	</body>
</html>
