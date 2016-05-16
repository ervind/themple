<?php


// Set up the sections in Theme Options

$section = array (
	"name"			=> 'general',
	"title"			=> __( 'General', 'themple-starter' ),
	"description"	=> __( 'General options for the theme and most of its elements', 'themple-starter' ),
	"tab"			=> __( 'General', 'themple-starter' )
);
tpl_register_section ( $section );


$section = array (
	"name"			=> 'header',
	"title"			=> __( 'Header', 'themple-starter' ),
	"description"	=> __( 'Options for the header, logo, etc.', 'themple-starter' ),
	"tab"			=> __( 'Header', 'themple-starter' )
);
tpl_register_section ( $section );


$section = array (
	"name"			=> 'colors',
	"title"			=> __( 'Colors', 'themple-starter' ),
	"description"	=> __( 'Set up the theme colors here', 'themple-starter' ),
	"tab"			=> __( 'Colors', 'themple-starter' )
);
tpl_register_section ( $section );


$section = array (
	"name"			=> 'content',
	"title"			=> __( 'Content', 'themple-starter' ),
	"description"	=> __( 'Options for the posts content', 'themple-starter' ),
	"tab"			=> __( 'Content', 'themple-starter' )
);
tpl_register_section ( $section );


$section = array (
	"name"			=> 'footer',
	"title"			=> __( 'Footer', 'themple-starter' ),
	"description"	=> __( 'Options for the footer', 'themple-starter' ),
	"tab"			=> __( 'Footer', 'themple-starter' )
);
tpl_register_section ( $section );



// And some custom field sections for posts

$section = array (
	"name"			=> 'post_layouts',
	"title"			=> __( 'Post Layouts', 'themple-starter' ),
	"description"	=> __( 'Choose the layout options for this post here', 'themple-starter' ),
	"post_type"		=> array( 'post', 'page' ),
);
tpl_register_section ( $section );


$section = array (
	"name"			=> 'post_options',
	"title"			=> __( 'Post Options', 'themple-starter' ),
	"description"	=> __( 'This is an example metabox section', 'themple-starter' ),
	"post_type"		=> 'post'
);
tpl_register_section ( $section );
