achilles
========

It's like AJAX, but...it's different. Achilles encourages accessibility and eliminates duplication by empowering the server to describe client-side behaviors.

Hello, world
------------

The simplest use of achilles uses a [pjax](https://github.com/defunkt/jquery-pjax/)-like behavior to speed page loading.

	<div id="content">
		<a href="path/to/content.html" class="achilles-able" data-target="#content">Swap content</a>
	</div>

	<script src="jquery.js"></script>
	<script src="achilles.js"></script>

That's really all it takes.

Overview
--------

Achilles implements a basic syntax that describes actions in terms of a JSON-encoded sequence of commands:

	[{
		"run":"alert",
		"arg":["hello, world!"]
	}]

When a commands is dispatched by the server, a callback on the client catches it and uses it to execute a pre-determined action. This empowers the server to describe actions to the client without enlisting [`eval()`](https://developer.mozilla.org/en/JavaScript/Reference/Global_Objects/eval#Don%27t_use_eval!).

To help support this behavior, HTTP requests initiated by the client have a twist: unless otherwise configured, achilles requests will include details of the client browser's state and capabilities:

	GET /path/to/content?achilles=true&availHeight=728availWidth=1366&height=768&orientation=-1&width=1366

This information allows the server to tailor its response to the current state of the client-side device.

This approach provides a natural fallback if achilles is unavailable. Because the URL of any resource is not changed whether achilles is in use or not, servers can be easily configured to respond with static HTML to any request lacking the `achilles=true` parameter.

### Designing server-side controllers

Under achilles' server-first approach, all critical application components&mdash;forms, validation routines, and so forth&mdash; reside exclusively on the server. Including achilles via the `achilles.js` script enables achilles to enhance these static components with a degree of dynamic interactivity:

	<?php echo achilles_anchor('controller/achilles_function', 'Say hello!'); ?>

Call achilles in your controller functions to dynamically respond to AJAX requests. When a controller is called by a link with the `.achilles-able` attribute set you might, for instance, have achilles respond by telling the client to log a message:

	public function achilles_function() {

		$message = 'hello, world!';

		if( $this->achilles->use_achilles() ) {
			$this->achilles
				->log( $message )
				->flush();
		} else {
			echo $message;
		}
	}

On the client side, a pre-defined `message` function will log the returned message:

	achilles.handlers.log = function(message) {
		try {
			console.log(message);
		} catch(e){}
	};

If for some reason achilles is unavailable, the controller will simply display its message as static HTML.
	
### That's awesome because:

* It's efficient: write validation routines and script behaviors once, avoiding duplication of effort between the client and server side
* It's clean: code is neatly wrapped up into the achilles namespace rather than being injected into the page
* It's accessible: achilles' server-first approach encourages fallbacks, redundancy and progressive enhancement
* **It makes sense**: using achilles, a controller queried via AJAX doesn't need bootstraps to make client-side magic happen

How it works
------------

1. A link or form with the `.achilles-able` class is triggered by a `click` or `submit` event (respectively)
2. The request is routed to a server-side handler that processes the request 
3. The server submits an achilles-formatted response
4. The client-side script processor (`achilles.js`) parses the JSON from the server and executes the requested actions

Dependencies
------------

achilles is dependent on jQuery v1.5+

How-to
------

achilles is available for Codeigniter via [Sparks](http://getsparks.org/install).

Once you've got the spark set up, you can load it using:

	$this->load->spark('achilles/[version #]');

## Getting started

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

2. Set up `views/achilles_view.php` to include the achilles scripts and a link referring back to the `achilles` controller function.

**Note**: since v0.0.2, the `achilles_scripts` helper may be used to print the path to the required scripts. Please edit the path in `config/achilles.php` according to your server configuration!

	<html>
	<body>
		<div class="achilles-message">Ground control to Major Tom?</div>

		<p><?php echo achilles_anchor('welcome/achilles', 'Phone home'); ?></p>

		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
		<?php echo achilles_scripts(); ?>

	</body>
	</html>

3. If everything is working correctly, achilles will dynamically respond with a javascript `alert`. If anything ever goes wrong (e.g., because Javascript is unavailable) the `achilles` controller function will still display its initial (static) message.

## Form Processing

One of achilles' built-in features is the ability to translate results from Codeigniter's `form_validation` class into AJAX-driven messages. Just create a view in `views/achilles_form.php` containing a form with the `achilles-able` class:

	<html>
	<body>

		<?php echo achilles_form_open('welcome/nametag', array('id'=>'nametag')); ?>
		
		<label for="name">Your name, please. <?php if( form_error('name') ) echo 'ERROR'; ?></label>
		<input type="text" id="name" name="name" value="<?php echo set_value('name'); ?>" />
		
		<input type="submit" value="submit" />
		
		</form>

		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
		<?php echo achilles_scripts(); ?>
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

Notice that each time the form is posted, achilles is given the opportunity to interject a response. If validation fails, the `showErrors` function is used to apply the results of validation to the form. If it succeeds, achilles replaces the nametag form with a note that everything turned out alright.

## Custom Javascript handlers

achilles is designed to be extended by the front end team through the use of 'handler' methods added to the `achilles.handlers` namespace. This is done by simply hooking in once the achilles script has been included. Adding an "alert" function, for instance, could be achieved using:

	<script>
	$.extend(achilles.handlers, {
		alert: function(message) {
			alert('server says: ' + message);
		}
	});
	</script>

**Note**: although you *can* include handlers inline in your view files, it sort of defeats the whole purpose. Please keep them in a library!

## Building custom routines on the server

achilles' core functionality can be extended with predefined actions. For example, you might want to modify the default message routine to slide new messages down into an element that matches a pre-defined selector. You can do this by defining a routine in the `achilles_lib` library (`libraries/achilles_lib.php`):
	
		public function my_message( $selector, $message ) {
	
			return $this->ci->achilles
				->select( $selector )
				->slideUp()
				->html( $message )
				->slideDown();
		}
	}

and telling `achilles_lib` to load the new function as a plugin in the `callbacks` array:

	$callbacks = array(
		'myMessage' => 'my_message'
	)

When `achilles_lib` loads, it will now register the custom routine with achilles as `achilles::myMessage`. The new routine can then be called in any controller function just like any other achilles function:

	if( $this->achilles->use_achilles() ) {

		$this->load->library( 'test/achilles_lib' );
		$this->achilles
			->myMessage('body','hello, world!')
			->flush();
	}

The only caveat for custom routines is that they MUST be chainable (i.e., they must return the achilles chain).

**Note**: in keeping with Codeigniter's on-demand methodology, `achilles_lib` is *not* auto-loaded by the `achilles` spark. `achilles_lib` *must* be loaded manually before any custom routines are called.

Contributing
------------

Please contribute! To add library routines or modify the core, please fork [achilles on Github](https://github.com/rjz/Spark-achilles) and submit your changes as a [pull request](http://help.github.com/send-pull-requests/).

## Contributing javascript handlers

To keep style consist, please make sure that contributed handler functions are:

1. added alphabetically to `js/achilles.lib.js`
2. described using [Closure-style](code.google.com/closure/compiler/docs/js-for-compiler.html) annotations

## Contributing Codeigniter routines

To ensure consistent style, please make sure that contributed routines are:

1. added alphabetically to `libraries/achilles_lib`
2. described using [PHPDoc-style](http://manual.phpdoc.org/HTMLSmartyConverter/HandS/phpDocumentor/tutorial_tags.pkg.html) tags.

Author
------

RJ Zaworski <rj@rjzaworski.com>

License
-------

achilles is released under the JSON License. You can read the license [here](http://www.json.org/license.html).