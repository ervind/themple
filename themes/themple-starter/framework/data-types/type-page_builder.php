<?php

// The file must have the type-[data-type].php filename format


class TPL_Page_Builder extends TPL_Combined {


	protected	$less			= false;
	public		$repeat			= true;


	public function __construct( $args ) {

		global $tpl_pb_apps;

		// Initializing with the app type selector
		$apps_array = array(
			array(
				"name"			=> "app_type",
				"title"			=> __( 'Select app for this box', 'themple' ),
				"description"	=> __( 'Select an app type from the dropdown. It will be used in this box.', 'themple' ),
				"type"			=> 'select',
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
					$apps_array[] = $admin_field;
				}
			}

		}


		// Putting together the whole Page Bulder admin fields array
		$args["parts"] = array(
			array(
				"name"			=> 'columnset',
				"title"			=> __( 'Columnset', 'themple' ),
				"description"	=> __( 'The columns layout of the row', 'themple' ),
				"type"			=> 'select',
				"admin_class"	=> 'tpl-columnset',
				"values"		=> array(
					"1/1"				=> __( '1/1 - Full width', 'themple' ),
					"1/2-1/2"			=> __( '1/2 - 1/2', 'themple' ),
					"1/3-1/3-1/3"		=> __( '1/3 - 1/3 - 1/3', 'themple' ),
					"1/3-2/3"			=> __( '1/3 - 2/3', 'themple' ),
					"2/3-1/3"			=> __( '2/3 - 1/3', 'themple' ),
					"1/4-1/4-1/4-1/4"	=> __( '1/4 - 1/4 - 1/4 - 1/4', 'themple' ),
					"1/4-3/4"			=> __( '1/4 - 3/4', 'themple' ),
					"3/4-1/4"			=> __( '3/4 - 1/4', 'themple' ),
					"1/4-1/4-2/4"		=> __( '1/4 - 1/4 - 2/4', 'themple' ),
					"1/4-2/4-1/4"		=> __( '1/4 - 2/4 - 1/4', 'themple' ),
					"2/4-1/4-1/4"		=> __( '2/4 - 1/4 - 1/4', 'themple' ),
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
				"parts"			=> $apps_array,
			),
		);

		$args["repeat_button_title"] = __( 'Add section', 'themple' );

		parent::__construct( $args );

	}


	// Same as the parent's form_field_before, but with other classes added
	public function form_field_before ( $extra_class = 'tpl-dt-page_builder combined' ) {

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
		if ( !isset( $args["path"][$path_i] ) ) {

			// These are the sections of the Page Builder
			foreach ( $values as $i => $value ) {

				// $this->path[$path_i] = $i;

				$result[$i] = '<div class="row tpl-pb-section">';

				// And now we run the columns inside Columnsets
				$columns = explode( '-', $value["columnset"] );

				foreach ( $columns as $j => $column ) {

					$ccol = explode( '/', $column );
					$x = 12 * $ccol[0] / $ccol[1];
					$result[$i] .= '<div class="tpl-pb-column column-' . $x . '">';

					$app_type = $value["apps"][$j]["app_type"];

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


}
