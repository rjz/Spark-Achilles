Achilles
========

It's like AJAX, but...it's different. To encourage recycling and minimize the amount of spaghetti in your code, Achilles provides a server-side interface for describing client-side behaviors. 

### Real quickly,

On the server, Achilles checks to see if it's supposed to respond to an AJAX request. If it is, it might respond by telling the client to log a message:

	if( $this->achilles->use_achilles() ) {
		$this->achilles->log('hello, world!')->flush();
	}

On the client side, a pre-defined `message` function is waiting to log the returned message:

	achilles.handlers.log = function(message) {
		try {
			console.log(message);
		} catch(e){}
	};

### And that's awesome because:

* It's clean: code is neatly wrapped up into the Achilles namespace rather than being injected into the page
* It's accessible: Achilles encourages redundancy and progressive enhancement
* It's efficient: static and dynamic sites can be developed simultaneously, and all code is recyclable
* **It makes sense**: using Achilles, a controller queried via AJAX can respond to the client directly

How it works
------------

1. A link or form with the `.achilles-able` class is triggered by a `click` or `submit` event (respectively)
2. The controller targeted by the `.achilles-able` element processes the request and responds through the achilles interface
3. Achilles converts the chained response into JSON
4. The client-side script processor (`achilles.js`) parses the JSON from the server and executes the requested actions.

Dependencies
------------

Achilles is dependent on jQuery v1.5+

How-to
------

Achilles is available for Codeigniter via [Sparks](http://getsparks.org/install).

Once you've got the spark set up, you can load it using:

	$this->load->spark('achilles/[version #]');

### Getting started

1. Add the following to your `welcome` controller:

	public function achilles() {
	
		$this->load->helper('url');
		$this->load->spark( 'achilles/0.0.1' );

		if( $this->achilles->use_achilles() ) {
			$this->achilles
				->message('body','Major Tom to ground control!')
				->flush();
		}
	
		$this->load->view('achilles_view');
	}

2. Set up `views/achilles_view.php` to include `achilles.js` and a link referring back to the `achilles` controller function. 

**Note**: though `achilles.js` comes bundled within the achilles spark, its ultimate destination is up to you. Please edit the path accordingly!

	<html>
	<body>
		<div class="achilles-message">Ground control to Major Tom?</div>

		<p><a href="<?php echo site_url('welcome/achilles'); ?>" class="achilles-able">Phone home</a></p>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
		<script src="<?php echo base_url('js/achilles.js'); ?>"></script>
	</body>
	</html>

3. If everything is working correctly, achilles will dynamically respond with a javascript `alert`. If anything ever goes wrong (e.g., because Javascript is unavailable) the `achilles` controller function will still display its initial (static) message.

### Form Processing

One of Achilles built-in features is the ability to translate results from Codeigniter's `form_validation` class into AJAX-driven messages. Just create a view in `views/achilles_form.php` containing a form with the `achilles-able` class:

	<html>
	<body>

		<?php echo form_open('welcome/nametag', array('id'=>'nametag', 'class' => 'achilles-able')); ?>
		
		<label for="name">Your name, please. <?php if( form_error('name') ) echo 'ERROR'; ?></label>
		<input type="text" id="name" name="name" value="<?php echo set_value('name'); ?>" />
		
		<input type="submit" value="submit" />
		
		</form>

		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
		<script src="<?php echo base_url('js/achilles.js'); ?>"></script>
	</body>
	</html>

Now, add that `nametag` function to the `welcome` controller:
	
	public function nametag() {
	
		$this->load->library('form_validation');
		$this->load->spark( 'achilles/0.0.1' );
	
		$this->form_validation->set_error_delimiters('','');
		$this->form_validation->set_rules('name','Name','required');

		if( isset( $_POST['name'] ) ) {

			if( $this->form_validation->run() ) {
	
				if( $this->achilles->use_achilles() ) {
					$this->achilles->select('#nametag')
						->replaceWith('<p>Hey, that worked!</p>')
						->flush();
				}
	
				echo 'Thanks for your name!';
			}
	
			if( $this->achilles->use_achilles() ) {
				$this->achilles->showErrors('#nametag')->flush();
			}
		}
		
		$this->load->view('achilles_form.php');
	}

Notice that each time the form is posted, Achilles is given the opportunity to interject a response. If validation fails, the `showErrors` function is used to apply the results of validation to the form. If it succeeds, Achilles replaces the nametag form with a note that everything turned out alright.

### Custom Javascript handlers on the client

Achilles isn't good for much by itself. Really, it's designed to be extended by the front end team through the use of 'handler' methods added to the `achilles.handlers` namespace. This is done by simply hooking in once the achilles script has been included. Adding an "alert" function, for instance, could be achieved using:

	<script>
	$.extend(achilles.handlers, {
		alert: function(message) {
			alert('server says: ' + message);
		}
	});
	</script>

**Note**: although you *can* include handlers inline in your view files, it sort of defeats the whole purpose. Please just include your handlers as a separate library!

### Routine libraries on the server

Achilles supports plugins that extend its core functionality with predefined action routines. For example, you might want to modify the default message routine to slide new messages down into an element that matches a pre-defined selector. Create a new library (`libraries/achilles_lib.php`) and add:

	class Achilles_lib {
	
		protected $ci;
	
		public function __construct() {
	
			$this->ci = &get_instance();
			$this->ci->load->library('achilles');
			
			// register a callback
			$this->ci->achilles->add_callback( 'myMessage', array( $this, 'my_message' ) );
		}
	
		public function my_message( $selector, $message ) {
	
			return $this->ci->achilles
				->select( $selector )
				->slideUp()
				->html( $message )
				->slideDown();
		}
	}

This new library creates a new routine (`my_message`) and registers it with achilles using the `add_callback` method. The only requirement for routine functions is that they MUST be chainable (i.e., they must return the achilles chain).

Author
------

RJ Zaworski <rj@rjzaworski.com>

License
-------

Atomizer is released under the JSON License. You can read the license [here](http://www.json.org/license.html).