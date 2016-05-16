<?php

/*
Themple Framework core file
For more information and documentation, visit [http://www.arachnoidea.com/themple-framework]
*/


// Version number of the framework
define( 'THEMPLE_VERSION', '1.2a1' );




// Adding admin script...
add_action ( 'admin_enqueue_scripts', 'tpl_admin_scripts' );

// Setting up the framework's text domain
add_action( 'after_setup_theme', function() {
	load_theme_textdomain( 'themple', get_template_directory() . '/framework/languages' );
});

// Load data type components
add_action ( 'after_setup_theme', 'tpl_load_data_types' );
// Load ALL options
add_action ( 'after_setup_theme', 'tpl_load_all_options' );

// Initial actions (menus, admin pages)
add_action ( 'admin_menu', 'tpl_admin_init' );

// LESS compiler function
add_action ( 'init', 'tpl_make_css' );
add_action ( 'update_option_tpl_theme_options', 'tpl_make_css' );

// Hook the success message to the Theme Options page
add_action ( 'admin_init', 'tpl_theme_options_message' );

// Enqueue scripts
add_action ( 'wp_enqueue_scripts', 'tpl_front_scripts' );

// Comment reply script
add_action( 'comment_form_before', 'tpl_comment_reply_script' );

// Pass vars to JS from options
add_action( 'wp_head', 'tpl_vars_to_js' );

// Add metaboxes for post editor
add_action( 'add_meta_boxes', 'tpl_add_custom_box' );

// Save postmeta when saving a post
add_action( 'pre_post_update', 'tpl_save_postdata' );

// Add the possibility to use shortcodes in text widgets
add_filter( 'widget_text', 'do_shortcode' );

// Security: hide the META Generator tag to make it more difficult for hackers to find out your WP version.
remove_action( 'wp_head', 'wp_generator' );

// Security: remove ?ver=x.x from css and js
add_filter( 'style_loader_src', 'tpl_remove_cssjs_ver', 10, 2 );
add_filter( 'script_loader_src', 'tpl_remove_cssjs_ver', 10, 2 );

// The options array that stores all the options to be displayed in Theme Options
$tpl_options_array = array();

// The sections array that stores the sections (tabs) in Theme Options and metaboxes in post editor
$tpl_sections = array();

// Registered data types
$tpl_data_types = array();

