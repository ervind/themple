<?php


// The margin above the logo
$tpl_option_array = array (
        "name"			=> 'grid_max_width',
        "title"			=> __( 'Maximum width of the theme', 'themple-starter' ),
        "description"	=> __( 'This is the maximum width in pixels, under this value the theme will behave responsively.', 'themple-starter' ),
        "section"		=> 'general',
        "type"			=> 'number',
        "step"			=> '1',
        "min"			=> '320',
        "default"		=> '1200',
        "suffix"		=> 'px',
        "placeholder"   => __( 'Enter a number here', 'themple-starter' ),
);
tpl_register_option ( $tpl_option_array );


// The basic font definition
$tpl_option_array = array (
        "name"			=> 'basic_font',
        "title"			=> __( 'Basic theme font', 'themple-starter' ),
        "description"	=> __( 'The values for the basic font declaration for the theme. You can override these in your style files.', 'themple-starter' ),
        "section"		=> 'general',
        "type"			=> 'combined',
        "parts"         => array(
            array(
                "name"          => 'family',
                "title"			=> __( 'Font family', 'themple-starter' ),
                "description"	=> __( 'Font family of the basic font', 'themple-starter' ),
                "type"			=> 'select',
                "values"		=> array(
                    "arial"			=> __( 'Arial', 'themple-starter' ),
                    "times"			=> __( 'Times New Roman', 'themple-starter' ),
                    "verdana"		=> __( 'Verdana', 'themple-starter' ),
                ),
                "default"		=> 'arial',
                "less_string"   => true,
                "placeholder"   => __( 'Select font family', 'themple-starter' ),
            ),
            array(
                "name"			=> 'size',
                "title"			=> __( 'Font size', 'themple-starter' ),
                "description"	=> __( 'Enter default font size in pixels.', 'themple-starter' ),
                "type"			=> 'number',
                "step"			=> '1',
                "min"			=> '8',
                "max"			=> '100',
                "default"		=> '14',
                "suffix"		=> 'px',
                "placeholder"   => __( 'Enter a number here', 'themple-starter' ),
            ),
            array(
                "name"          => 'weight',
                "title"			=> __( 'Font weight', 'themple-starter' ),
                "description"	=> __( 'Font weight / boldness of the basic font', 'themple-starter' ),
                "type"			=> 'select',
                "values"		=> array(
                    100 => __( 'Thin', 'themple-starter' ),
                    200 => __( 'Extra Light', 'themple-starter' ),
                    300 => __( 'Light', 'themple-starter' ),
                    400 => __( 'Normal', 'themple-starter' ),
                    500 => __( 'Medium', 'themple-starter' ),
                    600 => __( 'Semi Bold', 'themple-starter' ),
                    700 => __( 'Bold', 'themple-starter' ),
                    800 => __( 'Extra Bold', 'themple-starter' ),
                    900 => __( 'Black / Heavy', 'themple-starter' ),
                ),
                "default"		=> 400,
                "key"           => true,
            ),
            array(
                "name"			=> 'color',
                "title"			=> __( 'Font color', 'themple-starter' ),
                "description"	=> __( 'You can modify the main body text color here.', 'themple-starter' ),
                "type"			=> 'color',
                "default"		=> '#666666',
            ),
        ),
);
tpl_register_option ( $tpl_option_array );


// The "tpl_logo" is a special (unique) option. It can be reached with the tpl_logo() function in the frontend - if defined
$tpl_logo = array (
        "name"			=> 'tpl_logo',
        "title"			=> __( 'Theme Logo', 'themple-starter' ),
        "description"	=> __( 'Please upload your website\'s logo here.', 'themple-starter' ),
        "section"		=> 'header',
        "type"			=> 'image',
        "size"			=> 'logo-size',
);
tpl_register_option ( $tpl_logo );


