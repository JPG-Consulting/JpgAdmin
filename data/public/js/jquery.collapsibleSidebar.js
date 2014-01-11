(function ( $ ) {

	$.fn.collapsibleSidebar = function() {
		
		return this.each(function() {
			
			var that = this;
			
	        $(this).find('ul > li:has(ul)').addClass('has-sub');
	        $(this).find('ul > li > a').click( function(e) {
	        	var checkElement = $(this).next();
	        	
	        	
	        	
	        	$(that).find('li').removeClass('open');
	        	$(this).closest('li').addClass('open');
	        	
	        	if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
	        		$(this).closest('li').removeClass('open');
	        		checkElement.slideUp('normal');
	        	}
	        	
	        	if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
	        		$(this).children('ul ul:visible').slideUp('normal');
	        		checkElement.slideDown('normal');
	        	}
	        	
	        	if (checkElement.is('ul')) {
	        		e.preventDefault();
	        		return false;
	        	} else {
	        		return true;	
	        	}
	        });
	    });
	};
	
}(jQuery));