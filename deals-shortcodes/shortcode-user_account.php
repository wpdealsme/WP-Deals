<?php
/**
 * User Account Shortcode
 * 
 * Shows the 'User account' section where the customer can update their information.
 *
 * @package	WP Deals
 * @category	Shortcode
 * @author	WP Deals
 */


function get_deals_user_account() {
	return deals_user_account(); 
}	
function deals_user_account() {
    
	global $post, $current_user, $deals_error;

	$user_id = get_current_user_id();
	
	if (is_user_logged_in()) :
	
		?>
		
		<?php do_action('deals_before_user_account'); ?>
		
		<div class="deals-profile">
                    
                        <h3><?php _e('Change Password', 'wpdeals'); ?></h3>
                    
                        <?php                    

                        if ($_POST) :

                                if ($user_id > 0 && wp_verify_nonce($_POST['deals_change_password'], 'deals_change_password')) :

                                        if ( $_POST['password-1'] && $_POST['password-2']  ) :

                                                if ( $_POST['password-1'] == $_POST['password-2'] ) :

                                                        wp_update_user( array ('ID' => $user_id, 'user_pass' => $_POST['password-1']) ) ;
                                                
                                                        wp_safe_redirect( get_permalink(get_option('deals_page_profile_id')) );
                                                        exit;

                                                else :

                                                        $deals_error->add_error(__('Passwords do not match.', 'wpdeals'));

                                                endif;                                                

                                        else :

                                                $deals_error->add_error(__('Passwords do not match.', 'wpdeals'));

                                        endif;			

                                endif;

                        endif;

                        $deals_error->show_messages();                            
                        ?>

                        <form action="<?php echo esc_url( get_permalink(get_option('deals_page_profile_id')) ); ?>" method="post" class="deals-user change-password">

                                <p class="form-row form-row-first">
                                        <label for="reg_password"><?php _e('Password', 'wpdeals'); ?> <span class="required">*</span></label>
                                        <input type="password" class="input-text" name="password-1" id="reg_password" />
                                </p>
                                <p class="form-row form-row-last">
                                        <label for="reg_password2"><?php _e('Re-enter password', 'wpdeals'); ?> <span class="required">*</span></label>
                                        <input type="password" class="input-text" name="password-2" id="reg_password2" />
                                </p>

                                <div class="clear"></div>

                                <?php wp_nonce_field('deals_change_password', 'deals_change_password'); ?>
                                <p><input type="submit" class="button" name="save_password" value="<?php _e('Save', 'wpdeals'); ?>" /></p>

                        </form>
		
		</div><!-- /.deals-profile -->
                
		<?php
                
		do_action('deals_after_user_account');
		
	else :
		
		// Login/register template
		deals_get_template( 'form/deals-form.php' );
		
	endif;
		
}



/**
 * Shortcode for thanks page
 * @return string
 */

function get_deals_thanks_deal(){
    return deals_thanks_deal();
}
function deals_thanks_deal(){
    
    global $wpdb;
    
    if (is_page(get_option('deals_page_thanks_post_id')) ) {
        
                if(isset($_GET['payment_method']) && $_GET['payment_method'] != ''){
                    $payment_method = $_GET['payment_method'];
                    return apply_filters('deals_payment_message', $payment_method);
                }elseif(!isset($_REQUEST) || !isset($_GET['item_id']) || !isset($_GET['user_id'])) {
			return __('Invalid Request', 'wpdeals');
		}else{
						
			$item_id = intval($_GET['item_id']);
                        $user_id = intval($_GET['user_id']);
			$wp_user = wp_get_current_user();
			
			//check requested user_id and user login data
			if($wp_user->ID != $user_id) {
				return __('Invalid User. You must login first to do a transactions.','wpdeals');
			}else{
				
				/*
				 Get data query for:
				 1.item
				 2.sales
				 */
				$item_post = get_post($item_id);
				$sales_query = new WP_Query(array(
					'post_type' => 'deals-sales',
					'post_status' => 'publish',
					'posts_per_page' => 1,
					'meta_query' => array(
						array(
							'key' => '_deals_sales_item_id',
							'value' => $item_id,
							'type' => 'NUMERIC'
						),
						array(
							'key' => '_deals_sales_user_id',
							'value' => $user_id,
							'type' => 'NUMERIC'
						)
					)
				));
				
				if(empty($item_post)) {
					return __('Invalid requested item.','wpdeals');
				}else{									
					
					if(!$sales_query->have_posts()) {
						return __('You have not bought any item yet.','wpdeals');	
					}else{																		
						return __('Thank you for your buying, please check your user profile page to download your link. We have been sent an invoice to your email address : ', 'wpdeals') . $wp_user->user_email;
					}
					
				}							
				
			}
			
		}
		
    }    
    
}