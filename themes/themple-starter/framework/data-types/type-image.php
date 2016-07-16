<?php

// The file must have the type-[data-type].php filename format


// Enqueue the media uploader script
add_action( 'admin_print_scripts-appearance_page_tpl_theme_options', function() {
	wp_enqueue_media();
} );



class TPL_Image extends TPL_Data_Type {

	protected	$less_string	= true;				// Should the LESS variable forced to be a string or keep as a natural value
	public		$size			= "medium";			// The default size of the image if not set with the option registration - an image size registered in WP
	public		$js_func		= "get_image_url";	// Which function should create the JS variable


	// Sets up the object attributes while registering options
	public function __construct( $args ) {

		if ( !isset( $args["admin_class"] ) ) {
			$args["admin_class"] = '';
		}
		$args["admin_class"] .= ' uploader';

		parent::__construct( $args );

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

		echo '<div class="datatype-container">';

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
				<input class="img_id" type="hidden" name="' . $this->form_ref() . '" id="' . $this->form_ref() . '" value="' . $id . '" />
				<input class="button" type="button" name="' . $this->name . '_button" value="'. __( 'Upload', 'themple' ) .'" />';

		echo '</div>';

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
			'class'	=> "logo",
			'path'	=> array( 0 => 'tpl_logo' ),
		);
	}

	if ( tpl_option_registered ( "tpl_logo" ) ) {

		$imgobj = tpl_get_option_object( "tpl_logo" );
		if ( $imgobj->repeat !== false ) {
			$args["path"][1] = 0;
		}

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
