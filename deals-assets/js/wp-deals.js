// This is the wpdeals front end javascript "library"

jQuery(document).ready(function(){    

        // make deal as featured
	jQuery('.deals_featured_deal_toggle').livequery(function(){
		jQuery(this).click(function(event){
			target_url = jQuery(this).attr('href');
			post_values = "ajax=true";
			jQuery.post(target_url, post_values, function(returned_data){
				eval(returned_data);
			});
			return false;
		});
	});
    
});