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


// Backend tab in Framework Options
$section = array (
	"name"			=> 'fo_backend',
	"title"			=> __( 'Backend settings', 'themple' ),
	"description"	=> __( 'Settings for the Themple admin panels', 'themple' ),
	"tab"			=> __( 'Backend', 'themple' ),
	"post_type"		=> 'framework_options',
);
tpl_register_section ( $section );


// Theme version
$tpl_current_theme = wp_get_theme();
$tpl_option_array = array (
	"name"			=> 'theme_version',
	"title"			=> __( 'Theme version', 'themple' ),
	"section"		=> 'fo_general',
	"type"			=> 'static',
	"default"		=> $tpl_current_theme->version,
);
tpl_register_option ( $tpl_option_array );


// Framework version
$tpl_option_array = array (
    "name"			=> 'framework_version',
    "title"			=> __( 'Framework version', 'themple' ),
    "section"		=> 'fo_general',
    "type"			=> 'static',
    "default"		=> THEMPLE_VERSION,
);
tpl_register_option ( $tpl_option_array );


// WordPress version
$tpl_option_array = array (
    "name"			=> 'wp_version',
    "title"			=> __( 'WordPress version', 'themple' ),
    "section"		=> 'fo_general',
    "type"			=> 'static',
    "default"		=> get_bloginfo( 'version' ),
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


// Turn CSS minification on/off - can be used only when LESS compiler isn't turned off
$tpl_option_array = array (
    "name"			=> 'css_minification',
    "title"			=> __( 'CSS Minification', 'themple' ),
    "description"	=> __( '<b>ON:</b> Output CSS files are minified during the compilation process.<br>
		<b>OFF:</b> Output CSS files are not minified, only compiled.', 'themple' ),
    "section"		=> 'fo_libraries',
    "type"			=> 'select',
	"values"		=> array(
		"on" 		=> __( 'ON', 'themple' ),
		"off" 		=> __( 'OFF', 'themple' ),
	),
    "default"		=> 'on',
    "key"           => true,
	"condition"		=> array(
		array(
			"type"		=> 'option',
			"name"		=> 'less_compiler',
			"relation"	=> '!=',
			"value"		=> 'off',
		),
	),
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
	"condition"		=> array(
		array(
			"type"		=> 'option',
			"name"		=> 'less_compiler',
			"relation"	=> '!=',
			"value"		=> 'off',
		),
	),
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


// Turn confirmation on/off when removing rows from repeater fields
$tpl_option_array = array (
    "name"			=> 'remover_confirm',
    "title"			=> __( 'Remove confirmation for repeater fields', 'themple' ),
    "description"	=> __( 'If yes, you\'ll be prompted every time you want to remove a row from a repeater field in Theme Options', 'themple' ),
    "section"		=> 'fo_backend',
    "type"			=> 'select',
	"values"		=> array(
		"yes" 		=> __( 'Yes', 'themple' ),
		"no" 		=> __( 'No', 'themple' ),
	),
    "default"		=> 'yes',
    "key"           => true,
	"js"			=> true,
);
tpl_register_option ( $tpl_option_array );


// Turn confirmation on/off when switching for columnsets with fewer columns in Page Builder
$tpl_option_array = array (
    "name"			=> 'pb_fewer_confirm',
    "title"			=> __( 'Remove confirmation for Page Builder columnsets', 'themple' ),
    "description"	=> __( 'If yes, you\'ll be prompted every time you want to switch to a columnset with fewer columns in Page Builder', 'themple' ),
    "section"		=> 'fo_backend',
    "type"			=> 'select',
	"values"		=> array(
		"yes" 		=> __( 'Yes', 'themple' ),
		"no" 		=> __( 'No', 'themple' ),
	),
    "default"		=> 'yes',
    "key"           => true,
	"js"			=> true,
);
tpl_register_option ( $tpl_option_array );
