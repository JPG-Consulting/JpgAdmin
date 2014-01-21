(function ( $ ) {

	$.fn.accordion = function( options ) {
		
		// This is the easiest way to have default options.
        var settings = $.extend({
            // These are the defaults.
            speed: 300
        }, options );
		
		return this.each(function() {
			var $this = $(this);
			
			$(this).find('li:has(ul) > a').on('click', function( e ){
				e.preventDefault();
				
				var isClosing = $(this).parent().hasClass('open');
				
				// Close any previouslly opened sub-menu
				$this.find('.open').find('ul').slideUp(settings.speed, function() {
					$(this).parent().removeClass('open');
				});
				
				if (isClosing === false) {
					// Open the new sub-menu
					$(this).parent().find('ul').slideDown(settings.speed, function() {
						$(this).parent().addClass('open');
					});
				}
			});
		});
	};

})(jQuery);