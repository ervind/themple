jQuery(document).ready(function($){

	"use strict";


    // Cross-browser select field in forms
    $( 'select' ).not( '[multiple]' ).wrap( '<span class="select-wrapper"></span>' );


    // Cross-browser file upload field in forms
    $( 'input[type=file]' ).wrap( '<label class="file-wrapper"></label>' );
    $( '.file-wrapper' ).append( '<span class="filetext">No file selected.</span>' );
  	$( 'input[type=file]' ).change(function() {
	 	$( this ).parent().find( '.filetext' ).text( $( this ).val() );
	});


    // Search field input size tweak based on the button's text length
    $( '.searchform' ).each( function() {
        var sInputW = $( this ).width() - $( '.searchsubmit', this ).width();
        $( '.searchinput', this ).width( sInputW - 36 );
    });


	// Hamburger icon handler
    $( '.hamburger-icon' ).click( function() {
        var menuID = '#' + $( this ).attr( 'data-for' );
        $( menuID ).toggleClass( 'desktop-only' );
        $( menuID ).toggleClass( 'mobile-only' );
        $( window ).resize(function(){
            if ( $( window ).width() >= 768 ) {
                $( menuID ).addClass( 'desktop-only' );
                $( menuID ).removeClass( 'mobile-only' );
            }
        });
    });


	// Helper script for making videos responsive. You can find its CSS counterpart in /style/200-custom.less (end of Common elements part)
	$( 'iframe' ).each(function(){
		var str = $(this).attr('src');
		if ( str.indexOf( "youtube" ) > -1 || str.indexOf( "vimeo" ) > -1 ) {
			$( this ).wrap( '<div class="tpl-video-wrap"></div>' );
		}
	});


});
