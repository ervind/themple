<?php
/*
Plugin Name: Themple Helper
Plugin URI:  http://arachnoidea.com/themple-framework
Description: A helper plugin for the Themple Framework. It adds extra stuff that a standard WP theme can't contain. Also makes it easier to switch from a Themple-based theme to another.
Version:     1.2a1
Author:      A-idea Studio
Author URI:  http://arachnoidea.com/
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
