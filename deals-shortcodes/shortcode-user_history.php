<?php
/**
 * User History Shortcode
 * 
 * Shows the 'User history' section where the user can download their purchase.
 *
 * @package	WP Deals
 * @category	Shortcode
 * @author	WP Deals
 */



/**
 * Shortcode for user history
 * @return void
 */

function get_deals_user_history() {
	return deals_user_history(); 
}	
function deals_user_history() {
    

    if (is_user_logged_in()) :
        
        global $wpdb, $wp_rewrite;
    
        $user = wp_get_current_user();
    
        $args       = array(
            'post_type'     => 'deals-sales',
            'relation'      => 'AND',
            'meta_query'    => array(
                    array(
                            'key'       => '_deals_sales_user_id',
                            'value'     => $user->ID,
                            'compare'   => 'IN'
                    ),
                    array(
                            'key'       => '_deals_sales_transaction_status',
                            'value'     => 'completed',
                            'compare'   => 'IN'
                    )
            ),
            'posts_per_page'=> -1
        );
        $results    = new WP_Query($args); ?>

        <?php do_action('deals_before_user_history'); ?>

        <div class="deals-history">

        <h2><?php _e('Transaction History', 'wpdeals'); ?></h2>

        <?php if($results->have_posts()): ?>

                <table class="deals_table">

                    <thead>
                        <tr>
                            <th><?php _e('Date', 'wpdeals'); ?></th>
                            <th><?php _e('Items', 'wpdeals'); ?></th>
                            <th><?php _e('Link', 'wpdeals'); ?></th>
                            <th><?php _e('Invoices', 'wpdeals'); ?></th>
                        </tr>
                    </thead>

                    <tbody>
                <?php
                    while($results->have_posts()): $results->the_post();
                    
                    // get deals post
                    $deal_id        = get_post_meta(get_the_ID(), '_deals_sales_item_id', true);
                    $deal           = get_post($deal_id);
                    $link_download  = get_post_meta($deal_id, '_product_link', true);                    
                    $link_download  = apply_filters('deals_download_link', $link_download, $deal_id);
                    
                    // link invoice
                    if($wp_rewrite->using_permalinks())
                        $link_invoice   = deals_get_current_url() . '?invid=' . get_the_ID();
                    else
                        $link_invoice   = deals_get_current_url() . '&invid=' . get_the_ID();

                    ?>
                            <tr>
                                <td><?php the_date(); ?></td>
                                <td><?php echo $deal->post_title; ?></td>
                                <td><?php if($link_download != '') echo '<a href="' . $link_download. '"> '. __('Download', 'wpdeals') .'</a>'; else _e('No Link', 'wpdeals'); ?></td>
                                <td><a href="<?php echo $link_invoice; ?>"><?php _e('Download', 'wpdeals'); ?></a></td>
                            </tr>
                    <?php endwhile; ?>
                    </tbody>

                </table>
        
        <?php else: ?>

                <p><?php _e('No transaction result.', 'wpdeals'); ?></p>
        
        <?php endif; ?>
                
        </div>
        
        <?php do_action('deals_after_user_history'); ?>

<?php

    else :
        
        // Login/register template
        deals_get_template( 'form/deals-form.php' );
    
    endif;
    
}

add_action('init', 'deals_download_invoice', 1);
function deals_download_invoice(){    
        
        if(isset($_GET['invid']) AND $_GET['invid'] > 0) {
            
            $inv_id = intval($_GET['invid']);
            if(is_int($inv_id) && $inv_id > 0) {
                
                $sales      = get_post($inv_id);
                
                $sales_id   = $sales->ID;
                $item_id    = get_post_meta($sales->ID, '_deals_sales_item_id', true);
                $txn_id     = str_replace(array('#', ' '), '', $sales->post_title);
                $user_id    = get_post_meta($sales->ID, '_deals_sales_user_id', true);
                $user_info  = get_userdata($user_id);
                $itemprice  = deals_discount(true, $item_id);
                
                
                //create barcode
                deals_image_create_barcode($txn_id,$txn_id.'.png');
                $img_barcode_url = DEALS_IMG.'barcodes/'.$txn_id.'.png';
                
                $invoice_options = array(
                        'info' => deals_get_option('invoice_desc'),
                        'logo_url' => deals_get_option('invoice_logo_url'),
                        'store_name' => deals_get_option('store_name'),
                        'footer' => deals_get_option('invoice_footer'),
                        'barcode' => $img_barcode_url
                );
        
                $item_raw = get_post($item_id);
                $invoice_data = array(
                        'title' => $item_raw->post_title,
                        'link' => home_url('/my-history'),
                        'user_name' => $user_info->user_login
                );
    
                $checkVerify = new stdClass();
                $checkVerify->buy_date = $sales->post_date;
                $checkVerify->transaction_id = $txn_id;
                $checkVerify->total_price = $itemprice;
        
                require_once DEALS_LIB_DIR.'class-deals-invoice-pdf.php';
                
                $pdf = new Deals_Invoice_Pdf(compact('invoice_options','item_raw','invoice_data','checkVerify'));
                $pdf->make();                
                
                $pdf_download = DEALS_ASSETS.'invoices/invoice-'.$checkVerify->transaction_id.'.pdf';                
                $pdf_name = 'invoice-'.$checkVerify->transaction_id.'.pdf';                                                
                
                header("Cache-Control: no-cache, must-revalidate");                
                header("Content-Disposition: attachment; filename=$pdf_name");
                header("Content-Type: application/octet-stream");
                header("Content-Length: " . filesize ( $pdf_download ) ); 
                
                ob_clean();
                flush();
                readfile($pdf_download);
                
            }
        }
}