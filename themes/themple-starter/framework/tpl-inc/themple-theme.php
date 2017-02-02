<?php

/*
Theme-specific functions of the Themple Framework. Leave this file out of your package if you're using the framework for a plugin.
*/



// Save the framework version to the database, so plugins can detect if they need it
add_action( 'after_switch_theme', 'tpl_version_to_db' );
add_action( 'after_setup_theme', 'tpl_version_to_db' );
// And remove on deactivation
add_action( 'switch_theme', function() {
	delete_option( 'tpl_version' );
} );


// Setting up the framework's text domain. Loading it only in the admin to make the front-end faster
add_action( 'after_setup_theme', function() {
	if ( is_admin() ) {
		load_theme_textdomain( 'themple', tpl_base_dir() . '/framework/languages' );
	}
} );


// Load data type components
add_action ( 'init', 'tpl_load_data_types_less' );


// Load ALL options - happens only after data types are loaded
add_action ( 'init', 'tpl_load_all_options', 20 );


// Call the LESS compiler function
add_action ( 'init', 'tpl_make_css', 40 );
add_action ( 'update_option_tpl_theme_options', 'tpl_make_css' );
add_action ( 'update_option_tpl_framework_options', 'tpl_make_css' );


// Hook the save success message to the Appearance options pages
add_action ( 'admin_init', 'tpl_theme_options_message' );


// Enqueue front end scripts
add_action ( 'get_footer', 'tpl_front_scripts' );
// Pass vars to JS from options
add_action( 'get_footer', 'tpl_vars_to_js' );
// Front end styles
add_action ( 'wp_enqueue_scripts', 'tpl_front_styles' );

// Comment reply script
add_action( 'comment_form_before', 'tpl_comment_reply_script' );


// Add editor styles for better consistency between front and back end look
add_action( 'init', 'tpl_add_editor_styles' );


// Add the possibility to use shortcodes in text widgets
add_filter( 'widget_text', 'do_shortcode' );


// Security: hide the META Generator tag to make it more difficult for hackers to find out your WP version.
remove_action( 'wp_head', 'wp_generator' );


// Security: remove ?ver=x.x from css and js
add_filter( 'style_loader_src', 'tpl_remove_cssjs_ver', 10, 2 );
add_filter( 'script_loader_src', 'tpl_remove_cssjs_ver', 10, 2 );


// Customizing the option no name error message for themes
add_filter( 'tpl_option_noname_errormsg', function( $msg ) {
	global $tpl_less_files;
	$tpl_less_files["NONAME_ERROR"] = 1;
	return $msg . ' ' . __( 'LESS compiler is turned OFF until you resolve this issue.', 'themple' );
} );


// Now setting up global variables:

// Registered less files
$tpl_less_files = array();




// And now defining the theme-specific functions

/*
MAKER FUNCTIONS
*/

