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

		echo '<div class="tpl-datatype-container">';

		if ( $this->prefix ) {
			echo '<span class="tpl-datatype-prefix tpl-preview-0">' . $this->prefix . '</span>';
		}

		echo '<input type="number" class="tpl-preview-1" id="' . esc_attr( $this->form_ref() ) . '" name="' . esc_attr( $this->form_ref() ) . '" value="' . esc_attr( $value ) . '"';

		if ( $this->min != NULL ) {
			echo ' min="' . intval( $this->min ) . '"';
		}
		if ( $this->max != NULL ) {
			echo ' max="' . intval( $this->max ) . '"';
		}
		if ( $this->step != NULL ) {
			echo ' step="' . intval( $this->step ) . '"';
		}
		if ( $this->placeholder != '' ) {
			echo ' placeholder="' . esc_attr( $this->placeholder ) . '"';
		}

		echo '>';

		if ( $this->suffix ) {
			echo '<span class="tpl-datatype-suffix tpl-preview-2">' . $this->suffix . '</span>';
		}

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
