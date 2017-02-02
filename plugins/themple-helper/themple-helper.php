<?php
/*
Plugin Name: Themple Helper
Plugin URI:  http://a-idea.studio/themple-framework
Description: A helper plugin for the Themple Framework. It adds extra stuff that a standard WP theme can't contain. Also makes it easier to switch from a Themple-based theme to another.
Version:     1.2
Author:      a-idea studio
Author URI:  http://a-idea.studio/
License:     GNU General Public License v2.0
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: themple-helper
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



// Shortcode that displays an option's value in posts and pages
add_shortcode( 'tpl-var', 'tpl_shortcode_tpl_var' );
function tpl_shortcode_tpl_var ( $atts ) {

	if ( defined( 'THEMPLE_VERSION' ) ) {
		ob_start();
		tpl_value( $atts );
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
	else {
		return;
	}

}



// Shortcode that displays the current site's home url
add_shortcode( 'tpl-siteurl', 'tpl_shortcode_tpl_siteurl' );
function tpl_shortcode_tpl_siteurl () {

	return home_url();

}



// Shortcode that adds a button where it is called
add_shortcode( 'tpl-button', 'tpl_shortcode_tpl_button' );
function tpl_shortcode_tpl_button ( $atts ) {

	$a = shortcode_atts( array(
        'url'			=> '#',					// URL of the button link
        'newtab'		=> 'no',				// no / yes - should open on new browser tab?
		'class'			=> '',					// adds extra html class to the button
		'size'			=> 'small',				// small / medium / big
		'pos'			=> 'left',				// position of the button
		'bgcolor'		=> '',					// color hex
		'color'			=> '',					// color hex
		'id'			=> '',					// html ID of the element
		'content'		=> 'Click me!',			// text on the button
    ), $atts );

	$ret = '<p class="tpl-button-wrapper tpl-button-pos-' . $a["pos"] . '">';

	$ret .= '<a href="' . $a["url"] . '" class="tpl-button';

	$ret .= ' tpl-button-size-' . $a["size"];

	if ( $a["class"] != '' ) {
		$ret .= ' ' . $a["class"];
	}

	$ret .= '"';

	if ( $a["newtab"] == 'yes' ) {
		$ret .= ' target="_blank"';
	}

	if ( $a["id"] != '' ) {
		$ret .= ' id="' . $a["id"] . '"';
	}

	if ( $a["bgcolor"] != '' || $a["color"] != '' ) {
		$ret .= ' style="';

		if ( $a["bgcolor"] != '' ) {
			$ret .= 'background-color: ' . $a["bgcolor"] . ';';
		}

		if ( $a["color"] != '' ) {
			$ret .= 'color: ' . $a["color"] . ';';
			$ret .= 'border-color: ' . $a["color"] . ';';
		}

		$ret .= '"';
	}

	$ret .= '>' . $a["content"] . '</a>';

	$ret .= '</p>';

	return $ret;

}



// Shortcode that displays Toggle blocks
add_shortcode( 'tpl-toggle', 'tpl_shortcode_tpl_toggle' );
function tpl_shortcode_tpl_toggle ( $atts, $content = null ) {

	$a = shortcode_atts( array(
        'title'			=> '',
        'default_state'	=> 'open',
    ), $atts );

	if ( $a["title"] == '' || !isset( $a["title"] ) ) {
		$a["title"] = '&nbsp;';
	}

	if ( $a["default_state"] == 'open' ) {
		$dsc = ' tpl-toggle-open';
		$csc = '';
	}
	else {
		$dsc = '';
		$csc = ' style="display: none;"';
	}

	$ret = '<div class="tpl-toggle">
		<h3 class="tpl-toggle-title">' . $a["title"] . '<span class="tpl-toggle-action' . $dsc . '"></span></h3>
		<div class="tpl-toggle-content"' . $csc . '>'
			. do_shortcode( $content ) .
		'</div>
	</div>';

	return $ret;

}


// Toggle shortcode PB App
add_action ( 'init', function() {

	if ( function_exists( 'tpl_register_pb_app' ) ) {
		tpl_register_pb_app( array(
			'name'		=> 'tpl_toggle',
			'title'		=> __( 'Toggle shortcode', 'themple-helper' ),
			'class'		=> 'TPL_PB_Toggle',
			'pos'		=> 65,
		) );
	}

}, 20 );


class TPL_PB_Toggle {


	// The extra admin fields added by this app
	public function get_admin_fields() {

		return array(
			array(
				"name"			=> 'toggles',
				"title"			=> __( 'Toggle shortcodes', 'themple-helper' ),
				"description"	=> __( 'Add toggle shortcodes here', 'themple-helper' ),
				"type"			=> 'combined',
				"repeat"		=> true,
				"parts"			=> array(
					array(
						"name"			=> 'toggle_title',
						"title"			=> __( 'Title', 'themple-helper' ),
						"description"	=> __( 'Title of the toggle box.', 'themple-helper' ),
						"type"			=> 'text',
					),
					array(
						"name"			=> 'toggle_content',
						"title"			=> __( 'Content', 'themple-helper' ),
						"description"	=> __( 'Content of the toggle box.', 'themple-helper' ),
						"type"			=> 'tinymce',
					),
					array(
						"name"			=> 'toggle_default_state',
						"title"			=> __( 'Default state', 'themple-helper' ),
						"description"	=> __( 'Should the box be open or closed by default?', 'themple-helper' ),
						"type"			=> 'select',
						"values"		=> array(
							"open"			=> __( 'Open', 'themple-helper' ),
							"closed"		=> __( 'Closed', 'themple-helper' ),
						),
						"key"			=> true,
					),
				),
			),
		);

	}


	// Preview in admin
	public function get_preview( $values = array() ) {
		return '<i class="fa fa-2x fa-window-maximize"></i> [apps/toggles/toggle_title/tpl-preview-1] &hellip;';
	}


	public function frontend_value( $values = array() ) {

		$result = '';

		if ( !empty( $values["toggles"] ) ) {

			foreach ( $values["toggles"] as $toggle ) {

				$result .= '[tpl-toggle';

				$result .= ' title="' . $toggle["toggle_title"] . '"';

				if ( isset( $toggle["toggle_default_state"] ) ) {
					$result .= ' default_state="' . $toggle["toggle_default_state"] . '"';
				}

				$result .= ']';

				if ( isset( $toggle["toggle_content"] ) ) {
					$result .= $toggle["toggle_content"];
				}

				$result .= '[/tpl-toggle]';

			}

		}

		return do_shortcode( $result );

	}


}



// Shortcode that displays Accordion
add_shortcode( 'tpl-accordion', 'tpl_shortcode_tpl_accordion' );
function tpl_shortcode_tpl_accordion ( $atts, $content = null ) {

	wp_enqueue_script( 'jquery-ui-accordion', array( 'jquery' ) );

	$a = shortcode_atts( array(
        'default_state'	=> 0,
    ), $atts );

	$ret = '<div class="tpl-accordion" data-open="' . $a["default_state"] . '">'
		. do_shortcode( $content ) .
	'</div>';

	return $ret;

}

add_shortcode( 'tpl-accordion-section', 'tpl_shortcode_tpl_accordion_section' );
function tpl_shortcode_tpl_accordion_section ( $atts, $content = null ) {

	$a = shortcode_atts( array(
        'title'		=> '',
    ), $atts );

	if ( $a["title"] == '' || !isset( $a["title"] ) ) {
		$a["title"] = '&nbsp;';
	}

	$ret = '<h3 class="tpl-accordion-title">' . $a["title"] . '<span class="tpl-accordion-action"></span></h3>
	<div class="tpl-accordion-content">'
		. do_shortcode( $content ) .
	'</div>';

	return $ret;

}

// Accordion shortcode PB App
add_action ( 'init', function() {

	if ( function_exists( 'tpl_register_pb_app' ) ) {
		tpl_register_pb_app( array(
			'name'		=> 'tpl_accordion',
			'title'		=> __( 'Accordion shortcode', 'themple-helper' ),
			'class'		=> 'TPL_PB_Accordion',
			'pos'		=> 64,
		) );
	}

}, 20 );


class TPL_PB_Accordion {


	// The extra admin fields added by this app
	public function get_admin_fields() {

		return array(
			array(
				"name"			=> 'accordion_default_state',
				"title"			=> __( 'Default State', 'themple-helper' ),
				"description"	=> __( 'The number of the panel which is open by default, starting from 0. Use a negative number to close all panels.', 'themple-helper' ),
				"type"			=> 'number',
				"default"		=> 0,
			),
			array(
				"name"			=> 'accordion_panels',
				"title"			=> __( 'Accordion Panels', 'themple-helper' ),
				"description"	=> __( 'The collapsible panels of the accordion', 'themple-helper' ),
				"type"			=> 'combined',
				"repeat"		=> true,
				"preview"		=> '[apps/accordion_panels/accordion_panel_title/tpl-preview-1] / [apps/accordion_panels/accordion_panel_content/tpl-preview-1]',
				"parts"			=> array(
					array(
						"name"			=> 'accordion_panel_title',
						"title"			=> __( 'Title', 'themple-helper' ),
						"description"	=> __( 'Title of the accordion panel.', 'themple-helper' ),
						"type"			=> 'text',
					),
					array(
						"name"			=> 'accordion_panel_content',
						"title"			=> __( 'Content', 'themple-helper' ),
						"description"	=> __( 'Content of the accordion panel.', 'themple-helper' ),
						"type"			=> 'tinymce',
					),
				),
			),
		);

	}


	// Preview in admin
	public function get_preview( $values = array() ) {
		return '<i class="fa fa-2x fa-list-alt"></i> [apps/accordion_panels/accordion_panel_title/tpl-preview-1] &hellip;';
	}


	public function frontend_value( $values = array() ) {

		$result = '';

		$result .= '[tpl-accordion';

		if ( $values["accordion_default_state"] == '' ) {
			$values["accordion_default_state"] = 0;
		}

		$result .= ' default_state="' . $values["accordion_default_state"] . '"';

		$result .= ']';

		if ( !empty( $values["accordion_panels"] ) ) {

			foreach ( $values["accordion_panels"] as $panel ) {
				$result .= '[tpl-accordion-section title="' . $panel["accordion_panel_title"] . '"]';
				$result .= $panel["accordion_panel_content"];
				$result .= '[/tpl-accordion-section]';
			}

		}

		$result .= '[/tpl-accordion]';

		return do_shortcode( $result );

	}


}
