<?php

//secure included files
deals_secure();

require_once 'deals_list_table.php';

global $invoiceTable;

/*
 * generate list table for invoice reports
 */
$invoiceTable = new Deals_List_Table(array(
    'singular_name' => 'invoice',
    'plural_name' => 'invoices',
    'ajax_enabled' => true
));

/*
 * setup callback
 */
$invoiceTable->prepare_item_callback = 'deals_invoice_table_prepare';
$invoiceTable->column_default_callback = 'deals_invoice_column_default';
$invoiceTable->column_cb_callback = 'deals_invoice_column_cb';
$invoiceTable->bulk_action_callback = 'deals_invoice_bulk_action';

/*
 * setup columns 
 */
$invoiceTable->register_columns = array(
    'cb' => '<input type="checkbox" />',
    'id' => 'ID',        
    'sale_id' => 'View Sales',           
    'buyer' => 'Buyer',                      
    'item' => 'Item Name',
    'status' => 'Invoice Status',
    'invoice_preview' => 'Invoice Preview',
    'date' => 'Created'
);
/*
 setup bulk actions
 */
$invoiceTable->register_bulk_actions = array(
    'delete'    => 'Delete'
);
/*
 setup sortable columns
 */
$invoiceTable->register_sortable_columns = array(
    'date'  => array('date',true)
);

/**
 * Triggered at bulk action processing
 * 
 * @return void
 */
function deals_invoice_bulk_action() {
    
    global $wpdb;
    
    $requested_action2 = isset($_POST['action2']) ? $_POST['action2'] : null;
    $requested_action = isset($_POST['action']) ? $_POST['action'] : null;
    
    if($requested_action == 'delete' || $requested_action == 'delete') {
        
        foreach($_POST['invoice'] as $invoice_id) {
            
            $id = intval($invoice_id);
            $query = $wpdb->prepare('DELETE FROM '.$wpdb->prefix.'wpdeals_invoices WHERE id="'.$id.'"');
            $wpdb->query($query);
            
        }                
                
        wp_redirect(admin_url('/edit.php?post_type=daily-deals&page=deal-invoices'));
        exit();        
        
    }
    
}

/**
 *
 * Preparing table data
 * 
 * @param Deals_List_Table $obj 
 */
function deals_invoice_table_prepare(Deals_List_Table $obj) {
        
    $obj->process_bulk_action();
    
    $per_page = $obj->data_per_page;

    $columns = $obj->get_columns();
    $hidden = array();
    $sortable = $obj->get_sortable_columns();

    $obj->_column_headers = array($columns, $hidden, $sortable);    

    $data = $obj->data;

    //function usort_reorder($a,$b){            
    //    $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
    //    $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
    //    return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
    //}
    //usort($data, 'usort_reorder');
    
    $current_page = $obj->get_pagenum();

    $total_items = count($data);

    $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

    $obj->items = $data;        

    $obj->set_pagination_args( array(
        'total_items' => $total_items,                  //WE have to calculate the total number of items
        'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
        'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
    ) );
    
}

/**
 *
 * Generate column data
 * 
 * @param string $item
 * @param string $column_name
 * @return string 
 */
function deals_invoice_column_default($item,$column_name) {
    
    switch($column_name) {
            
        case 'id':
            echo '<a href="'.admin_url('/?page=deal-invoice-detail&invoice_id='.$item['id']).'">'.$item['id'].'</a>';
            break;

        case 'sale_id':
            echo '<a href="'.admin_url('/?page=deal-sales-detail&sale_id='.$item['sales_id']).'">'.$item['sales_id'].'</a>';
            break;

        case 'buyer':
            echo '<a href="'.admin_url('/user-edit.php?user_id='.$item['sales_data']->user_id).'">'.$item['sales_rel']['username'].'</a>';
            break;

        case 'item':
            echo '<a href="'.admin_url('/post.php?post='.$item['sales_data']->item_id.'&action=edit').'">'.$item['sales_rel']['item_title'].'</a>';
            break;                        

        case 'status':
            echo $item['pay_status'] == 1 ? 'completed' : '<span style="color:#ff0000">pending</span>';
            break;

        case 'invoice_preview':

            echo '<a href="#" id="'.$item['id'].'" class="js-invoice-preview">Preview Invoice</a>';

            break;

        case 'date':
            echo $item['created'];
            break;

    }
    
}

/**
 *
 * Generate checkbox column value
 * 
 * @global Deals_List_Table $invoiceTable
 * @param string $items
 * @return string 
 */
function deals_invoice_column_cb($item) {
    
    global $invoiceTable;
    
    echo sprintf(
        '<input type="checkbox" name="%1$s[]" value="%2$s" />',
        /*$1%s*/ $invoiceTable->_args['singular'], 
        /*$2%s*/ $item['id']               
    );
    
}

/**
 *
 * Prepare data and display table
 * 
 * @global Deals_List_Table $invoiceTable 
 */
