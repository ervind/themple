<?php

// The file must have the type-[data-type].php filename format


class TPL_Number extends TPL_Data_Type {

	protected	$step		= NULL;		// Distance between 2 consecutive values (whole number)
	protected	$min		= NULL;		// Minimum value
	protected	$max		= NULL;		// Maximum value


	// Writes the form field in wp-admin
	public function form_field_content ( $for_bank = false ) {

		if ( $this->get_option() == "" ) {
			$value = $this->default;
		}
		else {
			$value = $this->get_option();
		}

		if ( $for_bank == true ) {
			$value = $this->default;
		}

		echo '<div class="datatype-container">';

		echo '<input type="number" id="' . $this->form_ref() . '" name="' . $this->form_ref() . '" value="' . esc_attr( $value ) . '"';

		if ( $this->min != NULL ) {
			echo ' min="' . $this->min . '"';
		}
		if ( $this->max != NULL ) {
			echo ' max="' . $this->max . '"';
		}
		if ( $this->step != NULL ) {
			echo ' step="' . $this->step . '"';
		}
		if ( $this->placeholder != '' ) {
			echo ' placeholder="' . $this->placeholder . '"';
		}

		echo ' />';

		echo '</div>';

	}


	// Formats the option into value
	public function format_option ( $value, $args = array() ) {

		if ( !intval( $value ) ) {
			return false;
		}
		if ( is_array( $value ) ) {
			$value = $value[0];
		}
		else {
			return $this->prefix . $value . $this->suffix;
		}

	}


}
