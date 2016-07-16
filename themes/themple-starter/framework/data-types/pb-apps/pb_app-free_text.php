<?php

// This is the base Themple Page Builder App Class. Extend it to write your own Themple Page Builder Apps


tpl_register_pb_app( array(
	'name'		=> 'free_text',
	'title'		=> __( 'Free text', 'themple' ),
	'class'		=> 'TPL_PB_Free_Text',
	'pos'		=> 40,
) );


class TPL_PB_Free_Text {


	// The extra admin fields added by this app
	public function get_admin_fields() {

		return array(
			array(
				"name"			=> 'free_text_textarea',
				"title"			=> __( 'Free text input', 'themple' ),
				"description"	=> __( 'You can enter any kind of text and HTML into this box.', 'themple' ),
				"type"			=> 'textarea',
				"condition"		=> array(
					array(
						"type"		=> 'option',
						"name"		=> '_THIS_/app_type',
						"relation"	=> '=',
						"value"		=> 'free_text',
					),
				),
			),
		);

	}


	public function frontend_value( $values = array() ) {

		$result = '';

		if ( isset( $values["free_text_textarea"] ) ) {
			$result = wpautop( $values["free_text_textarea"] );
		}

		return $result;

	}


}
