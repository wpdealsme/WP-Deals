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
		else :
			echo '<div id="container"><div id="content" role="main">';	
		endif;
	}
}
if (!function_exists('deals_output_after_main_content')) {
	function deals_output_after_main_content() {
		if ( get_option('template') === 'twentyeleven' ) :
			echo  '</div></div>';
		else :
			echo '</div></div>';
		endif;
	}
}


/**
 * Sidebar
 */
if(!function_exists('deals_output_sidebar')){
        function deals_output_sidebar(){
                if ( get_option('template') === 'twentyeleven' ) :
                    
                else:
                        get_sidebar('deals');                    
                endif;
        }    
}


/**
 * View type
 */
if (!function_exists('deals_view_type')) {
        function deals_view_type(){
                $term = get_term_by( 'slug', get_query_var($wp_query->query_vars['taxonomy']), $wp_query->query_vars['taxonomy']);
                print_r($term);
        ?>     
            <div class="deals-view-type">
                    <a href="<?php echo deals_get_view_type('grid'); ?>"><img src="<?php echo DEALS_IMG.'grid-view.png'; ?>" alt="grid view" /></a>
                    <a href="<?php echo deals_get_view_type('list'); ?>"><img src="<?php echo DEALS_IMG.'list-view.png'; ?>" alt="list view" /></a>
            </div>           
        <?php
        }
}


/**
 * Pagination
 **/
if (!function_exists('deals_pagination')) {
	function deals_pagination() {

		global $wp_query;

		if (  $wp_query->max_num_pages > 1 ) :
			?>
                                <div class="navigation clear">
                                        <div class="nav-next"><?php next_posts_link( __( 'Next <span class="meta-nav">&rarr;</span>', 'dailydeals' ),$loop->max_num_pages ); ?></div>
                                        <div class="nav-previous"><?php previous_posts_link( __( '<span class="meta-nav">&larr;</span> Previous', 'dailydeals' ) ); ?></div>
                                </div>
			<?php
		endif;

	}
}


/**
 * Thumbnail on looping deals
 */
if (!function_exists('deals_loop_thumb')) {
        function deals_loop_thumb(){
            
                // get image size
                if(isset($_GET['view_type']) AND $_GET['view_type'] == 'list')
                    $view_type = '';
                elseif(isset($_GET['view_type']) AND $_GET['view_type'] == 'grid')
                    $view_type = '_grid';
                else
                    $view_type = (deals_get_option('view_type', 'list') == 'list') ? '' : '_grid';
                
                $default_img_width  = deals_get_option('default_image'.$view_type.'_width', 100);
                $default_img_height = deals_get_option('default_image'.$view_type.'_height', 100);
                $default_img_crop   = (deals_get_option('default_image'.$view_type.'_crop', 0) == 1)? 'true': 'false';
                
        ?>
                        
                <figure class="recent-deal-thumbnail">
                    <?php if(has_post_thumbnail()): ?>
                        <a href="<?php the_permalink(); ?>">
                                <?php deals_image( 'width='.$default_img_width.'&height='.$default_img_height.'&crop='.$default_img_crop.'&class=deal-thumbnail&alt='. get_the_title() ); ?>
                        </a>
                    <?php else: ?>
                        <img src="<?php echo DEALS_IMG.'default-deals.jpg'; ?>" width="<?php echo $default_img_width; ?>" height="<?php echo $default_img_height; ?>" class="deal-thumbnail no-image" alt="No Image"/>
                    <?php endif; ?>
                </figure>
                        
        <?php
        }
}

/**
 * Countdown deals loop
 */