// Registered less files
$tpl_less_files = array();



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

	// Is LESS compiler turned ON in Framework Options?
	$less_compiler = tpl_get_value( 'less_compiler' );
	// If it has the Dynamic setting and the Theme Options have just been modified, turn ON the compiler
	if ( current_filter() == 'update_option_tpl_theme_options' && tpl_get_value( 'less_compiler' ) == 'dynamic' ) {
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
		$files = glob ( dirname ( dirname ( __FILE__ ) ) . '/framework/style/*.less' );
		foreach ( $files as $file ) {
			$base_less .= '@import "' . $file . '";
			';
			$current_less_filedata[$file]["mod_time"] = filemtime( $file );
			$current_less_filedata[$file]["size"] = filesize( $file );
		}

		// Add the LESS files of the Theme
		$files = glob ( dirname ( dirname ( __FILE__ ) ) . '/style/*.less' );
		foreach ( $files as $file ) {
			$theme_less .= '@import "' . $file . '";
			';
			$current_less_filedata[$file]["mod_time"] = filemtime( $file );
			$current_less_filedata[$file]["size"] = filesize( $file );
		}

		// Add the LESS files of the Child theme (if exists and activated)
		if ( get_stylesheet_directory() != get_template_directory() ) {
			$files = glob ( get_stylesheet_directory() . '/style/*.less' );
			foreach ( $files as $file ) {
				$child_less .= '@import "' . $file . '";
				';
				$current_less_filedata[$file]["mod_time"] = filemtime( $file );
				$current_less_filedata[$file]["size"] = filesize( $file );
			}
		}


		// Admin styles
		$files = glob ( dirname ( __FILE__ ) . '/style/admin/*.less' );
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

	}

	if ( $less_compiler == 'dynamic' ) {

		foreach ( $current_less_filedata as $fname => $file ) {

			if ( $file["mod_time"] > $saved_less_filedata[$fname]["mod_time"] || $file["size"] != $saved_less_filedata[$fname]["size"] ) {
				$less_compiler = 'on';
			}

		}

	}

	if ( $less_compiler == 'on' ) {

		update_option( 'tpl_saved_less_filedata', $current_less_filedata );

		require_once ABSPATH . "wp-admin/includes/file.php";
		WP_Filesystem();
        global $wp_filesystem;
		require_once "lib/lessphp/Less.php";

		// Adds Font Awesome support
		$fa_less = '@import "' . get_template_directory() . '/framework/lib/font-awesome/less/font-awesome.less";
		';

		// Common variables
		$common_less = '@theme_url : "' . get_template_directory_uri() . '";
		';

		// Write front end CSS using the Theme Options and the front end Less files
		$parser = new Less_Parser();
		$parser->SetOptions( array( "relativeUrls" => false) );
		$parser->parse( $common_less );
		$parser->parse( $less_variables );
		if ( tpl_get_option( 'font_awesome' ) == 'yes' ) {
			$parser->parse( $fa_less );
		}
		$parser->parse( $base_less );
		$parser->parse( $theme_less );
		if ( get_stylesheet_directory() != get_template_directory() ) {
			$parser->parse( $child_less );
		}
		$css = $parser->getCss();
		$tpl_css = dirname ( dirname ( __FILE__ ) ) . '/style/css/theme.css';
		if ( !$wp_filesystem->put_contents( $tpl_css, $css ) ) {
			tpl_error( __( 'Could not write front end CSS file - check your file permissions!', 'themple' ), true );
		}

		// Write back end CSS using the admin Less files
		$alparser = new Less_Parser ();
		$alparser->SetOptions( array( "relativeUrls" => false) );
		$alparser->parse( $common_less );
		$alparser->parse( $fa_less );
		$alparser->parse( $admin_less );
		$alparser->parse( $addon_less );
		$admin_css = $alparser->getCss();
		$al_css = dirname ( __FILE__ ) . '/style/admin/css/admin.css';
		if ( !$wp_filesystem->put_contents( $al_css, $admin_css ) ) {
			tpl_error( __( 'Could not write admin CSS file - check your file permissions!', 'themple' ), true );
		}

	}

}




/*
HELPER FUNCTIONS
*/

// Displays an error message in an alert box. If global = true then it displays it as an admin notification. Else next to the option in the options flow
// $class can have 3 values: error, updated, update-nag (https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices)
function tpl_error ( $msg, $global = false, $class = "error" ) {

	if ( $global == false ) {

		echo '<p class="tpl-error">' . esc_html( $msg ) . '</p>';

	}

	else {

		add_action( 'admin_notices', function() use ( $msg, $class ) {
			if ( $class == 'error' ) {
				$msg = '<strong>' . __( 'Themple error', 'themple' ) . '</strong>: ' . $msg;
			}
			if ( $class == 'update-nag' ) {
				$msg = '<strong>' . __( 'Themple warning', 'themple' ) . '</strong>: ' . $msg;
			}
			echo '<div class="notice is-dismissible settings-error tpl-global-error '. esc_attr( $class ) .'"><p>'. $msg .'</p></div>';
		});

	}

}



// Safe loader for framework parts. Uses require_once if the file exists and is readable
function tpl_loader ( $filename ) {

	if ( file_exists ( $filename ) ) {

		require_once $filename;
		return $filename;

	}

	else {
		tpl_error ( __( 'File not exists', 'themple' ) . ': ' . $filename, true );
	}

}




