<?php

// The file must have the type-[data-type].php filename format


class TPL_Select extends TPL_Data_Type {


	// Writes the form field in wp-admin
	public function form_field_content () {

		// The saved or default value:
		$id = $this->get_current_option();

		if ( ( $id == '' ) && ( isset( $this->default ) ) ) {
			$id = $this->default;
		}

		echo $this->prefix . '<select id="' . $this->form_ref() . '" name="' . $this->form_ref() . '">';

		if ( $this->placeholder != '' ) {
			echo '<option value="">' . $this->placeholder . '</option>';
		}

		foreach ( $this->values as $key => $value ) {
			echo '<option value="' . esc_attr( $key ) . '"';

			if ( $key == $id ) {
				echo ' selected';
			}

			echo '>' . esc_html( $value ) . '</option>';

		}

		echo '</select>' . $this->suffix;

	}


	// Container end of the form field
	public function form_field_after () {

		if ( $this->repeat == true ) {
			echo '<div class="admin-icon remover"><span class="hovermsg">' . __( 'Remove row', 'themple' ) . '</span></div>';
			$this->instance++;
		}

		if ( !empty( $this->default ) ) {
			echo ' <i class="tpl-default-value">(' . __( 'default:', 'themple' ) . ' ' . $this->format_option( $this->default, array( "key" => false ) ) .')</i>';
		}

		echo '</div>';

	}


	// Formats the option into value
	public function format_option ( $id, $args = array() ) {

		// Deciding to return the key or the value
		if ( !isset( $args["key"] ) ) {

			if ( isset( $this->key ) ) {
				$key = $this->key;
			}
			else {
				$key = false;
			}

		}
		else {
			$key = $args["key"];
		}

		if ( $key ) {
			return $id;
		}
		else {
			return $this->prefix . $this->values[$id] . $this->suffix;
		}

	}


	// Returns the formatted value (with suffix and prefix)
	public function get_value ( $args = array() ) {

		// Spec branch (picks an instance of an array)
		if ( is_array( $args ) && isset( $args["i"] ) && is_numeric( $args["i"] ) ) {

			$id = $this->get_option( array( "i" => $args["i"] ) );
			return $this->format_option( $id, $args );

		}

		// Full branch (returns the full array)
		if ( $this->repeat == true ) {

			$values = $this->get_option();
			foreach ( $values as $i => $id ) {
				$values[$i] = $this->format_option( $id, $args );
			}
			return $values;

		}

		// Single mode if not repeater
		$id = $this->get_current_option();
		return $this->format_option( $id, $args );

	}

}
