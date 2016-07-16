<?php

// The file must have the type-[data-type].php filename format


// Enqueue the color picker script
add_action( 'admin_enqueue_scripts', function ( $hook_suffix ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker', array('jquery') );
});



class TPL_Color extends TPL_Data_Type {

	public	$default		= "#000000";		// Default color if no other defaults are set


	// Writes the form field in wp-admin
	public function form_field_content ( $for_bank = false ) {

		echo '<div class="datatype-container">';

		if ( $this->get_option() == "" ) {
			$value = $this->default;
		}
		else {
			$value = $this->get_option();
		}

		if ( $for_bank == true ) {
			$value = $this->default;
		}

		echo '<input name="' . $this->form_ref() . '" id="' . $this->form_ref() . '" type="text" value="' . esc_attr( $value ) . '" class="tpl-color-field" data-default-color="' . $this->default . '" />';

		echo '</div>';

	}


	// Formats the option into value
	public function format_option ( $value, $args = array() ) {

		return $value;

	}


}
