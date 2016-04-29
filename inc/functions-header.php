<?php

// Functions used by the header


// Adding the Title tag separator the WP 4.1+ way
function tpl_document_title_separator(){
    return '|';
}
add_filter( 'document_title_separator', 'tpl_document_title_separator' );


// Backwards compatibility for old title tag in WP < 4.1
if ( !function_exists( '_wp_render_title_tag' ) ) {

    function tpl_render_title() { ?>
		<title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php }
    add_action( 'wp_head', 'tpl_render_title' );

}


// Prints a hamburger icon for mobile menu
function tpl_hamburger_icon( $for_id, $class = '' ) {
	echo '<div class="hamburger-icon';
	if ( $class != '' ) {
		echo ' ' . $class;
	}
	echo '" data-for="'. $for_id .'">
	<span class="hamburger-line"></span>
	<span class="hamburger-line"></span>
	<span class="hamburger-line"></span>
</div>';
}