function deals_invoice_report() {            
    
    global $invoiceTable,$wpdb;
    
    $datadisplay = array();
    if(isset($_GET['order']) && $_GET['orderby'] == 'date') {
        $datadb = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wpdeals_invoices ORDER BY created '.$_GET['order']);
    }else{
        $datadb = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wpdeals_invoices ORDER BY id DESC');
    }      
    
    if(!empty($datadb)) {
        
        foreach($datadb as $single) {
                
            $sales_detail = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'wpdeals_sales WHERE id="'.$single->sales_id.'"');
            $datadisplay[] = array(
                'id' => $single->id,
                'sales_id' => $single->sales_id,
                'sales_data' => $sales_detail,
                'sales_rel' => array(
                    'username' => $wpdb->get_var('SELECT user_nicename FROM '.$wpdb->users.' WHERE ID="'.$sales_detail->user_id.'"'),
                    'item_title' => $wpdb->get_var('SELECT post_title FROM '.$wpdb->posts.' WHERE ID="'.$sales_detail->item_id.'"')
                ),
                'pay_status' => $single->invoice_status,
                'created' => $single->created
            );

        }                  
        
    }
    
    echo '<form action="'.admin_url('/edit.php?post_type=daily-deals&page=deal-invoices').'" method="post">';
    $invoiceTable->data = $datadisplay;
    $invoiceTable->prepare_items();
    $invoiceTable->display();
    echo '</form>';
    
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
                    
            var jsPrintArea = null;                    
            jQuery('.js-invoice-preview').click(function() {
                var idThis = jQuery(this).attr('id');

                jQuery.ajax({
                    url: "<?php echo admin_url(); ?>",
                    type: 'GET',
                    cache: false,
                    data: {page: 'deal-invoice-preview',invoice_id: idThis},
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
            
            jQuery('#js-invoice-close a#js-print').click(function() {
                jQuery(jsPrintArea).jqprint({
                    operaSupport: true,                    
                });
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

/**
 *
 * Display invoice detail
 * 
 * @global object $wpdb
 * @return void
 */
function deals_invoice_detail() {
    
    global $wpdb;
    
    $invoice_id = intval($_GET['invoice_id']);
    $invoiceData = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'wpdeals_invoices WHERE id="'.$invoice_id.'"');
    $invoiceRelData = array();

    if(!empty($invoiceData)) {

        $sales = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'wpdeals_sales WHERE id="'.$invoiceData->sales_id.'"');
        $username = $wpdb->get_var('SELECT user_nicename FROM '.$wpdb->users.' WHERE ID='.$sales->user_id);
        $item_title = $wpdb->get_var('SELECT post_title FROM '.$wpdb->posts.' WHERE ID='.$sales->item_id);

        $invoiceRelData['sales'] = $sales;
        $invoiceRelData['username'] = $username;
        $invoiceRelData['item_title'] = $item_title;            

    }
    
    ?>
    <table class="form-table">

        <tbody>
            <tr valign="top">
                <th scrope="row">
                    <label>User Buyer</label>
                </th>
                <td>
                    <span class="regular-text"><?php echo $invoiceRelData['username']; ?></span>
                </td>
            </tr>                
            <tr valign="top">
                <th scrope="row">
                    <label>Item</label>
                </th>
                <td>
                    <span class="regular-text"><?php echo $invoiceRelData['item_title']; ?></span>
                </td>
            </tr>                                
            <tr valign="top">
                <th scrope="row">
                    <label>Invoice Status</label>
                </th>
                <td>
                    <span class="regular-text"><?php echo $invoiceData->invoice_status == 1 ? 'complete' : 'pending'; ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scrope="row">
                    <label>Transaction Date</label>
                </th>
                <td>
                    <span class="regular-text"><?php echo $invoiceData->created; ?></span>
                </td>
            </tr>                
        </tbody>

    </table>
    <?php
    
}

/**
 * Set invoice preview
 * 
 * @return void
 */
function deals_invoice_preview() {
    
    global $wpdb;
    
    $template = DEALS_TEMPLATE_DIR . 'form/mail_invoice.php';
    
    if(isset($_GET['invoice_id'])) {
        
        $invoice_id = intval($_GET['invoice_id']);
        $sales_id = $wpdb->get_var('SELECT sales_id FROM '.$wpdb->prefix.'wpdeals_invoices WHERE id="'.$invoice_id.'"');            
        $item_id = $wpdb->get_var('SELECT item_id FROM '.$wpdb->prefix.'wpdeals_sales WHERE id="'.$sales_id.'"');            
        $txn_id = $wpdb->get_var('SELECT transaction_id FROM '.$wpdb->prefix.'wpdeals_sales WHERE item_id="'.$item_id.'"');        
        $user_id = $wpdb->get_var('SELECT user_id FROM '.$wpdb->prefix.'wpdeals_sales WHERE id="'.$sales_id.'"');        
        $user_name = $wpdb->get_var('SELECT user_nicename FROM '.$wpdb->prefix.'users WHERE ID="'.$user_id.'"');
                
    }else{
        
        $item_id = $wpdb->get_var('SELECT item_id FROM '.$wpdb->prefix.'wpdeals_sales ORDER BY RAND() LIMIT 1');         
        $sales_id = $wpdb->get_var('SELECT id FROM '.$wpdb->prefix.'wpdeals_sales WHERE item_id="'.$item_id.'"');         
        $txn_id = $wpdb->get_var('SELECT transaction_id FROM '.$wpdb->prefix.'wpdeals_sales WHERE item_id="'.$item_id.'"');        
        $user_id = $wpdb->get_var('SELECT user_id FROM '.$wpdb->prefix.'wpdeals_sales WHERE transaction_id="'.$txn_id.'"');   
        $user_name = $wpdb->get_var('SELECT user_nicename FROM '.$wpdb->prefix.'users WHERE ID="'.$user_id.'"');
        
    }
    
    if(file_exists($template)) {
        
        global $invoice_options, $item_raw, $checkVerify,$invoice_data;
        
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
            'user_name' => $user_name
        );

        $checkVerify = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'wpdeals_sales WHERE id="'.$sales_id.'"');    
        
        ob_start();
        require_once $template;
        $ob_content = ob_get_contents();
        ob_end_clean();
        
        echo '<div id="js-return-text">';
        echo $ob_content;
        echo '</div>';
        
    }else{
        echo 'Invoice template is missing';
    }
            
}