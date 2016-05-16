<?php

class TPL_Data_Type {

	// Setting up some defaults...

	protected	$less				= true;			// $less: LESS variable is created from the option if true
	protected	$less_string		= false;		// Should the LESS variable forced to be a string or keep as a natural value
	protected	$prefix				= "";			// Is put before the value
	protected	$suffix				= "";			// Is put after the value
	protected	$placeholder		= "";			// Used in admin if no value is added yet
	protected	$form_ref_suffix	= "";			// The form field reference name
	protected	$is_subitem			= false;		// Is set to TRUE if it's a subitem of another option
	protected	$default			= '';			// Default value init
	public		$js_func			= "get_value";	// Which function should create the JS variable
	public		$repeat				= false;		// Is it a repeater / multi-instance option?
	public		$instance			= 0;			// Used only for repeater fields
	public		$admin_class		= '';			// Extra class added to the admin field



	// Sets up the object attributes while registering options
	public function __construct( $args ) {

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

		// Set up the basic form reference. For simple data types it's recommended to be the same as the option name, for extended data types it can be a custom value
		if ( !isset( $this->form_ref_base ) ) {
			$this->form_ref_base = $this->name;
		}

		$this->form_ref_suffix = '[' . $this->instance . ']';

		// Turning off the LESS engine if it's not a primary section
		if ( !tpl_is_primary_section( $this->section ) ) {
			$this->less = false;
		}

	}


	// Gets the pure form reference name based on the option name. Virtually converts the option name used by Themple into a WP backend form friendly name
	public function form_ref () {

		global $tpl_sections;

		if ( tpl_has_section_post_type( $this->section, "framework_options" ) ) {
			$form_ref = 'tpl_framework_options[' . $this->form_ref_base . ']';
		}

		else if ( !tpl_has_section_post_type( $this->section, "theme_options" ) ) {
			$form_ref = $this->section . '_' . $this->form_ref_base;
		}

		else {
			$form_ref = 'tpl_theme_options[' . $this->form_ref_base . ']';
		}

		return $form_ref . $this->form_ref_suffix;

	}


	// Shows the form field in wp-admin
	public function form_field () {

		if ( $this->repeat == true ) {

			$values = $this->get_option();

			// If it was a non-repeater field before, convert the result to array
			if ( !is_array( $values ) ) {
				$values = array( 0 => $values );
			}

			$i = 0;

			foreach ( $values as $value ) {
				$this->instance = $i;
				$this->form_ref_suffix = '[' . $this->instance . ']';
				$this->form_field_before();
				$this->form_field_content();
				$this->form_field_after();
				$i++;
			}

		}

		else {

			$this->form_field_before();
			$this->form_field_content();
			$this->form_field_after();

		}

	}

	// Container start of the form field
	public function form_field_before ( $extra_class = '' ) {

		$data_instance = '';

		if ( $this->repeat == true ) {
			$data_instance =  ' data-instance="' . $this->instance . '"';
			$extra_class .= ' repeat';
		}

		if ( $this->admin_class != '' ) {
			$extra_class .= ' ' . $this->admin_class;
		}

		if ( $extra_class != '' ) {
			$extra_class = ' ' . $extra_class;
		}

		$class = preg_replace( '/\s+/', ' ', 'tpl-field '. $this->type . $extra_class  );

		echo '<div class="' . $class . '"' . $data_instance . '>';

		if ( $this->is_subitem == true ) {
			echo '<label for="'. $this->form_ref() .'">' . $this->title . '</label>';
		}

		if ( $this->repeat == true ) {
			echo '<div class="admin-icon arranger"><span class="hovermsg">' . __( 'Drag & Drop to reorder', 'themple' ) . '</span></div>';
		}

		echo '<div class="tpl-field-inner">';

	}

	// Content of the form field
	public function form_field_content () {

		$value = $this->get_current_option();
		if ( $value == '' ) {
			$value = $this->default;
		}
		echo $value;

	}

	// Container end of the form field
	public function form_field_after () {

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

		if ( $this->repeat == true ) {
			echo '<div class="admin-icon remover"><span class="hovermsg">' . __( 'Remove row', 'themple' ) . '</span></div>';
			$this->instance++;
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

		if ( isset( $args["i"] ) ) {
			$i = $args["i"];
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

		if ( is_array( $options ) ) {

			if ( !array_key_exists ( $this->form_ref_base, $options ) ) {
				return $this->default;
			}
			else {
				if ( $this->repeat == true ) {
					if ( !isset( $i ) ) {
						return $options[$this->form_ref_base];
					}
					else {
						if ( is_array( $options[$this->form_ref_base] ) ) {
							return $options[$this->form_ref_base][$i];
						}
						else {
							return $options[$this->form_ref_base];
						}
					}
				}
				else {
					if ( is_array( $options[$this->form_ref_base] ) ) {
						return $options[$this->form_ref_base][$this->instance];
					}
					else {
						return $options[$this->form_ref_base];
					}
				}
			}

		}
		else {

			return $this->default;

		}

	}


	// Gets the single option values from where the $this->instance pointer is
	public function get_current_option () {

		$value = $this->get_option( array( 'i' => $this->instance ) );

		if ( $this->is_subitem == true ) {

			if ( isset( $value[$this->name] ) && is_array( $value ) ) {
				return $value[$this->name];
			}
			else {
				return $this->default;
			}

		}

		else {

			return $value;

		}

	}


	// Formats the option into value
	public function format_option ( $value, $args = array() ) {

		return $this->prefix . $value . $this->suffix;

	}


	// Returns the formatted value (with suffix and prefix)
	public function get_value ( $args = array() ) {

		// Spec branch (picks an instance of an array)
		if ( is_array( $args ) && isset( $args["i"] ) && is_numeric( $args["i"] ) ) {
			return $this->format_option( $this->get_option( array( "i" => $args["i"] ) ) );
		}

		// Full branch (returns the full array)
		if ( $this->repeat == true ) {

			$values = $this->get_option();
			foreach ( $values as $i => $value ) {
				$values[$i] = $this->format_option( $value );
			}
			return $values;

		}

		// Single mode if not repeater
		return $this->format_option( $this->get_current_option() );

	}


	// Echoes the value of the option
	public function value ( $args = array() ) {

		if ( is_array( $args ) && isset( $args["i"] ) && is_numeric( $args["i"] ) ) {
			echo $this->get_value( $args );
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

		echo $this->get_value( $args );

	}


	// Returns the full option object
	public function get_object () {

		return $this;

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
	public function set_less_vars() {

		if ( $this->less == true ) {

			$values = $this->get_option();
			$less_variable = '';

			if ( $this->repeat == true ) {

				if ( is_array( $values ) ) {
					foreach ( $values as $i => $value ) {
						$this->instance = $i;
						if ( $i == 0 ) {
							$less_variable .= $this->format_less_var( $this->name, $value );
						}
						$name = $this->name . '__' . $i;
						$less_variable .= $this->format_less_var( $name, $value );
					}
					$this->instance = 0;
				}
				else {
					$less_variable .= $this->format_less_var( $this->name, $values );
				}

			}

			else {

				$less_variable = $this->format_less_var( $this->name, $values );

			}

			return $less_variable;

		}
		else {
			return;
		}

	}


}
