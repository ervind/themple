// Media uploader script
jQuery(document).ready(function($) {

	"use strict";

/*
TABLE OF CONTENTS
-----------------

1. Variable definition
2. Scripts for single data types
	2.1 Image
	2.2 Color
	2.3 Select
3. Scripts for repeater fields
4. Common settings
	4.1 Tabs in the backend
	4.2 Hover messages on icons
5. Conditional settings

*/




/*

	1. DEFINING THE VARIABLES NECESSARY FOR THIS SCRIPT
	===================================================

*/

	// Needed for the Image data type
    var custom_uploader;

	// Needed for the Select data type
	var select2_settings = {
		escapeMarkup: function(m) {
			return m;
		},
		width: "100%",
		minimumResultsForSearch: 10,
	}

	var fa_icon_settings = $.extend( {
		templateSelection: tpl_fa_icon_template,
		templateResult: tpl_fa_icon_template,
	}, select2_settings );

	// Decide which container to use based on  where we are: on Post editor or on Theme Options page
	if ( $('body').hasClass('post-new-php') || $('body').hasClass('post-php') ) {
		var repeat_container = '.meta-option-wrapper';
	}
	else {
		var repeat_container = 'td';
	}




/*

	2. SCRIPTS FOR SINGLE DATA TYPES
	================================

	2.1 IMAGE DATA TYPE
	-------------------

*/

	// Main uploader popup handler
    $('body').on('click', '.uploader .button, .uploader img', function(e) {

        e.preventDefault();
		var imgurl = $(this).parent().find('.uploaded-image');
		var current_item = imgurl.closest('.tpl-field');

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: Themple_Admin.uploader_title,
            button: {
                text: Themple_Admin.uploader_button
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
			imgurl.attr('src', attachment.sizes.thumbnail.url);
			imgurl.show();
			current_item.find('.img_id').val(attachment.id).trigger('change');
			current_item.find('.placeholder').hide();
			current_item.find('.closer').show();
        });

        //Open the uploader dialog
        custom_uploader.open();

    });


	// Clear the uploaded picture with the "X" icon
	$('body').on('click', '.uploader .closer', function(){

		var imgurl = $(this).parent().find('.uploaded-image');
		var current_item = imgurl.closest('.tpl-field');
		imgurl.attr('src', '');
		current_item.find('.placeholder').show();
        current_item.find('.uploaded-image').hide();
		$(this).closest('.tpl-field').find('.img_id').val('').trigger('change');
		$(this).hide();

	});




/*

	2.2 COLOR DATA TYPE
	-------------------

*/

	var color_picker_settings = {
		width : 258,
		change : function(event,ui){
			$(this).val($(this).wpColorPicker('color'));
			tpl_condition_updater();
		}
	}

	// Color picker script
    $('.tpl-color-field').each(function(){
		$(this).wpColorPicker( color_picker_settings );
	});





/*

	2.3 SELECT DATA TYPE
	--------------------

*/

	// Select scripts
	$('.tpl-field.select').not('.font_awesome').find('select').select2( select2_settings );
	$('.tpl-field.select.font_awesome select').select2( fa_icon_settings );

	function tpl_fa_icon_template(data, container) {
		return '<i class="fa fa-' + data.id + '"></i> ' + data.text;
	}




/*

	3. REPEATER FIELDS
	==================

*/

	// Add rows to repeater
	$('button.repeat-add').click(function(e){

		e.preventDefault();
		var container = $(this).closest(repeat_container);
		var donor = container.find('.repeat').first().clone();
		$(this).before(donor);
		var just_added = container.find('.repeat').last();

		// Special modifications for image fields
		if ( container.find('.repeat').first().hasClass('image') ) {
			$('.uploaded-image', just_added).attr('src', '');
			$('.uploaded-image', just_added).hide();
			$('.closer', just_added).hide();
			$('.placeholder', just_added).show();
			$('.img_id', just_added).val('').trigger('change');
		}
		else {
			container.find('.repeat').last().find('input[type=text]').val('');
			container.find('.repeat').last().find('input[type=number]').val('');
			container.find('.repeat').last().find('textarea').html('');
		}

		// Special modifications for color fields
		if ( container.find('.repeat').first().hasClass('color') ) {
			var orig_input = container.find('.tpl-field').first().find('.tpl-color-field').get(0).outerHTML;
			$('.wp-picker-container', just_added).remove();
			$('.datatype-container', just_added).html(orig_input);
			$('.tpl-color-field', just_added).show();
			$('.tpl-color-field', just_added).wpColorPicker( color_picker_settings );
		}

		// Special modifications for Select fields
		if ( container.find('.repeat').first().hasClass('select') ) {
			just_added.find('.select2-container').remove();
			if ( $(just_added).hasClass('font_awesome') ) {
				$('select', just_added).select2( fa_icon_settings );
			}
			else {
				$('select', just_added).select2( select2_settings );
			}
		}

		// Special modifications for combined DT
		if ( container.find('.repeat').first().hasClass('combined') ) {

			just_added.find('.image').each(function(){
				$('.uploaded-image', this).attr('src', '');
				$('.uploaded-image', this).hide();
				$('.closer', this).hide();
				$('.placeholder', this).show();
				$('.img_id', this).val('').trigger('change');
			});

			just_added.find('.color').each(function(){
				var orig_input = $('label', this).get(0).outerHTML + '<br>' + $('.tpl-color-field', this).get(0).outerHTML + $('.tpl-default-container', this).get(0).outerHTML;
				$('.wp-picker-container', this).remove();
				$(this).html(orig_input);
				$('.tpl-color-field', this).show();
				$('.tpl-color-field', this).val( $('.tpl-color-field', this).attr('data-default-color') );
				$('.tpl-color-field', this).wpColorPicker( color_picker_settings );
			});

			// Special modifications for Select fields
			just_added.find('.select').each(function(){
				$('.select2-container', this).remove();
				if ( $(this).hasClass('font_awesome') ) {
					$('select', this).select2( fa_icon_settings );
				}
				else {
					$('select', this).select2( select2_settings );
				}
			});

		}

		tpl_repeater_refresh(container);

	});


	// Remove rows from repeater
	$('body').on('click', '.remover', function(){

		if ( Themple_Admin.remover_confirm == 'yes' ) {
			var remove = confirm( Themple_Admin.remover_confirm_text );
		}
		else {
			var remove = true;
		}

		if ( remove == true ) {
			var container = $(this).closest(repeat_container);
			var i = container.find('.tpl-field').length;
			if ( i > 1 ) {
				$(this).closest('.tpl-field').remove();
			}
			else {
				container.find('.tpl-field input').val('');
			}
			tpl_repeater_refresh(container);
		}

	});


	// Arrange rows
	$('.repeat').closest(repeat_container).addClass('repeater');
	$('.repeater').sortable({
		handle : '.arranger',
		update : function( event, ui ) {
			tpl_repeater_refresh( $(this) );
		},
		start : function( event, ui ){
        	ui.placeholder.height( ui.item.height() );
    	}
	});


	// Update order numbers after repeater item added, removed or rearranged
	function tpl_repeater_refresh(container){

		var i = 0;
		container.find('.repeat').each(function(){
			$(this).attr('data-instance', i);
			$('input, select, textarea', this).each(function(){
				var oldname = $(this).attr('name');
				if ( typeof(oldname) !== 'undefined' ) {
					var newname = oldname.replace(/\[[0-9]+\]/, '['+i+']');
					$(this).attr('name', newname);
					$(this).attr('id', newname);
					$(this).closest('.repeat').find('label').each(function(){
						if ( $(this).attr('for') == oldname ) {
							$(this).attr('for', newname);
						}
					});
				}
			});
			if ( container.find('.repeat').first().hasClass('image') ) {
				var base_name = $('.button', this).attr('name');
				base_name = base_name.replace('_button', '');
				var ins_name = base_name + '_' + i;
				$('.img_id', this).attr('id', ins_name);
			}
			i++;
		});

	}




/*

	4. COMMON SETTINGS
	==================

	4.1 TABS IN THE BACKEND
	-----------------------

*/

	// Tabs in the Theme Options
	$('#tpl-settings-tabs').tabs();

	// Set the active tab if present in sessionStorage
	if (typeof(Storage) !== "undefined") {
		var tabName = $('#tpl-settings-tabs').attr('data-store');
		var tabValue = sessionStorage.getItem(tabName);
		if (typeof(tabValue) !== "undefined") {
			$('#tpl-settings-tabs').tabs('option', 'active', tabValue);
		}
	}

	// Save the active tab to sessionStorage for future use
	$('#tpl-settings-tabs .nav-tab').click(function(){

		if (typeof(Storage) !== "undefined") {
			var tabName = $('#tpl-settings-tabs').attr('data-store');
			var tabValue = $('#tpl-settings-tabs').tabs('option', 'active');
		    sessionStorage.setItem(tabName, tabValue);
		}

	});




/*

	4.2 HOVER MESSAGES ON ICONS
	---------------------------

*/

	// Hover messages for icons
	$('body').on('mouseover', '.closer, .remover, .arranger', function(){

		$('.hovermsg',this).show();

	});

	$('body').on('mouseout', '.closer, .remover, .arranger', function(){

		$('.hovermsg',this).hide();

	});




/*

	5. CONDITIONAL SETTINGS
	=======================

*/


	// Retrieves (name) parameter's value from (url)
	function tpl_get_url_param(name, url) {
	    if (!url) url = window.location.href;
	    name = name.replace(/[\[\]]/g, "\\$&");
	    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
	        results = regex.exec(url);
	    if (!results) return null;
	    if (!results[2]) return '';
	    return decodeURIComponent(results[2].replace(/\+/g, " "));
	}


	// Checks the current post type in admin
	function tpl_get_post_type(){
		var attrs, attr, post_type;
		post_type = null;

		attrs = $( 'body' ).attr( 'class' ).split( ' ' );
		$( attrs ).each(function() {
			if ( 'post-type-' === this.substr( 0, 10 ) ) {
				post_type = this.split( 'post-type-' );
				post_type = post_type[ post_type.length - 1 ];
				return;
			}
		});

		return post_type;
	}


	// Checks if a variable is numeric
	function isNumeric(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	}


	// Updates the state of the conditional options. Use it after every change in the conditions.
	function tpl_condition_updater() {
		var url = window.location.href;
		// Primary Options page (Theme Options, Framework Options)
		if ( url.indexOf('themes.php') > -1 ) {
			var section = tpl_get_url_param('page', url);
			var container = 'tr';
		}
		// Post options branch
		if ( url.indexOf('post.php') > -1 ) {
			var section = '';
			var container = '.meta-option';
		}

		$('.tpl-field').each(function(){

			var option_name = $(this).attr('data-name');

			// Do things only if the condition is registered in the Conditions object
			if ( Themple_Admin.Conditions[option_name] !== undefined ) {

				var Olength = Object.keys(Themple_Admin.Conditions[option_name]).length;
				var ci;
				var matches = [];

				for ( ci = 0; ci < Olength; ++ci ) {


					if ( typeof( Themple_Admin.Conditions[option_name][ci] ) !== 'undefined' ) {

						var condition_type = Themple_Admin.Conditions[option_name][ci]["type"];
						var condition_name = Themple_Admin.Conditions[option_name][ci]["name"];
						var condition_relation = Themple_Admin.Conditions[option_name][ci]["relation"];
						var condition_value = Themple_Admin.Conditions[option_name][ci]["value"];
						var base_id = '';
						var base_val = '';
						var cname_array = [];


						// If the condition is an option
						if ( condition_type == 'option' ) {

							// Modifications on the name if it's a cobined field
							if ( condition_name.indexOf( '/' ) > -1 ) {

								cname_array = condition_name.split("/");

								// Add 0 as the instance number if there's no instance number defined
								if ( !isNumeric( cname_array[1] ) ) {
									cname_array.splice( 1, 0, 0 );
								}

								if ( cname_array[0] == "_THIS_" ) {
									cname_array[0] = $(this).parent().closest('.tpl-field').attr('data-name');
									cname_array[1] = $(this).parent().closest('.tpl-field').attr('data-instance');
								}

							}

							else {

								cname_array[0] = condition_name;
								cname_array[1] = 0;

							}


							// Primary section branch for getting the base values
							// base_id: ID of the element that's the base of the condition
							if ( section != '' ) {
								base_id = '#' + section + '\\[' + cname_array[0] + '\\]\\[' + cname_array[1] + '\\]';
								if ( cname_array[2] !== undefined ) {
									base_id += '\\[' + cname_array[2] + '\\]';
								}
							}
							// We're now at the point where we can set up the section for the post option
							// After that we get the value of the base element
							else {
								section = $(this).closest('.postbox').attr('id');
								base_id = '#' + section + '_' + cname_array[0] + '\\[' + cname_array[1] + '\\]';
								if ( cname_array[2] !== undefined ) {
									base_id += '\\[' + cname_array[2] + '\\]';
								}
							}

							// base_val: the base element's value
							base_val = $(base_id).val();

						}


						// If the condition is a post type
						if ( condition_type == 'post' ) {

							// In this case base_val is the post type caught from the current post
							if ( condition_name == 'type' ) {

								base_val = tpl_get_post_type();

							}

							// In this case base_val is the ID of a specific post
							if ( condition_name == 'id' ) {

								base_val = tpl_get_url_param( 'post', url );

							}

						}


						// If the condition is a page template
						if ( condition_type == 'page' ) {

							// In this case base_val is the post type caught from the current post
							if ( condition_name == 'template' ) {

								base_val = $('#page_template').val();

							}

						}


						// If the condition is a taxonomy
						if ( condition_type == 'taxonomy' ) {

							// Post formats are a special case
							if ( condition_name == 'post_format' ) {

								$('#post-formats-select input').each(function(){
									if ( $(this).is(':checked') ) {
										base_val = $(this).attr('id').replace( 'post-format-', '' );
									}
								});

							}

							// Other taxonomies work in the same way (except tags - they are not supported at the moment)
							else {

								var base_val = [];
								var cat_no = 0;
								$('#' + condition_name + 'checklist li').each(function(){
									cat_no = $(this).attr('id').replace( condition_name + '-', '' );
									if ( $('input', this).is(':checked') ) {
										base_val.push( cat_no );
									}
								});

							}

						}


						// Setting up the results of the relations
						switch ( condition_relation ) {
							case '=':
								if ( $.isArray( base_val ) ) {
									if ( $.inArray( condition_value, base_val ) > -1 ) {
										matches.push( true );
									}
									else {
										matches.push( false );
									}
								}
								else {
									if ( base_val == condition_value ) {
										matches.push( true );
									}
									else {
										matches.push( false );
									}
								}
								break;
							case '!=':
								if ( $.isArray( base_val ) ) {
									if ( $.inArray( condition_value, base_val ) < 0 ) {
										matches.push( true );
									}
									else {
										matches.push( false );
									}
								}
								else {
									if ( base_val != condition_value ) {
										matches.push( true );
									}
									else {
										matches.push( false );
									}
								}
								break;
							case '<':
								if ( base_val < condition_value ) {
									matches.push( true );
								}
								else {
									matches.push( false );
								}
								break;
							case '>':
								if ( base_val > condition_value ) {
									matches.push( true );
								}
								else {
									matches.push( false );
								}
								break;
						}

					}


				} // Conditions FOR cycle


				// Now displaying it or not based on the results
				var met = false;
				var logic = 'and';

				if ( Themple_Admin.Conditions[option_name]["logic"] !== undefined ) {
					var logic = Themple_Admin.Conditions[option_name]["logic"];
				}

				// If logic is (and) and there is no FALSE result in the matches array, then the condition is met
				if ( logic == 'and' && $.inArray( false, matches ) < 0 ) {
					met = true;
				}

				// If logic is (or) and there is at least one TRUE result in the matches array, then the condition is met
				if ( logic == 'or' && $.inArray( true, matches ) > -1 ) {
					met = true;
				}


				// Now show or hide the option based on the (met) variable
				if ( met == true ) {
					if ( $(this).hasClass("subitem") ) {
						$(this).removeClass('tpl-admin-hide');
					}
					else {
						$(this).closest(container).removeClass('tpl-admin-hide');
						if ( container == 'tr' ) {
							$(this).closest(container).next(container).has('.optiondesc').removeClass('tpl-admin-hide');
						}
						else {
							$(this).closest(container).next('p.optiondesc').removeClass('tpl-admin-hide');
						}
					}
				}
				else {
					if ( $(this).hasClass("subitem") ) {
						$(this).addClass('tpl-admin-hide');
					}
					else {
						$(this).closest(container).addClass('tpl-admin-hide');
						if ( container == 'tr' ) {
							$(this).closest(container).next(container).has('.optiondesc').addClass('tpl-admin-hide');
						}
						else {
							$(this).closest(container).next('p.optiondesc').addClass('tpl-admin-hide');
						}
					}
				}

			} // Closing check for the Conditions object

		});
	}

	tpl_condition_updater();

	$('input, select, textarea').change(function(){
		tpl_condition_updater();
	});

	$('input, textarea').keyup(function(){
		tpl_condition_updater();
	});


});
