<?php

// The file must have the type-[data-type].php filename format


class TPL_Textarea extends TPL_Data_Type {

	public $size		= 8;		// number of rows
	public $less_string	= true;
	public $less		= false;	// LESS won't work with multi-line texts


	// Writes the form field in wp-admin
	public function form_field_content () {

		echo '<div class="tpl-textarea-wrapper">';

		echo '<textarea id="' . $this->form_ref() . '" name="' . $this->form_ref() . '" rows="' . $this->size . '">'
		. esc_textarea( $this->get_current_option() )
		. '</textarea>';

		echo '</div>';

	}


	// Formats the option into value
	public function format_option ( $value, $args = array() ) {

		return nl2br( $this->prefix . $value . $this->suffix );

	}


	// Echoes the value of the option
	public function value ( $args = array() ) {

		if ( is_array( $args ) && isset( $args["i"] ) && is_numeric( $args["i"] ) ) {
			echo '<p>' . $this->get_value( $args ) . '</p>';
			return;
		}

		if ( $this->repeat == true ) {

			$values = $this->get_value( $args );
			echo '<ul>';
			foreach ( $values as $value ) {
				echo '<li>' . $value . '</li>';
			}
			echo '</ul>';
			return;

		}

		echo '<p>' . $this->get_value( $args ) . '</p>';

	}



}
