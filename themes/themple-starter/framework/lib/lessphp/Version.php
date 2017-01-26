<?php

/**
 * Release numbers
 *
 * @package Less
 * @subpackage version
 */
class Less_Version{

	const version = '1.7.0.9';			// The current build number of less.php
	const less_version = '1.7';			// The less.js version that this build should be compatible with
    const cache_version = '170';		// The parser cache version

/* NOTE: the compiler was modified by A-idea Studio in order to pass the WordPress Theme Check requirements. Changes made to the original version:
- Use WP Filesystem functions instead pure PHP
- remove base64_encode functions
- remove ini_set directives
*/

}