// The margin above the logo
$tpl_option_array = array (
        "name"			=> 'starter_topmargin',
        "title"			=> __( 'Top margin above the logo', 'themple-starter' ),
        "description"	=> __( 'Enter the top margin value in pixels.', 'themple-starter' ),
        "section"		=> 'header',
        "type"			=> 'number',
        "step"			=> '1',
        "min"			=> '0',
        "max"			=> '200',
        "default"		=> '40',
        "prefix"		=> '',
        "suffix"		=> 'px',
        "placeholder"   => __( 'Enter a number here', 'themple-starter' ),
);
tpl_register_option ( $tpl_option_array );


// Position of the logo: left, center or right
$tpl_option_array = array (
        "name"			=> 'logo_position',
        "title"			=> __( 'Horizontal position of the logo', 'themple-starter' ),
        "description"	=> __( 'The logo will be placed to this side of the header', 'themple-starter' ),
        "section"		=> 'header',
        "type"			=> 'select',
        "values"		=> array(
            "left"			=> __( 'Left', 'themple-starter' ),
            "center"		=> __( 'Center', 'themple-starter' ),
            "right"			=> __( 'Right', 'themple-starter' ),
        ),
        "default"		=> 'left',
        "key"           => true,
);
tpl_register_option ( $tpl_option_array );


// Social icons displayed in the header
$tpl_option_array = array (
        "name"			=> 'social_icons',
        "title"			=> __( 'Social icons', 'themple-starter' ),
        "description"	=> __( 'These are the social icons displayed in the header', 'themple-starter' ),
        "section"		=> 'header',
        "type"			=> 'icon',
		"repeat"		=> true,
);
tpl_register_option ( $tpl_option_array );


// Color field
$tpl_option_array = array (
        "name"			=> 'acolor',
        "title"			=> __( 'Theme basic color scheme', 'themple-starter' ),
        "description"	=> __( 'Please select a color. It will be the base of your color scheme. Change it and test how your site responds to it.', 'themple-starter' ),
        "section"		=> 'colors',
        "type"			=> 'color',
        "default"		=> '#cccccc',
);
tpl_register_option ( $tpl_option_array );


// Color field
$tpl_option_array = array (
        "name"			=> 'h1color',
        "title"			=> __( 'Heading elements', 'themple-starter' ),
        "description"	=> __( 'Please select a color for the heading (h1 .. h6) elements.', 'themple-starter' ),
        "section"		=> 'colors',
        "type"			=> 'color',
        "default"		=> '#009fe3',
);
tpl_register_option ( $tpl_option_array );


// Color field
$tpl_option_array = array (
        "name"			=> 'link_color',
        "title"			=> __( 'Color for anchor tags / links inside the content area', 'themple-starter' ),
        "description"	=> __( 'This color is linked by default to post content, but not to header and footer.', 'themple-starter' ),
        "section"		=> 'colors',
        "type"			=> 'color',
        "default"		=> '#2a4360',
);
tpl_register_option ( $tpl_option_array );


// Should the links use inverse colors while hovering over them?
$tpl_option_array = array (
        "name"			=> 'link_hover',
        "title"			=> __( 'Link inverse effect', 'themple-starter' ),
        "description"	=> __( 'Should the links use inverse colors while hovering the mouse over them?', 'themple-starter' ),
        "section"		=> 'colors',
        "type"			=> 'select',
        "values"		=> array(
            "no"			=> __( 'No', 'themple-starter' ),
            "yes"			=> __( 'Yes', 'themple-starter' ),
        ),
        "default"		=> 'no',
        "key"           => true,
);
tpl_register_option ( $tpl_option_array );


// Global setting for the blog post layouts
$tpl_option_array = array (
        "name"			=> 'general_post_layout',
        "title"			=> __( 'General post layout', 'themple-starter' ),
        "description"	=> __( 'Global seting for the layout of the blog posts', 'themple-starter' ),
        "section"		=> 'content',
        "type"			=> 'select',
        "values"		=> array(
            "full"			=> __( 'Full width', 'themple-starter' ),
            "right"			=> __( 'Right sidebar', 'themple-starter' ),
			"left"			=> __( 'Left sidebar', 'themple-starter' ),
        ),
        "default"		=> 'right',
        "key"           => true,
);
tpl_register_option ( $tpl_option_array );


