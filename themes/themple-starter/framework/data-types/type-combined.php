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

			$part["section"]		= $this->section;
			$part["is_subitem"]		= true;
			$part["parent"]			= $this->name;
			$type_class				= tpl_get_type_class( $part["type"] );
			$part["data_name"]		= $this->data_name;

			if ( $this->js == true ) {
				$part["js"] = true;
			}

			if ( isset( $this->condition ) ) {
				$part["condition_connected"] = $this->data_name;
			}
			else if ( $this->condition_connected != '' ) {
				$part["condition_connected"] = $this->condition_connected;
			}

			$parts_objects[$part["name"]] = new $type_class( $part );

			$path_s = $this->get_level() * 2 + 2;
			$parts_objects[$part["name"]]->path = $this->path;
			$parts_objects[$part["name"]]->path[$path_s] = $part["name"];

		}

		$this->parts = $parts_objects;
		unset( $parts_objects );

	}


	// Writes the form field in wp-admin
	public function form_field_content ( $for_bank = false ) {

		$path_i = $this->get_level() * 2 + 1;
		$path_s = $this->get_level() * 2 + 2;

		echo '<div class="tpl-combined-wrapper datatype-container">';

		foreach ( $this->parts as $part ) {

			$data_connected = '';
			if ( $part->condition_connected != '' ) {
				$data_connected = ' data-connected="' . $part->condition_connected . '"';
			}

			$part->path = $this->path;
			if ( !isset( $part->path[$path_i] ) ) {
				$part->path[$path_i] = 0;
			}
			$part->path[$path_s] = $part->name;

			if ( $part->repeat !== false ) {

				if ( !isset( $part->path[$path_s+1] ) || $part->path[$path_s+1] == 0 ) {

					echo '<label for="'. $part->form_ref() .'"' . $data_connected . '>' . $part->title . ' </label>';
					if ( isset( $part->description ) && $part->description != '' ) {
						echo '<i class="fa fa-lg fa-question-circle tpl-admin-question admin-icon"' . $data_connected . '><span class="hovermsg">' . $part->description . '</span></i>';
					}

				}

				echo '<div class="subitem-repeat-wrapper"' . $data_connected . '>';

				$items = $part->get_option();

				if ( !is_array( $items ) ) {
					$items = array( 0 => $items );
				}

				$end = count( $items );

				// Fixed number instances needs some extra handling
				if ( isset( $part->repeat["number"] ) ) {

					if ( $for_bank == true ) {
						$part->repeat["number"] = 1;
					}

					$end = $part->repeat["number"];

				}

				for ( $i = 0; $i < $end; $i++ ) {

					$part->path[$path_s+1] = $i;
					$part->form_field( $for_bank );

					if ( $for_bank == true ) {
						break;
					}

				}

				echo '</div>';

				if ( !isset( $part->repeat["number"] ) ) {
					echo '<div class="button-container"' . $data_connected . '><button class="repeat-add" data-for="' . $part->data_name . '">' . $part->repeat_button_title . '</button></div>';
				}

			}

			else {

				if ( !isset( $part->path[$path_s+1] ) || $part->path[$path_s+1] == 0 ) {

					echo '<label for="'. $part->form_ref() .'"' . $data_connected . '>' . $part->title . ' </label>';
					if ( isset( $part->description ) && $part->description != '' ) {
						echo '<i class="fa fa-lg fa-question-circle tpl-admin-question admin-icon"' . $data_connected . '><span class="hovermsg">' . $part->description . '</span></i>';
					}

				}

				$part->form_field( $for_bank );

			}

		}

		echo '</div>';

	}


	// Container end of the form field
	public function form_field_after () {

		$path_i = $this->get_level() * 2 + 1;

		echo '</div>';		// .tpl-field-inner

		if ( $this->repeat !== false ) {
			if ( !isset( $this->repeat["number"] ) ) {
				echo '<div class="admin-icon remover"><span class="hovermsg">' . __( 'Remove row', 'themple' ) . '</span></div>';
			}
			$this->path[$path_i]++;
		}

		echo '</div>';

	}



	// Returns the values as an array
	public function get_value ( $args = array() ) {

		$path_n = $this->get_level() * 2;
		$path_i = $this->get_level() * 2 + 1;
		$path_s = $this->get_level() * 2 + 2;

		if ( !isset( $args["path"][$path_n] ) ) {
			$args["path"][$path_n] = $this->name;
		}

		if ( $this->repeat === false ) {
			$args["path"][$path_i] = 0;
		}

		$result = array();

		$values = $this->get_option( $args );

		// Full branch
		if ( !isset( $args["path"][$path_i] ) ) {

			foreach ( $values as $i => $value ) {

				$this->path[$path_i] = $i;

				foreach ( $this->parts as $part ) {
					$sub_args = array( 'path' => array_replace( $this->path, array( $path_n => $this->name, $path_i => $i, $path_s => $part->name ) ) );
					$result[$i][$part->name] = $part->get_value( $sub_args );
				}

			}

		}

		else {

			// One instance of the combined
			if ( !isset( $args["path"][$path_s] ) ) {

				$this->path[$path_i] = $args["path"][$path_i];

				foreach ( $this->parts as $part ) {
					$sub_args = array( 'path' => array_replace( $this->path, array( $path_n => $this->name, $path_i => $args["path"][$path_i], $path_s => $part->name ) ) );
					$result[$part->name] = $part->get_value( $sub_args );
				}

			}

			// Sub-item only
			else {

				$this->path[$path_i] = $args["path"][$path_i];

				foreach ( $this->parts as $part ) {
					if ( $part->name == $args["path"][$path_s] ) {
						$result = $part->get_value( $args );
					}
				}

			}

		}

		return $result;

	}


	// Prints the value as a list
	public function value ( $args = array() ) {

		$path_n = $this->get_level() * 2;
		$path_i = $this->get_level() * 2 + 1;
		$path_s = $this->get_level() * 2 + 2;

		if ( !isset( $args["path"][$path_n] ) ) {
			$args["path"][$path_n] = $this->name;
		}

		if ( $this->repeat === false ) {
			$args["path"][$path_i] = 0;
		}

		$values = $this->get_value( $args );

		// List all
		if ( !isset( $args["path"][$path_i] ) ) {

			foreach ( $values as $i => $value ) {

				$this->part[$path_i] = $i;
				echo '<dl>';

				foreach ( $this->parts as $part ) {

					if ( !empty( $value[$part->name] ) ) {
						echo '<dt>' . $part->title . '</dt>';
						$args["path"][$path_i] = $i;
						echo '<dd>';
						$part->value( $args );
						echo '</dd>';
					}

				}
				echo '</dl>';

			}

			return;

		}

		// Only one instance
		else {

			if ( !isset( $args["path"][$path_s] ) ) {

				echo '<dl>';
				foreach ( $this->parts as $part ) {

					if ( !empty( $values[$part->name] ) ) {
						$args["path"][$path_s] = $part->name;
						echo '<dt>' . $part->title . '</dt>';
						echo '<dd>';
						$part->value( $args );
						echo '</dd>';
					}

				}
				echo '</dl>';

			}

			// Only one sub-item
			else {

				foreach ( $this->parts as $part ) {
					if ( $part->name == $args["path"][$path_s] ) {
						$part->value( $args );
					}
				}

			}

		}

	}


	// Returns the full option object
	public function get_object ( $args = array() ) {

		$path_n = $this->get_level() * 2;
		$path_s = $this->get_level() * 2 + 2;

		if ( !isset( $args["path"][$path_n] ) ) {
			$args["path"][$path_n] = $this->name;
		}

		// Full object
		if ( !isset( $args["path"][$path_s] ) ) {

			return $this;

		}

		// Single branch
		else {

			foreach ( $this->parts as $part ) {

				if ( $part->name == $args["path"][$path_s] ) {
					return $part;
				}

			}

		}

	}


	// Set less vars for sub-options
	public function set_less_vars( $args = array() ) {

		if ( $this->less == true ) {

			$less_variable = '';

			$path_n = $this->get_level() * 2;
			$path_i = $this->get_level() * 2 + 1;
			$path_s = $this->get_level() * 2 + 2;

			$args["path"][$path_n] = $this->name;

			$values = $this->get_option( $args );

			// Fix for non-repeater arrays
			if ( $this->repeat === false && !$this->is_subitem ) {
				$values = array( 0 => $values );
			}

			// Generate the LESS variables
			foreach ( $values as $i => $value ) {

				foreach ( $this->parts as $part ) {

					if ( !isset( $value[$part->name] ) ) {
						$value[$part->name] = $part->default;
					}

					$name = '';
					$shortname = '';
					$shortable = true;
					$part->path = $this->path;
					$part->path[$path_i] = $i;
					$part->path[$path_s] = $part->name;

					foreach ( $part->path as $step => $item ) {

						$name .= $item;

						if ( $step % 2 == 0 ) {
							$shortname .= $item;
						}

						if ( $step < count( $part->path ) - 1 ) {
							$name .= '__';
							if ( $step % 2 == 0 ) {
								$shortname .= '__';
							}
						}

						if ( $step % 2 == 1 && $item != 0 ) {
							$shortable = false;
						}

					}

					$subvalue = $value[$part->name];

					if ( !is_array( $subvalue ) ) {

						$less_variable .= $part->format_less_var( $name, $subvalue );
						if ( $shortable == true ) {
							$less_variable .= $part->format_less_var( $shortname, $subvalue );
						}

					}
					else {

						$args["path"] = $part->path;
						$less_variable .= $part->set_less_vars( $args );

					}

				}

			}

			return $less_variable;

		}

	}


	public function set_js_vars ( $args = array() ) {

		$result = array();

		if ( $this->js == true ) {

			$path_n = $this->get_level() * 2;
			$path_i = $this->get_level() * 2 + 1;
			$path_s = $this->get_level() * 2 + 2;

			if ( !isset( $args["path"][$path_n] ) ) {
				$args["path"][$path_n] = $this->name;
			}

			if ( $this->repeat === false ) {
				$args["path"][$path_i] = 0;
			}

			$values = $this->get_option( $args );

			// Full branch
			if ( !isset( $args["path"][$path_i] ) ) {

				foreach ( $values as $i => $value ) {
					foreach ( $this->parts as $part ) {
						$js_func = $part->js_func;
						$sub_args = array( 'path' => array_replace( $args["path"], array( $path_n => $this->name, $path_i => $i, $path_s => $part->name ) ) );
						$result[$i][$part->name] = $part->$js_func( $sub_args );
					}
				}

			}

			else {

				// One instance of the combined
				if ( !isset( $args["path"][$path_s] ) ) {

					foreach ( $this->parts as $part ) {
						$js_func = $part->js_func;
						$sub_args = array( 'path' => array_replace( $args["path"], array( $path_n => $this->name, $path_i => $args["path"][$path_i], $path_s => $part->name ) ) );
						$result[$part->name] = $part->$js_func( $sub_args );
					}

				}

				// Sub-item only
				else {

					foreach ( $this->parts as $part ) {
						$js_func = $part->js_func;
						if ( $part->name == $args["path"][$path_s] ) {
							$result = $part->$js_func( $args );
						}
					}

				}

			}

		}

		return $result;

	}


	// Return the conditions (if any) for this option
	public function get_conditions() {

		$conditions = array();


		if ( isset( $this->condition ) ) {
			$conditions[$this->data_name] = $this->condition;
		}

		foreach ( $this->parts as $part ) {
			if ( isset( $part->condition ) ) {
				$conditions[$part->data_name] = $part->condition;
			}
			else {
				$sub_conditions = $part->get_conditions();
				if ( $sub_conditions !== false ) {
					return array_merge( $conditions, $sub_conditions );
				}
			}
		}

		if ( !empty( $conditions ) ) {
			return $conditions;
		}
		else {
			return false;
		}

	}


}
