<?php

// defining here
define('DEALS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('DEALS_PLUGIN_FILE', DEALS_PLUGIN_PATH.'deals.php');
define('DEALS_JS', plugins_url('wp-deals/deals-assets/js/'));
define('DEALS_CSS', plugins_url('wp-deals/deals-assets/css/'));
define('DEALS_IMG', plugins_url('wp-deals/deals-assets/images/'));
define('DEALS_DIR', plugins_url('wp-deals/'));
define('DEALS_URL', ABSPATH . 'wp-content/plugins/wp-deals/');
define('DEALS_TEMPLATE_DIR', DEALS_URL.'deals-template/');
define('DEALS_TEMPLATE_URL', DEALS_DIR.'deals-template/');
define('DEALS_FORM_DIR', DEALS_TEMPLATE_DIR.'form/');
define('DEALS_PAYMENT_DIR',DEALS_PLUGIN_PATH.'deals-payments/');
define('DEALS_LOG_DIR',DEALS_PLUGIN_PATH.'deals-assets/logs/');
define('DEALS_VENDOR_DIR',DEALS_PLUGIN_PATH.'deals-assets/vendors/');
define('DEALS_LIB_DIR',DEALS_PLUGIN_PATH.'deals-assets/libs/');
define('DEALS_ENABLE_LOG',false);

/*
 * define execution php file
 */
define('DEALS_EXEC',true);

/**
 *
 * Debugging variables 
 * 
 * @param string|int|object|array|bool|null $var
 * @param bool $print [optional]
 * @param bool $exit [optional]
 */
function deals_debug($var,$print=true,$exit=false) {
    
    echo '<pre>';
    if($print) {
        print_r($var);
    }else{
        var_dump($var);
    }
    echo '</pre>';
    
    if($exit) : exit(); endif;
    
}

/**
 *
 * Create log file
 * 
 * @param string $message
 * @param string $filename [optional]
 * @return void
 */
function deals_log($message,$filename='deals-log') {
    
    if(!empty($filename)) {
     
        $filepath = DEALS_LOG_DIR.$filename;
        $content = '['.date('d-m-Y H:i:s').'] > '.$message."\n";
        
        if( is_writable(DEALS_LOG_DIR) && DEALS_ENABLE_LOG == true ) {
            
            if( file_exists($filepath) ) {
                
                if( is_writable($filepath) ) {
                    
                    $fp = fopen($filepath,'a+');    
        
                    flock($fp,LOCK_EX);
                    fwrite($fp,$content);
                    flock($fp,LOCK_UN);
                    fclose($fp);    
                    
                }
                
            }else{
                
                $fp = fopen($filepath,'a+');    
        
                flock($fp,LOCK_EX);
                fwrite($fp,$content);
                flock($fp,LOCK_UN);
                fclose($fp); 
                
            }
            
        }
        
    }    
    
}

		
/**
 * Get the plugin path
 */
function deals_plugin_path() { 	
        return WP_PLUGIN_DIR . "/" . plugin_basename( dirname(dirname(__FILE__))); 
 }
 
 
/**
 * Get template part (for templates like loop)
 */
function deals_get_template_part( $slug, $name = '' ) {
	if ($name == 'deals') :
		if (!locate_template(array( 'loop-deals.php', DEALS_TEMPLATE_DIR.'loop-deals.php' ))) :
			load_template( DEALS_TEMPLATE_DIR . 'loop-deals.php',false );
			return;
		endif;
	elseif($name == ''):
            include_once DEALS_TEMPLATE_DIR . $slug.'.php';
        else:
            include_once DEALS_TEMPLATE_DIR . $slug.'-'. $name.'.php';
        endif;
}

/**
 * Set include path
 * 
 * @param string $vendor_path
 * @return void
 */
function deals_install_vendors($vendor_path) {    
    set_include_path(get_include_path().PATH_SEPARATOR.DEALS_VENDOR_DIR.$vendor_path);    
}

/**
 *
 * Create image barcode
 * 
 * @param string|int $barcode_data
 * @param string $barcode_name
 * @return void
 */
function deals_image_create_barcode($barcode_data,$barcode_name) {
    
    $img_barcode_path = DEALS_PLUGIN_PATH.'deals-assets/images/barcodes/';
    
    if( is_writable($img_barcode_path) ) {
        
        if( !file_exists($img_barcode_path.$barcode_name) ) {
            
            $barcode_filepath = $img_barcode_path.$barcode_name;
            
            require_once 'Barcode.php';
            $barcode = Image_Barcode::draw($barcode_data, 'code128', 'png',false);
            imagepng($barcode,$barcode_filepath);
            imagedestroy($barcode);
            
        }
        
    }
    
}

/**
 *
 * Get all deal option settings
 * 
 * @param string $name
 * @param string $default [optional]
 * @return string|int|null|bool|object|array
 */
function deals_get_option($name,$default=null) {
    
    $options = get_option('dealoptions');
    if(!empty($options)) {
        
        if(isset($options[$name]) && !empty($options[$name])) {            
            return $options[$name];            
        }else{
            return $default;
        }
        
    }else{//create file log if all option is empty
        $log_message = 'Unable to load settings, all option is empty.';
        deals_log($log_message);
    }
    
}


/**
 * secure included files
 *
 * @return void
 */
function deals_secure() {
    if (!defined('DEALS_EXEC') || DEALS_EXEC !== true) : exit('no direct access allowed');
    endif;
}


/* ----------------------------------------------------------------------------------- */
/* deals_image - Get Image from custom field  */
/* ----------------------------------------------------------------------------------- */

/*
  This function retrieves/resizes the image to be used with the post in this order:

  Parameters:
  $width = Set width manually without using $type
  $height = Set height manually without using $type
  $class = CSS class to use on the img tag eg. "alignleft". Default is "thumbnail"
  $id = Assign a custom ID, if alternative is required.
  $return = Return results instead of echoing out.
  $src = A parameter that accepts a img url for resizing. (No anchor)
 */

if (!function_exists('deals_image')) {

    function deals_image($args) { 

            global $post;

            //Defaults
            $width = '100';
            $height = '100';
            $class = 'image-thumbnail';
            $id = null;
            $src = '';
            $crop = true;
            $return = false;
            $alt = null;

            if ( !is_array($args) )
                    parse_str( $args, $args );

            extract($args);

            // Set post ID
            if ( empty($id) ) 
                $id = $post->ID;
            
            if ( !empty($alt) )
                $alt = ' alt="'.$alt.'"';
                    
            // get thumb id
            $thumb_id       = get_post_meta($id,'_thumbnail_id',true); 
            $image          = vt_resize( $thumb_id, $src, $width, $height, $crop );
            
            // for default images
            $img_url_default= DEALS_IMG.'default-deals.jpg';
            $img_url        = ($image['url'] != '')? $image['url']:apply_filters('deals_default_image', $img_url_default);
            $image_width    = ($image['width'] != '')? $image['width']:$width;
            $image_height   = ($image['height'] != '')? $image['height']:$height;
            
            // return image into array
            $output = '<img src="'.$img_url.'" width="'.$image_width.'" height="'.$image_height.'" class="'.$class.'"'.$alt.'/>';
            
            // Return or echo the output
            if ( $return == TRUE )
                    return $output;
            else
                    echo $output; // Done
    }

}


/**
 * Function for showing gallery images of deals on single product.
 * @author Onnay Okheng
 *
 * @param integer $id
 * @return array
 */
function deals_get_gallery_images($id = null){
    global $post;

    if($id == null)
        $id = $post->ID;

    return get_children( 'post_type=attachment&post_mime_type=image&post_parent='.$id );
}

/* ----------------------------------------------------------------------------------- */
/* vt_resize - Resize images dynamically using wp built in functions
  /*----------------------------------------------------------------------------------- */
/*
 * Resize images dynamically using wp built in functions
 * Victor Teixeira
 *
 * php 5.2+
 *
 * Exemplo de uso:
 *
 * <?php
 * $thumb = get_post_thumbnail_id();
 * $image = vt_resize( $thumb, '', 140, 110, true );
 * ?>
 * <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
 *
 * @param int $attach_id
 * @param string $img_url
 * @param int $width
 * @param int $height
 * @param bool $crop
 * @return array
 */
if (!function_exists('vt_resize')) {

    function vt_resize($attach_id = null, $img_url = null, $width, $height, $crop = false) {
      
        // this is an attachment, so we have the ID
        if ($attach_id) {

            $image_src = wp_get_attachment_image_src($attach_id, 'full');
            $file_path = get_attached_file($attach_id);

            // this is not an attachment, let's use the image url
        } else if ($img_url) {

            $file_path = parse_url($img_url);
            $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];

            //$file_path = ltrim( $file_path['path'], '/' );
            //$file_path = rtrim( ABSPATH, '/' ).$file_path['path'];
            if (!file_exists($file_path))
                return;

            $orig_size = getimagesize($file_path);

            $image_src[0] = $img_url;
            $image_src[1] = $orig_size[0];
            $image_src[2] = $orig_size[1];
        }

        $file_info = pathinfo($file_path);

        // check if file exists
        $base_file = $file_info['dirname'] . '/' . $file_info['filename'] . '.' . $file_info['extension'];
        if (!file_exists($base_file))
            return;

        $extension = '.' . $file_info['extension'];

        // the image path without the extension
        $no_ext_path = $file_info['dirname'] . '/' . $file_info['filename'];

        $cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . $extension;

        // checking if the file size is larger than the target size
        // if it is smaller or the same size, stop right here and return
        if ($image_src[1] > $width) {

            // the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
            if (file_exists($cropped_img_path)) {

                $cropped_img_url = str_replace(basename($image_src[0]), basename($cropped_img_path), $image_src[0]);

                $vt_image = array(
                    'url'       => $cropped_img_url,
                    'width'     => $width,
                    'height'    => $height
                );

                return $vt_image;
            }

            // $crop = false
            if ($crop == false) {

                // calculate the size proportionaly
                $proportional_size  = wp_constrain_dimensions($image_src[1], $image_src[2], $width, $height);
                $resized_img_path   = $no_ext_path . '-' . $proportional_size[0] . 'x' . $proportional_size[1] . $extension;

                // checking if the file already exists
                if (file_exists($resized_img_path)) {

                    $resized_img_url = str_replace(basename($image_src[0]), basename($resized_img_path), $image_src[0]);

                    $vt_image = array(
                        'url'       => $resized_img_url,
                        'width'     => $proportional_size[0],
                        'height'    => $proportional_size[1]
                    );

                    return $vt_image;
                }
            }

            // check if image width is smaller than set width
            $img_size = getimagesize($file_path);
            if ($img_size[0] <= $width)
                $width = $img_size[0];

            // no cache files - let's finally resize it
            $new_img_path = image_resize($file_path, $width, $height, $crop);
            $new_img_size = getimagesize($new_img_path);
            $new_img = str_replace(basename($image_src[0]), basename($new_img_path), $image_src[0]);

            // resized output
            $vt_image = array(
                'url' => $new_img,
                'width' => $new_img_size[0],
                'height' => $new_img_size[1]
            );

            return $vt_image;
        }

        // default output - without resizing
        $vt_image = array(
            'url' => $image_src[0],
            'width' => $width,
            'height' => $height
        );

        return $vt_image;
    }

}

