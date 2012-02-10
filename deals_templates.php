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
 * Template loader
 *
 * @param type $template
 * @return string 
 */
function deals_template_loader( $template ) {
	global $current_user;
	
	$buy_id = get_query_var('deal_buy_id');
        
        if($buy_id){
            
			$wp_nonce = $_REQUEST['_wpnonce'];
		
			require_once DEALS_PAYMENT_DIR.'class-payments.php';
			require_once DEALS_PAYMENT_DIR.'abstract-payment-gateway.php';

			//create payment object
			$payment = Payments::get_instance();						
			
			//load payments option hook
			$gateways = $payment->core('Gateways');
			if(!isset($_GET['payment_id'])) {
				$payment_method = $gateways->default_payment()->id;
				$plugin_template_buy = $gateways->default_payment()->get_payment_template();				
			}else{
				$payment_method = $_GET['payment_id'];
				$payment_choosen = $gateways->choosen($_GET['payment_id']);
				if(!is_null($payment_choosen)) {
					$plugin_template_buy = $payment_choosen->get_payment_template();
				}else{
					wp_die(__('invalid security check', 'wpdeals'));
                                        exit;
				}
				
			}
                
			if(is_user_logged_in()):
			
				//save sales transaction
				$time = time();
				$sales_data = array();
				$sales_data['post_title'] = $time;
				$sales_data['post_status'] = 'draft';
				$sales_data['post_type'] = 'deals-sales';
				$sales_id = wp_insert_post($sales_data);
				
				if(!is_wp_error($sales_id) || $sales_id > 0) {
					
					$updated_sales = array();
					$updated_sales['ID'] = $sales_id;
					$updated_sales['post_title'] = __('Sales ID #', 'wpdeals').$sales_id;
					$updated_sales['post_status'] = 'publish';
					wp_update_post($updated_sales);
					
					$itemdata = get_post($buy_id);
					$itemprice = get_post_meta($buy_id,'_discount_price',true);
					
					update_post_meta($sales_id,'_deals_sales_user_id',$current_user->ID);
					update_post_meta($sales_id,'_deals_sales_user_name',$current_user->user_login);
					update_post_meta($sales_id,'_deals_sales_item_id',$buy_id);
					update_post_meta($sales_id,'_deals_sales_item_name',$itemdata->post_title);
					update_post_meta($sales_id,'_deals_sales_amount',$itemprice);
					update_post_meta($sales_id,'_deals_sales_payment_method',$payment_method);
					update_post_meta($sales_id,'_deals_sales_transaction_status','pending');
					update_option('_deals_sales_used',$sales_id);
					
				}
	
				if(wp_verify_nonce($wp_nonce,'buy-button')) {
					$template   = $plugin_template_buy;
				}else{
					wp_die(__('invalid security check', 'wpdeals'));
                                        exit;
				}
		
			else:
                                wp_safe_redirect( get_permalink(get_option('deals_page_profile_id')) );
                                exit;
			endif;
        
        }elseif ( is_single() && get_post_type() == 'daily-deals' ) {
            		
		$template = locate_template( array( 'single-deal.php', DEALS_TEMPLATE . 'single-deal.php' ) );
		
		if ( ! $template ) $template = DEALS_TEMPLATE_DIR . 'single-deal.php';
		
	}elseif ( is_tax('deal-categories') ) {
		
		$template = locate_template(  array( 'taxonomy-deal_cat.php', DEALS_TEMPLATE . 'taxonomy-deal_cat.php' ) );
		
		if ( ! $template ) $template = DEALS_TEMPLATE_DIR . 'taxonomy-deal_cat.php';
	}elseif ( is_tax('deal-tags') ) {
		
		$template = locate_template( array( 'taxonomy-deal_tag.php', DEALS_TEMPLATE . 'taxonomy-deal_tag.php' ) );
		
		if ( ! $template ) $template = DEALS_TEMPLATE_DIR . 'taxonomy-deal_tag.php';
	}elseif (is_page( get_option('deals_page_featured_id') )) { // get page id featured deal

		$template = locate_template( array( 'featured-deal.php', DEALS_TEMPLATE . 'featured-deal.php' ) );
		
		if ( ! $template ) $template = DEALS_TEMPLATE_DIR . 'featured-deal.php';
                
        }elseif ( is_post_type_archive('daily-deals') ||  is_page( get_option('deals_page_post_id') )) {

		$template = locate_template( array( 'archive-deal.php', DEALS_TEMPLATE . 'archive-deal.php' ) );
		
		if ( ! $template ) $template = DEALS_TEMPLATE_DIR . 'archive-deal.php';
		
	}
        
        return $template;

}
add_filter( 'template_include', 'deals_template_loader' );