// Read all data types that are present in the 'framework/data-types' directory
function tpl_load_data_types () {

	global $tpl_data_types, $tpl_less_files;

	require_once ABSPATH . "wp-admin/includes/file.php";
	WP_Filesystem();
	global $wp_filesystem;

	$dt_json_file = dirname ( __FILE__ ) . '/data-types/data-types.json';
	$dt_json = json_decode( $wp_filesystem->get_contents( $dt_json_file ), true );
	$files = $dt_json["dt_files"];

	// $files = glob ( dirname ( __FILE__ ) . '/data-types/*.php' );
	foreach ( $files as $file ) {

		tpl_loader ( dirname ( __FILE__ ) . '/data-types/' . $file . ".php" );
		$curtype = explode ( '-', $file );
		$tpl_data_types[] = $curtype[1];

		if ( file_exists ( dirname ( __FILE__ ) . '/data-types/' . $file . '.less' ) ) {
			$tpl_less_files[$file] = dirname ( __FILE__ ) . '/data-types/' . $file . '.less';
		}

	}

}





// Read all options that are present in the 'options' directory
function tpl_load_all_options () {

	// Load Framework Options first
	tpl_loader( dirname ( __FILE__ ) . '/framework-options.php' );

	// Now load all other options
	$files = glob ( dirname ( dirname ( __FILE__ ) ) . '/options/*.php' );
	foreach ( $files as $file ) {
		tpl_loader ( $file );
	}

}




/*
OPTION HANDLING
*/

// Make an option appear in the Theme Options page. $narr is a foreign array coming from a module that is to be added to the global $tpl_options_array
function tpl_register_option ( $narr ) {

	// Use the global $tpl_options_array that builds up the Theme Options panel
	global $tpl_options_array, $tpl_data_types, $tpl_less_files;

	// Prevent the site frome freezing if an option has no name
	if ( !isset( $narr["name"] ) ) {
		tpl_error(
			__( 'You forgot to set name for an option. Please check your options files. LESS compiler is turned OFF until you resolve this issue.', 'themple' ), true
		);
		$tpl_less_files["NONAME_ERROR"] = 1;
		return;
	}

	$already_registered = false;
	$narr_name = $narr["name"];

	if ( isset( $tpl_options_array[$narr_name] ) ) {
		$already_registered = true;
	}

	if ( !$already_registered ) {

		if ( !isset( $narr["type"] ) || !in_array( $narr["type"], $tpl_data_types ) ) {

			tpl_error(
				sprintf(
					__( 'You forgot to set the type of %s. Using static to avoid errors...', 'themple' ),
					$narr["name"]
				), true
			);

			$narr["type"] = "static";

		}

		$type_class = tpl_get_type_class( $narr["type"] );
		if ( class_exists( $type_class ) ) {
			$tpl_options_array[$narr_name] = new $type_class( $narr );
		}
		else {
			tpl_error(
				sprintf( __( 'Data type %s doesn\'t exists', 'themple' ),
					'<strong>'. $narr["type"] .'</strong>'
				), true );
		}
	}
	else {
		tpl_error(
			sprintf( __( 'Option with name %1$s was defined more than once in your options files. Using the first instance (%2$s)', 'themple' ),
				'<strong>'. $narr["name"] .'</strong>',
				$tpl_options_array[$narr_name]->title
			), true );
	}

}




// Adds extra info for the tabs in the Theme Options
function tpl_register_section ( $narr ) {

	// Use the global $tpl_sections that builds up the Theme Options panel's sections and the post metaboxes
	global $tpl_sections;

	$narr_name = $narr["name"];

	if ( !isset( $narr["post_type"] ) ) {
		$narr["post_type"] = 'theme_options';
	}

	$tpl_sections[$narr_name] = $narr;

}




// Checks if a data type is present in the Framework
function tpl_type_registered ( $type ) {

	global $tpl_data_types;

	if ( in_array ( $type, $tpl_data_types ) ) {
		return true;
	}
	else {
		return false;
	}

}




// Checks if a section is registered
function tpl_section_registered ( $section ) {

	global $tpl_sections;

	if ( array_key_exists ( $section, $tpl_sections ) ) {
		return true;
	}
	else {
		return false;
	}

}




// Checks if an option is registered
function tpl_option_registered ( $name ) {

	global $tpl_options_array;

	if ( array_key_exists ( $name, $tpl_options_array ) ) {
		return true;
	}
	else {
		return false;
	}

}




