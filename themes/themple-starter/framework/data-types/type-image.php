<?php

// The file must have the type-[data-type].php filename format


class TPL_Image extends TPL_Data_Type {

	protected	$less_string	= true;				// Should the LESS variable forced to be a string or keep as a natural value
	public		$size			= "medium";			// The default size of the image if not set with the option registration - an image size registered in WP
	public		$js_func		= "get_image_url";	// Which function should create the JS variable


	// Sets up the object attributes while registering options
	public function __construct( $args ) {

		global $tpl_settings_pages;

 		if ( !isset( $args["admin_class"] ) ) {
			$args["admin_class"] = '';
		}
		$args["admin_class"] .= ' tpl-uploader';

		parent::__construct( $args );


		add_action( 'init', function() {

			if ( !isset( $tpl_settings_pages[$this->get_data_section()]["image_uploader"] ) && tpl_is_primary_section( $this->section ) ) {

				add_filter( 'tpl_admin_js_strings', array( $this, 'admin_js_strings' ) );

				// Enqueue the media uploader script
				add_action( 'admin_print_scripts-' . $this->get_settings_page() . '_' . $this->get_data_section(), function() {
					wp_enqueue_media();
				} );

			$tpl_settings_pages[$this->get_data_section()]["image_uploader"] = true;

			}

		}, 40 );

	}


	// Writes the form field in wp-admin
	public function form_field_content ( $for_bank = false ) {

		if ( $for_bank == true ) {
			$id = '';
		}
		else {
			$id = $this->get_option();
		}

		$imgdata = wp_get_attachment_image_src( $id, 'thumbnail' );

		echo '<div class="tpl-datatype-container">';

		echo '	<div class="tpl-image-container">
					<img class="tpl-uploaded-image tpl-preview-0" alt="' . sprintf( __( '%s image tag', 'themple' ), esc_attr( $this->title ) ) . '" src="' . esc_url( $imgdata[0] ) . '"';

			if ( $id == '' || !is_numeric( $id ) ) {
				echo ' style="display: none"';
			}

		echo '>
					<img class="tpl-img-placeholder" alt="' . __( 'Placeholder image', 'themple' ) . '" src="' . esc_url( tpl_base_uri() ) .'/framework/img/no-image-placeholder.png"';

			if ( $id != '' && is_numeric( $id ) ) {
				echo ' style="display: none;"';
			}

		echo '>
					<div class="tpl-admin-icon tpl-closer" style="';

			if ( $id == '' || !is_numeric( $id ) ) {
				echo ' display: none';
			}

		echo '" title="'. __( 'Click here to remove image.', 'themple' ) .'">
					</div>
				</div>
				<input class="tpl-img_id" type="hidden" name="' . esc_attr( $this->form_ref() ) . '" id="' . esc_attr( $this->form_ref() ) . '" value="' . esc_attr( $id ) . '">
				<input class="button" type="button" name="' . esc_attr( $this->name ) . '_button" value="'. __( 'Upload', 'themple' ) .'">';

		echo '</div>';

	}


	// Formats the option into value
	public function format_option ( $value, $args = array() ) {

		// Return the image ID if we force it to be unformatted
		if ( $this->unformatted ) {
			return $value;
		}

		// Determining the size to be displayed
		if ( !isset( $args["size"] ) ) {
			$size = $this->size;
		}
		else {
			$size = $args["size"];
		}

		// Adding the extra attributes if needed
		$atts = array();
		if ( isset( $args["class"] ) ) {
			$atts["class"] = $args["class"];
		}
		if ( isset( $args["alt"] ) ) {
			$atts["alt"] = $args["alt"];
		}
		if ( isset( $args["title"] ) ) {
			$atts["title"] = $args["title"];
		}

		if ( !empty( $atts ) ) {
			return wp_get_attachment_image ( intval( $value ), $size, 0, $atts );
		}
		else {
			return wp_get_attachment_image ( intval( $value ), $size );
		}

	}



