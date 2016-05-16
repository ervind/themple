<?php

// The file must have the type-[data-type].php filename format


class TPL_Text extends TPL_Data_Type {

	protected	$size			= 0;		// 0 = Infinite string length; Other number = max length of the string
	protected	$less_string	= true;		// Should the LESS variable forced to be a string or keep as a natural value


	// Writes the form field in wp-admin
	public function form_field_content () {

		echo '<div class="datatype-container">';

		echo '<input type="text" id="' . $this->form_ref() . '" name="' . $this->form_ref() . '" value="' . esc_attr( $this->get_current_option() ) . '"';

		if ( $this->size > 0 ) {
			echo ' maxlength="' . intval( $this->size ) . '"';
		}

		if ( $this->placeholder != '' ) {
			echo ' placeholder="' . esc_attr( $this->placeholder ) . '"';
		}

		echo ' />';

		echo '</div>';

	}


}
