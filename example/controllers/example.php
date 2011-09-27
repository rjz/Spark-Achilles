<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 *	Achilles: a class for structuring AJAX processing and responses
 *
 *	@author    RJ Zaworski <rj@rjzaworski.com>
 */

 class Example extends CI_Controller {
 	
 	public function index() {

		$this->load->helper( 'url' );
		redirect('example/link');
	}

	/**
	 *  Example: respond to an AJAX page request with achilles
	 */
	public function link() {

 		$this->load->helper( 'url' );
		$this->load->spark( 'achilles/0.0.1' );

		// check achilles: if this requested originated from an
		// achilles request, we'll send an actionable response
		if( $this->achilles->use_achilles() ) {

			// get access to the message() function
			$this->load->library( 'achilles_lib' );

			// replace the body with a little note
			$this->achilles
				->message( 'body','I\'m floating in a most peculiar way...' )
				->flush();
		}

		// send the default view
		$this->load->view( 'achilles_link' );
 	}

	/**
	 *  Example: process a form using achilles
	 */
	public function form() {

		$this->load->library( 'form_validation' );
		$this->load->spark( 'achilles/0.0.1' );

		$this->form_validation->set_error_delimiters( '','' );
		$this->form_validation->set_rules( 'name', 'Name', 'required' );

		if( isset( $_POST['name'] ) ) {

			if( $this->form_validation->run() ) {

				$message = '<p>Hi, my name is <u>' . $_POST['name'] . '</u>!</p>';

				// if achilles is in use, we'll respond in kind
				if( $this->achilles->use_achilles() ) {

					// select the #nametag element on the page
					// and fade in a notice
					$this->achilles
						->select( '#nametag' )
						->replaceWith( $message )
						->flush();
				}
				
				// No achilles, no problem. We'll respond in text!
				echo $message;
			}

			// well, validatino failed. if achilles is in use,
			// we'll show the errors dynamically
			if( $this->achilles->use_achilles() ) {

				$this->achilles
					->showErrors( '#nametag' )
					->flush();
			}
		}

		$this->load->view( 'achilles_form' );
	}
}