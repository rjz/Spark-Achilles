<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 *	Achilles: a class for structuring AJAX processing and responses
 *
 *	@author     RJ Zaworski <rj@rjzaworski.com>
 */
class Achilles {

	protected 
		$callbacks = array(),
		$ci,
		$queue = array();

	/**
	 *	@constructor
	 */
	public function __construct() {

		$this->ci = &get_instance();
	}

	/**
	 *	Determine if Achilles should be involved
	 *	@return	boolean
	 */
	public function use_achilles() {
		return ( isset( $_POST['achilles'] ) && $_POST['achilles'] );
	}

	/**
	 *	Demonstration message
	 */
	public function message( $parent, $message, $class = '' ) {

		$this->select( $parent . ' .achilles-message')
			->hide()
			->html( $message )
			->addClass('notice ' . $class )
			->fadeIn();

		return $this;
	}

	/**
	 *	Extend Achilles library to support form validation
	 *
	 *	@param	string	a CSS selector pointed at the target form
	 *	@return	array	the achilles queue
	 */
	public function showErrors( $selector ) {

		$this->ci->load->library( 'form_validation' );

		$errors = array();
	
		foreach( $_POST as $key => $value ) {
			if( $f = form_error( $key ) ) {
				$errors[ $key ] = $f;
			}
		}

		$this->queue[] = array(
			'action' => 'showErrors',
			'arguments' => array( $selector, $errors )
		);

		return $this;
	}

	/**
	 *	Allow registration of custom callback functions
	 *
	 *	Callbacks might be registered using:
	 *
	 *	<code>$this->achilles->add_callback( 'addMarker', array( $this, 'achilles_do_stuff' ) );</code>
	 *
	 *	@param	string    the callback name
	 *	@param	callback  a valid PHP callback to execute
	 */
	public function add_callback( $key, $callback = false ) {

		if( $callback ) {

			$this->callbacks[ $key ] = $callback;
		}
		else {

			$this->actions[] = $key;
		}
	}

	/**
	 *	Handle requests for methods that haven't been explicitly defined
	 *	@param	string	requested function
	 *	@param	array	parameters for the requested function
	 */
	public function __call( $function, $params ) {

		// check callback array for hooked functions
		if( array_key_exists( $function, $this->callbacks ) ) {
		
			$this->queue[] = call_user_func_array( $this->callbacks[$function], $params );
		}
		else {

			$data = array(
				'action' => $function,
				'arguments' => $params
			);

			$this->queue[] = $data;
		}
		
		return $this;
	}

	/**
	 *	Send response to AJAX request
	 */
	public function flush() {

		echo json_encode( $this->queue );
		exit;
	}
}

?>