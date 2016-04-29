// Media uploader script
jQuery(document).ready(function($) {

	"use strict";

    var custom_uploader;

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
			current_item.find('.img_id').val(attachment.id);
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
		$(this).closest('.tpl-field').find('.img_id').val('');
		$(this).hide();

	});



	// Color picker script
    $('.tpl-color-field').wpColorPicker();


    // Tabs in the Theme Options
	$('#tpl-settings-tabs').tabs();

	// Set the active tab if present
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


	// Handle icons
	$('body').on('mouseover', '.closer, .remover, .arranger', function(){

		$('.hovermsg',this).show();
		$('.remover').not(this).css('z-index', 0);
		$('.arranger').not(this).css('z-index', 0);

	});

	$('body').on('mouseout', '.closer, .remover, .arranger', function(){

		$('.hovermsg',this).hide();
		$('.remover').css('z-index', 2);
		$('.arranger').css('z-index', 2);

	});


	// Decide which container to use based on  where we are: on Post editor or on Theme Options page
	if ( $('body').hasClass('post-new-php') || $('body').hasClass('post-php') ) {
		var repeat_container = '.meta-option-wrapper';
	}
	else {
		var repeat_container = 'td';
	}


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
			$('.img_id', just_added).val('');
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
			$('.arranger', just_added).after(orig_input);
			$('.tpl-color-field', just_added).show();
			$('.tpl-color-field', just_added).wpColorPicker();
		}

		// Special modifications for combined DT
		if ( container.find('.repeat').first().hasClass('combined') ) {

			just_added.find('.image').each(function(){
				$('.uploaded-image', this).attr('src', '');
				$('.uploaded-image', this).hide();
				$('.closer', this).hide();
				$('.placeholder', this).show();
				$('.img_id', this).val('');
			});

			just_added.find('.color').each(function(){
				var orig_input = $('.tpl-color-field', this).get(0).outerHTML;
				$('.wp-picker-container', this).remove();
				$(this).html(orig_input);
				$('.tpl-color-field', this).show();
				$('.tpl-color-field', this).val( $('.tpl-color-field', this).attr('data-default-color') );
				$('.tpl-color-field', this).wpColorPicker();
			});

		}

		repeater_refresh(container);

	});


	// Remove rows from repeater
	$('body').on('click', '.remover', function(){

		var container = $(this).closest(repeat_container);
		var i = container.find('.tpl-field').length;
		if ( i > 1 ) {
			$(this).closest('.tpl-field').remove();
		}
		else {
			container.find('.tpl-field input').val('');
		}
		repeater_refresh(container);

	});


	// Arrange rows
	$('.repeat').closest(repeat_container).addClass('repeater');
	$('.repeater').sortable({
		handle : '.arranger',
		update : function( event, ui ) {
			repeater_refresh( $(this) );
		}
	});


	// Update order numbers after repeater item added, removed or rearranged
	function repeater_refresh(container){

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


});
