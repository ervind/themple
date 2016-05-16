<?php

// The file must have the type-[data-type].php filename format


class TPL_Combined extends TPL_Data_Type {


	protected	$default		= array( 0 => '' );		// Needed for initializing the combined option
	public		$parts			= array();				// Initialize the parts objects
	public		$js_func		= "set_js_vars";		// Which function should create the JS variable


	public function __construct( $args ) {

		parent::__construct( $args );

		$parts_objects	= array();

		foreach ( $this->parts as $part ) {

			$part["section"] = $this->section;
			$type_class = tpl_get_type_class( $part["type"] );
			$parts_objects[$part["name"]] = new $type_class( $part );
			$parts_objects[$part["name"]]->form_ref_base = $this->name;
			$parts_objects[$part["name"]]->is_subitem = true;

		}

		$this->parts = $parts_objects;
		unset( $parts_objects );

	}


	// Writes the form field in wp-admin
	public function form_field_content () {

		echo '<div class="tpl-combined-wrapper datatype-container">';

		foreach ( $this->parts as $part ) {

			$part->form_ref_suffix = '[' . $this->instance . '][' . $part->name . ']';
			$part->instance = $this->instance;

			$part->form_field();

		}

		echo '</div>';

	}


	// Container end of the form field
	public function form_field_after () {

		echo '</div>';		// .tpl-field-inner

		if ( $this->repeat == true ) {
			echo '<div class="admin-icon remover"><span class="hovermsg">' . __( 'Remove row', 'themple' ) . '</span></div>';
			$this->instance++;
		}

		echo '</div>';

	}


	// Returns the values unformatted
	public function get_option ( $args = array() ) {

		$value = array();

		if ( isset( $args["i"] ) ) {
			$i = $args["i"];
		}


		if ( $this->repeat == true ) {

			$value = parent::get_option();

		}
		else {

			foreach ( $this->parts as $part ) {
				$value[$part->name] = $part->get_current_option();
			}

		}


		if ( $this->repeat == false ) {

			return $value;

		}
		else {

			if ( !isset( $i ) ) {
				return $value;
			}
			else {
				return $value[$i];
			}

		}

	}


	// Returns the values as an array
	public function get_value ( $args = array() ) {

		$values = array();

		// Spec branch (picks an instance of an array)
		if ( is_array( $args ) && isset( $args["i"] ) && is_numeric( $args["i"] ) ) {

			$values = $this->get_option( array( 'i' => $args["i"] ) );

			foreach ( $this->parts as $part ) {
				$values[$part->name] = $part->format_option( $values[$part->name], $args );
			}

			return $values;

		}

		// Full branch (returns the full array)
		if ( $this->repeat == true ) {

			$values = $this->get_option();
			foreach ( $values as $i => $value ) {
				foreach ( $this->parts as $part ) {

					$values[$i][$part->name] = $part->format_option( $values[$i][$part->name], $args );

				}
			}
			return $values;

		}

		// Single branch
		foreach ( $this->parts as $part ) {
			$values[$part->name] = $part->get_value();
		}

		return $values;

	}


	// Prints the value as a list
	public function value ( $args = array() ) {

		// Spec branch (picks an instance of an array)
		if ( is_array( $args ) && isset( $args["i"] ) && is_numeric( $args["i"] ) ) {

			$values = $this->get_value( array( 'i' => $args["i"] ) );

			echo '<dl>';

			foreach ( $this->parts as $part ) {
				echo '<dt>' . $part->title . '</dt>';
				echo '<dd>' . $values[$part->name] . '</dd>';
			}

			echo '</dl>';
			return;

		}

		// Full branch (returns the full array)
		if ( $this->repeat == true ) {

			$values = $this->get_value();

			foreach ( $values as $i => $value ) {

				echo '<dl>';
				foreach ( $this->parts as $part ) {
					echo '<dt>' . $part->title . '</dt>';
					echo '<dd>' . $values[$i][$part->name] . '</dd>';
				}
				echo '</dl>';

			}

			return;

		}

		// Single branch
		echo '<dl>';

		foreach ( $this->parts as $part ) {
			echo '<dt>' . $part->title . '</dt>';
			echo '<dd>' . $part->get_value() . '</dd>';
		}

		echo '</dl>';

	}


	// Set less vars for sub-options
	public function set_less_vars() {

		if ( $this->less == true ) {

			$values = $this->get_option();
			$less_variable = '';

			if ( $this->repeat == true ) {

				foreach ( $values as $i => $value ) {

					foreach ( $this->parts as $part ) {

						if ( isset( $value[$part->name] ) ) {
							$part->instance = $i;
							$name = $this->name . '__' . $i . '__' . $part->name;
							$less_variable .= $part->format_less_var( $name, $value[$part->name] );
						}
						$part->instance = 0;

					}

				}

				foreach ( $this->parts as $part ) {

					if ( isset( $values[0][$part->name] ) ) {
						$name = $this->name . '__' . $part->name;
						$less_variable .= $part->format_less_var( $name, $values[0][$part->name] );
					}

				}

			}

			else {

				foreach ( $this->parts as $part ) {

						$name = $this->name . '__' . $part->name;
						$less_variable .= $part->format_less_var( $name, $values[$part->name] );

				}

			}

			return $less_variable;

		}

	}


	public function set_js_vars () {

		if ( $this->js == true ) {

			$values = $this->get_option();
			$js_arr = array();

			if ( $this->repeat == true ) {

				foreach ( $values as $i => $value ) {

					foreach ( $this->parts as $part ) {

						if ( isset( $value[$part->name] ) ) {
							$part->instance = $i;
							$js_func = $part->js_func;
							$js_arr[$i][$part->name] = $part->$js_func();
						}

					}

				}

			}

			else {

				foreach ( $this->parts as $part ) {

					$js_func = $part->js_func;
					$js_arr[$part->name] = $part->$js_func();

				}

			}

		}

		return $js_arr;

	}


}
