<?php

// The file must have the type-[data-type].php filename format


class TPL_Text extends TPL_Data_Type {

	public $size		= 0;		// Infinite
	public $less_string	= true;


	// Writes the form field in wp-admin
	public function form_field_content () {

		echo $this->prefix .'<input type="text" id="' . $this->form_ref() . '" name="' . $this->form_ref() . '" value="' . esc_attr( $this->get_current_option() ) . '"';

		if ( $this->size > 0 ) {
			echo ' maxlength="' . intval( $this->size ) . '"';
		}

		if ( $this->placeholder != '' ) {
			echo ' placeholder="' . esc_attr( $this->placeholder ) . '"';
		}

		echo ' />'. $this->suffix;

	}


}
