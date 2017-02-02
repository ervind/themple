<?php

// This is the base Themple Page Builder App Class. Extend it to write your own Themple Page Builder Apps


tpl_register_pb_app( array(
	'name'		=> 'empty',
	'title'		=> __( 'Empty space', 'themple' ),
	'class'		=> 'TPL_PB_Empty',
	'pos'		=> 0,
) );


class TPL_PB_Empty {


	public function get_admin_fields() {

		return false;

	}


	// Preview in admin
	public function get_preview( $values = array() ) {
		return '(Empty)';
	}


	public function frontend_value( $values = array() ) {

		return '';

	}


}
