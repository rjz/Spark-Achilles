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
	}
}

$.extend(achilles.handlers, handlers);

})(jQuery)