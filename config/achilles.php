<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 *	Autoload achilles library
 *
 *	@author    RJ Zaworski <rj@rjzaworski.com>
 */

// Define the class for achilles elements 
$config[ 'achilles_class' ] = 'achilles-able';

// Define the path to achilles scripts, relative to documentroot
$config[ 'achilles_path' ] = '/z/sparks/achilles/0.0.1/js';

// Define scripts to be loaded for achilles to run
$config[ 'achilles_scripts' ] = array( 
	'achilles.js', 
	'achilles.lib.js'
);

?>
