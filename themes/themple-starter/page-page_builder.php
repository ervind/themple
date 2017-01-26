<?php

/* Template Name: Page Builder */


get_header();
?>

<div id="contentWrapper">

    <main id="content" class="content">

		<?php get_template_part( 'inc/loops/loop', 'page_builder' ); ?>

    </main><!-- content -->

</div><!-- contentWrapper -->

<?php get_footer();