// This function compiles the frontend and admin CSS files from the LESS files in the system using the values of the options as LESS variables
function tpl_make_css () {

	global $tpl_options_array, $tpl_less_files;

	// If there is a variable with no name, don't run the LESS compiler, because it can break the site
	if ( isset( $tpl_less_files["NONAME_ERROR"] ) ) {
		return;
	}

	// Data init for front end
	$base_less = '';
	$theme_less = '';
	$less_variables = '';
	if ( get_stylesheet_directory() != get_template_directory() ) {
		$child_less = '';
	}

	// Data init for back end
	$admin_less = '';
	$addon_less = '';
	$editor_less = '';

	// Is LESS compiler turned ON in Framework Options?
	$less_compiler = tpl_get_value( 'less_compiler' );

	// If it has the Dynamic setting and the Theme Options have just been modified, turn ON the compiler
	if ( ( current_filter() == 'update_option_tpl_theme_options' || current_filter() == 'update_option_tpl_framework_options' ) && tpl_get_value( 'less_compiler' ) == 'dynamic' ) {
		$less_compiler = 'on';
	}

	if ( $less_compiler == 'on' || $less_compiler == 'dynamic' ) {

		// Collect the LESS variables from Theme Options
		foreach ( $tpl_options_array as $option ) {

			// Not primary sections can't have LESS variables, because it won't work when the LESS compiler is turned OFF / kills the cacheability of the system
			if ( tpl_is_primary_section ( $option->section ) ) {
				$less_variables .= $option->set_less_vars();
			}

		}

	}


	if ( $less_compiler == 'on' || $less_compiler == 'dynamic' ) {

		$saved_less_filedata = get_option( 'tpl_saved_less_filedata', array() );
		$current_less_filedata = array();

		// Load the base LESS files from the Framework
		$files = glob ( get_template_directory() . '/framework/style/*.less' );
		foreach ( $files as $file ) {
			$base_less .= '@import "' . $file . '";
			';
			// We're also collecting the file sizes and dates if we need it for the dynamic compiler mode
			$current_less_filedata[$file]["mod_time"] = filemtime( $file );
			$current_less_filedata[$file]["size"] = filesize( $file );
		}

		// Add the LESS files of the Theme
		$files = glob ( get_template_directory() . '/style/*.less' );
		foreach ( $files as $file ) {
			$fname_fragments = explode( '/', $file );
			if ( end( $fname_fragments ) != '250-editor.less' ) {
				$theme_less .= '@import "' . $file . '";
				';
			}
			$current_less_filedata[$file]["mod_time"] = filemtime( $file );
			$current_less_filedata[$file]["size"] = filesize( $file );
		}

		// Add the LESS files of the Child theme (if exists and activated)
		if ( get_stylesheet_directory() != get_template_directory() ) {
			$files = glob ( get_stylesheet_directory() . '/style/*.less' );
			foreach ( $files as $file ) {
				$fname_fragments = explode( '/', $file );
				if ( end( $fname_fragments ) != '250-editor.less' ) {
					$child_less .= '@import "' . $file . '";
					';
				}
				$current_less_filedata[$file]["mod_time"] = filemtime( $file );
				$current_less_filedata[$file]["size"] = filesize( $file );
			}
		}


		// Admin styles
		$files = glob ( get_template_directory() . '/framework/style/admin/*.less' );
		foreach ( $files as $file ) {
			$admin_less .= '@import "' . $file . '";
			';
			$current_less_filedata[$file]["mod_time"] = filemtime( $file );
			$current_less_filedata[$file]["size"] = filesize( $file );
		}

		// Add data type specific admin styles (can be stored separately for better extensibility)
		foreach ( $tpl_less_files as $file ) {
			$addon_less .= '@import "' . $file . '";
			';
			$current_less_filedata[$file]["mod_time"] = filemtime( $file );
			$current_less_filedata[$file]["size"] = filesize( $file );
		}


		// Editor styles
		if ( file_exists( get_template_directory() . '/style/200-typography.less' ) ) {
			$editor_less .= '@import "' . get_template_directory() . '/style/200-typography.less";
			';
		}
		if ( file_exists( get_template_directory() . '/style/250-editor.less' ) ) {
			$editor_less .= '@import "' . get_template_directory() . '/style/250-editor.less";
			';
		}
		// Add the LESS files of the Child theme (if exists and activated)
		if ( get_stylesheet_directory() != get_template_directory() ) {
			if ( file_exists( get_stylesheet_directory() . '/style/200-typography.less' ) ) {
				$editor_less .= '@import "' . get_stylesheet_directory() . '/style/200-typography.less";
				';
			}
			if ( file_exists( get_stylesheet_directory() . '/style/250-editor.less' ) ) {
				$editor_less .= '@import "' . get_stylesheet_directory() . '/style/250-editor.less";
				';
			}
		}

	}

	// When the dynamic setting is used, it is only activated if there was a change in the files
	if ( $less_compiler == 'dynamic' ) {

		foreach ( $current_less_filedata as $fname => $file ) {

			if ( $file["mod_time"] > $saved_less_filedata[$fname]["mod_time"] || $file["size"] != $saved_less_filedata[$fname]["size"] ) {
				$less_compiler = 'on';
				break;
			}

		}

	}

	if ( $less_compiler == 'on' ) {

		update_option( 'tpl_saved_less_filedata', $current_less_filedata );

		require_once ABSPATH . "wp-admin/includes/file.php";
		WP_Filesystem();
        global $wp_filesystem;
		require_once get_template_directory() . "/framework/lib/lessphp/Less.php";

		// Adds Font Awesome support
		$fa_less = '@import "' . get_template_directory() . '/framework/lib/font-awesome/less/font-awesome.less";
		';

		// Common variables for all CSS files
		$common_less = '@theme_url : "' . get_template_directory_uri() . '";
		';

		// Turn ON or OFF the CSS minification
		if ( tpl_get_value( 'css_minification' ) == 'on' ) {
			$parser_options = array( 'compress' => true );
		}
		else {
			$parser_options = array();
		}

		// If we're using a multisite network, make sure that we're rewriting only the current site's CSS files
		if ( is_multisite() ) {
			$ms_addition = '-' . get_current_blog_id();
		}
		else {
			$ms_addition = '';
		}

		/*

		Write front end CSS using the Theme Options and the front end Less files

		*/

		try {

			$parser = new Less_Parser( $parser_options );
			$parser->SetOptions( array( "relativeUrls" => false) );

			$parser->parse( $common_less );
		    $parser->parse( $less_variables );

			// Include Font Awesome?
			if ( tpl_get_option( 'font_awesome' ) == 'yes' ) {
				$parser->parse( $fa_less );
			}

			$parser->parse( $base_less );
			$parser->parse( $theme_less );

			// Include child theme LESS files?
			if ( get_stylesheet_directory() != get_template_directory() ) {
				$parser->parse( $child_less );
			}

			$css = $parser->getCss();
			$css = apply_filters( 'tpl_output_css', $css );
			$css_file = get_template_directory() . '/style/css/theme' . $ms_addition . '.css';

			// Can we write the output CSS?
			if ( !$wp_filesystem->put_contents( $css_file, $css ) ) {
				tpl_error( __( 'Could not write front end CSS file - check your file permissions!', 'themple' ), true );
			}

			unset( $parser );

		}
		catch ( Exception $e ) {

		    tpl_error( sprintf( __( 'LESS Compiler error caught: %s', 'themple' ), $e->getMessage() ), true );

		}




		/*

		Write back end CSS using the admin Less files

		*/

		try {

			$parser = new Less_Parser ( $parser_options );
			$parser->SetOptions( array( "relativeUrls" => false) );
			$parser->parse( $common_less );
			$parser->parse( $fa_less );
			$parser->parse( $admin_less );
			$parser->parse( $addon_less );
			$css = $parser->getCss();
			$css_file = get_template_directory() . '/framework/style/admin/css/admin.css';
			if ( !$wp_filesystem->put_contents( $css_file, $css ) ) {
				tpl_error( __( 'Could not write admin CSS file - check your file permissions!', 'themple' ), true );
			}

			unset( $parser );

		}
		catch ( Exception $e ) {

			tpl_error( sprintf( __( 'LESS Compiler error caught: %s', 'themple' ), $e->getMessage() ), true );

		}




		/*

		Write back end editor CSS based on the typography LESS

		*/

		try {

			$parser = new Less_Parser ( $parser_options );
			$parser->SetOptions( array( "relativeUrls" => false) );
			$parser->parse( $common_less );
			$parser->parse( $less_variables );
			$parser->parse( $editor_less );
			$css = $parser->getCss();
			$css_file = get_template_directory() . '/style/css/editor' . $ms_addition . '.css';
			if ( !$wp_filesystem->put_contents( $css_file, $css ) ) {
				tpl_error( __( 'Could not write editor CSS file - check your file permissions!', 'themple' ), true );
			}
			unset( $parser );

		}
		catch ( Exception $e ) {

			tpl_error( sprintf( __( 'LESS Compiler error caught: %s', 'themple' ), $e->getMessage() ), true );

		}


	}

}





