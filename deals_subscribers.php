<?php

deals_secure();

require_once 'deals_list_table.php';

global $subscriberTable;

/*
 * generate list table for invoice reports
 */
$subscriberTable = new Deals_List_Table(array(
    'singular_name' => 'subscriber',
    'plural_name' => 'subscribers',
    'ajax_enabled' => true
));

/*
 * setup callback
 */
$subscriberTable->prepare_item_callback = 'deals_subscriber_table_prepare';
$subscriberTable->column_default_callback = 'deals_subscriber_column_default';
$subscriberTable->column_cb_callback = 'deals_subscriber_column_cb';
$subscriberTable->extra_table_nav_callback = 'deals_extra_table_nav';
$subscriberTable->bulk_action_callback = 'deals_subscriber_bulk_action';

/*
 * setup columns 
 */
$subscriberTable->register_columns = array(
    'cb' => '<input type="checkbox" />',
    'email' => 'Email',            
);
/*
 setup bulk actions
 */
$subscriberTable->register_bulk_actions = array(
    'delete'    => 'Delete'
);

/**
 * Bulk subscriber 
 */
function deals_subscriber_bulk_action() {    
    
    $requested_action2 = isset($_POST['action2']) ? $_POST['action2'] : null;
    $requested_action = isset($_POST['action']) ? $_POST['action'] : null;
    
    if($requested_action == 'delete' || $requested_action2 == 'delete') {
        
        $subscribers = array_flip(get_option('deals_subscribed_emails'));
        
        foreach($_POST['subscriber'] as $i => $value) {            
            unset($subscribers[$value]);
        }
        
        $subscribers = array_flip($subscribers);
        update_option(deals_subscribed_emails, $subscribers);
        
        wp_redirect(admin_url('/edit.php?post_type=daily-deals&page=deal-subscribers'));
        exit();        
        
    }
    
}


/**
 *
 * Preparing table data
 * @param Deals_List_Table $obj 
 */
