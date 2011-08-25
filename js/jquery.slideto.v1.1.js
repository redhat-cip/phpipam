/**
 * jQuery SlideTo Plugin (version 1.0)
 * 
 * @copyright Jesse Price <jesseprice.com>
 * @description Animate slide scroll to effects to specific elements in the document
 * 
 * @license Licensed under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 */

// Create closure
(function($) {
	$.fn.slideto = function(options) {
		var opts = $.extend({}, $.fn.slideto.defaults, options);
		var elem = (this.length > 0) ? this : 'a';
		// Element instance specific actions
		$(elem).each(function() {
			var e = $(this);
			var o = $.meta ? $.extend({}, opts, e.data()) : opts;
			var url = $(e).attr("href");
			var anchor = '';
			if(url && url.indexOf("#") != -1 && url.indexOf("#") == 0) {
				var pieces = url.split("#",2);
				anchor = $("a[name='"+pieces[1]+"']");
				$(this).attr('href', 'javascript:void(0);');
			} else
				anchor = $(o.target);
			
			$(e).bind('click', function(){
				$('html, body').animate({
					scrollTop: anchor.offset().top,
					scrollLeft: anchor.offset().left
				}, o.speed);
			});
		});
		
		// Allow jQuery chaining
		return this;
	};
	
	/**
	 * Plugin Options
	 * ---------------------------------------------------------------------
	 * Overwrite the default options? Go for it!
	 */
	$.fn.slideto.defaults = {
		target : false, 	// Where to scroll? If it's false, we use the "scroll attribute"
		speed  : 1500 	// slow, medium, fast, numeric microseconds
	}
})(jQuery);
