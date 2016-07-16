<?php

// This is the default data type class. All the data types are children of this class.


class TPL_Data_Type {

	// Setting up some defaults...

	protected	$less				= true;			// $less: LESS variable is created from the option if true
	protected	$less_string		= false;		// Should the LESS variable forced to be a string or keep as a natural value
	protected	$prefix				= "";			// Is put before the value
	protected	$suffix				= "";			// Is put after the value
	protected	$placeholder		= "";			// Used in admin if no value is added yet
	protected	$is_subitem			= false;		// Is set to TRUE if it's a subitem of another option
	protected	$default			= '';			// Default value init
	protected	$path				= array();		// Used to find instances and subitems of an option
	public		$data_name			= '';			// Used by forms in the admin
	public		$js					= false;		// By default, JS is turned off
	public		$js_func			= "get_value";	// Which function should create the JS variable
	public		$repeat				= false;		// Is it a repeater / multi-instance option?
	public		$admin_class		= '';			// Extra class added to the admin field
	public		$description		= '';			// Initialize the option's description
	public		$condition_connected = '';			// Some initialization for conditions


	// Sets up the object attributes while registering options
	public function __construct( $args ) {

		// If the field is repeater, this is written on the Add button
		if ( !isset( $args["repeat_button_title"] ) ) {
			$args["repeat_button_title"] = __( 'Add row', 'themple' );
		}

		// Setting up initial values
		foreach ( $args as $key => $arg ) {
			$this->$key = $arg;
		}

		// Error handling
		if ( !isset( $this->name ) ) {
			tpl_error( __( 'The "name" attribute is required during registering options', 'themple' ), true );
			die();
		}

		if ( !isset( $this->title ) ) {
			$this->title = $this->name;
			tpl_error(
				sprintf(
					__( 'It\'s recommended to set a title for "%s" option', 'themple' ),
					$this->name
				), true, 'update-nag'
			);
		}

		if ( !isset( $this->section ) ) {
			$this->section = "dummy-section";
			tpl_error(
				sprintf(
					__( 'It\'s recommended to assign %s to a section. Using a dummy section to avoid errors...', 'themple' ),
					$this->name
				), true
			);
		}

		if ( empty( $this->path ) ) {
			$path_n = $this->get_level() * 2;
			$this->path[$path_n] = $this->name;
		}

		// Turning off the LESS engine if it's not a primary section
		if ( !tpl_is_primary_section( $this->section ) ) {
			$this->less = false;
		}

		if ( $this->is_subitem ) {
			$this->data_name .= '/' . $this->name;
		}
		else {
			$this->data_name = $this->name;
		}

		if ( isset( $this->condition ) ) {
			$this->condition_connected = $this->data_name;
		}

	}


	// Gets the pure form reference name based on the option name. Virtually converts the option name used by Themple into a WP backend form friendly name
	public function form_ref () {

		global $tpl_sections;

		if ( tpl_has_section_post_type( $this->section, "framework_options" ) ) {
			$form_ref = 'tpl_framework_options[' . $this->path[0] . ']';
		}

		else if ( !tpl_has_section_post_type( $this->section, "theme_options" ) ) {
			$form_ref = $this->section . '_' . $this->path[0];
		}

		else {
			$form_ref = 'tpl_theme_options[' . $this->path[0] . ']';
		}

		foreach ( $this->path as $i => $step ) {
			if ( $i > 0 ) {
				$form_ref .= '[' . $step . ']';
			}
		}

		return $form_ref;

	}


	// Shows the form field in wp-admin
	public function form_field ( $for_bank = false ) {

		$path_i = $this->get_level() * 2 + 1;

		if ( !$this->is_subitem ) {
			$this->path = array( 0 => $this->name );
		}

		if ( $this->repeat !== false ) {

			$values = $this->get_option();

			// If it was a non-repeater field before, convert the result to array
			if ( !is_array( $values ) || !is_numeric( key( $values ) ) ) {
				$values = array( 0 => $values );
			}

			if ( ( count( $values ) >= 1 && $values[0] != '' ) || $for_bank == true || isset( $this->repeat["number"] ) ) {

				$end = count( $values );

				for ( $i = 0; $i < $end; $i++ ) {

					if ( !$this->is_subitem ) {
						$this->path[$path_i] = $i;
					}

					$this->form_field_before();
					$this->form_field_content( $for_bank );
					$this->form_field_after();

					if ( $for_bank == true ) {
						break;
					}

				}

			}

		}

		else {

			if ( !$this->is_subitem ) {
				$this->path[$path_i] = 0;
			}

			$this->form_field_before();
			$this->form_field_content( $for_bank );
			$this->form_field_after();

		}

	}