/**
 * deals_update_featured_deals function.
 * 
 * @todo Should be refactored to e
 * @return void 
 */
function deals_update_featured_products() {
	$is_ajax = (int)(bool)$_POST['ajax'];
	$deal_id = absint( $_GET['deal_id'] );
	check_admin_referer( 'feature_deal_' . $deal_id );
	$status = get_option( 'sticky_deals' );

	$new_status = (in_array( $deal_id, $status )) ? false : true;

	if ( $new_status ) {

		$status[] = $deal_id;
	} else {
		$status = array_diff( $status, array( $deal_id ) );
		$status = array_values( $status );
	}
	update_option( 'sticky_deals', $status );

	if ( $is_ajax == true ) {
		if ( $new_status == true ) : ?>
                    jQuery('.featured_toggle_<?php echo $deal_id; ?>').html("<img src='<?php echo DEALS_IMG.'unset-featured.png'; ?>' alt='Unset Featured' title='Unset Featured'/>");
            <?php else: ?>
                    jQuery('.featured_toggle_<?php echo $deal_id; ?>').html("<img src='<?php echo DEALS_IMG.'set-featured.png'; ?>' alt='Set Featured' title='Set Featured'/>");
<?php
		endif;
		exit();
	}
	wp_redirect( wp_get_referer() );
	exit();
}