// Global setting for the blog post layouts
$tpl_option_array = array (
        "name"			=> 'index_layout',
        "title"			=> __( 'Index page layout', 'themple-starter' ),
        "description"	=> __( 'Layout setting for the blog index page (and other archive pages)', 'themple-starter' ),
        "section"		=> 'content',
        "type"			=> 'select',
        "values"		=> array(
            "full"			=> __( 'Full width', 'themple-starter' ),
            "right"			=> __( 'Right sidebar', 'themple-starter' ),
			"left"			=> __( 'Left sidebar', 'themple-starter' ),
        ),
        "default"		=> 'right',
        "key"           => true,
);
tpl_register_option ( $tpl_option_array );


// Text field: the read more text at the end of the excerpt
$tpl_option_array = array (
		"name"			=> 'readmore',
		"title"			=> __( 'Read more text', 'themple-starter' ),
		"description"	=> __( 'The read more text after the post excerpts in blog view.', 'themple-starter' ),
		"section"		=> 'content',
		"type"			=> 'text',
		"size"			=> '',
		"default"		=> __( 'Read more...', 'themple-starter' ),
);
tpl_register_option ( $tpl_option_array );


// Text field
$tpl_option_array = array (
        "name"			=> 'source_label',
        "title"			=> __( 'Label for the source', 'themple-starter' ),
        "description"	=> __( 'The text label that\'s displayed before the source link right after the post contents.', 'themple-starter' ),
        "section"		=> 'content',
        "type"			=> 'text',
        "size"			=> '',
        "default"		=> __( 'Source:', 'themple-starter' ),
);
tpl_register_option ( $tpl_option_array );


// Text field: File upload button label
$tpl_option_array = array (
        "name"			=> 'file_upload',
        "title"			=> __( 'File upload button text', 'themple-starter' ),
        "description"	=> __( 'This text will be displayed on the file upload buttons.', 'themple-starter' ),
        "section"		=> 'content',
        "type"			=> 'text',
        "size"			=> '',
        "default"		=> __( 'Upload File', 'themple-starter' ),
);
tpl_register_option ( $tpl_option_array );


// Text field
$tpl_option_array = array (
        "name"			=> 'newerposts',
        "title"			=> __( 'Newer Posts link text', 'themple-starter' ),
        "description"	=> __( 'Newer posts text used for pagination in blog view.', 'themple-starter' ),
        "section"		=> 'content',
        "type"			=> 'text',
        "size"			=> '',
        "default"		=> __( 'Newer Posts', 'themple-starter' ),
);
tpl_register_option ( $tpl_option_array );


// Text field
$tpl_option_array = array (
        "name"			=> 'olderposts',
        "title"			=> __( 'Older Posts link text', 'themple-starter' ),
        "description"	=> __( 'Older posts text used for pagination in blog view.', 'themple-starter' ),
        "section"		=> 'content',
        "type"			=> 'text',
        "size"			=> '',
        "default"		=> __( 'Older Posts', 'themple-starter' ),
);
tpl_register_option ( $tpl_option_array );


// Text field: footer copyright text
$tpl_option_array = array (
		"name"			=> 'copyright_text',
		"title"			=> __( 'Copyright Text', 'themple-starter' ),
		"description"	=> __( 'Copyright Text in the footer\'s bottom line.', 'themple-starter' ),
		"section"		=> 'footer',
		"type"			=> 'textarea',
		"size"			=> 2,
		"default"		=> __( '&copy; Copyright 2016', 'themple-starter' ),
);
tpl_register_option ( $tpl_option_array );
