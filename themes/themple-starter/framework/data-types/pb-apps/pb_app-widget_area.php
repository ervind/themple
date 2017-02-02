<?php

// This is the base Themple Page Builder App Class. Extend it to write your own Themple Page Builder Apps


tpl_register_pb_app( array(
	'name'		=> 'widget_area',
	'title'		=> __( 'Widget area', 'themple' ),
	'class'		=> 'TPL_PB_Widget_Area',
	'pos'		=> 30,
) );


class TPL_PB_Widget_Area {


	// The extra admin fields added by this app
	public function get_admin_fields() {

		global $wp_registered_sidebars;

		$values = array();
		foreach ( $wp_registered_sidebars as $key => $sidebar ) {
			$values[$key] = $sidebar["name"];
		}

		return array(
			array(
				"name"			=> "widget_area",
				"title"			=> __( 'Select a widget area', 'themple' ),
				"description"	=> __( 'You can use here a widget area already set up in Appearance > Widgets', 'themple' ),
				"type"			=> 'select',
				"values"		=> $values,
			),
		);

	}


	// Preview in admin
	public function get_preview( $values = array() ) {
		return '<i class="fa fa-2x fa-clone"></i> [apps/widget_area/tpl-preview-1]';
	}


	// Frontend output
	public function frontend_value( $values = array() ) {

		$result = '';

		if ( isset( $values["widget_area"] ) ) {
			ob_start();
			if ( is_active_sidebar( $values["widget_area"] ) ) {
				dynamic_sidebar( $values["widget_area"] );
			}
			$result = ob_get_contents();
			ob_end_clean();
		}

		return $result;

	}


}