if ( isset( $_REQUEST['deals_admin_action'] ) && ( $_REQUEST['deals_admin_action'] == 'update_featured_deal' ) )
    add_action( 'admin_init', 'deals_update_featured_products' );



/**
 * remove deal from sticky_deals
 *
 * @param int $postid
 * @return void
 */
function deals_remove_sticky($postid){          
        
    $status = get_option( 'sticky_deals' );
    
    if(is_array($status) && !empty($status)) {
     
        if(in_array( $postid, $status )){
            $status = array_diff( $status, array( $postid ) );
            $status = array_values( $status );

            update_option( 'sticky_deals', $status );
        }
        
    }    
    
}



/**
 * Add custom rewrite rule for buy page
 *
 * @param object $wp_rewrite
 * @return array 
 */
function deals_rewrite_buy($wp_rewrite) {                        

    $page_id = get_option('deals_page_post_id');

    if($page_id) {

        $deal_id_key = '%wpdealsid%';                          
        $wp_rewrite->add_rewrite_tag($deal_id_key, '(.+?)', 'deal_buy_id=');

        $custom_rules = array(
            'daily-deals/buy/(.+)' => 'index.php?page_id='.$page_id.'&deal_buy_id='.$wp_rewrite->preg_index(1)
        );

        $wp_rewrite->rules = $custom_rules + $wp_rewrite->rules;
    }        	

    return $wp_rewrite->rules;

}
add_action('generate_rewrite_rules', 'deals_rewrite_buy');