	// Container start of the form field
	public function form_field_before ( $extra_class = '' ) {

		$path_i = $this->get_level() * 2 + 1;

		if ( isset( $this->path[$path_i] ) ) {
			$data_instance = $this->path[$path_i];
		}
		else {
			$data_instance = 0;
		}

		if ( $this->repeat !== false ) {
			$extra_class .= ' repeat';
		}

		// Extra admin classes if needed
		if ( $this->admin_class != '' ) {
			$extra_class .= ' ' . $this->admin_class;
		}

		// If child item of a combined field
		if ( $this->is_subitem ) {
			$extra_class .= ' subitem';
		}

		// Which condition is it connected to if there's any
		$data_connected = '';
		if ( $this->condition_connected != '' ) {
			$data_connected = ' data-connected="' . $this->condition_connected . '"';
		}

		$class = preg_replace( '/\s+/', ' ', 'tpl-field '. $this->type . ' ' . $extra_class  );

		echo '<div class="' . $class . '" data-instance="' . $data_instance . '" data-name="' . $this->data_name . '" data-level="' . $this->get_level() . '"' . $data_connected . '>';

		if ( $this->repeat !== false ) {
			echo '<div class="admin-icon arranger"><span class="hovermsg">' . __( 'Drag & Drop to reorder', 'themple' ) . '</span></div>';
		}

		echo '<div class="tpl-field-inner">';

	}