/**
 * Display form download if on single deal.
 */
function deals_display_popup_single(){
        if(is_singular('daily-deals') AND deals_is_free()):
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
                                                <div class="modal-download">
                                                        <div class="modal-icon">
                                                                <span class="email"><?php _e('Download here', 'wpdeals'); ?></span>
                                                        </div>
                                                        <h4><?php _e('Enter your email', 'wpdeals'); ?></h4>
                                                        <h5><?php _e('Check your INBOX or SPAM folder.', 'wpdeals'); ?></h5> 
                                                        <?php deals_form_subscribe(array('idform' => 'free-deals', 'free' => true, 'text' => 'Give Me!')); ?>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
    <?php
        endif;
}
add_action('wp_footer', 'deals_display_popup_single', 1);


 
/**
 * Get template part (for templates like loop)
 */
function deals_get_template_part( $slug, $name = '' ) {
	if ($name == 'deals') :
		if (!locate_template(array( $slug.'-'.$name.'.php', DEALS_TEMPLATE.$slug.'-'.$name.'.php' ))) :
			load_template( DEALS_TEMPLATE_DIR . $slug.'-'.$name.'.php',false );
			return;
		endif;
        endif;        
        get_template_part(DEALS_TEMPLATE. $slug, $name );
}


/**
 * Get other templates (e.g. product attributes)
 */
function deals_get_template($template_name, $require_once = true) {
	if (file_exists( STYLESHEETPATH . '/' . DEALS_TEMPLATE . $template_name )) load_template( STYLESHEETPATH . '/' . DEALS_TEMPLATE . $template_name, $require_once ); 
	elseif (file_exists( STYLESHEETPATH . '/' . $template_name )) load_template( STYLESHEETPATH . '/' . $template_name , $require_once); 
	else load_template( DEALS_TEMPLATE_DIR . $template_name , $require_once);
}


/**
 * Add Body classes based on page/template
 **/
function deals_body_class($classes) {
    
	$deals_body_classes = (array) $deals_body_classes;
	
	$deals_body_classes[] = 'theme-' . strtolower( get_current_theme() );
	
	if (is_wp_deals()) $deals_body_classes[] = 'wpdeals';
	
	if (is_account_deal()) $deals_body_classes[] = 'wpdeals-account';
        
        if (is_history()) $deals_body_classes[] = 'wpdeals-history';
        
        if (is_deal() || is_feature_deal()) $deals_body_classes[] = 'wpdeals-single';
	
	if ( is_account_deal() || is_history() || is_deals_page() || is_thanks() ) $deals_body_classes[] = 'wpdeals-page';	
	
	$deals_body_classes = (array) $deals_body_classes;
	
	$classes = array_merge($classes, $deals_body_classes);
	
	return $classes;
}
add_filter('body_class','deals_body_class');


/**
 * Get current uri.
 *
 * @return type 
 */
function deals_get_current_url( $remove_tag = ''){
    
    global $wp_rewrite;
        
    // request current url
    $home_root  = parse_url(home_url());
    $home_root  = ( isset($home_root['path']) ) ? $home_root['path'] : '';
    $home_root  = preg_quote( trailingslashit( $home_root ), '|' );
    
    // remove tag uri
    $request    = remove_query_arg( $remove_tag );    
    
    if ( !$wp_rewrite->using_permalinks() ) {
        $request = preg_replace('|^'. $home_root . '|', '', $request);
        $request = preg_replace('|^/+|', '', $request);
                
    } elseif ( $wp_rewrite->using_permalinks() ) {
        
            $qs_regex = '|\?.*?$|';
            preg_match( $qs_regex, $request, $qs_match );

            if ( !empty( $qs_match[0] ) ) {
                    $query_string = $qs_match[0];
                    $request = preg_replace( $qs_regex, '', $request );
            }
            
            $request    = preg_replace('|' . $remove_tag . '^' . $home_root . '|', '', $request);
            $request    = preg_replace('|^/+|', '', $request);
            
    }
    
    // get base uri
    $base       = trailingslashit( get_bloginfo( 'url' ) );
    
    return $base . $request;
}


