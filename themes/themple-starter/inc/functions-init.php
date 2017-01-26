<?php

// Functions for setting up initial settings


// Run the SETUP process
function tpl_setup () {

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );
	// This is a HTML5 theme. Make some WP functions know it.
	current_theme_supports( 'html5' );
	// Handle title tags in the WP 4.1 way
	add_theme_support( 'title-tag' );

	// Load the Theme textdomain for localization
	load_theme_textdomain( 'themple-starter', get_template_directory() . '/languages' );

	// Register the menu locations
	register_nav_menu( 'primary', __( 'Primary Menu', 'themple-starter' ) );

	// Set up the default content width
	if ( !isset( $content_width ) ) {
		$content_width = 780;
	}

}
add_action ( 'after_setup_theme', 'tpl_setup' );



// Add the Extra CSS from the Theme Options at the end of the theme.css file
add_filter( 'tpl_output_css', function( $css ){
	return $css . tpl_get_option( 'extra_css' );
} );
