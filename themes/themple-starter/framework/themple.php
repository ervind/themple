<?php

/*
Themple Framework main file
For more information and documentation, visit [https://a-idea.studio/themple-framework]
*/



// Version number of the framework
define( 'THEMPLE_VERSION', '1.2' );

// Loading basic configuration for this installation
require_once "themple-config.php";

// Loading the Themple Framework CORE file
require_once "tpl-inc/themple-core.php";

// If available, load the theme specific functions for the framework
if ( file_exists( tpl_base_dir() . "/framework/tpl-inc/themple-theme.php" ) ) {
	require_once "tpl-inc/themple-theme.php";
}
