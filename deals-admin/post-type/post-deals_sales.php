<?php

add_filter('manage_edit-deals-sales_columns','deals_sales_columns');
function deals_sales_columns($columns) {
    
    $columns = array();
    
    $columns['cb'] = '<input type="checkbox" />';
    $columns['transaction_id'] = __('Transaction ID','wpdealss');
    $columns['transaction_stat'] = __('Status','wpdealss');
    $columns['payment_method'] = __('Method','wpdealss');
    $columns['user_name'] = __('User Name','wpdealss');
    $columns['item_name'] = __('Item Name','wpdealss');
    $columns['amount'] = __('Amount','wpdealss');
    $columns['invoice_preview'] = __('Invoice Preview','wpdealss');
    $columns['date'] = __('Date','wpdealss');
    
    return $columns;
}

add_filter('manage_deals-sales_posts_custom_column','deals_sales_posts_custom_column');
function deals_sales_posts_custom_column($column) {
    global $post;
        
    $transaction_id = $post->post_title;
    $transaction_stat = get_post_meta($post->ID,'_deals_sales_transaction_status',true);
    $payment_method = get_post_meta($post->ID,'_deals_sales_payment_method',true);
    $user_name = get_post_meta($post->ID,'_deals_sales_user_name',true);
    $item_name = get_post_meta($post->ID,'_deals_sales_item_name',true);
    $amount = get_post_meta($post->ID,'_deals_sales_amount',true);
    $date = $post->post_date;
    
    switch($column) {
        
        case 'transaction_id':
            echo '<a href="'.get_edit_post_link($post->ID).'">'.$transaction_id.'</a>';
            break;
        
        case 'transaction_stat':
            echo $transaction_stat;
            break;
        
        case 'payment_method':
            echo $payment_method;
            break;
        
        case 'user_name':
            echo $user_name;
            break;
        
        case 'item_name':
            echo $item_name;
            break;
        
        case 'amount':
            echo empty($amount) ? 'price not available' : $amount;
            break;
        
        case 'invoice_preview':
            echo '<a href="#" id="'.$post->ID.'" class="js-invoice-preview">Invoice Preview</a>';
            break;
        
        case 'date':
            echo $date;
            break;
        
    }
}

add_action('admin_footer','deals_sales_admin_footer');
function deals_sales_admin_footer() {
    
    ?>
    <div id="js-overlay" style="width:100%; height:100%;filter:alpha(opacity=50);opacity: 0.5;background:#000; position:absolute; top:0; left:0; z-index:3000;display:none;"></div>
    <div id="invoice-preview" style="
         background:#eee;
         width:600px;
         position:absolute; 
         z-index:5000; 
         display:none;
         padding: 10px;
         -webkit-box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
         -moz-box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
         -moz-border-radius: 5px;
         -webkit-border-radius: 5px;">

        <div id="js-invoice-close">
            <a href="#" id="js-close">[close]</a>
            <a href="#" id="js-print">[print]</a>
        </div>
        <hr />
        <div id="js-invoice-content">

        </div>

    </div>
    <script type="text/javascript">
        
        jQuery(document).ready(function() {
           
           jQuery('.js-invoice-preview').click(function() {
                
                var idThis = jQuery(this).attr('id');

                jQuery.ajax({
                    url: "<?php echo admin_url(); ?>",
                    type: 'GET',
                    cache: false,
                    data: {page: 'deal-invoice-preview',sale_id: idThis},
                    dataType: 'html',
                    success: function(data,textStatus,jqXHR) {
                        var htmlContent = jQuery(data).find('#js-return-text').contents();
                        jsPrintArea = htmlContent;
                        jQuery('#js-invoice-content').append(htmlContent);                                

                    }
                });

                showModal('invoice-preview');                        
                return false;
                
           });
           
           jQuery('#js-invoice-close a#js-close').click(function() {
               closeModal('invoice-preview');
               return false;
            });
           
        });
        
        //show popup divs
        function showModal(id) {

            // get the screen height and width  
            var maskHeight = jQuery(document).height();  
            var maskWidth = jQuery(window).width();

            // calculate the values for center alignment
            var dialogTop =  (maskHeight/3) - (jQuery('#'+id).height());  
            var dialogLeft = (maskWidth/2) - (jQuery('#'+id).width()/2); 

            // assign values to the overlay and dialog box
            jQuery('#js-overlay').css({height:maskHeight, width:maskWidth}).show();
            jQuery('#'+id).css({top:dialogTop, left:dialogLeft}).show();

        }   

        function closeModal(id) {        
            jQuery('#js-overlay').hide();
            jQuery('#'+id).hide();
            jQuery('#js-invoice-content').text('');
        }
        
    </script>
    <?php
    
}

add_filter('manage_edit-deals-sales_sortable_columns','deals_sales_sortable_columns');
function deals_sales_sortable_columns($columns) {
    
    $columns['transaction_stat'] = __('Status','wpdealss');
    $columns['payment_method'] = __('Method','wpdealss');
    return $columns;
    
}

add_filter('request','deals_sales_request');
function deals_sales_request($vars) {
    
    if(isset($vars['orderby']) && isset($vars['order'])) {
        
        switch($vars['orderby']) {
            
            case 'Status':
                $vars = array_merge($vars,array(
                    'meta_key' => '_deals_sales_transaction_status',
                    'orderby' => 'meta_value'
                ));
                break;
            
            case 'Method':
                $vars = array_merge($vars,array(
                    'meta_key' => '_deals_sales_payment_method',
                    'orderby' => 'meta_value'
                ));
                break;
            
        }
        
    }
    
    return $vars;
    
}