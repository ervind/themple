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

	echo '<div class="tpl-hamburger-icon';

	if ( $class != '' ) {
		echo ' ' . esc_attr( $class );
	}

	echo '" data-for="' . esc_attr( $for_id ) . '">
	<span class="tpl-hamburger-line"></span>
	<span class="tpl-hamburger-line"></span>
	<span class="tpl-hamburger-line"></span>
</div>';

}


// Adds Google fonts to the header
function tpl_add_google_fonts() {

	global $tpl_font_family_sets;

	if ( isset( $tpl_font_family_sets["Google fonts"] ) && array_key_exists( tpl_get_value( 'basic_font/0/family' ), $tpl_font_family_sets["Google fonts"] ) ) {
		wp_enqueue_style( 'tpl-google-fonts', '//fonts.googleapis.com/css?family=' . tpl_get_value( 'basic_font/0/family' ) . ':300,400,600,700' );
	}

}
add_action( 'wp_enqueue_scripts', 'tpl_add_google_fonts' );
