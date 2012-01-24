<?php

//secure included files
deals_secure();

require_once 'deals_list_table.php';

global $salesTable;

/*
 * generate list table for sale reports
 */
$salesTable = new Deals_List_Table(array(
    'singular_name' => 'sale',
    'plural_name' => 'sales',
    'ajax_enabled' => true
));

/*
 * setup callback
 */
$salesTable->prepare_item_callback = 'deals_sale_table_prepare';
$salesTable->column_default_callback = 'deals_sale_column_default';
$salesTable->column_cb_callback = 'deals_sale_column_cb';
$salesTable->bulk_action_callback = 'deals_sale_bulk_action';

/*
 * setup columns 
 */
$salesTable->register_columns = array(
    'cb' => '<input type="checkbox" />',
    'id' => 'ID',
    'paypal_id' => 'Paypal Transaction ID',
    'name' => 'Username',        
    'item' => 'Item Name',
    'buyer_email' => 'Buyer Email',           
    'total_price' => 'Amount',
    'status' => 'Payment Status',
    'date' => 'Buy Date'
);
/*
 setup bulk actions
 */
$salesTable->register_bulk_actions = array(
    'delete'    => 'Delete'
);
/*
 setup sortable columns
 */
$salesTable->register_sortable_columns = array(
    'date'  => array('date',true)
);

/**
 * Run bulk action process
 * 
 * @return void
 */
function deals_sale_bulk_action() {
    
    global $wpdb;
    
    $requested_action2 = isset($_POST['action2']) ? $_POST['action2'] : null;
    $requested_action = isset($_POST['action']) ? $_POST['action'] : null;
    
    if($requested_action2 == 'delete' || $requested_action == 'delete') {
        
        foreach($_POST['sale'] as $sale_id) {
            
            $id = intval($sale_id);
            $query = $wpdb->prepare('DELETE FROM '.$wpdb->prefix.'wpdeals_sales WHERE id="'.$id.'"');
            $wpdb->query($query);
            
        }                
                
        wp_redirect(admin_url('/edit.php?post_type=daily-deals&page=deal-sales'));
        exit();        
        
    }
    
}

/**
 *
 * Preparing table data
 * @param Deals_List_Table $obj 
 */
function deals_sale_table_prepare(Deals_List_Table $obj) {
    
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
function deals_sale_column_default($item,$column_name) {
    
    switch($column_name) {
            
        case 'id':
            echo '<a href="'.admin_url('/?page=deal-sales-detail&sale_id='.$item['id']).'">'.$item['id'].'</a>';
            break;

        case 'paypal_id':
            echo $item['paypal_id'];
            break;

        case 'name':
            echo '<a href="'.admin_url('/user-edit.php?user_id='.$item['user_id']).'">'.$item['name'].'</a>';
            break;

        case 'item':
            echo '<a href="'.admin_url('/post.php?post='.$item['item_id'].'&action=edit').'">'.$item['item'].'</a>';
            break;

        case 'buyer_email':
            echo $item['buyer_email'];
            break;

        case 'total_price':
            echo '$'.$item['total_price'];
            break;

        case 'status':
            echo $item['status'] == 1 ? 'completed' : '<span style="color:#ff0000">pending</span>';
            break;

        case 'date':
            echo $item['date'];
            break;

    }
    
}

/**
 *
 * Generate checkbox column value
 * 
 * @global Deals_List_Table $salesTable
 * @param string $items
 * @return string 
 */
function deals_sale_column_cb($item) {
    
    global $salesTable;
    
    echo sprintf(
        '<input type="checkbox" name="%1$s[]" value="%2$s" />',
        /*$1%s*/ $salesTable->_args['singular'], 
        /*$2%s*/ $item['id']               
    );
    
}

/**
 *
 * Prepare data and display table
 * 
 * @global Deals_List_Table $salesTable 
 */
function deals_sales_report() {            
    
    global $salesTable,$wpdb;
    
    $datadisplay = array();
    if(isset($_GET['order']) && $_GET['orderby'] == 'date') {
        $datadb = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wpdeals_sales ORDER BY buy_date '.$_GET['order']);
    }else{
        $datadb = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wpdeals_sales ORDER BY id DESC');
    }     
    
    if(!empty($datadb)) {
        
        foreach($datadb as $single) {
                
            $username = $wpdb->get_var('SELECT user_nicename FROM '.$wpdb->users.' WHERE ID="'.$single->user_id.'"');
            $title_item = $wpdb->get_var('SELECT post_title FROM '.$wpdb->posts.' WHERE ID="'.$single->item_id.'"');

            $datadisplay[] = array(
                'id' => $single->id,
                'user_id' => $single->user_id,
                'paypal_id' => $single->transaction_id,
                'name' => $username,
                'item_id' => $single->item_id,
                'item' => $title_item,
                'buyer_email' => $single->buyer_email,
                'total_price' => $single->total_price,
                'status' => $single->payment_status,
                'date' => $single->buy_date
            );

        }  
        
    }
    //deals_debug($datadisplay);
    echo '<form action="'.admin_url('/edit.php?post_type=daily-deals&page=deal-sales').'" method="post">';
    $salesTable->data = $datadisplay;
    $salesTable->prepare_items();
    $salesTable->display();
    echo '</form>';
    
}

/**
 *
 * Display sale detail data
 * 
 * @global object $wpdb 
 */
function deals_sales_detail() {
    
    global $wpdb;
    
    $sale_id = intval($_GET['sale_id']);
    $salesData = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wpdeals_sales WHERE id="'.$sale_id.'"');
    $salesRelData = array();

    if(!empty($salesData)) {

        foreach($salesData as $sale) {

            $username = $wpdb->get_var('SELECT user_nicename FROM '.$wpdb->users.' WHERE ID='.$sale->user_id);
            $item_title = $wpdb->get_var('SELECT post_title FROM '.$wpdb->posts.' WHERE ID='.$sale->item_id);

            $salesRelData['username'] = $username;
            $salesRelData['item_title'] = $item_title;

        }

    }
    
    ?>
    <table class="form-table">

        <tbody>
            <tr valign="top">
                <th scrope="row">
                    <label>User Buyer</label>
                </th>
                <td>
                    <span class="regular-text"><?php echo $salesRelData['username']; ?></span>
                </td>
            </tr>                
            <tr valign="top">
                <th scrope="row">
                    <label>Item</label>
                </th>
                <td>
                    <span class="regular-text"><?php echo $salesRelData['item_title']; ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scrope="row">
                    <label>Buyer email</label>
                </th>
                <td>
                    <span class="regular-text"><?php echo $salesData[0]->buyer_email; ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scrope="row">
                    <label>Total Price</label>
                </th>
                <td>
                    <span class="regular-text">$ <?php echo $salesData[0]->total_price; ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scrope="row">
                    <label>Payment Status</label>
                </th>
                <td>
                    <span class="regular-text"><?php echo $salesData[0]->payment_status == 1 ? 'complete' : 'pending'; ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scrope="row">
                    <label>Transaction Date</label>
                </th>
                <td>
                    <span class="regular-text"><?php echo $salesData[0]->buy_date; ?></span>
                </td>
            </tr>                
        </tbody>

    </table>
    <?php
    
}