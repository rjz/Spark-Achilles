<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 *	A stub library for Achilles plugins
 *
 *	@author    RJ Zaworski <rj@rjzaworski.com>
 */
class Achilles_lib {

	protected
		/**
		 *	Array of user-defined callback functions
		 *	@type array
		 */
		$callbacks = array(
			'myMessage' => 'my_message'
		),
		/**
		 *	Reference to Codeigniter instance
		 *	@type object
		 */
		$ci;

	/** 
	 *	Register callbacks
	 *	@constructor
	 */
	public function __construct() {

		$this->ci = &get_instance();

		foreach( $this->callbacks as $key => $handle ) {

			// register callbacks
			$this->ci->achilles->add_callback( $key, array( $this, $handle ) );
		}
	}

	/**
	 *	Animate display of a message into the specified selector
	 *	
	 *	@link	http://api.jquery.com/category/selectors/
	 *
	 *	@param	string	a jQuery-compatible selector
	 *	@param	string	the message to display
	 */
	public function my_message( $selector, $message ) {

		return $this->ci->achilles
			->select( $selector )
			->slideUp()
			->html( $message )
			->slideDown();
	}
}