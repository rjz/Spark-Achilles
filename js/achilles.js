/**
 *	Achilles core functions
 *	https://github.com/rjz/Achilles-js
 *
 *	@author 	RJ Zaworski <rj@rjzaworski.com>
 *	@license	JSON <http://www.json.org/license.html>
 */
(function($){

// don't overwrite existing version
if(typeof achilles != 'undefined') {
	return;
}

// add a few helper functions to jQuery
$.extend($.fn,  {
	/**
	 *	convert a form's fields into an object
	 *	@this	{jQuery}
	 *	@return	{Object}	a JSONable representation of this form's fields
	 */
	serializeObject: function(){var c={};var b=this.serializeArray();$.each(b,function(){if(c[this.name]){if(!c[this.name].push){c[this.name]=[c[this.name]]}c[this.name].push(this.value||"")}else{c[this.name]=this.value||""}});return c},
	/**
	 *	extend jQuery to find the label for the specified form element
	 *	@this	{jQuery|Element}
	 *	@return	{jQuery}	The label corresponding to the requested input
	 */
	labelFor: function(){var a='';$(this).each(function(){a+='label[for="'+this.id+'"],';});return $(a);}
});

/**
 *	extend AJAX namespace with a few helpers
 *	@param	{Object}	params	initialization options
 */
var achilles = function(params) {

	var /**
		 *	Options for this version of achilles
		 *	@type {Object}
		 *	@access	private
		 */
		opts = $.extend({
			history    : true,
			postOnly   : false,
			properties : ['availHeight','availWidth','height','orientation','width']
		}, params),

		/**
		 *	Has achilles been used yet? It will wake up when it's called.
		 *	@access	private
		 */
		_awake = false,

		/**
		 *	If an element's loading, _loader will indicate it
		 *	@type	{achilles.Loader}
		 */
		_loader = null,

		_pushedState = false,
		/**
		 *	Return browser information
		 *	@access	private
		 */
		_getDeviceInfo = function() {

			var properties = opts.properties,
				result = {achilles:true},
				screen = window.screen;

			$.each(properties, function (i, prop) {
				result[prop] = -1;
				if (prop in screen) {
					result[prop] = screen[prop];
				}
			});

			return result;
		},

		/**
		 *	Do something cool with whatever the server said
		 *
		 *	@access	private
		 *	@param	{Array}	results	a list of action items
		 */
		_parse = function (results) {

			var action,
				actions,
				html,
				subject,
				verb;

			try {
				actions = $.parseJSON(results);
			} catch(e) {

				// so parsing as JSON didn't work out. We'll try to figure out what went wrong
				if (!results) {
					_toss('Got nothin\', boss.');
				} else if (!achilles.selected) {
					_toss('No selector set');
				} else if (!(html = $(achilles.selected, results))) {
					_toss('Couldn\'t find selector "' + achilles.selected + '" in results!');
				} else {
					// [fixme]: refine the HTML fallback
					// primitive, but it works:
					$(achilles.selected).replaceWith(html);
				}
				return;
			}

			if (actions) {

				// execute each method chained up in Codeigniter
				while (action = actions.shift()) {

					if (typeof action.run  == 'string') {

						if (achilles.selected) {
							subject = $(achilles.selected);
						}
						// (1) try user functions
						verb = achilles.handlers[action.run];

						// (2) try jQuery
						if( (typeof verb  != 'function') && subject ) {
							verb = $(subject)[action.run];
						}

						// (3) unknown function -- toss an error and get the hell out
						if( typeof( verb ) != 'function' ) {
							_toss( 'undefined handler: ' + action.run );
							return;
						}

						// call the function with whatever arguments the server supplied
						verb.apply( subject, action.arg );
					}
				}
			}
		},

		/**
		 *	Handle the results of an XMLHTTPRequest
		 *
		 *	@param	{String}	url    	the address stuff was sent to
		 *	@param	{Object}	results	what happened
		 *	@access	private
		 */
		_success = function(url, results, push){

			var title = ''; //document.title? need to fix.

			_parse(results);

			if (opts.history) {

				// set initial state to make the back button play nicely
				if ( !_awake ) {
					_history.replaceState({url:location.href}, '')
					_awake = true
				}

				if (push) {
					_history.pushState({url:url}, '', url);
				}

				// google analytics
				if ( window._gaq ) {
					_gaq.push(['_trackPageview']);
				}
			}
		},

		/**
		 *	Throw an exception
		 *	@param	{String}	message	what to say
		 */
		_toss = function (message) {

			throw('!Achilles: ' + message);
		},

		/**
		 *	(fairly) safe history interface. Supports HTML5 history and History.js or falls back softly.
		 *	@access	private
		 */
		_history = (function(){

			var $stub = function(){},
				hst = window.history || window.History;
			
			if (hst) { return hst; }

			return {
				go: $stub,
				pushState: $stub,
				replaceState: $stub
			}
		})(),

		/**
		 *	Handle the nav buttons via history API's window.onpopstate event
		 *
		 *	@param	{Object}	event	the event triggered by popstate()
		 *	@access	private
		 */
		_stateHandler = function (event) {

			if (!_pushedState && event.state) {
				if (event.state.url) {
					achilles.get( '', event.state.url, {}, {push:false} );
				}
			}
		},

		/**
		 *	Submit an asynchronous request (uses jQuery.ajax)
		 *
		 *	@param	{String}  	method  	HTTP request method (GET|POST)
		 *	@param	{String}  	url     	the url to submit request to
		 *	@param	{Object}  	data    	the data to submit
		 *	@param	{Function}	callback	what to do on success
		 *	@access	private
		 */
		_xhr = function (params) {

			var settings = {
					success: function(results) { 
						_success(params.url, results, params.push); 
					},
					type: params.method.toUpperCase()
				},
				selector;

			if (['POST','GET'].indexOf(settings.type) < 0) {
				_toss('unknown HTTP Method: ' + settings.type);
			}

			try {
				settings.data = $.extend(params.data, _getDeviceInfo());
			} catch(e) {
				settings.data = _getDeviceInfo();
			}
			
			if (params.target) {

				selector = $(params.target).attr('data-target');

				if (selector && selector.length) {
					achilles.selected = selector;
				} else {
					achilles.selected = null;
				}
			}

			$.ajax(params.url, settings);
		};

	/**
	 *	The currently selected jQuery collection
	 *	@type	{jQuery}
	 */
	this.selected = false;

	/**
	 *	Loader - mark an element as loading
	 *	@param	{jQuery|Element|String}	el	The container element to load within
	 *	@return	{Object}	The result of the loader function
	 */
	this.Loader = function (el) {

		var $el = $(el),
			wrapper,
			loading;

		/**
		 *	Add loading state to the element
		 */
		this.set = function(){

			wrapper = $($el.wrapInner('<span class="content-wrapper" />').get(0));
			wrapper.fadeTo(100,0.1);

			var pos = $el.position();

			loading = $('<span class="achilles-loading" />').css({
				'position':'absolute',
				'left':pos.left,
				'top':pos.top,
				'width':$el.width(),
				'height':$el.height()
			});

			loading.appendTo($el);
		}

		/**
		 *	Clear loading state from the element
		 */
		this.cancel = function() {
			$(wrapper).fadeTo(100,1,function(){
				$el.html(wrapper.get(0).firstChild.innerHTML);
			});
		}
	};

	/**
	 *	Get method
	 *
	 *	GET a resource and handle the server's response.
	 *
	 *	@param	{jQuery|Element|String}	el the element (usually a link or form) that is calling post()
	 *	@param	{String}	url     The URL to post data to
	 *	@param	{Object}	data	An object containing post data. Forms may generate this using
	 *					            the $.serializeObject plugin.
	 */
	this.get = function( el, url, data, params ) {

		var method = opts.postOnly ? 'POST' : 'GET';
	
		_xhr($.extend({
			method: method,
			push: true,
			target: el,
			url: url,
			data: data
		}, params));
	};
	
	/**
	 *	Post helper
	 *
	 *	POST a request and handle the server's response.
	 *
	 *	@param	{jQuery|Element|String}	el the element (usually a link or form) that is calling post()
	 *	@param	{String}	url     The URL to post data to
	 *	@param	{Object}	data	An object containing post data. Forms may generate this using
	 *					            the $.serializeObject plugin.
	 */
	this.post = function( el, url, data ) {

		if( $(el).hasClass('achilles-load') ) {
			_loader = new achilles.Loader(el);
			_loader.set();
		}

		_xhr({
			method: 'POST',
			push: true,
			target: el,
			url: url,
			data: data
		});
	};

	/**
	 *	A container for all handler functions
	 *	@namespace
	 */
	this.handlers = {

		/**
		 *	Set the currently selected element
		 *	@param	{String}	selector	the selector to find
		 */
		select: function (selector) {
			achilles.selected = $(selector);
		},
		/**
		 *	Deselect the currently selected element
		 */
		deselect: function() {
			achilles.selected = null;
		},
		/**
		 *	Log a message to the console
		 */
		log: function (message) {
			try {
				console.log(message);
			} catch(e){}
		}
	};

	/**
	 *	Get all set up
	 */
	this.init = function() {

		$(document).ready(function(){
		
			// set up forms
			$('form.achilles-able').live('submit', function(e){
				e.preventDefault();
				achilles.post( this, this.action, $(this).serializeObject() );
			});

			// set up links
			$('a.achilles-able').live('click',function(e){

				if (e.which > 1 || e.metaKey) {
				      return true;
				}

				e.preventDefault();
				achilles.get(this, this.href);
			});

			if (opts.history) {
				// set up history
				window.onpopstate = function(event) {
					_stateHandler(event);
				}
			}
		});

		return this;
	}
};

// instantiate achilles
achilles = new achilles({
	history: true,
	postOnly: true
});
window['achilles'] = achilles.init();

})(jQuery);
