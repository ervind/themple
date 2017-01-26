<?php

// The file must have the type-[data-type].php filename format


class TPL_Page_Builder extends TPL_Combined {


	protected	$less			= false;
	public		$repeat			= true;


	public function __construct( $args ) {

		global $tpl_pb_apps, $tpl_pb_added;

		// Initializing with the app type selector
		$apps_array = array(
			array(
				"name"			=> "app_type",
				"title"			=> __( 'Select app for this box', 'themple' ),
				"description"	=> __( 'Select an app type from the dropdown. It will be used in this box.', 'themple' ),
				"type"			=> 'select',
				"admin_class"	=> 'tpl-app_type',
				"values"		=> array(),
			),
		);


		// Reading the different app files and setting up the admin fields
		foreach ( $tpl_pb_apps as $app ) {

			$class_name = $app["class"];
			$app_obj = new $class_name();

			foreach ( $apps_array as $key => $a ) {
				if ( $a["name"] == "app_type" ) {
					$apps_array[$key]["values"][$app["name"]] = $app["title"];
				}
			}

			if ( $app_obj->get_admin_fields() !== false ) {
				foreach ( $app_obj->get_admin_fields() as $admin_field ) {
					// Setting in admin should be displayed only when the specific app is selected
					$admin_field["condition"][] = array(
						"type"		=> 'option',
						"name"		=> '_THIS_/app_type',
						"relation"	=> '=',
						"value"		=> $app["name"],
					);
					$apps_array[] = $admin_field;
				}
			}

		}


		// Putting together the whole Page Bulder admin fields array
		$args["parts"] = array(
			array(
				"name"			=> 'section_settings',
				"title"			=> __( 'Section Settings', 'themple' ),
				"description"	=> __( 'Settings for the whole section', 'themple' ),
				"type"			=> 'combined',
				"admin_class"	=> 'tpl-pb-section-settings',
				"parts"			=> array(
					array(
						"name"			=> 'title',
						"title"			=> __( 'Section title', 'themple' ),
						"description"	=> __( 'This title will appear at the top of the section', 'themple' ),
						"type"			=> 'text',
						"default"		=> '',
					),
					array(
						"name"			=> 'bgcolor',
						"title"			=> __( 'Background color', 'themple' ),
						"description"	=> __( 'The background color of the section', 'themple' ),
						"type"			=> 'color',
						"default"		=> '',
					),
					array(
						"name"			=> 'bgimage',
						"title"			=> __( 'Background image', 'themple' ),
						"description"	=> __( 'The background image of the section', 'themple' ),
						"type"			=> 'image',
					),
					array(
						"name"			=> 'class',
						"title"			=> __( 'Extra CSS class', 'themple' ),
						"description"	=> __( 'Add extra CSS class to this section. If you add more classes, separate them with spaces.', 'themple' ),
						"type"			=> 'text',
					),
				),
			),
			array(
				"name"			=> 'columnset',
				"title"			=> __( 'Columnset', 'themple' ),
				"description"	=> __( 'The columns layout of the row', 'themple' ),
				"type"			=> 'select',
				"admin_class"	=> 'tpl-columnset',
				"values"		=> array(
					"1/1"				=> __( '1/1 - Full width', 'themple' ),
					"1/2-1/2"			=> '1/2 - 1/2',
					"1/3-1/3-1/3"		=> '1/3 - 1/3 - 1/3',
					"1/3-2/3"			=> '1/3 - 2/3',
					"2/3-1/3"			=> '2/3 - 1/3',
					"1/4-1/4-1/4-1/4"	=> '1/4 - 1/4 - 1/4 - 1/4',
					"1/4-3/4"			=> '1/4 - 3/4',
					"3/4-1/4"			=> '3/4 - 1/4',
					"1/4-1/4-2/4"		=> '1/4 - 1/4 - 2/4',
					"1/4-2/4-1/4"		=> '1/4 - 2/4 - 1/4',
					"2/4-1/4-1/4"		=> '2/4 - 1/4 - 1/4',
				),
				"key"			=> true,
			),
			array(
				"name"			=> "apps",
				"title"			=> __( 'Contents of the columns', 'themple' ),
				"description"	=> __( 'Set up your apps', 'themple' ),
				"type"			=> 'combined',
				"repeat"		=> array(
					"number"		=> 0,
				),
				"admin_class"	=> 'tpl-pb-apps',
				"preview"		=> '[apps/app_type/tpl-preview-1]',
				"parts"			=> $apps_array,
				"preview"		=> '[apps/app_type/tpl-preview-1]',
			),
		);

		$args["repeat_button_title"] = __( 'Add section', 'themple' );

		parent::__construct( $args );

		if ( $tpl_pb_added !== true ) {

			add_filter( 'tpl_admin_js_strings', array( $this, 'admin_js_strings' ) );
			$tpl_pb_added = true;

		}

	}


	// Same as the parent's form_field_before, but with other classes added
	public function form_field_before ( $extra_class = 'tpl-dt-combined tpl-preview-multi' ) {

		$this->set_columns_number();

		parent::form_field_before( $extra_class );

	}


	// Returns the number of columns specified in the columnset
	public function set_columns_number() {

		$full_arr = $this->get_option();
		$cset_arr = explode( '-', $full_arr["columnset"] );
		$this->parts["apps"]->repeat["number"] = count( $cset_arr );

		return $this->parts["apps"]->repeat["number"];

	}


