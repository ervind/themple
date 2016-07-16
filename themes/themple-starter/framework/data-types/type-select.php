<?php

// The file must have the type-[data-type].php filename format


class TPL_Select extends TPL_Data_Type {


	public		$key				= false;		// Should return the key (true) or the label (false)?


	// Writes the form field in wp-admin
	public function form_field_content ( $for_bank = false ) {

		echo '<div class="datatype-container">';

		// The saved or default value:
		$id = $this->get_option();

		if ( ( $id == '' || $for_bank == true ) && ( isset( $this->default ) ) ) {
			$id = $this->default;
		}

		echo '<select id="' . $this->form_ref() . '" name="' . $this->form_ref() . '" autocomplete="off">';

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

		echo '</select>';

		echo '</div>';

	}


	// Container end of the form field
	public function form_field_after () {

		$path_i = $this->get_level() * 2 + 1;

		if ( !empty( $this->default ) || !empty( $this->prefix ) || !empty( $this->suffix ) ) {
			echo ' <div class="tpl-default-container">
				<i class="tpl-default-value">(';

			$text = '';

			if ( !empty( $this->prefix ) ) {
				$text .= __( 'prefix:', 'themple' ) . ' ' . $this->prefix . '; ';
			}

			if ( !empty( $this->suffix ) ) {
				$text .= __( 'suffix:', 'themple' ) . ' ' . $this->suffix . '; ';
			}

			if ( !empty( $this->default ) ) {
				$text .= __( 'default:', 'themple' ) . ' ' . $this->format_option( $this->default, array( "key" => false ) ) . '; ';
			}

			echo rtrim( $text, '; ' );

			echo ')</i>
			</div>';
		}

		echo '</div>';		// .tpl-field-inner

		if ( $this->repeat !== false ) {
			if ( !isset( $this->repeat["number"] ) ) {
				echo '<div class="admin-icon remover"><span class="hovermsg">' . __( 'Remove row', 'themple' ) . '</span></div>';
			}
			$this->path[$path_i]++;
		}

		echo '</div>';

	}


	// Formats the option into value
	public function format_option ( $id, $args = array() ) {

		// Deciding to return the key or the value
		if ( !isset( $args["key"] ) ) {
			$key = $this->key;
		}
		else {
			$key = $args["key"];
		}

		if ( $key ) {
			return $id;
		}
		elseif ( !isset( $values[$id] ) ) {
			return $id;
		}
		else {
			return $this->prefix . $this->values[$id] . $this->suffix;
		}

	}


}