// Collects the sections for the $post_type requested. If no post type is selected, it returns the Theme Options sections.
function tpl_get_sections ( $post_type = "theme_options" ) {

	global $tpl_sections;

	$output_sections = array();

	foreach ( $tpl_sections as $section ) {

		if ( tpl_has_section_post_type( $section["name"], $post_type ) ) {
			$output_sections[] = $section;
		}

	}

	return $output_sections;

}



// Gets the class name for the specified data type
function tpl_get_type_class( $type ) {

	return 'TPL_' . ucfirst( $type );

}




// Gets all options for $section and returns an array of them.
function tpl_options_by_section ( $section ) {

    global $tpl_options_array;

    $result = array();

    foreach ( $tpl_options_array as $option ) {

		if ( $option->section == $section ) {
			$result[] = $option;
		}

    }

    return $result;

}




// Initializes the Theme Options and the Framework Options pages
function tpl_admin_init () {

    global $tpl_options_array, $tpl_sections;

    add_theme_page (
		__( 'Theme Options', 'themple' ),
		__( 'Theme Options', 'themple' ),
		'edit_theme_options',
		'tpl_theme_options',
		'tpl_theme_options'
	);

	if ( !defined( 'HIDE_FRAMEWORK_OPTIONS' ) || HIDE_FRAMEWORK_OPTIONS == false ) {
		add_theme_page (
			__( 'Framework Options', 'themple' ),
			__( 'Framework Options', 'themple' ),
			'edit_theme_options',
			'tpl_framework_options',
			'tpl_framework_options'
		);
	}

	if ( get_option( 'tpl_theme_options' ) == false ) {
		add_option( 'tpl_theme_options' );
	}
	if ( get_option( 'tpl_framework_options' ) == false ) {
		add_option( 'tpl_framework_options' );
	}

	register_setting (
		'tpl_theme_options',
		'tpl_theme_options'
	);
	register_setting (
		'tpl_framework_options',
		'tpl_framework_options'
	);

	$loaded_sections = array();


	// Add the option fields
	foreach ( $tpl_options_array as $option ) {

		// Making it foolproof - if the developer forgot to register the section...
		if ( !tpl_section_registered ( $option->section ) ) {

			tpl_register_section ( array(
				"name"			=> $option->section,
				"title"			=> $option->section,
				"description"	=> "",
				"tab"			=> $option->section
			) );

			tpl_error (
				sprintf( __( 'Section "%s" hasn\'t been registered properly. Please register it in options/sections.php.', 'themple' ),
				$option->section
			), true, 'update-nag' );

		}

		// Setting up which page it has to appear on
		if ( tpl_is_primary_section( $option->section ) ) {
			if ( $tpl_sections[$option->section]['post_type'] != 'framework_options' ) {
				$page = 'tpl_theme_options';
			}
			else {
				$page = 'tpl_framework_options';
			}
		}

		// Dynamically create sections if not registered yet.
		if ( !in_array ( $option->section, $loaded_sections ) ) {

			add_settings_section(
				$option->section,
				tpl_section_name ( $option->section ),
				'tpl_section_info',
				$page
			);

			$loaded_sections[] = $option->section;

		}

		if ( !isset ( $option->type ) ) {
			$option->type = "";
		}

		if ( $tpl_sections[$option->section]['post_type'] == 'theme_options' ) {

			add_settings_field (
				$option->name,
				$option->title,
				'tpl_theme_options_callback',
				'tpl_theme_options',
				$option->section,
				(array) $option
			);

		}

		elseif ( $tpl_sections[$option->section]['post_type'] == 'framework_options' ) {

			add_settings_field (
				$option->name,
				$option->title,
				'tpl_theme_options_callback',
				'tpl_framework_options',
				$option->section,
				(array) $option
			);

		}

	}


	// Load remaining registered sections
	foreach ( $tpl_sections as $section ) {

		if ( $tpl_sections[$section['name']]['post_type'] != 'framework_options' ) {
			$page = 'tpl_theme_options';
		}
		else {
			$page = 'tpl_framework_options';
		}

		if ( !in_array( $section["name"], $loaded_sections ) ) {

			add_settings_section(
				$section["name"],
				tpl_section_name ( $section["name"] ),
				'tpl_section_info',
				$page
			);

		}

		if ( !in_array( $section["name"], $loaded_sections ) ) {
			$loaded_sections[] = $section["name"];
		}

	}

}





