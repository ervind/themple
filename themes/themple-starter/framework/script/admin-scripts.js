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
6. Page Builder settings

*/




/*

	1. DEFINING THE VARIABLES NECESSARY FOR THIS SCRIPT
	===================================================

*/

	// Needed for the Image data type
    var custom_uploader;

	// Needed for the Select data type
	// Basic settings for all types of dropdowns
	var basic_select2_settings = {
		escapeMarkup: function(m) {
			return m;
		},
		width: "100%",
	}

	// General settings for simple select dropdowns
	var select2_settings = $.extend( {
		minimumResultsForSearch: 10,
	}, basic_select2_settings );

	// Font Awesome dropdown settings
	var fa_icon_settings = $.extend( {
		templateSelection: tpl_fa_icon_template,
		templateResult: tpl_fa_icon_template,
		minimumResultsForSearch: 10,
	}, basic_select2_settings );

	// Page Builder columnset dropdown settings
	var pb_columnset_settings = $.extend( {
		templateSelection: tpl_pb_columnset_template,
		templateResult: tpl_pb_columnset_template,
		minimumResultsForSearch: Infinity,
	}, basic_select2_settings );

	// Do an initial row arrangement, just in case...
	tpl_arrange_rows();




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
    $('#wpcontent .tpl-color-field').each(function(){
		$(this).wpColorPicker( color_picker_settings );
	});





/*

	2.3 SELECT DATA TYPE
	--------------------

*/

	// Select scripts
	$('#wpcontent .tpl-field.select').not('.font_awesome').find('select').select2( select2_settings );
	$('#wpcontent .tpl-field.select.font_awesome select').select2( fa_icon_settings );
	$('#wpcontent .tpl-field.select.tpl-columnset select').select2( pb_columnset_settings );

	function tpl_fa_icon_template(data, container) {
		return '<i class="fa fa-' + data.id + '"></i> ' + data.text;
	}

	function tpl_pb_columnset_template(data, container) {
		var structure = '<span class="columnset-structure">';
		if ( data.id != undefined ) {
			var columnset = data.id.split('-');
			for ( var i = 0; i < columnset.length; i++ ) {
				structure += '<span class="columnset-structure-part csp-'+ columnset[i].replace('/','_') +'"></span>';
			}
		}
		structure += '</span>';
		return structure + data.text;
	}




