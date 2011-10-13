/**
 *	Achilles custom library functions
 *
 *	@license	JSON <http://www.json.org/license.html>
 */
(function($){
/**
 *	All custom handlers belong here.
 */
var handlers = {
	/**
	 *	Example handler to drive an alert message
	 *	@param	{String}	message	the message to alert
	 */
	alert: function(message) {
		alert('server says: ' + message);
	},	
	/**
	 *	Show form errors
	 *	@param	{String}	selector	the form to target
	 *	@param	{{String:String}}	errors	errors returned by form validation for each field
	 */
	showErrors : function(selector, errors) {
	
		var input,
			fields = errors;

		// clear errors
		$('input,textarea,select', $(selector))
			.removeClass('error')
			.labelFor()
			.find('.error')
			.remove();
console.log('errors' + errors);
		// set errors
		for(x in fields) {
			input = $('[name="'+x+'"]')
				.addClass('error')
				.labelFor()
				.append(' <span class="error">'+fields[x]+'</span>' );
		}
	},
	/**
	 * Cycle content
	 */
	sleight: function (content, opts) {
		
		var content = content,
			opts = $.extend({
				direction: 'left' // (up|down|left|right)
			}, opts);
		
		this.each( function() {

			var $el = $(this),
				reset = {
					height: 'auto',
					overflow: 'visible',
					position: 'static'
				},
				/**
				 *	@param	{jQuery}	old	the element to be replaced
				 *	@param	{jQuery}	nu 	the element replacing {@link old}
				 *	@param	{Object} opts	options
				 *	@private
				 */
				_animate = function (old,nu,opts) {

					var direction = {
							left:  [-1, 0],
							right: [1, 0],
							up:    [0, -1],
							down:  [0, 1]
						}[opts.direction],
						props = {
							height: $el.height(),
							position: 'absolute',
							width : $el.width()
						};

					old.css($.extend(props,{
						left     : 0,
						top      : 0
					})).stop().animate({
						left     : direction[0] * props.width,
						opacity  : 0,
						top      : direction[1] * props.height
					});
					
					nu.css($.extend(props, {
						left     : -direction[0] * props.width,
						top      : -direction[1] * props.height 
					})).stop().animate({
						left     : 0,
						top      : 0
					}, 300, _onComplete);
				},
				/**
				 * Reset elements when animation is finished
				 */
				_onComplete = function(){
					$('div', $el).first().remove();
					$('div', $el).first().css(reset)
					$el.css(reset)
				};

			$el.css({
				height:   $el.height(),
				overflow: 'hidden',
				position: 'relative',
				width:    $el.width()
			});

			$el.wrapInner('<div />');

			_animate(
				$($el.children(0)),
	
				$('<div />')
					.html(content)
					.appendTo($el),
				opts
			);
		});
	}
}

$.extend(achilles.handlers, handlers);

})(jQuery)