// Section callback function
function tpl_section_info ( $arg ) {

	global $tpl_sections;

	if ( !tpl_section_registered ( $arg["id"] ) ) {
		return;
	}

	echo '<p>' . $tpl_sections[$arg["id"]]["description"] . '</p>';

}



// Section callback function
function tpl_section_name ( $section_slug ) {

	global $tpl_sections;

	if ( !tpl_section_registered ( $section_slug ) ) {
		return $section_slug;
	}

	return $tpl_sections[$section_slug]["title"];

}



// Returns true if $section_name is connected to $post_type
function tpl_has_section_post_type ( $section_name, $post_type ) {

	global $tpl_sections;

	if ( is_array( $tpl_sections[$section_name]["post_type"] ) ) {
		if ( in_array( $post_type, $tpl_sections[$section_name]["post_type"] ) ) {
			return true;
		}
	}
	else {
		if ( $post_type == $tpl_sections[$section_name]["post_type"] ) {
			return true;
		}
	}

	return false;

}



// This function puts an option input field on the Theme Options page
function tpl_theme_options_callback ( $args ) {

	global $tpl_options_array, $tpl_sections;

	$name = $args["name"];
	$type = $args["type"];
	$page = 'tpl_theme_options';

	if ( tpl_has_section_post_type( $args["section"], "framework_options" ) ) {
		$page = 'tpl_framework_options';
	}

	if ( get_option ( $page ) == "" ) {
		$options = array ();
	}
	else {
		$options = get_option ( $page );
	}

	if ( !array_key_exists ( $name, $options ) ) {
		$options[$name] = "";
	}


	if ( $type == "" ) {
		tpl_error (
			sprintf( __( 'No data type was set up for option: %s', 'themple' ),
				$name
			) );
	}

	elseif ( !tpl_type_registered ( $type ) ) {
		tpl_error (
			sprintf( __( 'Invalid data type (%1$s) was set for option: %2$s', 'themple' ),
				$type,
				$name
			) );
	}

	else {

		$tpl_options_array[$name]->form_field();

		if ( $tpl_options_array[$name]->repeat == true ) {
			echo '<button class="repeat-add">' . __( 'Add row', 'themple' ) . '</button>';
		}

		if ( isset( $args["description"] ) && ( $args["description"] != "" ) ) {
			echo '</td></tr><tr><td class="optiondesc clearfix" colspan="2">'. $args["description"];
		}

	}

}



// Gets the formatted value of an option
function tpl_get_value ( $name ) {

    global $tpl_options_array;

	if ( is_array( $name ) ) {
		if ( isset ( $tpl_options_array[$name["name"]]->type ) ) {
			return $tpl_options_array[$name["name"]]->get_value( $name );
		}
		else {
			return '';
		}
	}

	else {
		if ( isset ( $tpl_options_array[$name]->type ) ) {
			return $tpl_options_array[$name]->get_value();
		}
		else {
			return '';
		}
	}

}


// Prints the value of an option
function tpl_value ( $name ) {

    global $tpl_options_array;

	if ( is_array( $name ) ) {
		if ( isset ( $tpl_options_array[$name["name"]]->type ) ) {
			return $tpl_options_array[$name["name"]]->value( $name );
		}
		else {
			return '';
		}
	}

	else {
		if ( isset ( $tpl_options_array[$name]->type ) ) {
			return $tpl_options_array[$name]->value();
		}
		else {
			return '';
		}
	}

}


// Gets the unformatted value from wpdb
function tpl_get_option ( $name ) {

	global $tpl_options_array;

	if ( is_array( $name ) ) {
		if ( isset ( $tpl_options_array[$name["name"]]->type ) ) {
			return $tpl_options_array[$name["name"]]->get_option( $name );
		}
		else {
			return '';
		}
	}

	else {
		if ( isset ( $tpl_options_array[$name]->type ) ) {
			return $tpl_options_array[$name]->get_option();
		}
		else {
			return '';
		}
	}

}


