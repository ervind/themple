<?php

// You can add here your own Page Builder apps based on the Page Builder App API


// The Excerpt PB App
add_action( 'init', function() {
	tpl_register_pb_app( array(
		'name'		=> 'the_excerpt',
		'title'		=> __( 'The excerpt (Starter)', 'themple-starter' ),
		'class'		=> 'TPL_PB_The_Excerpt',
		'pos'		=> 60,
	) );
});

class TPL_PB_The_Excerpt {


	// The extra admin fields added by this app
	public function get_admin_fields() {

		return array(
			array(
				"name"			=> 'the_excerpt_readmore',
				"title"			=> __( 'Show read more link?', 'themple-starter' ),
				"description"	=> __( 'You can turn the read more link at the end of the excerpt on/off here.', 'themple-starter' ),
				"type"			=> 'select',
				"values"		=> array(
					"no"			=> __( 'No', 'themple-starter' ),
					"yes"			=> __( 'Yes', 'themple-starter' ),
				),
			),
		);

	}


	// Front end output of the app
	public function frontend_value( $values = array() ) {

		global $post;

		$result = '';

		ob_start();
		tpl_show_post_in_loop( $post, tpl_get_value( 'excerpt_format' ) );
		$result = ob_get_contents();
		ob_end_clean();

		if ( $values["the_excerpt_readmore"] != 'yes' ) {
			$link = explode( '<a class="tpl-readmore', $result );
			$result = $link[0];
		}

		return $result;

	}


}
