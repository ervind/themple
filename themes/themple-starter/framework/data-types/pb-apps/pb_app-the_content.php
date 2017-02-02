<?php

// This is the base Themple Page Builder App Class. Extend it to write your own Themple Page Builder Apps


tpl_register_pb_app( array(
	'name'		=> 'the_content',
	'title'		=> __( 'The content', 'themple' ),
	'class'		=> 'TPL_PB_The_Content',
	'pos'		=> 50,
) );


class TPL_PB_The_Content {


	// The extra admin fields added by this app
	public function get_admin_fields() {

		return false;

	}


	// Preview in admin
	public function get_preview( $values = array() ) {
		return '<i class="fa fa-2x fa-file-text"></i> The Content';
	}


	public function frontend_value( $values = array() ) {

		return do_shortcode( wpautop( get_the_content() ) );

	}


}
