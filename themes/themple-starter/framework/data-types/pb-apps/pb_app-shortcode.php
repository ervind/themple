<?php

// This is the base Themple Page Builder App Class. Extend it to write your own Themple Page Builder Apps


tpl_register_pb_app( array(
	'name'		=> 'shortcode',
	'title'		=> __( 'Custom shortcode', 'themple' ),
	'class'		=> 'TPL_PB_Shortcode',
	'pos'		=> 20,
) );


class TPL_PB_Shortcode {


	// The extra admin fields added by this app
	public function get_admin_fields() {

		return array(
			array(
				"name"			=> "shortcode_name",
				"title"			=> __( 'Shortcode name', 'themple' ),
				"description"	=> __( 'The shortcode name / identifier', 'themple' ),
				"type"			=> 'text',
			),
			array(
				"name"			=> "shortcode_type",
				"title"			=> __( 'Shortcode type', 'themple' ),
				"description"	=> __( 'Self-closing or Enclosing?', 'themple' ),
				"type"			=> 'select',
				"values"		=> array(
					"self-closing"	=> __( 'Self-closing', 'themple' ),
					"enclosing"		=> __( 'Enclosing', 'themple' ),
				),
			),
			array(
				"name"			=> "shortcode_content",
				"title"			=> __( 'Shortcode content', 'themple' ),
				"description"	=> __( 'Content between the starting and the ending tag of the enclosing shortcode', 'themple' ),
				"type"			=> 'textarea',
				"condition"		=> array(
					array(
						"type"		=> 'option',
						"name"		=> '_THIS_/shortcode_type',
						"relation"	=> '=',
						"value"		=> 'enclosing',
					),
				),
			),
			array(
				"name"			=> "shortcode_params",
				"title"			=> __( 'Shortcode parameters', 'themple' ),
				"description"	=> __( 'You can add here as many shortcode parameters as you\'d like to', 'themple' ),
				"type"			=> 'combined',
				"repeat"		=> true,
				"repeat_button_title"	=> __( 'Add parameter', 'themple' ),
				"parts"			=> array(
					array(
						"name"			=> "shortcode_param_name",
						"title"			=> __( 'Name', 'themple' ),
						"description"	=> __( 'The shortcode parameter\'s name', 'themple' ),
						"type"			=> 'text',
					),
					array(
						"name"			=> "shortcode_param_value",
						"title"			=> __( 'Value', 'themple' ),
						"description"	=> __( 'The shortcode parameter\'s value', 'themple' ),
						"type"			=> 'text',
					),
				),
			),
		);

	}


	// Preview in admin
	public function get_preview( $values = array() ) {
		return '<i class="fa fa-2x fa-code"></i> [apps/shortcode_name/tpl-preview-1]';
	}


	// Frontend output
	public function frontend_value( $values = array() ) {

		$result = '';

		if ( isset( $values["shortcode_name"] ) ) {

			$result .= '[' . $values["shortcode_name"];

			if ( isset( $values["shortcode_params"] ) ) {
				foreach ( $values["shortcode_params"] as $param ) {
					if ( $param["shortcode_param_name"] != '' ) {
						$result .= ' ' . $param["shortcode_param_name"] . '="' . $param["shortcode_param_value"] . '"';
					}
				}
			}

			if ( $values["shortcode_type"] == 'enclosing' ) {
				$result .= ']' . $values["shortcode_content"] . '[/' . $values["shortcode_name"] . ']';
			}
			else {
				$result .= ']';
			}

		}

		return do_shortcode( $result );

	}


}