/*
HELPER FUNCTIONS
*/

// Version check and write it to the DB if necessary
function tpl_version_to_db() {

	if ( get_option( 'tpl_version' ) === false || get_option( 'tpl_version' ) != THEMPLE_VERSION ) {
		update_option( 'tpl_version', THEMPLE_VERSION );
	}

}



// Read all options that are present in the 'options' directory and in Framework Options
function tpl_load_all_options () {

	// Load Framework Options first
	tpl_loader( tpl_base_dir() . '/framework/tpl-inc/framework-options.php' );

	// Now load all other options
	$files = glob ( tpl_base_dir() . '/options/*.php' );
	foreach ( $files as $file ) {
		tpl_loader ( $file );
	}

}



// Theme Options page generator function
function tpl_theme_options () {

	tpl_settings_page( 'tpl_theme_options' );

}



// Framework Options page generator function
function tpl_framework_options () {

	tpl_settings_page( 'tpl_framework_options' );

}



// Add success message
function tpl_theme_options_message() {

	global $tpl_settings_pages;

	if ( ( isset( $_GET["settings-updated"] ) && $_GET["settings-updated"] == 'true' ) && ( isset( $_GET["page"] ) && array_key_exists( $_GET["page"], $tpl_settings_pages ) && $tpl_settings_pages[$_GET["page"]]["menu_func"] == 'add_theme_page' ) ) {
		tpl_error( __( 'Settings saved.', 'themple' ), true, "updated" );
	}

}



