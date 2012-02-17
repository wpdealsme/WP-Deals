// This is the wpdeals front end javascript "library"

jQuery(document).ready(function(){  
        
        jQuery("a.fancy").fancybox({
		'titleShow'     : false,
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic'
	});
        
        
	jQuery("a.free").fancybox({        
	});
        
        (function($){
            jQuery.fn.extend({
                center: function () {
                    return this.each(function() {
                        jQuery(this).css({
                             "position" : "fixed",
                             left: (jQuery(window).width() - jQuery(this).width()) / 2,
                             top: (jQuery(window).height() - jQuery(this).height()) / 2
                        });
                    });
                }
            }); 
        })($);

        jQuery(".updated").center();
        jQuery(".updated").delay(3000).fadeOut(3000);
                
		
});