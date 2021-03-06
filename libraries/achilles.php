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
	 *
	 *	@return	boolean
	 */
	public function use_achilles() {
	
		if ( ( isset( $_POST['achilles'] ) && $_POST['achilles'] ) ) {

//			$this->_restore_client_info();
			return true;
		}

		return false;
	}

	/**
	 *	Return a value from the POST array
	 *
	 *	@param	string	the key to retrieve
	 *	@return	mixed 	the value of the requested item or NULL if item does not exist
	 */
	public function param( $key ) {

		if( isset( $_POST['achilles'] ) && isset( $_POST[ $key ] ) ) {
			return $_POST[ $key ];
		}
		
		return NULL;
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
		} else {
			$this->actions[] = $key;
		}
	}

	/**
	 *	Handle requests for methods that haven't been explicitly defined
	 *
	 *	@param	string	requested function
	 *	@param	array	parameters for the requested function
	 */
	public function __call( $function, $params ) {

		// check callback array for hooked functions
		if( array_key_exists( $function, $this->callbacks ) ) {
		
			$this->queue[] = call_user_func_array( $this->callbacks[$function], $params );
		} else {

			$data = array(
				'run' => $function,
				'arg' => $params
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