/**
 * Get buy permalink url
 * 
 * @global object $wp_rewrite
 * @param int $id
 * @return string
 */
function deals_get_buy_url($id) {
    
    global $wp_rewrite;
    
    $page_id = get_option('deals_page_post_id');
    $id = intval($id);
    
    if($wp_rewrite->using_permalinks()) {        
        return home_url('/daily-deals/buy/'.$id);        
    }else{
        return home_url('/index.php?page_id='.$page_id.'&deal_buy_id='.$id);
    }
    
}

      
/**
 * Set wordpress custom query var
 * 
 * @param array $query
 * @return array 
 */
function deals_custom_query_var($query) {

    $query[] = 'deal_buy_id';        

    return $query;

}   

add_filter('query_vars', 'deals_custom_query_var');  


/*-----------------------------------------------------------------------------------*/
/* Ajax Save Action - deals_ajax_callback */
/*-----------------------------------------------------------------------------------*/

add_action('wp_ajax_deals_ajax_post_action', 'deals_ajax_callback');
/**
 * Wordpress ajax callback hook
 * 
 * @global object $wpdb
 * @return void
 */
function deals_ajax_callback(){
    global $wpdb;
    
    $save_type = $_POST['type'];
    
    if($save_type == 'expired'){
        
        $post_ID = intval($_POST['data']); // Acts as the name  
        
        update_post_meta($post_ID, 'is_expired', 'yes');
        deals_remove_sticky($post_ID);
        
    }
    
    die();
}


