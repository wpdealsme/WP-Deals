<?php
/**
 * WP-Deals Template Functions
 * 
 * Functions used in the template files to output content - in most cases hooked in via the template actions.
 *
 * @package	WP-Deals
 * @category	Core
 * @author	Onnay Okheng
 */


/**
 * Content Wrappers
 **/
if (!function_exists('deals_output_before_main_content')) {
	function deals_output_before_main_content() {	
		if ( get_option('template') === 'twentyeleven' ) :
			echo '<div id="primary"><div id="content" role="main">';
		elseif ( get_option('template') === 'twentyten' ) :
			echo '<div id="container">';
		else :
			echo '<div id="container"><div id="content" role="main">';	
		endif;
	}
}
if (!function_exists('deals_output_after_main_content')) {
	function deals_output_after_main_content() {	
		if ( get_option('template') === 'twentyeleven' ) :
			echo  '</div></div>';
		elseif ( get_option('template') === 'twentyten' ) :
                        echo  '</div>';
		else :
			echo '</div></div>';
		endif;
	}
}


/**
 * Content Wrappers Single
 **/
if (!function_exists('deals_output_before_single_content')) {
	function deals_output_before_single_content() {	
		if ( get_option('template') === 'twentyeleven' ) :
			echo '<div id="primary"><div id="content" role="main">';
                else:
			echo '<div id="container"><div id="content" role="main">';	
		endif;
	}
}
if (!function_exists('deals_output_after_single_content')) {
	function deals_output_after_single_content() {	
		if ( get_option('template') === 'twentyeleven' ) :
			echo  '</div></div>';
                else:
			echo '</div></div>';
		endif;
	}
}


if(!function_exists('deals_output_sidebar')){
        function deals_output_sidebar(){            
                if(is_singular('daily-deals') OR is_page(get_option('deals_page_featured_id'))){
                        get_sidebar('deals');
                }            
        }    
}

/**
 * Display form download if on single deal.
 */
function deals_display_popup_single(){
?>
		<!-- popup form -->    
		<div id="subscribe_deals">
			<div class="modal-overlay"></div>

			<div class="modal-container">
				<div class="modal-bg">
					<div class="modal-close"><a href=""><?php _e('Close', 'wpdeals'); ?></a></div>
					<h2 class="modal-title"><?php _e('Download Form', 'wpdeals'); ?></h2>
					<h3 class="modal-tagline"><?php _e('Enter your email below, for the download link.', 'wpdeals'); ?></h3>

					<div class="subs-container clearfix">
						<div class="modal-side left">
							<div class="modal-icon">
								<span class="email"><?php _e('Download here', 'wpdeals'); ?></span>
							</div>
							<h4><?php _e('Enter your email', 'wpdeals'); ?></h4>
							<h5></h5> 
							<?php deals_form_subscribe('top'); ?>
                                                </div><div id="modal-or">OR</div>
                                                <div class="modal-side right">
                                                    <div class="modal-icon">
                                                        <span class="rss">Subscribe by Email</span>
                                                    </div>
                                                    <h4>Subscribe by RSS</h4>
                                                    <h5>Add the RSS to your feedreader and get news of deals releases as they happen!</h5> 
                                                    <a class="sub" href="<?php echo deals_get_option('rss_link'); ?>">Subscribe</a>
                                                </div>
					</div> 
                                        <a class="destroy" href="">Hey, I'm already subscribed to this awesome site, there's no need to show me this ever again.</a>
				</div>
			</div>
		</div>
<?php
}

add_action('wp_footer', 'deals_display_popup_single', 1);
