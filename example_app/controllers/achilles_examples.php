<?php if (! defined('BASEPATH')) exit('No direct script access');
/**
 *	Achilles: a class for structuring AJAX processing and responses
 *
 *	@author    RJ Zaworski <rj@rjzaworski.com>
 */

class Achilles_examples extends CI_Controller {

	public function __construct() {

		parent::__construct();
	
		$this->load->spark( 'achilles/0.1.1' );
		$this->achilles->add_callback( 'fadeStuffIn', array( $this, 'fade_stuff_in' ) );
		$this->load->helper( 'achilles');
		$this->load->helper( 'url' );

	}

	/**
	 *	Return the device state as noted by achilles
	 *
	 *	Generate a link with:
	 *
	 *		<?php echo achilles_anchor( 'achilles_examples/info', 'Click me!', ?>
	 *
	 *	If the request didn't originate with achilles, no information 
	 *	is available. We'll want to say as much...
	 *
	 *	Achilles selects `.results`
	 */
	public function info() {

		$html = '<h3>Reported Device State</h3><ul>';
		$keys = array( 'availHeight','availWidth','height','orientation','width' );
		
		foreach( $keys as $key ) {
			$value = $this->achilles->param($key);
			$html .= '<li>' . $key . ' = ' . $value . '</li>';
		}
		
		$html .= '</ul>';

	
		// check achilles: if this requested originated from an
		// achilles request, we'll send an actionable response
		if( $this->achilles->use_achilles() ) {
			
			$this->achilles
				->select('.results')
				->hide()
				->html($html)
				->fadeIn('fast')
				->flush();
		}

		echo 'Can\'t get the device state from Achilles!';
	}

	/**
	 *	Demonstrate content transitions via the sleight plugin.
	 *
	 *
	 *
	 *	If achilles is unavailable, the content will be echoed instead
	 *
	 *	Achilles selects `#wisdom`
	 *
	 */
	public function transitions( $index = 0, $direction = 'left' ) {

		$sayings = array(
			'And I\'m floating in a most peculiar way', 
			'And the stars look very different today', 
			'Here am I sitting in a tin can far above the world',
			'Ground Control to Major Tom'
		);
		
		$response = '&ldquo;' . $sayings[ $index ] . '&rdquo; ';

		// check achilles: if this requested originated from an
		// achilles request, we'll send an actionable response
		if( $this->achilles->use_achilles() ) {

			$opts = array(

				// set travel direction (right|left|up|down)
				'direction' => $direction
			);

			// replace the body with a little note
			$this->achilles
				->select('#wisdom')
				->sleight($response, $opts )
				->flush();
		}

		echo $response;
	}

	/**
	 *  Demonstrate a (very) basic .achilles-able link
	 *
	 *	Generate a link with:
	 *
	 *		<?php echo achilles_anchor( 'achilles_examples/links', 'Click me!', ?>
	 *
	 *	If achilles didn't originate the request, the new content
	 *	will just be echoed instead.
	 *
	 *	Achilles selects `.results`
	 *
	 */
	public function links() {

		$content = 'I\'m floating in a most peculiar way...';
		
		// check achilles: if this requested originated from an
		// achilles request, we'll send an actionable response
		if( $this->achilles->use_achilles() ) {

			// return a little note
			$this->achilles
				->select( '.results' )
				->hide()
				->html( $content )
				->fadeIn('fast')
				->flush();
		}

		echo $content;
 	}
	
	/**
	 *  Process a form using achilles
	 *
	 *	If Achilles is unavailable, the form is processed through the usual channels
	 *
	 *	Implement form as:
	 *
	 *	<?php echo achilles_form_open('achilles_examples/form', array('id'=>'loginform','class'=>'dark')); ?>
	 *
	 *		<input type="hidden" name="submitted" value="yup" />
	 *
	 *		<div class="clearfix">
	 *			<label for="username">Username</label>
	 *				<input type="text" id="username" name="username" />
	 *		</div>
	 *
	 *		<div class="clearfix">
	 *			<label for="password">Password</label>
	 *			<input type="password" id="password" name="password" />
	 *		</div>
	 *
	 *		<div class="actions">
	 *			<input type="submit" class="button" name="submit" value="Login" />
	 *		</div>
	 *
	 *	</form>
	 *
	 *
	 *	Achilles selects #loginform
	 */
	public function form() {

		$this->load->library( 'achilles_lib' );
		$this->load->library( 'form_validation' );

		$this->form_validation->set_error_delimiters( '','' );
		$this->form_validation->set_rules( 'username', 'Username', 'required' );
		$this->form_validation->set_rules( 'password', 'Password', 'required' );

		$data = array();

		// make sure form was POSTed
		if( isset( $_POST[ 'submitted' ] ) ) {

			// check form validation
			if( $this->form_validation->run() ) {

				// check username
				if( $_POST[ 'username' ] == 'demo' && $_POST[ 'password' ] == 'achilles' ) {

					$message = '<span class="success">Hi, <u>' . $_POST['username'] . '</u>!</span>';

					// if achilles is in use, we'll respond in kind
					if( $this->achilles->use_achilles() ) {

						// select the #nametag element on the page
						// and fade in a notice
						$this->achilles
							->fadeStuffIn( '#loginform', $message )
							->flush();
					}

					// No achilles, no problem. We'll respond in text!
					$data['success'] = true;
					$data['message'] = $message;

				} else {
					// handle invalid login
					
					$message = '<span class="error">Sorry, that didn\'t work out.</span>';
					
					if( $this->achilles->use_achilles() ) {

						$this->achilles
							->fadeStuffIn( '#loginform .message', $message )
							->flush();
					}

					$data['message'] = $message;
				}
			} else { 
			
				// well, validation failed. if achilles is in use,
				// we'll show the errors dynamically
				if( $this->achilles->use_achilles() ) {
				
					$this->achilles
						->showErrors( '#loginform' )
						->flush();
				}
			}
		}

		$this->load->view( 'achilles_examples/form', $data );
	}
	
	/**
	 *	Example achilles routine that fades content in
	 */
	public function fade_stuff_in( $selector, $message ) {

		return $this->achilles
			->select( $selector )
			->hide()
			->html( $message )
			->fadeIn( 'fast' );
	}
}
