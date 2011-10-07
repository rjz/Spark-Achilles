<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 *	Helper functions for achilles
 *
 *	@author    RJ Zaworski <rj@rjzaworski.com>
 */

/**
 * Print <script> tags for achilles.js and any additional libraries
 */
function achilles_scripts() {

	$ci = &get_instance();
	$ci->load->helper( 'url' );

	$path = $ci->config->item( 'achilles_path' );
	$scripts = $ci->config->item( 'achilles_scripts' );

	//$path = $ci->config->item( 'base_url' ) . rtrim( $path,'/' );
	$path = rtrim( $path,'/' );

	echo "\n";

	foreach( $scripts as $script ) {

		echo "\t<script src=\"$path/$script\"></script>\n";	
	}

}

/**
 * Create a <form> tag compatible with achilles
 * 
 * @param	string	the route to post the form to
 * @param	array 	(optional) attributes to add to the form tag
 */
function achilles_form_open( $route, $attributes = NULL ) {

	$ci = &get_instance();
	$ci->load->helper( 'form' );

	$className = $ci->config->item( 'achilles_class' );

	// create attributes array if not set
	if( !is_array( $attributes ) ) {
		$attributes = array();
	}

	// create class attribute if it doesn't exist
	if( !isset( $attributes[ 'class' ] ) ) {
		$attributes[ 'class' ] = '';
	}

	// test for achilles-able class
	if( !preg_match( "#\b$className\b#", ' ' . $attributes[ 'class' ] . ' ' ) ) {
		$attributes[ 'class' ] .= ' ' . $className;
	}

	return form_open( $route, $attributes );
}

/**
 * Create an anchor compatible with achilles
 * 
 * @param	string	the route to refer the anchor to
 * @param	text  	the link text
 * @param	array 	(optional) attributes to add to the form tag
 */
function achilles_anchor( $route, $text = '', $attributes = NULL ) {

	$ci = &get_instance();
	$ci->load->helper( 'url' );

	$className = $ci->config->item( 'achilles_class' );

	// set text if not set
	if( $text == '' ) {
		$text = site_url( $route );
	}

	// create attributes array if not set
	if( !is_array( $attributes ) ) {
		$attributes = array();
	}

	// create class attribute if it doesn't exist
	if( !isset( $attributes[ 'class' ] ) ) {
		$attributes[ 'class' ] = '';
	}

	// test for achilles-able class
	if( !preg_match( "#\b$className\b#", ' ' . $attributes[ 'class' ] . ' ' ) ) {
		$attributes[ 'class' ] .= ' ' . $className;
	}

	return anchor( $route, $text, $attributes );
} 

?>
