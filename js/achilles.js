/**
 *	Achilles
 *
 *	Closure externs at http://closure-compiler.googlecode.com/svn/trunk/contrib/externs/jquery-1.5.js
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
 *	extend AJAX namespace with some default magic
 */
var achilles = {
	/**
	 *	The currently selected jQuery collection
	 *	@type	{jQuery}
	 */
	'selected' : false,

	/**
	 *	Loader - display loading graphic to element
	 *	@param	{jQuery|Element|String}	el	The container element to load within
	 *	@return	{Object}	The result of the loader function
	 */
	Loader: function( el ) {

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
	},
	/**
	 *	Post method
	 *
	 *	Submit a request and handle the server's response.
	 *
	 *	@param	{jQuery|Element|String}	el the element (usually a link or form) that is calling post()
	 *	@param	{String}	url     The URL to post data to
	 *	@param	{Object}	data	An object containing post data. Forms may generate this using
	 *					            the $.serializeObject plugin.
	 */
	post: function( el, url, data ) {

		try {
			data = $.extend( data, {'achilles':1} );
		} catch(e) {
			data = { 'achilles' : 1 };	
		}

		if( $(el).hasClass('achilles-load') ) {
			var loader = new achilles.Loader(el);
			loader.set();
		}

		$.post( url, data, function(results){

			// clear selector for action chaining
			achilles.selected = false;

			// cut loading graphic, if it's being used
			if(loader) loader.cancel();

			// process result from server into a JS object
			if(o = $.parseJSON(results)) {

				var r, 
					i = 0;

				// execute each method chained up in Codeigniter
				while(r = o[i++] ) {

					if( typeof( r.action ) == 'string' ) {

						var what = null,
							func = null;

						if( achilles.selected ) what = $(achilles.selected);

						// we'll need to find the function that Codeigniter has requested
						// (1) try user functions
						func = achilles.handlers[r.action];

						// (2) try jQuery
						if( typeof( func ) != 'function' && what ) {
							func = $(what)[r.action];
						}

						// (3) unknown function -- toss an error and get the hell out
						if( typeof( func ) != 'function' ) {
							throw( 'undefined handler: ' + r.action );
							return;	
						}

						// call the function with whatever arguments Codeigniter supplied
						func.apply( what, r.arguments );
					}
				}
			}				
		});
	},
	/**
	 *	A container for all handler functions
	 *	@type	{Object<String, Function>}
	 */
	'handlers' : {}
};

// extend AJAX namespace with a few default handler functions
$.extend(achilles.handlers, {
	/**
	 *	Set the currently selected element
	 */
	'select' : function( selector ) {
		achilles.selected = $(selector);
	},
	/**
	 *	Unselect the currently selected element
	 */
	'unselect' : function() {
		achilles.selected = null;
	},
	'log' : function(message) {
		try {
			console.log(message);
		} catch(e){}
	},
	/**
	 *	Show form errors
	 */
	'showErrors' : function(selector, errors) {
	
		var input,
			fields = errors;
	
		// clear errors
		$('input,textarea,select', $(selector))
			.removeClass('error')
			.labelFor()
			.removeClass('error')
			.find('.error')
			.remove();

		// set errors
		for(x in fields) {
			input = $('[name="'+x+'"]').addClass('error');
			label = input.labelFor().addClass('error');
			i = label.find('.instruction');

			if(i.length) {
				i.addClass('error').html(fields[x]);
			} else {
				label.append(' <span class="instruction error">'+fields[x]+'</span>' );
			}
		}
	}
});

window['achilles'] = achilles;

$(document).ready(function(){

	// set up forms
	$('form.achilles-able').live('submit', function(e){
		e.preventDefault();
		achilles.post( this, this.action, $(this).serializeObject() );
	});

	// set up links
	$('a.achilles-able').live('click',function(e){
		e.preventDefault();
		achilles.post( this, this.href );
	});
});

})(jQuery);