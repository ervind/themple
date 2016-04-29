<?php

// The file must have the type-[data-type].php filename format


class TPL_Number extends TPL_Data_Type {

	public $step		= NULL;
	public $min			= NULL;
	public $max			= NULL;


	// Writes the form field in wp-admin
	public function form_field_content () {

		if ( $this->get_current_option() == "" ) {
			$value = $this->default;
		}
		else {
			$value = $this->get_current_option();
		}

		echo $this->prefix . '<input type="number" id="' . $this->form_ref() . '" name="' . $this->form_ref() . '" value="' . esc_attr( $value ) . '"';

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

		echo ' />' . $this->suffix;

	}


	// Returns the formatted value (with suffix and prefix)
	public function get_value ( $args = array() ) {

		// Spec branch (picks an instance of an array)
		if ( is_array( $args ) && isset( $args["i"] ) && is_numeric( $args["i"] ) ) {

			$value = $this->get_option( array( "i" => $args["i"] ) );
			if ( !intval( $value ) ) {
				return false;
			}
			return $this->format_option( $value );

		}

		// Full branch (returns the full array)
		if ( $this->repeat == true && is_array( $this->get_option() ) ) {

			$values = $this->get_option();
			foreach ( $values as $i => $value ) {
				if ( !intval( $value ) ) {
					$values[$i] = false;
				}
				else {
					$values[$i] = $this->format_option( $value );
				}
			}
			return $values;

		}

		// Single mode if not repeater
		if ( !intval( $this->get_current_option() ) ) {
			return false;
		}
		return $this->format_option( $this->get_current_option() );

	}



}