if (!function_exists('deals_loop_countdown')) {
        function deals_loop_countdown(){
                
                $is_expired     = deals_is_expired(); // checking deals               
                $deals_end      = deals_get_end_date(get_the_ID()); // convert date and time to array

        ?>
                <?php if ($is_expired == 0) : ?>
                <div class="another-timer-block">
                                <script type="text/javascript">
                                        // jQuery Countdown
                                        jQuery(document).ready(function() {
                                            var austDay = new Date(
                                                    <?php echo $deals_end[0]; // year  ?>,
                                                    <?php echo ($deals_end[1] - 1); // month - 1 ?>,
                                                    <?php echo $deals_end[2]; // date  ?>,  
                                                    <?php echo $deals_end[3]; // hour  ?>,
                                                    <?php echo $deals_end[4]; // minute  ?>)
                                            jQuery("#deal-ends-<?php the_ID(); ?>").countdown({until: austDay,onExpiry:dealExpired<?php the_ID();?>,
                                                expiryText:'<div class="deal-expired"><?php _e('Deals Expired', 'wpdeals'); ?></div>'});

                                            checkZero = jQuery('.hasCountdown').find('span.countdown_amount').text();

                                            if(checkZero < 1) {                        
                                                jQuery('#deal-ends-<?php the_ID(); ?>').find('span').remove();
                                                jQuery('#deal-ends-<?php the_ID(); ?>').append('<div class="deal-expired"><?php _e('Deals Expired', 'wpdeals'); ?></div>');

                                            }

                                        });
                                        function dealExpired<?php the_ID(); ?>() {
                                            var postID = <?php the_ID(); ?>;                                                            
                                            jQuery.post(
                                               '<?php echo admin_url("admin-ajax.php"); ?>', 
                                               {
                                                  'action':'deals_ajax_post_action',
                                                  'type': 'expired',
                                                  'data': postID
                                               }, 
                                               function(response){                            
                                                    //pass
                                               }
                                            );
                                        }
                                </script>
                                
                                <?php if(is_deal()): ?>
                                        <div class="timer-msg">
                                            <?php _e('This deal will end in:', 'wpdeals'); ?>
                                        </div> 
                                <?php endif; ?>
                                
                                <div id="deal-ends-<?php the_ID(); ?>"></div>   
                </div> <!-- timer block -->                             
                <?php endif; ?>

        <?php
        }
}

/**
 * Countdown deals loop
 */
if (!function_exists('deals_table_price')) {
        function deals_table_price(){
            
                // default
                $is_expired     = deals_is_expired();
                ?>
                
                <?php if($is_expired == 1): ?>
                
                        <div class="deal-expired"><?php _e('Deals Expired', 'wpdeals'); ?></div>

                <?php elseif ($is_expired == 0 OR is_deal()) : ?>

                        <?php if( deals_is_free() ) : ?>
                                <div class="deal-free"><?php _e('Free', 'wpdeals'); ?></div>
                        <?php else: ?>
                                <div class="deal-discount-block">
                                        <span class="list-price-title"><?php _e('Value', 'wpdeals'); ?><br/><span class="price-num"><?php echo deals_price(); ?></span></span>
                                        <span class="list-price-title"><?php _e('Discount', 'wpdeals'); ?><br/><span class="price-num"><?php echo deals_save_percent(); ?></span></span>
                                        <span class="list-price-title"><?php _e('You Save', 'wpdeals'); ?><br/><span class="price-num"><?php echo deals_save_price(); ?></span></span>							
                                </div>
                        <?php endif; ?>

                <?php endif; ?>

        <?php
        }
}


/**
 * description on loop
 */
if (!function_exists('deals_loop_description')) {
        function deals_loop_description(){
            
            $num_word   = apply_filters('deals_length_excerpt', 20); ?>
            <div class="deal-description"><p><?php echo wp_trim_words(get_the_excerpt(), $num_word); ?></p></div>
                    
        <?php }
}


/**
 * deals container
 */
if (!function_exists('deals_container_class')) {
        function deals_container_class(){

                if(isset($_GET['view_type']) AND $_GET['view_type'] == 'list')
                    $container_class = 'wp-deals-list';
                elseif(isset($_GET['view_type']) AND $_GET['view_type'] == 'grid')
                    $container_class = 'wp-deals-grid';
                else
                    $container_class = (deals_get_option('view_type', 'list') == 'list') ? 'wp-deals-list' : 'wp-deals-grid';
                
                echo 'class="'.apply_filters('deals_container_class', $container_class).' wp-deals-wrapper"';

        }
}

/**
 * bottom of content
 */
if (!function_exists('deals_single_meta_content')) {
        function deals_single_meta_content(){
        ?>                
                <!-- display footer deal -->
                <div id="deal-meta-footer">
                    <span class="deal-categories">
                        <?php echo get_the_term_list(get_the_ID(), 'deal-categories', __('Posted in: ', 'wpdeals'), ', ', ''); ?> 
                    </span>
                    <span class="deal-tags">
                        <?php echo get_the_term_list(get_the_ID(), 'deal-tags', __('Tags: ', 'wpdeals)'), ', ', ''); ?> 
                    </span>
                </div>
        <?php
        }
}


/**
 * Add button bottom of description
 */
if (!function_exists('deals_button_bottom_desc')) {
        function deals_button_bottom_desc(){

                    $args = array(
                            'container_open'    => '<div id="bottom-price-block">',
                            'container_close'   => '</div>'
                    );
                    deals_button($args);

        }
}


/**
 * share button
 */
if (!function_exists('deals_share_button')) {
        function deals_share_button(){
        ?>
                <?php if(deals_get_option('share') == 'yes'): ?>
                    <!-- display share deal -->
                    <div id="deal-share-block">
                            <!-- AddThis Button BEGIN -->
                            <div class="addthis_toolbox addthis_default_style " addthis:url="<?php the_permalink(); ?>" addthis:title="<?php the_title(); ?>">
                            <a class="addthis_button_preferred_1"></a>
                            <a class="addthis_button_preferred_2"></a>
                            <a class="addthis_button_preferred_3"></a>
                            <a class="addthis_button_preferred_4"></a>
                            <a class="addthis_button_compact"></a>
                            <a class="addthis_counter addthis_bubble_style"></a>
                            </div>
                            <script type="text/javascript"
                                    src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=<?php echo deals_get_option('share_id', ''); ?>">			
                            </script>
                            <!-- AddThis Button END -->
                    </div>
                <?php endif; ?>
        <?php
        }
}

/**
 * container single deals
 */
if(!function_exists('deals_before_single_content')){
        function deals_before_single_content(){
                echo '<div class="entry-content">';
        }
}

if(!function_exists('deals_after_single_content')){
        function deals_after_single_content(){
                echo '</div>';
        }
}


/**
 * Greeting for user who has loggin
 */
if(!function_exists('deals_user_info')){
        function deals_user_info(){
                global $current_user;               
        ?>
                <div class="alignleft avatar"><?php echo get_avatar($current_user->user_email, '80'); ?></div>
                <p><?php echo sprintf( __('Hello, <strong>%s</strong>. This is your dashboard.<br/><a href="%s" title="Your Profile">Your Profile</a><br/><a href="%s" title="Your history page">Your History</a><br/><a href="%s" title="Logout">Logout</a>', 'wpdeals'), $current_user->display_name, get_permalink(get_option('deals_page_profile_id')), get_permalink(get_option('deals_page_history_id')), wp_logout_url( home_url() )); ?></p>
        <?php
        }
}