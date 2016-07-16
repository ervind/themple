<?php

// The file must have the type-[data-type].php filename format


class TPL_Icon extends TPL_Combined {


	protected	$less			= false;
	public		$js_func		= "get_value";		// Which function should create the JS variable


	public function __construct( $args ) {

		$args["parts"] = array(
			array(
				"name"			=> 'code',
				"title"			=> __( 'Icon', 'themple' ),
				"description"	=> __( 'The Font Awesome code of the icon (uses the fa-xxx name structure)', 'themple' ),
				"type"			=> 'font_awesome',
			),
			array(
				"name"			=> 'color',
				"title"			=> __( 'Icon Color', 'themple' ),
				"description"	=> __( 'What should be the color of this icon?', 'themple' ),
				"type"			=> 'color',
				"default"		=> '#666666',
			),
			array(
				"name"			=> 'size',
				"title"			=> __( 'Icon size', 'themple' ),
				"description"	=> __( 'Size of the icon in Font Awesome sizes', 'themple' ),
				"type"			=> 'select',
				"default"		=> '',
				"values"		=> array(
					""				=> __( 'Normal', 'themple' ),
					"lg"			=> __( 'Larger', 'themple' ),
					"2x"			=> __( 'Double', 'themple' ),
					"3x"			=> __( 'Triple', 'themple' ),
					"4x"			=> __( '4x', 'themple' ),
					"5x"			=> __( '5x', 'themple' ),
				),
				"key"			=> true,
			),
			array(
				"name"			=> 'url',
				"title"			=> __( 'Icon URL', 'themple' ),
				"description"	=> __( 'Where should the icon be linked to?', 'themple' ),
				"type"			=> 'text',
			),
			array(
				"name"			=> 'title',
				"title"			=> __( 'Link Title', 'themple' ),
				"description"	=> __( 'This is displayed when hovering the mouse over the icon when it is a link', 'themple' ),
				"type"			=> 'text',
				"condition"		=> array(
					array(
						"type"		=> 'option',
						"name"		=> '_THIS_/url',
						"relation"	=> '!=',
						"value"		=> '',
					),
				),
			),
			array(
				"name"			=> 'newtab',
				"title"			=> __( 'Open in new tab?', 'themple' ),
				"description"	=> __( 'If yes, the link will open in a new browser tab', 'themple' ),
				"type"			=> 'select',
				"default"		=> 'no',
				"values"		=> array(
					"no"			=> __( 'No', 'themple' ),
					"yes"			=> __( 'Yes', 'themple' ),
				),
				"key"			=> true,
				"condition"		=> array(
					array(
						"type"		=> 'option',
						"name"		=> '_THIS_/url',
						"relation"	=> '!=',
						"value"		=> '',
					),
				),
			),
		);

		parent::__construct( $args );

	}


	public function form_field_before ( $extra_class = 'tpl-dt-icon combined' ) {

		parent::form_field_before( $extra_class );

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

		if ( !isset( $args["path"][$path_i] ) ) {

			foreach ( $values as $i => $value ) {
				foreach ( $this->parts as $part ) {
					if ( $part->name == "code" && isset( $value["code"] ) ) {
						$result[$i] = $part->format_option( $value["code"], $value );
					}
				}
			}

		}

		else {

			if ( !isset( $args["path"][$path_s] ) ) {

				foreach ( $this->parts as $part ) {
					if ( $part->name == "code" && isset( $values["code"] ) ) {
						$result = $part->format_option( $values["code"], $values );
					}
				}

			}
			else {

				foreach ( $this->parts as $part ) {
					if ( $part->name == $args["path"][$path_s] ) {
						$result = $part->get_value( $args );
					}
				}

			}

		}

		return $result;

	}



	// Echoes the value of the option
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

			echo '<ul>';
			foreach ( $values as $value ) {
				echo '<li>' . $value . '</li>';
			}
			echo '</ul>';
			return;

		}

		// Only one instance
		else {

			if ( !isset( $args["path"][$path_s] ) ) {

				echo $values;

			}

			// Only one sub-item
			else {

				foreach ( $this->parts as $part ) {
					if ( $part->name == $args["path"][$path_s] ) {
						echo $part->get_value( $args );
					}
				}

			}

		}

	}

}
