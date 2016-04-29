<?php

// The file must have the type-[data-type].php filename format


// Enqueue the media uploader script
add_action( 'admin_print_scripts-appearance_page_tpl_theme_options', function() {
	wp_enqueue_media();
} );



class TPL_Image extends TPL_Data_Type {

	public $size		= "medium";			// an image size registered in WP
	public $less_string	= true;				// Should the LESS variable forced to be a string or keep as a natural value
	public $js_func		= "get_image_url";	// Which function should create the JS variable


	public function form_field_before ( $extra_class = 'uploader' ) {

		parent::form_field_before( $extra_class );

	}


	// Writes the form field in wp-admin
	public function form_field_content () {

		$id = $this->get_current_option();
		$imgdata = wp_get_attachment_image_src( $id, 'thumbnail' );

		if ( $this->repeat == true ) {
			$id_name = $this->name . '_' . $this->instance;
		}
		else {
			$id_name = $this->name;
		}

		echo '	<div class="image-container">
					<img class="uploaded-image" alt="" src="' . $imgdata[0] . '"';

			if ( $id == '' || !is_numeric( $id ) ) {
				echo ' style="display: none"';
			}

		echo ' />
					<img class="placeholder" alt="" src="' . get_template_directory_uri() .'/framework/img/no-image-placeholder.png"';

			if ( $id != '' && is_numeric( $id ) ) {
				echo ' style="display: none;"';
			}

		echo '  />
					<div class="admin-icon closer" style="';

			if ( $id == '' || !is_numeric( $id ) ) {
				echo ' display: none';
			}

		echo '">
						<span class="hovermsg">'. __( 'Click here to remove image.', 'themple' ) .'</span>
					</div>
				</div>
				<input class="img_id" type="hidden" name="' . $this->form_ref() . '" id="' . $id_name . '" value="' . $id . '" />
				<input class="button" type="button" name="' . $this->name . '_button" value="'. __( 'Upload', 'themple' ) .'" />';

	}


	// Formats the option into value
	public function format_option ( $value, $args = array() ) {

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
			return wp_get_attachment_image ( $value, $size, 0, $atts );
		}
		else {
			return wp_get_attachment_image ( $value, $size );
		}

	}


	// Returns the image element
	public function get_value ( $args = array() ) {

		// Spec branch (picks an instance of an array)
		if ( is_array( $args ) && isset( $args["i"] ) && is_numeric( $args["i"] ) ) {

			$img_id = intval( $this->get_option( array( "i" => $args["i"] ) ) );
			if ( is_numeric ( $img_id ) ) {
				return $this->format_option( $img_id, $args );
			}

		}

		// Full branch (returns the full array)
		if ( $this->repeat == true ) {

			$values = $this->get_option();
			foreach ( $values as $i => $img_id ) {
				$img_id = intval( $img_id );
				if ( is_numeric ( $img_id ) ) {
					$values[$i] = $this->format_option( $img_id, $args );
				}
			}
			return $values;

		}

		// Single mode if not repeater
		$img_id = intval( $this->get_current_option() );
		if ( is_numeric ( $img_id ) ) {
			return $this->format_option( $img_id, $args );
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

		// Spec branch (picks an instance of an array)
		if ( is_array( $args ) && isset( $args["i"] ) && is_numeric( $args["i"] ) ) {

			$img_id = intval( $this->get_option( array( "i" => $args["i"] ) ) );
			if ( is_numeric ( $img_id ) ) {
				$img_src = wp_get_attachment_image_src ( $img_id, $size );
				return $img_src[0];
			}

		}

		// Full branch (returns the full array)
		if ( $this->repeat == true ) {

			$values = $this->get_option();
			foreach ( $values as $i => $img_id ) {
				$img_id = intval( $img_id );
				if ( is_numeric ( $img_id ) ) {
					$img_src = wp_get_attachment_image_src ( $img_id, $size );
					$values[$i] = $img_src[0];
				}
			}
			return $values;

		}

		// Single mode if not repeater
		$img_id = intval( $this->get_current_option() );
		if ( is_numeric ( $img_id ) ) {
			$img_src = wp_get_attachment_image_src ( $img_id, $size );
		}
		return $img_src[0];

	}


	// LESS variable helper function
	public function format_less_var( $name, $value ) {

		$less_variable = '@' . $name . ': ';

		// Should it be included in LESS as a string variable? If yes, put it inside quote marks
		if ( $this->less_string == true ) {
			$less_variable .= '"';
		}

		if ( $this->repeat == true ) {
			$less_variable .= $this->get_image_url( array( 'i' => $this->instance ) );
		}
		else {
			$less_variable .= $this->get_image_url();
		}

		// closing the string if needed
		if ( $this->less_string == true ) {
			$less_variable .= '"';
		}

		$less_variable .= ';';

		return $less_variable;

	}

}



// Gives you the image URL based on the option name
function tpl_get_image_url ( $args ) {

	global $tpl_options_array;

	if ( is_array( $args ) ) {
		$name = $args["name"];
		$caller = $args;
	}
	else {
		$name = $args;
		$caller = array(
			'name'	=> $name
		);
	}

	$imgobj = tpl_get_option_object( $name );

	$values = $imgobj->get_image_url( $caller );

	if ( is_array( $values ) ) {
		return $values[0];
	}
	else {
		return $values;
	}

}




// This is a special function that shows your "tpl_logo" option as the site logo, with linking it to the Home page. If no logo is present's shows the textual site name
function tpl_logo ( $link = true, $args = false ) {

	global $tpl_options_array;

	if ( $args === false ) {
		$args = array(
			'alt'	=> get_bloginfo( "name" ),
			'title'	=> get_bloginfo( "name" ),
			'class'	=> "logo"
		);
	}

	if ( tpl_option_registered ( "tpl_logo" ) ) {

		$imgobj = tpl_get_option_object( "tpl_logo" );
		$args["i"] = $imgobj->instance;

		if ( $imgobj->get_option() != '' ) {

			if ( $link ) {
				echo '<a href="' . get_bloginfo ('url') . '">' . $imgobj->get_value( $args ) . '</a>';
			}
			else {
				echo $imgobj->get_value( $args );
			}

		}

		else {

			if ( $link ) {
				echo '<h1 class="logo"><a href="' . get_bloginfo ('url') . '">' . get_bloginfo( "name" ) . '</a></h1>';
			}
			else {
				echo '<h1 class="logo">' . get_bloginfo( "name" ) . '</h1>';
			}

		}

	}

	else {
		tpl_error( __( 'Please register a logo first before calling the tpl_logo() function.', 'themple' ) );
	}

}