/*-------------------------------------------------------------------------------------
 * Product deal
 *-------------------------------------------------------------------------------------*/

/**
 * Check if deal is expired.
 *
 * @global object $post
 * @param int $postid [optional]
 * @return int 
 */
function deals_is_expired($postid = null){
    global $post;
    
    if($postid == null)
        $postid     = $post->ID;
    
    $is_expired     = (get_post_meta($postid, 'is_expired', true) == 'yes')? 1:0;
    
    if($is_expired == 1){
        update_post_meta($postid, 'is_expired', 'yes', 'no'); // update key value, set 'yes' expired.
        return 1;
    }
    
    // check expired from date deal meta
    $date_end       = get_post_meta($postid, 'end_time', true); // e.g. "2011-11-29 11:29:00 "
    
    // set timezone.
    $timezone       = 'Asia/Jakarta'; // set the location get from http://www.php.net/manual/en/timezones.php
    date_default_timezone_set($timezone); // set timezone date. http://www.php.net/manual/en/function.date-default-timezone-get.php
    $date_now       = date('Y-m-d H:i:s');   
    
    $deal_end       = strtotime($date_end);
    $deal_now       = strtotime($date_now);    
    $deal_date      = $deal_end - $deal_now;    
    
    if($deal_date <= 0){        
        update_post_meta($postid, 'is_expired', 'yes', 'no'); // update key value, set 'yes' expired.
        deals_remove_sticky($postid);
        return 1;
    }
        
    return 0;
    
}

/**
 * get price deal
 *
 * @global object $post
 * @return string deals_price_format
 */
function deals_price($currency = true){
    global $post;
    
    $base_price     = (get_post_meta($post->ID, 'base_price', true)) ? get_post_meta($post->ID, 'base_price', true) : 0;
    
    if (!$currency)
        return $base_price;
    
    return deals_price_format($base_price);
}

/**
 * get discon price
 *
 * @global object $post
 * @return int 
 */
function deals_discount($currency = true){
    global $post;
    $disc_price     = (get_post_meta($post->ID, 'discount_price', true))? get_post_meta($post->ID, 'discount_price', true) : 0;
    
    if(!$currency)
        return $disc_price;
    
    return deals_price_format($disc_price);
}

/**
 * get discount percent
 *
 * @return string 
 */
function deals_save_percent(){
    
    $save_price     = deals_price(false) - deals_discount(false);
    $disc_percent   = (deals_price(false) != 0) ? round(($save_price / deals_price(false) * 100), 1) : 0;
    
    return $disc_percent . '%';
}
/**
 * get discount percent
 *
 * @return string deals_price_format 
 */
function deals_save_price(){
    
    $save_price     = deals_price(false) - deals_discount(false);
        
    return deals_price_format($save_price);
}

/**
 * Check if deal is free or not.
 *
 * @return boolean 
 */
function deals_is_free(){
    
    if((deals_price(false) == 0 AND deals_discount(false) == 0) OR (deals_price(false) == deals_discount(false)))
        return TRUE;
    
    return FALSE;
    
}

/**
 * get link free product
 *
 * @global object $post
 * @return string 
 */
function deals_product_free(){
    global $post;
    
    if (is_user_logged_in()) {
        $hreffree = get_post_meta($post->ID, 'product_link', true);
    } else {
        $hreffree = '#subscribe_deals';
    }
    
    return $hreffree;
}

/**
 * get end date deal
 *
 * @param int $postid
 * @return array
 */
