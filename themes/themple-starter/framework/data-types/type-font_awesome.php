<?php

// The file must have the type-[data-type].php filename format


class TPL_Font_Awesome extends TPL_Select {


	protected	$less				= false;
	public		$key				= true;				// Should return the key (true) or the label (false)?


	// Sets up the object attributes while registering options
	public function __construct( $args ) {

		require_once ABSPATH . "wp-admin/includes/file.php";
		WP_Filesystem();
        global $wp_filesystem;

		$fa_json_file = dirname ( dirname ( __FILE__ ) ) . '/lib/font-awesome/icons.json';
		$fa_json = json_decode( $wp_filesystem->get_contents( $fa_json_file ), true );

		foreach ( $fa_json["icons"] as $icon ) {
			$fa_icons[$icon["id"]] = $icon["name"];
		}

		ksort( $fa_icons );

		$args["values"] = $fa_icons;

		if ( !isset( $args["admin_class"] ) ) {
			$args["admin_class"] = '';
		}
		$args["admin_class"] .= ' select';

		parent::__construct( $args );

	}


	// Formats the option into value
	public function format_option ( $id, $args = array() ) {

		$result = '';

		if ( isset( $args["url"] ) && $args["url"] != '' ) {
			$result .= '<a href="' . $args["url"] . '"';

			if ( isset( $args["title"] ) ) {
				$result .= ' title="' . $args["title"] . '"';
			}

			if ( isset( $args["newtab"] ) && $args["newtab"] == 'yes' ) {
				$result .= ' target="_blank"';
			}

			$result .= '>';
		}

		$result .= '<i class="fa fa-' . $id;

		if ( isset( $args["size"] ) && $args["size"] != '' ) {
			$result .= ' fa-' . $args["size"];
		}

		$result .= '"';

		if ( isset( $args["color"] ) ) {
			$result .= ' style="color: ' . $args["color"] . '"';
		}

		$result .= '></i>';

		if ( isset( $args["url"] ) && $args["url"] != '' ) {
			$result .= '</a>';
		}

		return $result;

	}


}