	// Returns the values as an array
	public function get_value ( $args = array() ) {

		global $tpl_pb_apps;

		$path_n = $this->get_level() * 2;
		$path_i = $this->get_level() * 2 + 1;
		$path_s = $this->get_level() * 2 + 2;

		if ( !isset( $args["path"][$path_n] ) ) {
			$args["path"][$path_n] = $this->name;
		}

		if ( $this->repeat === false ) {
			$args["path"][$path_i] = 0;
		}

		$result = array();

		$values = $this->get_option( $args );

		// Full branch
		if ( !isset( $args["path"][$path_i] ) && $values[0] != '' ) {

			// These are the sections of the Page Builder
			foreach ( $values as $i => $value ) {

				$this->path[$path_i] = $i;

				if ( isset( $value["section_settings"][0]["class"] ) && $value["section_settings"][0]["class"] != '' ) {
					$extra_class = ' ' . $value["section_settings"][0]["class"];
				}
				else {
					$extra_class = '';
				}

				$result[$i] = '<section class="tpl-pb-section' . $extra_class . '" style="';

				if ( isset( $value["section_settings"][0]["bgcolor"] ) && $value["section_settings"][0]["bgcolor"] ) {
					$result[$i] .= 'background-color: ' . $value["section_settings"][0]["bgcolor"] . ';';
				}

				if ( isset( $value["section_settings"][0]["bgimage"] ) && $value["section_settings"][0]["bgimage"] ) {
					$img = wp_get_attachment_image_src( $value["section_settings"][0]["bgimage"], 'full' );
					$result[$i] .= 'background-image: url(' . $img[0] . ');';
				}

				$result[$i] .= '">';

				// Display the section title if present
				if ( isset( $value["section_settings"][0]["title"] ) && $value["section_settings"][0]["title"] ) {
					$result[$i] .= '<h2 class="tpl-pb-row-title tpl-grid-row">' . $value["section_settings"][0]["title"] . '</h2>';
				}

				$result[$i] .= '<div class="tpl-grid-row">';

				// And now we run the columns inside Columnsets
				$columns = explode( '-', $value["columnset"] );

				foreach ( $columns as $j => $column ) {

					$app_type = $value["apps"][$j]["app_type"];
					$ccol = explode( '/', $column );
					$x = 12 * $ccol[0] / $ccol[1];
					$result[$i] .= '<div class="tpl-pb-column tpl-grid-column-' . $x . ' tpl-pb-app-' . $app_type . '">';

					$v = array();

					foreach ( $tpl_pb_apps as $app ) {

						if ( $app["name"] == $app_type ) {
							$app_class = $app["class"];

							$uset_cond = array();

							foreach ( $this->parts as $part ) {

								$sub_args = array( 'path' => array_replace( $this->path, array( $path_n => $this->name, $path_i => $i, $path_s => $part->name, $path_s+1 => $j ) ) );
								$v = $part->get_value( $sub_args );

								if ( $part->name == 'apps' ) {

									foreach ( $part->parts as $sub_part ) {

										if ( isset( $sub_part->condition ) ) {

											$uset = true;

											foreach ( $sub_part->condition as $cond ) {
												if ( $cond["name"] == '_THIS_/app_type' && $cond["value"] == $app_type ) {
													$uset = false;
												}
											}

											if ( $uset == true ) {
												$uset_cond[] = $sub_part->name;
											}

										}

									}

									foreach ( $uset_cond as $uc ) {
										unset( $v[$uc] );
										unset( $v["app_type"] );
									}

								}

							}
							break;
						}

					}

					$app_obj = new $app_class();

					if ( method_exists( $app_obj, 'frontend_value' ) ) {
						$result[$i] .= $app_obj->frontend_value( $v );
					}

					$result[$i] .= '</div>';

				}

				$result[$i] .= '</div>';
				$result[$i] .= '</section>';

			}

		}

		return $result;

	}


	// Prints the Page Builder output - preferred function to be used in templates
	public function value ( $args = array() ) {

		$sections = $this->get_value();

		echo '<div class="tpl-pb-wrapper">';

		foreach ( $sections as $section ) {
			echo $section;
		}

		echo '</div>';

	}


	// Strings to be added to the admin JS files
	public function admin_js_strings( $strings ) {

		$strings = array_merge( $strings, array(
			'pb_fewer_instances'			=> __( 'The columnset you have just selected contains fewer columns than the previous setting. It means that the last ##N## column(s) will be removed with all their contents. Do you want to proceed?', 'themple' ),
			'tpl-dt-page_builder_preview-template'	=> '[columnset/tpl-preview-3][apps/app_type/tpl-preview-1]',
		) );

		return $strings;

	}


}



// load the basic Page Builder apps
$files = glob ( get_template_directory() . '/framework/data-types/pb-apps/*.php' );
foreach ( $files as $file ) {
	tpl_loader ( $file );
}



// Registered Page Builder apps
$tpl_pb_apps = array();


// Register a Page builder App
function tpl_register_pb_app ( $narr ) {

	global $tpl_pb_apps;

	$pos = $narr["pos"];
	$inserted = false;

	while ( $inserted == false ) {
		if ( !isset( $tpl_pb_apps[$pos] ) ) {
			$tpl_pb_apps[$pos] = $narr;
			$inserted = true;
		}
		$pos++;
	}

	ksort( $tpl_pb_apps );

}