// Read all the LESS files connected to data types
function tpl_load_data_types_less () {

	global $tpl_data_types, $tpl_less_files;

	// Now run the load loop for Data Types
	foreach ( $tpl_data_types as $type ) {

		$file = 'type-' . $type;
		if ( file_exists ( tpl_base_dir() . '/framework/data-types/' . $file . '.less' ) ) {
			$tpl_less_files[$file] = tpl_base_dir() . '/framework/data-types/' . $file . '.less';
		}

	}

}








/*
SCRIPT HANDLING
*/

// Remove version numbers from JS and CSS files
function tpl_remove_cssjs_ver( $src ) {
	if ( strpos( $src, '?ver=' ) ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}



// Load the scripts needed in the front-end
function tpl_front_scripts() {

	if ( file_exists( tpl_base_dir() . '/script/script.js' ) ) {
		wp_enqueue_script( 'tpl-script', tpl_base_uri() . '/script/script.js', array( 'jquery' ), THEMPLE_VERSION, true );
	}

}



// Load the front-end styles
function tpl_front_styles() {

	// If we're using a multisite network, make sure that we're loading the current site's CSS file
	if ( is_multisite() ) {
		$ms_addition = '-' . get_current_blog_id();
	}
	else {
		$ms_addition = '';
	}

	if ( file_exists( tpl_base_dir() . '/style/css/theme' . $ms_addition . '.css' ) ) {
		wp_enqueue_style( 'tpl-front-style', tpl_base_uri() . '/style/css/theme' . $ms_addition . '.css', array(), THEMPLE_VERSION );
	}

}



// Comment reply script loader
function tpl_comment_reply_script() {

	if ( is_singular() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}



// Apply the editor style to the TinyMCE editors in the back end
function tpl_add_editor_styles() {

	// If we're using a multisite network, make sure that we're loading the current site's CSS file
	if ( is_multisite() ) {
		$ms_addition = '-' . get_current_blog_id();
	}
	else {
		$ms_addition = '';
	}

    add_editor_style( get_template_directory_uri() . '/style/css/editor' . $ms_addition . '.css' );

}



// Pass the required vars to JS
function tpl_vars_to_js() {

	global $tpl_options_array;
	$to_js = array();

	foreach ( $tpl_options_array as $option ) {

		if ( !tpl_has_section_post_type ( $option->section, "framework_options" ) ) {

			if ( isset ( $option->js ) && ( $option->js == true ) ) {

				$func_name = $option->js_func;
				$to_js[$option->name] = $option->$func_name();

			}

		}

	}

	wp_localize_script( 'tpl-script', 'Themple', $to_js );

}





/*
PLUGIN HANDLING
*/

// Include the TGM library only if it's enabled in Framework Options
if ( is_admin() ) {

	$tpl_fw_options = get_option( 'tpl_framework_options' );

	if ( ( isset( $tpl_fw_options["tgm_pa"][0] ) && $tpl_fw_options["tgm_pa"][0] == 'yes' ) || !isset( $tpl_fw_options["tgm_pa"][0] ) ) {
		require_once tpl_base_dir() . '/framework/lib/tgmpa/class-tgm-plugin-activation.php';
		add_action( 'tgmpa_register', 'tpl_register_required_plugins' );
	}

}



// Helps in adding the Themple Helper plugin to the Framework using the TGM Plugin Activation library
function tpl_register_required_plugins() {

	// The tpl_set_plugins function needs to be defined in the front end part
	if ( function_exists( 'tpl_set_plugins' ) ) {
		$plugins = tpl_set_plugins();
	}
	else {
		$plugins = array();
	}

	$config = array(
		'id'           => 'themple',
		'default_path' => tpl_base_dir() . '/inc/plugins/',
		'menu'         => 'tgmpa-install-plugins',
		'has_notices'  => true,
		'dismissable'  => true,
		'dismiss_msg'  => '',
		'is_automatic' => false,
		'message'      => '',

		'strings'      => array(
			'page_title'                      => __( 'Install Required Plugins', 'themple' ),
			'menu_title'                      => __( 'Install Plugins', 'themple' ),
			'installing'                      => __( 'Installing Plugin: %s', 'themple' ), // %s = plugin name.
			'oops'                            => __( 'Something went wrong with the plugin API.', 'themple' ),
			'notice_can_install_required'     => _n_noop(
				'This theme requires the following plugin: %1$s.',
				'This theme requires the following plugins: %1$s.',
				'themple'
			), // %1$s = plugin name(s).
			'notice_can_install_recommended'  => _n_noop(
				'This theme recommends the following plugin: %1$s.',
				'This theme recommends the following plugins: %1$s.',
				'themple'
			), // %1$s = plugin name(s).
			'notice_cannot_install'           => _n_noop(
				'Sorry, but you do not have the correct permissions to install the %1$s plugin.',
				'Sorry, but you do not have the correct permissions to install the %1$s plugins.',
				'themple'
			), // %1$s = plugin name(s).
			'notice_ask_to_update'            => _n_noop(
				'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
				'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
				'themple'
			), // %1$s = plugin name(s).
			'notice_ask_to_update_maybe'      => _n_noop(
				'There is an update available for: %1$s.',
				'There are updates available for the following plugins: %1$s.',
				'themple'
			), // %1$s = plugin name(s).
			'notice_cannot_update'            => _n_noop(
				'Sorry, but you do not have the correct permissions to update the %1$s plugin.',
				'Sorry, but you do not have the correct permissions to update the %1$s plugins.',
				'themple'
			), // %1$s = plugin name(s).
			'notice_can_activate_required'    => _n_noop(
				'The following required plugin is currently inactive: %1$s.',
				'The following required plugins are currently inactive: %1$s.',
				'themple'
			), // %1$s = plugin name(s).
			'notice_can_activate_recommended' => _n_noop(
				'The following recommended plugin is currently inactive: %1$s.',
				'The following recommended plugins are currently inactive: %1$s.',
				'themple'
			), // %1$s = plugin name(s).
			'notice_cannot_activate'          => _n_noop(
				'Sorry, but you do not have the correct permissions to activate the %1$s plugin.',
				'Sorry, but you do not have the correct permissions to activate the %1$s plugins.',
				'themple'
			), // %1$s = plugin name(s).
			'install_link'                    => _n_noop(
				'Begin installing plugin',
				'Begin installing plugins',
				'themple'
			),
			'update_link' 					  => _n_noop(
				'Begin updating plugin',
				'Begin updating plugins',
				'themple'
			),
			'activate_link'                   => _n_noop(
				'Begin activating plugin',
				'Begin activating plugins',
				'themple'
			),
			'return'                          => __( 'Return to Required Plugins Installer', 'themple' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'themple' ),
			'activated_successfully'          => __( 'The following plugin was activated successfully:', 'themple' ),
			'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'themple' ),  // %1$s = plugin name(s).
			'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'themple' ),  // %1$s = plugin name(s).
			'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'themple' ), // %s = dashboard link.
			'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'themple' ),

			'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
		),

	);

	tgmpa( $plugins, $config );

}
