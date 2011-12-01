<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 *	Set up achilles script parameters
 *
 *	@author    RJ Zaworski <rj@rjzaworski.com>
 */

// Define the path to achilles scripts. Most installations are fine
// to use the default scripts in the sparks directory, but you may
// serve them from anywhere you want. 
//
// If you aren't using the `achilles_scripts` helper to autogenerate 
// <script> tags, you may safely ignore this option.
$config[ 'achilles_path' ] = '/sparks/achilles/0.1.1/js';
 
// Define the class for achilles elements. This can be set to 
// whatever you want, but be SURE to update the `className` option
// used in your achilles.js file.
$config[ 'achilles_class' ] = 'achilles-able';

// Define scripts required for achilles to run. These are the files
// that will be automatically included by the `achilles_scripts`
// helper
$config[ 'achilles_scripts' ] = array( 
	'achilles.js', 
	'achilles.lib.js'
);

?>
