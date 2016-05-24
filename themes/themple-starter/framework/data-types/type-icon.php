<?php

// The file must have the type-[data-type].php filename format


class TPL_Icon extends TPL_Combined {


	protected	$less			= false;
	public		$js_func		= "get_value";		// Which function should create the JS variable


	public function __construct( $args ) {

		$fa_icons = array();

		$args["parts"] = array(
			array(
				"name"			=> 'code',
				"title"			=> __( 'Icon', 'themple' ),
				"description"	=> __( 'The Font Awesome code of the icon (uses the fa-xxx name structure)', 'themple' ),
				"type"			=> 'font_awesome',
				"admin_class"	=> 'tpl-dt-icon-code tpl-combi-sub-third',
			),
			array(
				"name"			=> 'url',
				"title"			=> __( 'Icon URL', 'themple' ),
				"description"	=> __( 'Where should the icon be linked to?', 'themple' ),
				"type"			=> 'text',
				"admin_class"	=> 'tpl-dt-icon-url tpl-combi-sub-third',
			),
			array(
				"name"			=> 'title',
				"title"			=> __( 'Link Title', 'themple' ),
				"description"	=> __( 'This is displayed when hovering the mouse over the icon when it is a link', 'themple' ),
				"type"			=> 'text',
				"admin_class"	=> 'tpl-dt-icon-title tpl-combi-sub-third',
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
				"admin_class"	=> 'tpl-dt-icon-color tpl-combi-sub-third clearfix',
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
				"admin_class"	=> 'tpl-dt-icon-color tpl-combi-sub-third',
			),
			array(
				"name"			=> 'color',
				"title"			=> __( 'Icon Color', 'themple' ),
				"description"	=> __( 'What should be the color of this icon?', 'themple' ),
				"type"			=> 'color',
				"default"		=> '#666666',
				"admin_class"	=> 'tpl-dt-icon-color tpl-combi-sub-third',
			),
		);

		parent::__construct( $args );

	}


	public function form_field_before ( $extra_class = 'tpl-dt-icon combined' ) {

		parent::form_field_before( $extra_class );

	}



	// Returns the values as an array
	public function get_value ( $args = array() ) {

		$values = array();

		// Spec branch (picks an instance of an array)
		if ( is_array( $args ) && isset( $args["i"] ) && is_numeric( $args["i"] ) ) {

			$result = '';
			$values = $this->get_option( array( 'i' => $args["i"] ) );

			foreach ( $this->parts as $part ) {
				if ( $part->name == "code" ) {
					$result = $part->format_option( $values["code"], $values );
				}
			}

			return $result;

		}

		// Full branch (returns the full array)
		if ( $this->repeat == true ) {

			$result = array();
			$values = $this->get_option();
			foreach ( $values as $i => $value ) {
				foreach ( $this->parts as $part ) {
					if ( $part->name == "code" ) {
						$result[$i] = $part->format_option( $value["code"], $value );
					}
				}
			}
			return $result;

		}

		// Single branch
		$values = $this->get_option();
		$result = '';

		foreach ( $this->parts as $part ) {
			if ( $part->name == "code" ) {
				$result = $part->format_option( $values["code"], $values );
			}
		}

		return $result;

	}



	// Echoes the value of the option
	// Prints the value as a list
	public function value ( $args = array() ) {

		// Spec branch (picks an instance of an array)
		if ( is_array( $args ) && isset( $args["i"] ) && is_numeric( $args["i"] ) ) {

			$values = $this->get_value( array( 'i' => $args["i"] ) );

			echo $values;

			return;

		}

		// Full branch (returns the full array)
		if ( $this->repeat == true ) {

			$values = $this->get_value();

			echo '<ul>';

			foreach ( $values as $value ) {

				echo '<li>' . $value . '</li>';

			}

			echo '</ul>';

			return;

		}

		// Single branch
		echo $this->get_value();

	}


}