function deals_subscriber_table_prepare(Deals_List_Table $obj) { 
    
    $obj->process_bulk_action();
    
    $per_page = $obj->data_per_page;

    $columns = $obj->get_columns();
    $hidden = array();
    $sortable = $obj->get_sortable_columns();

    $obj->_column_headers = array($columns, $hidden, $sortable);    

    $data = $obj->data;

    function usort_reorder($a,$b){
        
        $order = isset($_REQUEST['order']) && (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
        $result = isset($a['email']) ? strcmp($a['email'], $b['email']) : $a['email']; //Determine sort order
        return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        
    }
    usort($data, 'usort_reorder');
    
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
 * Print extra table navigation
 * 
 * @param string $which
 * @return void
 */
function deals_extra_table_nav($which) {
    
   if( ('top' == $which) OR ('bottom' == $which)) {    
        echo '<div class="alignleft actions" style="padding-top: 6px;"><a href="#" style="display: inline;" id="js-import-csv" class="reset-button button-secondary">Import</a> <a href="'.admin_url('/edit.php?post_type=daily-deals&page=deal-subscribers&action=export').'" id="export-csv" class="button-primary">Export</a></div>';     
   }
   
}

/**
 *
 * Generate column data
 * 
 * @param string $item
 * @param string $column_name
 * @return string 
 */
function deals_subscriber_column_default($item,$column_name) {
    
    switch($column_name) {                

        case 'email':
            echo $item['email'];
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
function deals_subscriber_column_cb($item) {
    
    global $subscriberTable;
    
    echo sprintf(
        '<input type="checkbox" name="%1$s[]" value="%2$s" />',
        /*$1%s*/ $subscriberTable->_args['singular'], 
        /*$2%s*/ $item['email']               
    );
    
}

add_action('admin_init', 'deals_export_csv');
/**
 * Export subscribers to csv
 * @return void
 */
function deals_export_csv(){    
    
    if(isset($_FILES['subscriber-import-file']) && $_FILES['subscriber-import-file']['error'] == 0) {

        $file_upload_name = $_FILES['subscriber-import-file']['name'];
        $file_upload_type = $_FILES['subscriber-import-file']['type'];
        $file_upload_tmp  = $_FILES['subscriber-import-file']['tmp_name'];

        $tmp_path = DEALS_URL.'deals-assets/tmps/';
        if(!is_dir($tmp_path)){
            mkdir($tmp_path, 0755);
        }

        $allowedExt = array('csv');
        $file_upload_get_ext = explode('.',$file_upload_name);
        $file_upload_ext = end($file_upload_get_ext);

        if( in_array($file_upload_ext,$allowedExt) && is_uploaded_file($file_upload_tmp) ) {

            if( is_writable($tmp_path) ) {

                $new_file_path = $tmp_path.$file_upload_name;
                move_uploaded_file($file_upload_tmp,$new_file_path);
                @chmod($new_file_path,0777);

                if( file_exists($new_file_path) && is_readable($new_file_path) ) {

                    $fp = fopen($new_file_path,'r');
                    $result = array();
                    while( ($data = fgetcsv($fp,1000,',')) !== false ) {                

                        $email = $data[0];
                        $validate_email = filter_var($email,FILTER_VALIDATE_EMAIL);

                        if($validate_email) {
                            $result[] = $email;    
                        }                

                    }

                }

                $subscriber_emails = get_option('deals_subscribed_email');                        
                if(!empty($subscriber_emails)) {

                    $new_subscriber = array();
                    foreach($result as $email) {

                        if(!in_array($email,$subscriber_emails)) {
                            $new_subscriber[] = $email;
                        }

                    }

                    array_push($subscriber_emails,$new_subscriber);
                    update_option('deals_subscribed_emails',$subscriber_emails);                    

                }else{
                    update_option('deals_subscribed_emails',$result);                    
                }

                //delete file
                @unlink($new_file_path);

                ob_start();
                wp_redirect(admin_url('/edit.php?post_type=daily-deals&page=deal-subscribers'));

            }else{
                echo 'dir not writable';
            }
        }else{
            echo 'file error';
        }

    }
    
    if( isset($_GET['action']) && ($_GET['action'] == 'export') AND ($_GET['post_type'] == 'daily-deals') AND ($_GET['page'] == 'deal-subscribers')){
        
        // If there is more than one email, add the new email to the array
        $emails = get_option('deals_subscribed_emails');
        
        // array_push($stack, maybe_unserialize($emails));

        //update the option with the new set of emails
        // update_option('deals_subscribed_emails', $stack);
        
        $fp = fopen(DEALS_FORM_DIR.'subscribers.csv', 'w');

        //write in a format that CSV intepreters can understand
        foreach($emails as $line){
                $val = explode(",",$line);
                fputcsv($fp, $val);
        }

        //close file
        fclose($fp);
        //echo DEALS_DIR;
        wp_redirect(DEALS_DIR.'deals-template/form/subscribers.csv');
        exit();
    }
    
}

/**
 * Subscribers report
 * 
 * @global object $subscriberTable
 * @return void
 */
function deals_subscribers() {
    
    global $subscriberTable;
    
    
    $datadisplay = array();            
    $subscribers = get_option('deals_subscribed_emails');
    
    if( ! empty($subscribers) ) {
        
        foreach($subscribers as $subscriber) {
            
            $datadisplay[] = array(
                                   'email' => $subscriber);
            
        }
        
    }
    
    echo '<form action="'.admin_url('/edit.php?post_type=daily-deals&page=deal-subscribers').'" method="post">';    
    $subscriberTable->data = $datadisplay;
    $subscriberTable->prepare_items();
    $subscriberTable->display();
    echo '</form>';
    
    ?>
    <div id="js-overlay" style="width:100%; height:100%;filter:alpha(opacity=50);opacity: 0.5;background:#000; position:absolute; top:0; left:0; z-index:3000;display:none;"></div>
    <div id="subscriber-import" style="
         background:#eee;
         width:300px;
         position:absolute; 
         z-index:5000; 
         display:none;
         padding: 10px;
         -webkit-box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
         -moz-box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
         -moz-border-radius: 5px;
         -webkit-border-radius: 5px;">

        <div id="js-subscriber-close">
            <a href="#" id="js-close">[close]</a>            
        </div>
        <hr />
        <div id="js-subscriber-content">
            <h3>Import CSV</h3>
            <form enctype="multipart/form-data" method="POST" id="subscriber-form" action="">
                <input type="file" name="subscriber-import-file" id="subscriber-import-file" />
                <input type="submit" name="submit" value="Upload" />
            </form>
        </div>

    </div>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            
            jQuery('#js-import-csv').click(function() {
                jQuery('#subscriber-import-file').val('');
                showModal('subscriber-import');
                return false;
            });
            
            jQuery('#js-subscriber-close a#js-close').click(function() {
               closeModal('subscriber-import');
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