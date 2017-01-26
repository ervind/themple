<?php

/*
Themple Framework configuration file. This file runs first and it's the only editable file of the framework.
*/



// Defines that we're using the theme version
define( 'THEMPLE_THEME', true );


// Register the settings pages used in the admin panel
add_filter( 'tpl_settings_pages', 'tpl_theme_settings_pages', 10, 1 );

function tpl_theme_settings_pages ( $pages ) {

	$pages["tpl_theme_options"] = array(
		"page_title"	=> __( 'Theme Options', 'themple' ),
		"menu_title"	=> __( 'Theme Options', 'themple' ),
		"capability"	=> 'edit_theme_options',
		"menu_slug"		=> 'tpl_theme_options',
		"function"		=> 'tpl_theme_options',
		"post_type"		=> 'theme_options',
		"menu_func"		=> 'add_theme_page'
	);

	$pages["tpl_framework_options"] = array(
		"page_title"	=> __( 'Framework Options', 'themple' ),
		"menu_title"	=> __( 'Framework Options', 'themple' ),
		"capability"	=> 'edit_theme_options',
		"menu_slug"		=> 'tpl_framework_options',
		"function"		=> 'tpl_framework_options',
		"post_type"		=> 'framework_options',
		"menu_func"		=> 'add_theme_page'
	);

	return $pages;

}
