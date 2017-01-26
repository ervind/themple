<?php

// Load the Themple Framework engine. This line has to stay at the top of functions.php
require_once "framework/themple.php";

// If you want to hide the Framework Options page, define the constant below:
// define( 'HIDE_FRAMEWORK_OPTIONS', true );


// Now loading the theme functions sorted into different functions subfiles.
tpl_loader( get_template_directory() . '/inc/functions-init.php' );
tpl_loader( get_template_directory() . '/inc/functions-images.php' );
tpl_loader( get_template_directory() . '/inc/functions-header.php' );
tpl_loader( get_template_directory() . '/inc/functions-widget.php' );
tpl_loader( get_template_directory() . '/inc/functions-content.php' );
tpl_loader( get_template_directory() . '/inc/functions-comments.php' );
tpl_loader( get_template_directory() . '/inc/functions-plugins.php' );
tpl_loader( get_template_directory() . '/inc/functions-pb_apps.php' );


// Now you can start adding your own functions below this line...
// ... or better: keep to this structure above and add your functions in the files of the /inc folder