	// Content of the form field
	public function form_field_content ( $for_bank = false ) {

		$value = $this->get_option();
		if ( $value == '' || $for_bank == true ) {
			$value = $this->default;
		}
		echo $value;

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
				$text .= __( 'default:', 'themple' ) . ' ' . $this->format_option( $this->default ) . '; ';
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


	// Gets the section name based on the option name. Returns tpl_theme_options by default
	public function get_section () {

		global $tpl_sections;

		if ( tpl_has_section_post_type( $this->section, "framework_options" ) ) {
			return 'tpl_framework_options';
		}

		if ( tpl_has_section_post_type( $this->section, "theme_options" ) ) {
			return 'tpl_theme_options';
		}

		return $this->section;

	}


	// Gets the pure value of an option from the database and returns it if any values found --- returns default value or empty string if no value found in database.
	// This function is used by data types, please use it if you are writing your own data type!
	// In your template files use tpl_get_value instead
	public function get_option ( $args = array() ) {

		global $post, $tpl_sections;

		if ( isset( $args["path"] ) ) {
			$this->path = $args["path"];
		}

		if ( !isset( $this->path[0] ) ) {
			$this->path[0] = $this->name;
		}

		if ( !is_object ( $post ) ) {
			$id = 0;
		}
		else {
			$id = get_the_ID();
		}
		$meta_key = '_tpl_' . $this->get_section();

		// If the option is connected to a post meta, let it be the return value
		if ( !tpl_is_primary_section ( $this->section ) ) {

			if ( !tpl_has_section_post_type( $this->section, get_post_type() ) ) {
				return false;
			}

			// If the metadata exists in the database, return it!
			if ( metadata_exists ( 'post', $id, $meta_key ) ) {
				$options = get_post_meta ( $id, $meta_key );
				$options = $options[0];
			}
			// If not, return the default value defined in your options file
			else {
				return $this->default;
			}
		}

		// In the case it's not a post meta, return the value from the Framework Options page...
		elseif ( tpl_has_section_post_type( $this->section, 'framework_options' ) ) {
			$options = get_option ( 'tpl_framework_options', $this->default );
		}

		// ... or the Theme Options page by default
		else {
			$options = get_option ( 'tpl_theme_options', $this->default );
		}


		// Deciding what to return
		if ( is_array( $options ) ) {

			if ( $this->repeat === false && count( $this->path ) == 1 ) {
				$this->path[1] = 0;
			}

			if ( isset( $options[$this->path[0]] ) ) {

				$value = $options[$this->path[0]];

				foreach ( $this->path as $step => $item ) {
					if ( $step > 0 ) {
						if ( isset( $value[$item] ) ) {
							if ( is_array( $value ) ) {
								$value = $value[$item];
							}
						}
						else {
							$value = $this->default;
							break;
						}
					}
				}

			}

			else {

				$value = $this->default;

			}

			// Normal return
			return $value;

		}
		else {

			return $this->default;

		}

	}


	// Returns the formatted value (with suffix and prefix)
	public function get_value ( $args = array() ) {

		$path_n = $this->get_level() * 2;
		$path_i = $this->get_level() * 2 + 1;

		if ( !isset( $args["path"][$path_n] ) ) {
			$args["path"][$path_n] = $this->name;
		}

		if ( $this->repeat === false ) {
			$args["path"][$path_i] = 0;
		}

		$result = array();
		ksort( $args["path"] );

		$values = $this->get_option( $args );

		// Repeater branch
		if ( !isset( $args["path"][$path_i] ) && is_array( $values ) ) {

			foreach ( $values as $i => $value ) {
				$result[$i] = $this->format_option( $value, $args );
			}

		}

		// Single branch
		else {

			$result = $this->format_option( $values, $args );

		}

		return $result;

	}


	// Echoes the value of the option
	public function value ( $args = array() ) {

		$path_i = $this->get_level() * 2 + 1;

		if ( $this->repeat === false ) {
			$args["path"][$path_i] = 0;
		}

		$values = $this->get_value( $args );

		if ( !isset( $args["path"][$path_i] ) ) {

			if ( is_array( $values ) ) {
				echo '<ul>';
				foreach ( $values as $value ) {
					echo '<li>' . $value . '</li>';
				}
				echo '</ul>';
				return;
			}

		}

		else {

			echo $this->get_value( $args );

		}

	}


	// Returns the full option object
	public function get_object ( $args = array() ) {

		return $this;

	}


	// Formats the option into value
	public function format_option ( $value, $args = array() ) {

		return $this->prefix . $value . $this->suffix;

	}


	// Return which level the option is on in the subitem hierarchy ( 0 = base level )
	protected function get_level () {

		$level = substr_count( $this->data_name, '/' );

		return $level;

	}


	// Helper function if you want to echo the value
	public function __toString() {

        return $this->get_value();

    }


	// LESS variable helper function
	public function format_less_var( $name, $value ) {

		$less_variable = '@' . $name . ': ';

		// Should it be included in LESS as a string variable? If yes, put it inside quote marks
		if ( $this->less_string == true ) {
			$less_variable .= '"';
		}

		$less_variable .= $this->format_option( $value );

		// closing the string if needed
		if ( $this->less_string == true ) {
			$less_variable .= '"';
		}

		$less_variable .= ';';

		return $less_variable;

	}


	// Set less var
	public function set_less_vars( $args = array() ) {

		if ( $this->less == true ) {

			$less_variable = '';

			$path_n = $this->get_level() * 2;
			$path_i = $this->get_level() * 2 + 1;

			$args["path"][$path_n] = $this->name;

			$values = $this->get_option( $args );

			$this->path = $args["path"];

			$name = '';
			$shortname = '';
			$shortable = true;

			foreach ( $this->path as $step => $item ) {

				$name .= $item;

				if ( $step % 2 == 0 ) {
					$shortname .= $item;
				}

				if ( $step < count( $this->path ) - 1 ) {
					$name .= '__';
					if ( $step % 2 == 0 ) {
						$shortname .= '__';
					}
				}

				if ( $step % 2 == 1 && $item != 0 ) {
					$shortable = false;
				}

			}

			if ( is_array( $values ) || ( $this->repeat !== false ) ) {

				foreach ( $values as $i => $value ) {

					$this->path[$path_i] = $i;

					if ( $this->path[$path_i] > 0 ) {
						$shortable = false;
					}

					if ( $shortable == true ) {
						$less_variable .= $this->format_less_var( $shortname, $value );
					}

					$sname = $name . '__' . $i;
					$less_variable .= $this->format_less_var( $sname, $value );

				}

			}
			else {

				$less_variable .= $this->format_less_var( $name, $values );

			}

			return $less_variable;

		}

	}


	// Return the conditions (if any) for this option
	public function get_conditions() {

		if ( isset( $this->condition ) ) {
			return array(
				$this->data_name => $this->condition
			);
		}

		else {
			return false;
		}

	}


}
