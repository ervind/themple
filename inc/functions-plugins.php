<?php

// Defining bundled plugins here


// Sets up the bundled plugins for the TGM Plugin Activation library. This function is used by the Themple Framework. Add arrays of bundled plugins as you need them
function tpl_set_plugins() {

	$plugins = array(
		array(
			'name'               => 'Themple Helper',
			'slug'               => 'themple-helper',
			'source'             => 'themple-helper.zip',
			'required'           => true,
			'version'            => '1.1',
			'force_activation'   => false,
			'force_deactivation' => false,
		),
	);

	return $plugins;

}
