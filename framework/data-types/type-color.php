<?php

// The file must have the type-[data-type].php filename format


// Enqueue the color picker script
add_action( 'admin_enqueue_scripts', function ( $hook_suffix ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker', array('jquery') );
});



class TPL_Color extends TPL_Data_Type {

	public $default		= "#000000";


	// Writes the form field in wp-admin
	public function form_field_content () {

		if ( $this->get_current_option() == "" ) {
			$value = $this->default;
		}
		else {
			$value = $this->get_current_option();
		}

		echo '<input name="' . $this->form_ref() . '" type="text" value="' . esc_attr( $value ) . '" class="tpl-color-field" data-default-color="' . $this->default . '" />';

	}


	// Formats the option into value
	public function format_option ( $value, $args = array() ) {

		return $value;

	}


	// Gives you the color code based on the option name
	public function get_value ( $args = array() ) {

		// Spec branch (picks an instance of an array)
		if ( is_array( $args ) && isset( $args["i"] ) && is_numeric( $args["i"] ) ) {
			if ( $this->get_option( array( "i" => $args["i"] ) ) == "" ) {
				return $this->default;
			}
			else {
				return $this->get_option( array( "i" => $args["i"] ) );
			}
		}

		// Full branch (returns the full array)
		if ( $this->repeat == true ) {

			$values = $this->get_option();
			foreach ( $values as $i => $value ) {
				if ( $value == "" ) {
					$values[$i] = $this->default;
				}
			}
			return $values;

		}

		// Single mode if not repeater
		if ( $this->get_current_option() == "" ) {
			return $this->default;
		}
		return $this->get_current_option();

	}


}