// Gets the full option object for more advanced use. Devs can reach the full spectrum of data type functions with this function.
function tpl_get_option_object ( $name ) {

	global $tpl_options_array;

	if ( !isset ( $tpl_options_array[$name] ) ) {

		return false;

	}

    else {

		return $tpl_options_array[$name]->get_object();

    }

}



// Checks if this is primary (Theme Options, Framework Options) or secondary (e.g. Post Metabox) section
function tpl_is_primary_section ( $section ) {

	global $tpl_sections;

	if ( isset( $tpl_sections[$section] ) && ( tpl_has_section_post_type( $section, 'framework_options' ) || tpl_has_section_post_type( $section, 'theme_options' ) ) ) {
		return true;
	}

	if ( !isset( $tpl_sections[$section]["post_type"] ) || $tpl_sections[$section]["post_type"] == '' ) {
		return true;
	}

	return false;

}




// This is the modified version of WP's do_settings_sections that allows us to use jQuery UI tabs in the Theme Options page.
function tpl_settings_sections ( $page ) {
	global $wp_settings_sections, $wp_settings_fields;

	if ( !isset( $wp_settings_sections[$page] ) ) {
		return;
	}

	foreach ( (array) $wp_settings_sections[$page] as $section ) {

		if ( tpl_is_primary_section ( $section["id"] ) == true ) {

			echo '<div id="' . $section["id"] . '">';

			if ( $section['title'] ) {
				echo "<h3>{$section['title']}</h3>\n";
			}

			if ( $section['callback'] ) {
				call_user_func( $section['callback'], $section );
			}

			if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) ) {
				continue;
			}

			echo '<table class="form-table">';
			do_settings_fields( $page, $section["id"] );
			echo '</table>
			</div>';

		}
	}
}





// Theme Options page generator function
function tpl_theme_options () {

	global $tpl_sections;

	// Make the base structure of the Theme Options page
	echo '<div class="wrap">
            <h2 id="tpl-options-main-title">' . __( 'Theme Options', 'themple' ) . '</h2>
            <form method="post" action="options.php">';

	// The number of sections to be created for Theme Options page
	$tpl_to_sections = tpl_get_sections ();


	// Launch the tabbed layout only if there are more than 1 sections defined
	if ( count ( $tpl_to_sections ) > 1 ) {
		echo '<div id="tpl-settings-tabs" data-store="tpl_theme_options_activetab">
				<ul class="nav-tab-wrapper">';

		foreach ( $tpl_to_sections as $section ) {
			echo '<li><a class="nav-tab" href="#' . $section["name"] . '">' . $section["tab"] . '</a></li>';
		}

		echo '</ul>
		';
    }

	// Output the sections
	tpl_settings_sections ( 'tpl_theme_options' );

	// There was an open div if we used the tabbed layout
    if ( count ( $tpl_to_sections ) > 1 ) {
		echo '</div>';
	}

	settings_fields ( 'tpl_theme_options' );
    echo get_submit_button() . '</form>
        </div>';

}



// Framework Options page generator function
function tpl_framework_options () {

	global $tpl_sections;

	// Make the base structure of the Framework Options page
	echo '<div class="wrap">
            <h2 id="tpl-options-main-title">' . __( 'Framework Options', 'themple' ) . '</h2>
            <form method="post" action="options.php">';

	// The number of sections to be created for Theme Options page
	$tpl_fo_sections = tpl_get_sections( "framework_options" );

	// Launch the tabbed layout only if there are more than 1 sections defined
	if ( count ( $tpl_fo_sections ) > 1 ) {
		echo '<div id="tpl-settings-tabs" data-store="tpl_framework_options_activetab">
				<ul class="nav-tab-wrapper">';

		foreach ( $tpl_fo_sections as $section ) {
			echo '<li><a class="nav-tab" href="#' . $section["name"] . '">' . $section["tab"] . '</a></li>';
		}

		echo '</ul>
		';
    }

	// Output the sections
	tpl_settings_sections ( 'tpl_framework_options' );

	// There was an open div if we used the tabbed layout
    if ( count ( $tpl_fo_sections ) > 1 ) {
		echo '</div>';
	}

	settings_fields ( 'tpl_framework_options' );
    echo get_submit_button() . '</form>
        </div>';

}





