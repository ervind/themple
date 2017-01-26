// Themple Starter JS scripts
jQuery(document).ready(function($){

	"use strict";


    // Cross-browser select field in forms
    $( 'select' ).not( '[multiple]' ).wrap( '<span class="tpl-select-wrapper"></span>' );


    // Cross-browser file upload field in forms
    $( 'input[type=file]' ).wrap( '<label class="tpl-file-wrapper"></label>' );
    $( '.tpl-file-wrapper' ).append( '<span class="tpl-filetext">No file selected.</span>' );
  	$( 'input[type=file]' ).change(function() {
	 	$( this ).parent().find( '.tpl-filetext' ).text( $( this ).val() );
	});


    // Search field input size tweak based on the button's text length
    $( '.searchform' ).each( function() {
        var sInputW = $( this ).width() - $( '.searchsubmit', this ).width();
        $( '.searchinput', this ).width( sInputW - 36 );
    });


	// Hamburger icon handler
    $( '.tpl-hamburger-icon' ).click( function() {
        var menuID = '#' + $( this ).attr( 'data-for' );
		if ( $( menuID ).hasClass( 'tpl-desktop-only' ) ) {
			$( menuID ).removeClass( 'tpl-desktop-only' );
			$( menuID ).addClass( 'tpl-mobile-only' );
		}
		else {
			$( menuID ).addClass( 'tpl-desktop-only' );
			$( menuID ).removeClass( 'tpl-mobile-only' );
		}
        $( window ).resize(function(){
            if ( $( window ).width() >= 768 && !tpl_is_touch_device() ) {
                $( menuID ).addClass( 'tpl-desktop-only' );
                $( menuID ).removeClass( 'tpl-mobile-only' );
            }
        });
    });

	// Clicking outside the menu closes it
	$( 'body' ).on( "click", function( event ){
		if ( !$( event.target ).is( '.tpl-menu-wrapper nav > ul, .tpl-hamburger-icon, .tpl-hamburger-icon *' ) ) {
			$( '.tpl-menu-wrapper nav > ul' ).addClass( 'tpl-desktop-only' );
			$( '.tpl-menu-wrapper nav > ul' ).removeClass( 'tpl-mobile-only' );
		}
	});


	// Helper script for making videos responsive. You can find its CSS counterpart in /style/200-custom.less (Common elements)
	$( 'iframe' ).each(function(){
		var str = $(this).attr('src');
		if ( typeof str !== typeof undefined && ( str.indexOf( "youtube" ) > -1 || str.indexOf( "vimeo" ) > -1 ) ) {
			$( this ).wrap( '<div class="tpl-video-wrap"></div>' );
		}
	});


	// Toggle shortcode
	$( '.tpl-toggle-title' ).click( function() {
		if ( $( '.tpl-toggle-action', this ).hasClass( 'tpl-toggle-open' ) ) {
			$( '.tpl-toggle-action', this ).removeClass( 'tpl-toggle-open' );
		}
		else {
			$( '.tpl-toggle-action', this ).addClass( 'tpl-toggle-open' );
		}
		$( this ).closest( '.tpl-toggle' ).find( '.tpl-toggle-content' ).slideToggle();
	});


	// Accordion shortcode
	if ( $.ui !== undefined && $.ui.accordion !== undefined ) {
		$( '.tpl-accordion' ).each( function(){
			var data_open = $( this ).attr( 'data-open' );
			if ( data_open == 'closed' || data_open < 0 ) {
				data_open = false;
			}
			else if ( data_open == 'open' ) {
				data_open = 0;
			}
			else {
				data_open = parseInt( data_open );
				var panels = $( '.tpl-accordion-content', this ).length - 1;
				if ( data_open > panels ) {
					data_open = panels;
				}
			}
			$( '.tpl-accordion' ).accordion( {
				collapsible : true,
				heightStyle : "content",
				active		: data_open
			} );
		});
	}


	// Try to detect touchscreens
	function tpl_is_touch_device() {
		return 'ontouchstart' in window || navigator.maxTouchPoints;
	};

	// Add a body class if touchscreen is detected - so can address it with CSS
	if ( tpl_is_touch_device() ) {
		$( 'body' ).addClass( 'tpl-touch' );
	}


});
