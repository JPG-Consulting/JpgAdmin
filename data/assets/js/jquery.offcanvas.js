$(document).ready(function(){

    $('.offcanvas-left-toggle').on("click", function( e ) {
		e.preventDefault();
		
		if ($('#offcanvas-left').hasClass('in')) {
			$('#offcanvas-left').removeClass('in');
			// Clear dynamic settings
			$('#offcanvas-left').css({'width':''});
			$('.navbar-fixed-top').css({'left':''});
			$('#page-wrap').css({'margin-left':''});
		} else {
			if ($(window).width() <= 480) {
				var menuWidth = ($(window).width() - 44) + 'px';
				$('#offcanvas-left').css({'width':menuWidth});
				$('.navbar-fixed-top').css({'left':menuWidth});
				$('#page-wrap').css({'margin-left':menuWidth});
			}
			$('#offcanvas-left').addClass('in');
			$('#offcanvas-right').removeClass('in');
		}
	});
    
    $(window).on("resize", function( e ) {
    	if ($('#offcanvas-left').hasClass('in')) {
    		if ($(window).width() > 480) {
        		$('#offcanvas-left').css({'width':''});
    			$('.navbar-fixed-top').css({'left':''});
    			$('#page-wrap').css({'margin-left':''});
        	} else {
        		var menuWidth = ($(window).width() - 44) + 'px';
        		$('#offcanvas-left').css({'width':menuWidth});
    			$('.navbar-fixed-top').css({'left':menuWidth});
    			$('#page-wrap').css({'margin-left':menuWidth});
        	}
    	}
    });
	
	
});