/*

	3. REPEATER FIELDS
	==================

*/

	function tpl_repeat_set_container( elem ){

		var url = window.location.href;

		// Primary Options page (Theme Options, Framework Options)
		if ( url.indexOf('themes.php') > -1 ) {
			if ( elem.parent().prev().hasClass('subitem-repeat-wrapper') ) {
				var container = elem.parent().prev('.subitem-repeat-wrapper');
			}
			else {
				var container = elem.closest('tr').prev().children('.repeater');
			}
		}
		// Post options branch
		if ( url.indexOf('post.php') > -1 ) {
			if ( elem.parent().prev().hasClass('subitem-repeat-wrapper') ) {
				var container = elem.parent().prev('.subitem-repeat-wrapper');
			}
			else {
				var container = elem.parent().prev('.meta-option-wrapper');
			}
		}

		return container;

	}



	// Add rows to repeater
	$('body').on('click', 'button.repeat-add', function(e){
		e.preventDefault();
		var container = tpl_repeat_set_container( $(this) );
		tpl_add_row( container, $(this).attr('data-for') );
	});

	function tpl_add_row( container, data_name ) {

		var donor = $("#repeater_bank .tpl-field[data-name='" + data_name + "']").clone();
		container.append(donor);
		var just_added = container.find('.repeat').last();

		// Launch color picker
		$('#wpcontent .tpl-color-field').wpColorPicker( color_picker_settings );

		// Launch Select2 on select fields
		$('#wpcontent .tpl-field.select').not('.font_awesome').find('select').select2( select2_settings );
		$('#wpcontent .tpl-field.select.font_awesome select').select2( fa_icon_settings );
		$('#wpcontent .tpl-field.select.tpl-columnset select').select2( pb_columnset_settings );

		tpl_repeater_refresh(container);

		tpl_arrange_rows();

		tpl_condition_updater();

	}


	// Remove rows from repeater
	$('body').on('click', '.remover', function(){
		tpl_remove_row( $(this) );
	});

	function tpl_remove_row( elem ) {

		var container = $(elem).closest('.repeater');

		if ( Themple_Admin.remover_confirm == 'yes' ) {
			var remove = confirm( Themple_Admin.remover_confirm_text );
		}
		else {
			var remove = true;
		}

		if ( remove == true ) {
			$(elem).closest('.tpl-field').remove();
			container.each(function(){
				tpl_repeater_refresh($(elem));
			});
		}

	};


	// Arrange rows
	function tpl_arrange_rows(){
		$('.repeat').each(function(){
			$(this).parent().addClass('repeater');
		});
		$('.repeater').sortable({
			handle : '.arranger',
			update : function( event, ui ) {
				tpl_repeater_refresh( $(this) );
			},
			start : function( event, ui ){
				ui.placeholder.height( ui.item.height() );
			}
		});
	}


	// Update order numbers after repeater item added, removed or rearranged
	function tpl_repeater_refresh(container){

		var url = window.location.href;
		var i = [];
		i[0] = 0;
		var name = '';

		container.find('.tpl-field').each(function(){

			// Setting up levels + instances for the current field
			var level = parseInt($(this).attr('data-level'));
			var name_array = $(this).attr('data-name').split('/');

			if ( level > 0 ) {

				for (var k=0; k<level; k++) {
					var data_name = '';

					for (var l=0; l<=k; l++) {
						data_name += name_array[l];
						if ( l < k ) {
							data_name += '/';
						}
					}

					i[k] = parseInt( $(this).parents('.tpl-field[data-name="'+ data_name +'"]').last().attr('data-instance') );
				}

			}

			// If a new sort of item is found
			var inum = parseInt( $(this).prev('.tpl-field[data-name="'+ $(this).attr('data-name') +'"]').attr('data-instance') );

			if ( isNumeric(inum) ) {
				i[level] = inum + 1;
			}
			else {
				i[level] = 0;
			}

			$(this).attr('data-instance', i[level]);
			if ( level == 0 ) {
				for (var j=1; j<i.length; j++) {
					i[j] = 0;
				}
			}

			$('input, select, textarea', this).each(function(){
				var oldname = $(this).attr('name');
				if ( typeof(oldname) !== 'undefined' ) {
					var oldname_array = oldname.split('[');
					var newname = oldname_array[0];
					for (var j=0; j<name_array.length; j++) {
						if ( j < name_array.length - 1 ) {
							if ( url.indexOf('post.php') > -1 && j == 0 ) {
								newname += '[' + i[j] + ']';
							}
							else {
								newname += '[' + name_array[j] + '][' + i[j] + ']';
							}
						}
						else {
							if ( $(this).closest('.tpl-field').hasClass('repeat') ) {
								newname += '[' + name_array[j] + '][' + i[j] + ']';
							}
							else {
								newname += '[' + name_array[j] + ']';
							}
						}
					}

					$(this).attr('name', newname);
					$(this).attr('id', newname);
					$(this).closest('.tpl-field').prev('label').each(function(){
						$(this).attr('for', newname);
					});
				}
			});

			if ( container.find('.repeat').first().hasClass('image') ) {
				var base_name = $('.button', this).attr('name');
				base_name = base_name.replace('_button', '');
				var ins_name = base_name + '_' + i[level];
				$('.img_id', this).attr('id', ins_name);
			}

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
	$('body').on('mouseover', '.admin-icon', function(){

		$('.hovermsg',this).show();

	});

	$('body').on('mouseout', '.admin-icon', function(){

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

		$('#wpcontent .tpl-field, #wpcontent .subitem-repeat-wrapper, #wpcontent .meta-option').each(function(){

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

			var option_name = $(this).attr('data-name');
			var data_connected = $(this).attr('data-connected');

			if ( $(this).hasClass('subitem-repeat-wrapper') || $(this).hasClass('meta-option') ) {
				option_name = data_connected;
			}

			// Section is different when we're dealing with a post option
			if ( section == '' ) {
				section = $(this).closest('.postbox').attr('id');
			}


			// Do things only if the condition is registered in the Conditions object
			if ( Themple_Admin.Conditions !== undefined && Themple_Admin.Conditions[option_name] !== undefined ) {

				var Olength = Object.keys(Themple_Admin.Conditions[option_name]).length;
				var ci;
				var matches = [];

				for ( ci = 0; ci < Olength; ci++ ) {


					if ( typeof( Themple_Admin.Conditions[option_name][ci] ) !== 'undefined' ) {

						var condition_type = Themple_Admin.Conditions[option_name][ci]["type"];
						var condition_name = Themple_Admin.Conditions[option_name][ci]["name"];
						var condition_relation = Themple_Admin.Conditions[option_name][ci]["relation"];
						var condition_value = Themple_Admin.Conditions[option_name][ci]["value"];
						var base_id = '';
						var base_val = '';
						var cname_array = [];
						var c_array = [];
						var dname_array = [];


						// If the condition is an option
						if ( condition_type == 'option' ) {

							// Modifications on the name if it's a cobined field
							if ( condition_name.indexOf( '/' ) > -1 ) {

								cname_array = condition_name.split("/");
								dname_array = option_name.split("/");

								// If the base of the condition is a sibling field of the current one
								if ( cname_array[0] == "_THIS_" ) {

									for ( var j = 0; j < dname_array.length - 1; j++ ) {
										c_array[2*j] = dname_array[j];

										var current_data_name = '';
										for ( var k = 0; k <= j; k++ ) {
											current_data_name += dname_array[k];
											if ( k < j ) {
												current_data_name += '/';
											}
										}

										c_array[2*j+1] = $(this).closest('.tpl-field[data-name="'+ current_data_name +'"]').attr('data-instance');
									}

									c_array.push( cname_array[cname_array.length-1] );

								}

								// If the base of the condition is an absolute path
								else {

									c_array = cname_array;

								}

							}

							// Condition is a single variable
							else {

								c_array[0] = condition_name;
								c_array[1] = 0;

							}

							base_id = '#' + section;
							if ( url.indexOf('post.php') > -1 ) {
								base_id += '_' + c_array[0];
							}
							for ( var l = 0; l < c_array.length; l++ ) {
								if ( url.indexOf('post.php') > -1 ) {
									if ( l > 0 ) {
										base_id += '\\[' + c_array[l] + '\\]';
									}
								}
								else {
									base_id += '\\[' + c_array[l] + '\\]';
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

				// If logic is (AND) and there is no FALSE result in the matches array, then the condition is met
				if ( logic == 'and' && $.inArray( false, matches ) < 0 ) {
					met = true;
				}

				// If logic is (OR) and there is at least one TRUE result in the matches array, then the condition is met
				if ( logic == 'or' && $.inArray( true, matches ) > -1 ) {
					met = true;
				}


				// Now show or hide the option based on the (met) variable
				data_connected = data_connected.replace('/', '\/');
				$(this).closest(container).attr('data-connected', data_connected);

				if ( met == true ) {

					if ( typeof( data_connected ) !== "undefined" ) {
						$(this).parent().parent().parent().find('[data-connected="'+ data_connected +'"]').removeClass('tpl-admin-hide');
						if ( container == 'tr' ) {
							$(this).closest(container).next(container).has('.optiondesc').removeClass('tpl-admin-hide');
						}
						else {
							$(this).closest(container).next('p.optiondesc').removeClass('tpl-admin-hide');
						}
						$(this).find('input, select, textarea').each(function(){
							$(this).attr('name', $(this).attr('id'));
						});
					}

				}
				else {

					if ( typeof( data_connected ) !== "undefined" ) {
						$(this).parent().parent().parent().find('[data-connected="'+ data_connected +'"]').addClass('tpl-admin-hide');
						if ( container == 'tr' ) {
							$(this).closest(container).next(container).has('.optiondesc').addClass('tpl-admin-hide');
						}
						else {
							$(this).closest(container).next('p.optiondesc').addClass('tpl-admin-hide');
						}
						$(this).find('input, select, textarea').removeAttr('name');
					}

				}

			} // Closing check for the Conditions object

		});

		$('.postbox').each(function(){
			var meta_hide = [];
			$('.meta-option', this).each(function(){
				if ( $(this).hasClass('tpl-admin-hide') ) {
					meta_hide.push( true );
				}
				else {
					meta_hide.push( false );
				}
			});
			if ( $.inArray( true, meta_hide ) > -1 && $.inArray( false, meta_hide ) < 0 ) {
				$(this).addClass('tpl-admin-hide');
			}
			else {
				$(this).removeClass('tpl-admin-hide');
			}
		});


	}

	tpl_condition_updater();

	$('body').on('change', 'select', function(){
		tpl_condition_updater();
	});

	$('body').on('keyup', 'input, textarea', function(){
		tpl_condition_updater();
	});




/*

	6. PAGE BUILDER SETTINGS
	========================

*/


	// First let's save the previous value of the columnset into a global variable in case we want to cancel the action
	$('body').on('select2:open', '.tpl-columnset select', function(){

		Themple_Admin.prev_columnset = $(this).val();

	// Now handle the select environments
	}).on('select2:select', '.tpl-columnset select', function(){

		var old_col_no = Themple_Admin.prev_columnset.split('-').length;
		var columnset = $(this).val().split('-');

		// Remove branch
		if ( columnset.length < old_col_no ) {
			Themple_Admin.pb_fewer_instances = Themple_Admin.pb_fewer_instances.replace( '##N##', old_col_no - columnset.length );

			// Do we need to confirm the remove command? (can be set up in Framework Options)
			if ( Themple_Admin.pb_fewer_confirm == 'yes' ) {
				var remove = confirm( Themple_Admin.pb_fewer_instances );
			}
			// If not, the green light is the default for deletion
			else {
				var remove = true;
			}
			// Now removing the rows
			if ( remove == true ) {
				for ( var i = old_col_no; i >= columnset.length; i-- ) {
					tpl_remove_row( $(this).closest('.tpl-field').parent().find('.tpl-pb-apps[data-instance="'+ i +'"]') );
				}
			}
			// If cancel was pressed, make sure that we set Select2 to the previous value
			else {
				$(this).val(Themple_Admin.prev_columnset).trigger('change');
			}
		}

		// Add branch
		else if ( columnset.length > old_col_no ) {

			for ( var i = old_col_no + 1; i <= columnset.length; i++ ) {
				tpl_add_row( $(this).closest('.tpl-field').parent().find('.tpl-pb-apps').closest('.subitem-repeat-wrapper'), $(this).closest('.tpl-field').parent().find('.tpl-pb-apps').first().attr('data-name') );
			}

		}

	});



});
