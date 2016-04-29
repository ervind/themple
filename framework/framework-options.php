<?php

// General tab in Framework Options
$section = array (
	"name"			=> 'fo_general',
	"title"			=> __( 'General', 'themple' ),
	"description"	=> __( 'General information about the Framework and the Theme', 'themple' ),
	"tab"			=> __( 'General', 'themple' ),
	"post_type"		=> 'framework_options',
);
tpl_register_section ( $section );


// Libraries tab in Framework Options
$section = array (
	"name"			=> 'fo_libraries',
	"title"			=> __( 'Libraries', 'themple' ),
	"description"	=> __( '3rd party libraries bundled with the Themple Framework', 'themple' ),
	"tab"			=> __( 'Libraries', 'themple' ),
	"post_type"		=> 'framework_options',
);
tpl_register_section ( $section );


// Framework version
$tpl_option_array = array (
    "name"			=> 'framework_version',
    "title"			=> __( 'Framework version', 'themple' ),
    "section"		=> 'fo_general',
    "type"			=> 'static',
    "default"		=> THEMPLE_VERSION,
	"prefix"		=> 'v',
);
tpl_register_option ( $tpl_option_array );


// Theme version
$tpl_current_theme = wp_get_theme();
$tpl_option_array = array (
    "name"			=> 'theme_version',
    "title"			=> __( 'Theme version', 'themple' ),
    "section"		=> 'fo_general',
    "type"			=> 'static',
    "default"		=> $tpl_current_theme->version,
	"prefix"		=> 'v',
);
tpl_register_option ( $tpl_option_array );


// Turn LESS compiler on/off
$tpl_option_array = array (
    "name"			=> 'less_compiler',
    "title"			=> __( 'Less Compiler', 'themple' ),
    "description"	=> __( '<b>ON:</b> Less files are compiled upon each page reload, even if there was no change (slowest, suitable for development environments)<br>
		<b>OFF:</b> Less files are NOT compiled in any circumstances (fastest, use it if you\'re not going to change styles or Theme Options for a long time, suitable for live sites with cached assets)<br>
		<b>Dynamic:</b> Less files are updated when there is a change in the Theme Options or inside the Less files (middle speed, use it in test environments or when you\'re changing options frequently on live sites.)', 'themple' ),
    "section"		=> 'fo_libraries',
    "type"			=> 'select',
	"values"		=> array(
		"on" 		=> __( 'ON', 'themple' ),
		"off" 		=> __( 'OFF', 'themple' ),
		"dynamic" 	=> __( 'Dynamic', 'themple' ),
	),
    "default"		=> 'on',
    "key"           => true,
	"less"			=> false,
);
tpl_register_option ( $tpl_option_array );


// Turn Font Awesome support on/off
$tpl_option_array = array (
    "name"			=> 'font_awesome',
    "title"			=> __( 'Font Awesome support', 'themple' ),
    "description"	=> __( '<b>Yes:</b> The Font Awesome library will be compiled into your front end CSS.<br>
		<b>No:</b> The Font Awesome library won\'t be compiled into your front end CSS. Recommended if you\'re not using these font icons (in this case the site will load faster).', 'themple' ),
    "section"		=> 'fo_libraries',
    "type"			=> 'select',
	"values"		=> array(
		"yes" 		=> __( 'Yes', 'themple' ),
		"no" 		=> __( 'No', 'themple' ),
	),
    "default"		=> 'yes',
    "key"           => true,
);
tpl_register_option ( $tpl_option_array );


// Turn TGM plugin activation on/off
$tpl_option_array = array (
    "name"			=> 'tgm_pa',
    "title"			=> __( 'TGM Plugin Activation', 'themple' ),
    "description"	=> __( '<b>Yes:</b> The TGM Plugin Activation library is enabled.<br>
		<b>No:</b> The TGM Plugin Activation library is disabled.', 'themple' ),
    "section"		=> 'fo_libraries',
    "type"			=> 'select',
	"values"		=> array(
		"yes" 		=> __( 'Yes', 'themple' ),
		"no" 		=> __( 'No', 'themple' ),
	),
    "default"		=> 'yes',
    "key"           => true,
);
tpl_register_option ( $tpl_option_array );
