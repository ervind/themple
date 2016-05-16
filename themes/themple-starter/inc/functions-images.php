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
