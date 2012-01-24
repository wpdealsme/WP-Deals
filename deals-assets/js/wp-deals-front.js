// This is the wpdeals front end javascript "library"

jQuery(document).ready(function(){    
    
        // fancybox for subscribe
        /* jQuery(".free").fancybox({
                'transitionIn'		: 'none',
                'transitionOut'		: 'none'
        });*/
        
        jQuery("a.fancy").fancybox({
		'titleShow'     : false,
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic'
	});
	

	
	function deals_displayModal_free() {
		
		// variable calcs for centering the modal
		var windowHeight = jQuery(window).height() / 2;
		var modalHeight = jQuery('.modal-container').height() / 2;
		var windowWidth = jQuery(window).width() / 2;
		var modalWidth = jQuery('.modal-container').width() / 2;
		
		// set the containers position
		jQuery('.modal-container').css({ 
			marginTop	: 	windowHeight - modalHeight,
			marginLeft	: 	windowWidth - modalWidth 
		});
		
		//set the height to the documents height
		jQuery('#subscribe_deals').height(jQuery(document).height());
	
		//disable sidebar
		jQuery('body').css("overflow", "auto");
		
		//fade in the modal
		jQuery('#subscribe_deals').animate({
			opacity: 1,
			zIndex: 10000
		}, 300);
		
		jQuery('.modal-overlay').css({
			zIndex : 500
		});
		
		//exit modal on close, "f'of" link and background
		jQuery('.modal-close a, a.destroy, .modal-overlay').click( function (e) {
			
			jQuery('#subscribe_deals, .modal-overlay').animate({ opacity: 0 }, 300, function () {
				jQuery('body').css("overflow", "auto");
				jQuery(this).remove(); 
				jQuery('.result p.success').remove(); 
			});
			
			e.preventDefault();
			
		});
	}
	
	jQuery("a.free").click( function () {
		deals_displayModal_free();
	});
		
});