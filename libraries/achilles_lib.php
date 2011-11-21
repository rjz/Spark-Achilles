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
			'myMessage' => 'my_message',
			'showErrors' => 'showErrors'
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
	 *	Extend Achilles library to support form validation
	 *
	 *	@param	string	a CSS selector pointed at the target form
	 *	@return	array	the goods!
	 */
	public function showErrors( $selector ) {

		$this->ci->load->library( 'form_validation' );

		$errors = array();
	
		foreach( $_POST as $key => $value ) {
			if( $f = form_error( $key ) ) {
				$errors[ $key ] = $f;
			}
		}
		
		return array(
			'run' => 'showErrors',
			'arg' => array( $selector, $errors )
		);
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