// Adds the post metaboxes from the registered sections
function tpl_add_custom_box ( $post_type ) {

    $sections = tpl_get_sections ( $post_type );

    foreach ( $sections as $section ) {

        add_meta_box(
            $section["name"],
            $section["title"],
            'tpl_inner_custom_box',
            $post_type,
            'normal',
            'low',
            array ( "section" => $section["name"], "description" => $section["description"] )
        );

    }
}




// Display contents of custom metabox for post types
function tpl_inner_custom_box ( $post, $metabox ) {

	// Add an nonce field so we can check for it later.
	wp_nonce_field ( 'tpl_inner_custom_box', 'tpl_inner_custom_box_nonce' );

	if ( $metabox["args"]["description"] != '' ) {
		echo $metabox["args"]["description"];
	}


	// Selects all options from $tpl_options_array which are in the current section
	$options = tpl_options_by_section ( $metabox["args"]["section"] );

	foreach ( $options as $option ) {

		$meta_key = '_tpl_' . $option->get_section();

		if ( get_post_meta ( $post->ID, $meta_key ) == "" ) {
			$values = array();
		}
		else {
			$values = get_post_meta ( $post->ID, $meta_key );
		}

		if ( !isset ( $values[0][$option->name] ) ) {
			$values[0][$option->name] = "";
		}

		echo '<div class="clearfix meta-option">';

		echo '<span class="meta-option-label">' . $option->title . '</span>';

		if ( !isset( $option->type ) || $option->type == "" ) {
			tpl_error(
				sprintf( __( 'No data type was set up for option: %s', 'themple' ),
				$option->name
			) );
		}

		elseif ( !tpl_type_registered ( $option->type ) ) {
			tpl_error (
				sprintf( __( 'Invalid data type (%1$s) was set for option: %2$s', 'themple' ),
					$option->type,
					$option->name
				) );
		}

		else {

			echo '<div class="meta-option-wrapper">';
				$option->form_field();
				if ( $option->repeat == true ) {
					echo '<button class="repeat-add">' . __( 'Add row', 'themple' ) . '</button>';
				}
			echo '</div>';

		}

		echo '</div><p class="optiondesc clearfix">'. $option->description .'</p><div class="clearfix"></div>';

	}

	echo '<div class="clearfix"></div>';

}



// Saves post meta into the database
function tpl_save_postdata( $post_id ) {

	// Check if our nonce is set.
	if ( ! isset( $_POST['tpl_inner_custom_box_nonce'] ) ) {
		return $post_id;
	}

	$nonce = $_POST['tpl_inner_custom_box_nonce'];

	// Verify that the nonce is valid.
	if ( !wp_verify_nonce ( $nonce, 'tpl_inner_custom_box' ) ) {
		return $post_id;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// Check the user's permissions.
	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
	}
	else {
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
	}

	/* OK, its safe for us to save the data now. */

	// Update the meta field in the database.

	$sections = tpl_get_sections ( get_post_type ( $post_id ) );

	foreach ( $sections as $section ) {

		$options = tpl_options_by_section ( $section["name"] );
		$data = array();

		foreach ( $options as $option ) {

			$sn = $option->form_ref();
			$sn = explode( '[', $sn );
			$sn = $sn[0];
			$data[$option->name] = $_POST[$sn];

		}

		update_post_meta( $post_id, '_tpl_' . $section["name"], $data );
		unset ( $data );

	}

}


