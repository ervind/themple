<?php


// Page layout
$tpl_option_array = array (
        "name"			=> 'local_layout',
        "title"			=> __( 'Post Layout', 'themple-starter' ),
        "description"	=> __( 'Layout of this post. If not specified, the global seting is used.', 'themple-starter' ),
        "section"		=> 'post_layouts',
		"type"			=> 'select',
        "values"		=> array(
            "full"			=> __( 'Full width', 'themple-starter' ),
            "right"			=> __( 'Right sidebar', 'themple-starter' ),
			"left"			=> __( 'Left sidebar', 'themple-starter' ),
			"inherit"		=> __( 'Inherit', 'themple-starter' ),
        ),
        "default"		=> 'inherit',
        "key"           => true,
);
tpl_register_option ( $tpl_option_array );


// Format of the excerpt in the loop
$tpl_option_array = array (
        "name"			=> 'excerpt_format',
        "title"			=> __( 'Excerpt Format', 'themple-starter' ),
        "description"	=> __( 'Format of the excerpt in the loop.<br>
			<b>Default:</b> unformatted excerpt if no more tag was used. Else use the standard formatting until the more tag.<br>
			<b>Formatted:</b> uses the formatted first part of the article<br>
			<b>Unformatted:</b> uses the unformatted excerpt of the article<br>
			<b>Full:</b> full article is displayed in the loop', 'themple-starter' ),
        "section"		=> 'post_layouts',
		"type"			=> 'select',
        "values"		=> array(
            "default"		=> __( 'Default', 'themple-starter' ),
            "formatted"		=> __( 'Formatted', 'themple-starter' ),
			"unformatted"	=> __( 'Unformatted', 'themple-starter' ),
			"full"			=> __( 'Full', 'themple-starter' ),
        ),
        "default"		=> 'default',
        "key"           => true,
);
tpl_register_option ( $tpl_option_array );


// Text field
$tpl_option_array = array (
        "name"			=> 'source',
        "title"			=> __( 'Source link', 'themple-starter' ),
        "description"	=> __( 'Source URL of the original article.', 'themple-starter' ),
        "section"		=> 'post_options',
        "type"			=> 'text',
        "size"			=> '',
        "placeholder"   => __( 'Enter a URL here', 'themple-starter' ),
);
tpl_register_option ( $tpl_option_array );
