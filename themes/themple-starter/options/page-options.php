<?php

// Page builder
$tpl_option_array = array (
        "name"			=> 'page_builder',
        "title"			=> __( 'Page Builder sections', 'themple-starter' ),
        "description"	=> __( 'Build your page using this special option', 'themple-starter' ),
        "section"		=> 'page_builder',
        "type"			=> 'page_builder',
		"condition"		=> array(
			array(
				"type"		=> 'page',
				"name"		=> 'template',
				"relation"	=> '=',
				"value"		=> 'page-page_builder.php',
			),
		),
);
tpl_register_option ( $tpl_option_array );