// Add success message
function tpl_theme_options_message() {
	if ( ( isset( $_GET["settings-updated"] ) && $_GET["settings-updated"] == 'true' ) && ( isset( $_GET["page"] ) && $_GET["page"] == 'tpl_theme_options' ) ) {
		tpl_error( __( 'Settings saved.', 'themple' ), true, "updated" );
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

	wp_enqueue_style( 'front-style', get_template_directory_uri() . '/style/css/theme.css', array(), THEMPLE_VERSION );

	if ( file_exists( dirname( dirname ( __FILE__ ) ) . '/script/script.js' ) ) {
		wp_enqueue_script( 'tpl-script', get_template_directory_uri() . '/script/script.js', array( 'jquery' ), THEMPLE_VERSION, true );
	}

}


// Comment reply script loader
function tpl_comment_reply_script() {

	if ( is_singular() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}


// Load the scripts needed in Theme Options
function tpl_admin_scripts() {

	wp_enqueue_script( 'jquery-ui-tabs', '', array('jquery', 'jquery-ui-core') );
	wp_enqueue_script( 'jquery-ui-sortable', '', array('jquery', 'jquery-ui-core') );
	wp_enqueue_script( 'select2', get_template_directory_uri() . '/framework/lib/select2/js/select2.min.js', array('jquery' ), THEMPLE_VERSION );
	wp_enqueue_style( 'select2-style', get_template_directory_uri() . '/framework/lib/select2/css/select2.min.css', array(), THEMPLE_VERSION );
	wp_enqueue_script( 'tpl-admin-scripts', get_template_directory_uri() . '/framework/script/admin-scripts.js', array( 'jquery', 'jquery-ui-tabs' ), THEMPLE_VERSION );
	wp_enqueue_style( 'tpl-admin-style', get_template_directory_uri() . '/framework/style/admin/css/admin.css', array(), THEMPLE_VERSION );

	wp_localize_script( 'tpl-admin-scripts', 'Themple_Admin', array_merge( array(
		'uploader_title'		=> __( 'Choose Image', 'themple' ),
		'uploader_button'		=> __( 'Choose Image', 'themple' ),
		'remover_confirm_text'	=> __( 'Do you really want to remove this instance?', 'themple' ),
	), tpl_admin_vars_to_js() ) );

	tpl_admin_vars_to_js();

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


// JS vars, admin version
function tpl_admin_vars_to_js() {

	global $tpl_options_array;
	$to_js = array();

	foreach ( $tpl_options_array as $option ) {

		if ( tpl_has_section_post_type ( $option->section, "framework_options" ) ) {

			if ( isset ( $option->js ) && ( $option->js == true ) ) {

				$func_name = $option->js_func;
				$to_js[$option->name] = $option->$func_name();

			}

		}

	}

	return $to_js;

}



/*
DEALING WITH IMAGE SIZES
*/

// Sets up added image sizes
function tpl_images_setup () {

	$image_sizes = tpl_image_sizes();

	// Add post thumbnail support to the theme
	add_theme_support( 'post-thumbnails' );

	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	if ( is_array( $image_sizes ) ) {

		if ( isset( $image_sizes["post-thumbnail"] ) ) {
			set_post_thumbnail_size( $image_sizes["post-thumbnail"]["width"], $image_sizes["post-thumbnail"]["height"], $image_sizes["post-thumbnail"]["crop"] );
		}
		// Add extra image sizes
		foreach ( $image_sizes as $name => $image_size ) {
			if ( $name != 'post-thumbnail' ) {
				add_image_size( $name, $image_size["width"], $image_size["height"], $image_size["crop"] );
			}
		}

	}

}
add_action ( 'after_setup_theme', 'tpl_images_setup' );


// If you defined the "select" attribute of an image size as TRUE in $tpl_image_sizes, this function will add it to the image size selector menu in the post editor
function tpl_image_selector_sizes( $sizes ) {

	$image_sizes = tpl_image_sizes();
	$addsizes = array();

	foreach ( $image_sizes as $name => $image_size ) {
		if ( $image_size["select"] == true ) {
			$addsizes[$name] = $image_size["title"];
		}
	}

	$newsizes = array_merge( $sizes, $addsizes );
	return $newsizes;

}
add_filter('image_size_names_choose', 'tpl_image_selector_sizes');






/*
PLUGIN HANDLING
*/

add_action( 'after_setup_theme', function() {

	// Include the TGM library only if it's enabled in Framework Options
	if ( tpl_get_value( 'tgm_pa' ) == 'yes' ) {
		require_once get_template_directory() . '/framework/lib/tgmpa/class-tgm-plugin-activation.php';
		add_action( 'tgmpa_register', 'tpl_register_required_plugins' );
	}

} );

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
		'default_path' => get_template_directory() . '/inc/plugins/',
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
