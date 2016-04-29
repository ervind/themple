<?php

// Functions for image handling


// Setting up custom image sizes. 'post-thumbnail' is a special item, it describes the default post thumbnail size.
function tpl_image_sizes() {

	$image_sizes = array(
		// The post thumbnail in sidebar view
		'post-thumbnail' => array(
			'title'		=> __( 'Post Thumbnail', 'themple-starter' ),
			'width'		=> 780,
			'height'	=> 350,
			'crop'		=> array( 'center', 'center' ),
			'select'	=> true,
		),
		// Size of the logo
		'logo-size' => array(
			'title'		=> __( 'Logo size', 'themple-starter' ),
			'width'		=> 220,
			'height'	=> 180,
			'crop'		=> false,
			'select'	=> false,
		),
		// Post thumbnails in full width mode
		'full-width' => array(
			'title'		=> __( 'Full Width', 'themple-starter' ),
			'width'		=> 1180,
			'height'	=> 529,
			'crop'		=> array( 'center', 'center' ),
			'select'	=> true,
		),
		// Post thumbnails for small mobile screens
		'thumb-small' => array(
			'title'		=> __( 'Small Thumbnail', 'themple-starter' ),
			'width'		=> 480,
			'height'	=> 215,
			'crop'		=> array( 'center', 'center' ),
			'select'	=> true,
		),
	);

	return $image_sizes;

}


// Run the SETUP process
function tpl_images_setup () {

	$image_sizes = tpl_image_sizes();

	// Add post thumbnail support to the theme
	add_theme_support( 'post-thumbnails' );

	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	if ( isset( $image_sizes["post-thumbnail"] ) ) {
		set_post_thumbnail_size( $image_sizes["post-thumbnail"]["width"], $image_sizes["post-thumbnail"]["height"], $image_sizes["post-thumbnail"]["crop"] );
	}
	// Add extra image sizes
	foreach ( $image_sizes as $name => $image_size ) {
		if ( $name != 'post-thumbnail' ) {
			add_image_size( $name, $image_size["width"], $image_size["height"], $image_size["crop"] );
		}
	}

}
add_action ( 'after_setup_theme', 'tpl_images_setup' );


// If you defined the "select" attribute of an image size as TRUE in $tpl_image_sizes, this function will add it to the image size selector menu in the post editor
function tpl_image_selector_sizes( $sizes ) {

	$image_sizes = tpl_image_sizes();
	$addsizes = array();

	foreach ( $image_sizes as $name => $image_size ) {
		if ( $image_size["select"] == true ) {
			$addsizes[$name] = $image_size["title"];
		}
	}

	$newsizes = array_merge( $sizes, $addsizes );
	return $newsizes;

}
add_filter('image_size_names_choose', 'tpl_image_selector_sizes');


// Decides which image size to use in the loop depending on the settings and the image size
function tpl_get_loop_image_size() {

	global $post;

	$post_thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
	$image_sizes = tpl_image_sizes();

	if ( ( tpl_get_layout() == 'full' ) && ( $post_thumbnail[1] >= $image_sizes["full-width"]["width"] ) && ( $post_thumbnail[2] >= $image_sizes["full-width"]["height"] ) ) {
		$thumb_size = 'full-width';
	}
	else {
		$thumb_size = 'post-thumbnail';
	}

	return $thumb_size;

}