	// Gives you the image URL based on the option name
	public function get_image_url ( $args = array() ) {

		// Determining the size to be displayed
		if ( !isset( $args["size"] ) ) {
			$size = $this->size;
		}
		else {
			$size = $args["size"];
		}

		$path_n = $this->get_level() * 2;
		$path_i = $this->get_level() * 2 + 1;

		if ( !isset( $args["path"][$path_n] ) ) {
			$args["path"][$path_n] = $this->name;
		}

		if ( $this->repeat === false ) {
			$args["path"][$path_i] = 0;
		}

		$result = array();

		$values = $this->get_option( $args );

		// Repeater branch
		if ( !isset( $args["path"][$path_i] ) && is_array( $values ) ) {

			$values = $this->get_option( $args );
			foreach ( $values as $i => $img_id ) {
				$img_id = intval( $img_id );
				if ( is_numeric ( $img_id ) ) {
					$img_src = wp_get_attachment_image_src ( $img_id, $size );
					$result[$i] = $img_src[0];
				}
			}

		}

		// Single branch
		else {

			$img_id = intval( $values );
			if ( is_numeric ( $img_id ) ) {
				$img_src = wp_get_attachment_image_src ( $img_id, $size );
			}
			$result = $img_src[0];

		}

		return $result;

	}


	// LESS variable helper function
	public function format_less_var( $name, $value ) {

		$less_variable = '@' . $name . ': ';

		// Should it be included in LESS as a string variable? If yes, put it inside quote marks
		if ( $this->less_string == true ) {
			$less_variable .= '"';
		}

		$image_url_obj = wp_get_attachment_image_src ( $value, $this->size );
		$less_variable .= $image_url_obj[0];

		// closing the string if needed
		if ( $this->less_string == true ) {
			$less_variable .= '"';
		}

		$less_variable .= ';';

		return $less_variable;

	}


	// Strings to be added to the admin JS files
	public function admin_js_strings( $strings ) {

		$strings = array_merge( $strings, array(
			'uploader_title'		=> __( 'Choose Image', 'themple' ),
			'uploader_button'		=> __( 'Choose Image', 'themple' ),
			'tpl-dt-image_preview-template'	=> '<img src="[tpl-preview-0]" class="tpl-image-preview">',
		) );

		return $strings;

	}


}



// Gives you the image URL based on the option name
function tpl_get_image_url ( $args ) {

	if ( is_array( $args ) ) {
		$name = $args["name"];
		$caller = $args;
	}
	else {
		$name = $args;
		$path = explode( '/', $name );
		$caller["name"] = $path[0];
		$caller["path"] = $path;
	}

	$imgobj = tpl_get_option_object( $name );

	$values = $imgobj->get_image_url( $caller );

	return $values;

}




// This is a special function that shows your "tpl_logo" option as the site logo, with linking it to the Home page. If no logo is present's shows the textual site name
function tpl_logo ( $link = true, $args = false ) {

	global $tpl_options_array;

	if ( $args === false ) {
		$args = array(
			'alt'	=> get_bloginfo( "name" ),
			'title'	=> get_bloginfo( "name" ),
			'class'	=> "tpl-logo",
			'path'	=> array( 0 => 'tpl_logo' ),
		);
	}

	if ( tpl_option_registered ( "tpl_logo" ) ) {

		$imgobj = tpl_get_option_object( "tpl_logo" );
		if ( $imgobj->repeat !== false ) {
			$args["path"][1] = 0;
		}

		// If there is a logo set up, display it with the link or without
		if ( $imgobj->get_option() != '' ) {

			if ( $link ) {
				echo '<a href="' . esc_url( get_bloginfo( 'url' ) ) . '">';
			}

			echo $imgobj->get_value( $args );

			if ( $link ) {
				echo '</a>';
			}

		}

		// Else display an H1 tag with the site title
		else {

			echo '<h1 class="tpl-logo">';

			if ( $link ) {
				echo '<a href="' . esc_url( get_bloginfo ('url') ) . '">';
			}

			echo esc_html( get_bloginfo( "name" ) );

			if ( $link ) {
				echo '</a>';
			}

			echo '</h1>';

		}

	}

	else {
		tpl_error( __( 'Please register a logo first before calling the tpl_logo() function.', 'themple' ) );
	}

}