function deals_get_end_date($postid){
    
    $deals_end_raw  = get_post_meta($postid, 'end_time', true); // e.g. "2011-11-29 11:29:00 "
    $deals_end_arr  = str_replace(array('-', ' ', ':'), '-', $deals_end_raw); // e.g. "11-29-2011-11-29-"
    
    return explode('-', $deals_end_arr);
}


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
if(!function_exists('deals_button')){
    function deals_button( $args = array() ){
        global $post;
        
        /* Set up the default arguments for the button. */
	$defaults = array(
        'container_open'    => '<div id="price-block">',
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
                $output .= '<a href="'.$link.'" class="'.$args->link_free_class.'">'.$args->text_free.'</a>';
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
}


/**
 * Check if sticky_deals has been set or not
 * 
 * @return array|null
 */
function deals_is_featured_exists() {
    return get_option('sticky_deals');
}

/**
 * this function will get standart feature
 *
 * @param int $limit [optional]
 * @return object
 */
function deals_featured($limit = 1){
    $sticky = get_option( 'sticky_deals' );
    $args   = array(
            'post__in'      => $sticky,
            'posts_per_page'=> $limit,
            'post_type'     => 'daily-deals',
            'orderby'       => 'meta_value',
            'meta_key'      => 'is_expired',
            'meta_value'    => 'no',
            'order'         => 'ASC',
            'paged'         => 1);
    return query_posts( $args );
}

/**
 * Display form subscribe
 *
 * @global object $post
 * @param int $idForm [optional]
 * @return void
 */
function deals_form_subscribe($idForm = null, $text='Download'){
    global $post;    
?>    
    <script type="text/javascript">
        jQuery(document).ready(function() {

                jQuery("#<?php echo $idForm; ?>").submit(function(e) {
                        
                        e.stopImmediatePropagation();                        
                        jQuery(this).unbind('submit');
                        jQuery('input[type=submit]', this).attr('disabled', 'disabled');
                        
                        var formParam = jQuery(this).serializeArray();
                        var postID      = null;
                        var emailUser   = null;
                        var wpNonce     = null;
                        var formCheckId = '<?php echo $idForm; ?>';
                        var formHidden = null;                        
                        
                        jQuery.each( formParam, function(k, v){
                            
                            if(v.name == 'email') {
                                emailUser = v.value;                                
                            }
                            
                            if(v.name == 'id') {
                                postID = v.value;
                            }
                            
                            if(v.name == 'idForm') {
                                formHidden = v.value;
                            }
                            
                            if(v.name == '_wpnonce') {
                                wpNonce = v.value;                                
                            }
                            
                        });                                                
                        
                        var ajaxParamSubscribe = {email: emailUser,postid: postID,wpnonce: wpNonce};
                        
                        if(formHidden == formCheckId) {
                            
                            jQuery('#<?php echo $idForm; ?> .loader').html('<img src="<?php echo DEALS_IMG.'ajax-loader.gif'; ?>" alt="Loading..." />');                        
                            jQuery('#<?php echo $idForm; ?> .result').html('');
                            
                            jQuery('#<?php echo $idForm; ?> .result').html('<img src="<?php echo DEALS_IMG.'ajax-loader.gif'; ?>" alt="Loading..." /> Loading...');
                            
                            jQuery('.result').load('<?php echo DEALS_TEMPLATE_URL.'form/subscribe_save.php'; ?>', ajaxParamSubscribe,
                            function() {
                                    jQuery('#<?php echo $idForm; ?> .loader').html('');
                                    jQuery('#<?php echo $idForm; ?>').fadeOut(200);
                                    jQuery(this).fadeIn(200);
                            });   
                            
                        }                                                

                        return false;
                });    

        });
    </script>
    
    <form id="<?php echo $idForm; ?>" action="" method="post" class="form-subscriber clearfix">
            <div class="input">
                    <input type="email" name="email" id="form_email" placeholder="Enter Email Address" />
            </div>
        
            <?php //$text = ($post->ID == null)? __('Get Notified', 'wpdeals'):__('Download', 'wpdeals'); ?>                        

            <div class="submit">
                    <input type="submit" name="submit_form" value="<?php echo $text; ?>">
            </div>

            <input type="hidden" name="id" id="postid" value="<?php if(is_singular('daily-deals')) echo $post->ID; ?>" />
            <input type="hidden" name="idForm" id="idForm" value="<?php echo $idForm; ?>" />
            <?php wp_nonce_field('nonce_form_subscribe'); ?>
            
    </form>
    <div class="result"></div>
<?php
}

/**
 * Send email to user for free deal
 *
 * @param int $postid
 * @param string $email
 * @return void
 */
function deals_send_email($postid, $email){

    /*
     hook to download link
     */
    $link_download = get_post_meta($postid, 'product_link', true);
    $link_download = apply_filters('deals_download_link',$link_download,$postid);
    
    $mail_validate = filter_var($email,FILTER_VALIDATE_EMAIL);
    $mail_subject = 'Your Free Deals - ' . get_the_title($postid);
    $mail_content = 'Hello,'."\r\n".
                    'Here the link for the deal that you are requested :'."\r\n".
                    $link_download."\r\n"
                    .'Enjoy this product';

    $headers = "Content-Type: text/html" . "\r\n";
    $headers .= ' From: '.  get_bloginfo('name') . ' <'.  get_option('admin_email') .'>' . "\r\n";
    
    //$sent_email = wp_mail($email, $mail_subject, $mail_content, $headers);
    $sent_email_status = 'error';
    
    if($mail_validate) {
        $sent_email_status = (wp_mail($email, $mail_subject, $mail_content, $headers) == true) ? 'sent' : 'error';
    }    
    
    /*
     not counting download and inventory
     if requested post is not daily-deals
     post type
    */
    $wppost = get_post($postid);
    if($wppost->post_type == 'daily-deals') {
        deals_minus_inventory($postid);
        deals_count_download($postid);
    }    
    
    deals_log($mail_subject.' to '.$email.' : '.$sent_email_status);
    
}


/**
 * Price Formatting
 *
 * @param float $price
 * @param array $args [optional]
 * @return string
 **/
function deals_price_format( $price, $args = array() ) {
	
	extract(shortcode_atts(array(
		'ex_tax_label' 	=> '0'
	), $args));
	
	$return = '';
	$num_decimals = (int) deals_get_option('price_num_decimals'); 
	$currency_pos = deals_get_option('currency_pos');
	$currency_symbol = get_deals_currency_symbol();
        
        if($price > 0 ) {
         
            $price = number_format( (double) $price, $num_decimals, deals_get_option('price_decimal_sep'), deals_get_option('price_thousand_sep') );
            
        }else{            
            $price = 0;
        }
        
	switch ($currency_pos) :
		case 'left' :
			$return = $currency_symbol . $price;
		break;
		case 'right' :
			$return = $price . $currency_symbol;
		break;
		case 'left_space' :
			$return = $currency_symbol . ' ' . $price;
		break;
		case 'right_space' :
			$return = $price . ' ' . $currency_symbol;
		break;
	endswitch;
	
	return $return;
}	
	
/**
 * Decrease inventory stock
 * 
 * @param int $post_id
 * @return void
 */
function deals_minus_inventory($post_id) {
    
    $stock_before = get_post_meta($post_id,'stock',true);
    
    if(!empty($stock_before) || $stock_before > -1) {
             
        $stock_after = $stock_before - 1;
        deals_expired_inventory($post_id);
        
        if($stock_before > 0) {
            update_post_meta($post_id,'stock',$stock_after);
        }        
        
    }        
    
}

/**
 * Check if inventory is out of stock limit
 * if true then set to expired
 * 
 * @param int $post_id
 * @return void
 */
function deals_expired_inventory($post_id) {
    
    $stock = get_post_meta($post_id,'stock',true);
    if(!empty($stock) && $stock == 0) {
        update_post_meta($post_id, 'is_expired', 'yes', 'no');
    }
    
}

/**
 * Insert item_id to download table
 *
 * @global object $wpdb
 * @param int $post_id
 * @return void
 */
function deals_count_download($post_id) {
    
    global $wpdb;
    
    $table = $wpdb->prefix.'wpdeals_download';
    $wpdb->insert($table,array('item_id' => $post_id,
                               'download_date' => date('c')));
    
}
