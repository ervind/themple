<?php

// This is the base Themple Page Builder App Class. Extend it to write your own Themple Page Builder Apps


tpl_register_pb_app( array(
	'name'		=> 'image',
	'title'		=> __( 'Image', 'themple' ),
	'class'		=> 'TPL_PB_Image',
	'pos'		=> 10,
) );


class TPL_PB_Image {


	// The extra admin fields added by this app
	public function get_admin_fields() {

		return array(
			array(
				"name"			=> 'image_selector',
				"title"			=> __( 'Select image', 'themple' ),
				"description"	=> __( 'Upload or select previously uploaded image here', 'themple' ),
				"type"			=> 'image',
				"size"			=> 'full',
				"condition"		=> array(
					array(
						"type"		=> 'option',
						"name"		=> '_THIS_/app_type',
						"relation"	=> '=',
						"value"		=> 'image',
					),
				),
			),
		);

	}


	public function frontend_value( $values = array() ) {

		$result = '';

		if ( isset( $values["image_selector"] ) ) {
			$result = $values["image_selector"];
		}

		return $result;

	}


}