/**
 * Get buy permalink url
 * 
 * @global object $wp_rewrite
 * @param int $id
 * @return string
 */
function deals_get_view_type($type = 'list') {

        global $post, $wp_rewrite;

        if ( !$wp_rewrite->using_permalinks() || is_admin() )
                $result = deals_get_current_url('view_type') . '&view_type='.$type;  
        else
                $result = deals_get_current_url('view_type') . '?view_type=' . $type;

        $result = apply_filters('get_viewtype_link', $result);

        return $result;

}


/**
 * Display subscribe form
 *
 * @global object $deals_error
 * @global object $post
 * @param array $args 
 */
function deals_form_subscribe($args = null){
    global $deals_error, $post;     

    $args = wp_parse_args( (array)$args, array(
                'idform'    => 'subscribe-form',
                'text'      => 'Subscribe',
                'free'      => false
    ) );
        
    extract($args);
    
    if($free)
        $post_id = $post->ID;
    
    $deals_error->show_messages();
    
    ?>
    <form id="<?php echo $idform; ?>" action="" method="post" class="form-subscriber clearfix">
            <input type="email" name="email" id="form_email" placeholder="<?php _e('Enter Email Address', 'wpdeals'); ?>" />
            <input type="submit" name="submit_form" value="<?php echo $text; ?>" />
            <?php wp_nonce_field('deals_process_subscribe', '_subscribe', false); ?>
            <?php if($free) echo '<input type="hidden" name="post_id" value="'.$post_id.'" />'; ?>
    </form>
<?php }



/**
 * get deals button
 * 
 * 'container_open'    => '<div id="price-block">',
 * 'container_close'   => '</div>',
 * 'expired_open'      => '<span class="expired-button">',
 * 'expired_close'     => '</span>',
 * 'free_open'         => null,
 * 'free_close'        => null,
 * 'link_free_class'   => 'buy-button free',
 * 'link_buy_class'    => 'buy-button',
 * 'text_buy'          => 'Buy now',
 * 'text_free'         => 'Free Download',
 * 'text_expired'      => 'Deal expired',
 *
 * @global object $post
 * @return void
 */
function deals_button( $args = array() ){
    global $post;

    /* Set up the default arguments for the button. */
    $defaults = array(
    'container_open'    => '<div id="price-block-'.$post->ID.'" class="price-block">',
    'container_close'   => '</div>',
    'expired_open'      => '<span class="expired-button">',
    'expired_close'     => '</span>',
    'free_open'         => null,
    'free_close'        => null,
    'buy_open'          => null,
    'buy_close'         => null,
    'link_free_class'   => 'buy-button free',
    'link_buy_class'    => 'buy-button',
    'text_buy'          => 'Buy now',
    'text_free'         => 'Free Download',
    'text_expired'      => 'Deal expired',
    'show_text_buy'     => true
    );

    /* Parse the arguments and extract them for easy variable naming. */
    $args   = wp_parse_args( $args, $defaults );
    $args   = apply_filters( 'deals_button_args', $args );
    $args   = (object) $args;

    $output = '';

    if($args->container_open)
        $output = $args->container_open;

     if (deals_is_expired() == 1) :

         $output .= $args->expired_open;
         $output .= '<span class="buy-label">'.$args->text_expired.'</span>';
         $output .= $args->expired_close;

     else: 

         if ( deals_is_free() ) :

            $link   = (is_deal())? '#subscribe_deals':get_permalink($post->ID);

            $output .= $args->free_open;
            $output .= '<a href="'.$link.'" class="'.$args->link_free_class.'"><span>'.$args->text_free.'</span></a>';
            $output .= $args->free_close;

        else: 

            $link   = (is_deal())? wp_nonce_url(deals_get_buy_url($post->ID), 'buy-button'):get_permalink($post->ID);

            $output .= $args->buy_open;
            $output .= '<a href="'.$link.'" class="'.$args->link_buy_class.'">';

            if($args->show_text_buy)
                $output .= '<span class="buy-label">'.$args->text_buy.'</span> ';

            $output .= '<span class="price-label">'.deals_discount().'</span>';
            $output .= '</a>';
            $output .= $args->buy_close;

        endif; 

    endif;

    if($args->container_close)
        $output .= $args->container_close;

    echo $output;

}
