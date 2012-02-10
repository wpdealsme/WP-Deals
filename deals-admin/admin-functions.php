<?php

/**
 * Custom action to send an email
 * when saving post
 * 
 * @param int $post_id
 * @return void
 */
function deals_publish($post_id){    
    
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    
    $posts = get_post($post_id);
    if($posts->post_type == 'daily-deals' && isset($_POST['publish']) && $_POST['publish'] == 'Publish'){
        
        deals_log($posts->post_type.' - '.$posts->post_status);

        deals_log('Start logging to sent mail to all subscribers');

        $subscribers = get_option('deals_subscribed_emails');
        $subscribers = maybe_unserialize($subscribers);

        if(!empty($subscribers) && is_array($subscribers)) {

            foreach($subscribers as $subscriber) {
                deals_log('Start sent mail subscribes to: '.$subscriber);
                $mail_subject = 'WP-Deals - '.$posts->post_title;
                $mail_message = 'Hello <br />';
                $mail_message .= 'For today, we announcing a new deal that is, '.$posts->post_title.'<br />';
                $mail_message .= 'Please go to our site to read more detail, '.get_permalink($post_id);

                $mail_header = "Content-Type: text/html" . "\r\n";
                $mail_header .= ' From: '.  get_bloginfo('name') . ' <'.  get_option('admin_email') .'>' . "\r\n";
                wp_mail($subscriber,$mail_subject,$mail_message,$mail_header);

            }

            deals_log('================================================');

        }else{
            deals_log('Empty subscribers when try to sent : '.$posts->post_title);
        }  

    }    
    
}
add_action('save_post', 'deals_publish');

/**
 * Set invoice preview
 * 
 * @return void
 */
function deals_invoice_preview() {
    
    $template = DEALS_TEMPLATE_DIR . 'form/mail-invoice.php';
    $sale_id = $_GET['sale_id'];
    $post = get_post($sale_id);
    $item_id = get_post_meta($sale_id,'_deals_sales_item_id',true);
    $user_id = get_post_meta($sale_id,'_deals_sales_user_id',true);
	
    global $checkVerify,$invoice_options,$invoice_data;		
    $itemdata = get_post($item_id);
    $userdata = get_userdata($user_id);
    $itemprice = get_post_meta($item_id,'_discount_price',true);
    
    //create barcode
    $barcode_id = str_replace(array('#',' '),'',$post->post_title);
    deals_image_create_barcode($barcode_id,$barcode_id.'.png');
    $img_barcode_url = DEALS_IMG.'barcodes/'.$barcode_id.'.png';
    
    $checkVerify = new stdClass();
    $checkVerify->buy_date = $post->post_date;
    $checkVerify->transaction_id = $post->post_title;
    $checkVerify->total_price = $itemprice;
    
    $invoice_options = array(
        'info'      => deals_get_option('invoice_desc'),
        'logo_url'  => deals_get_option('invoice_logo_url'),
        'store_name'=> deals_get_option('store_name'),
        'footer'    => deals_get_option('invoice_footer'),
        'barcode' => $img_barcode_url
    );
    
    $invoice_data = array(
        'title' => $itemdata->post_title,   
        'link' => home_url('/my-history'),
        'user_name' => $userdata->user_login
    );
    
    ob_start();
    load_template($template);        
    $preview_content = ob_get_clean();    
    
    echo '<div id="js-return-text">';
    echo $preview_content;
    echo '</div>';
    
}


