<?php

/**
 * Just to test global variable $wpdb
 * @global object $wpdb
 */
function paypal_query_check_wpdb() {
    
    global $wpdb;
    
    $id = '123445';
    $query = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wpdeals_sales');
    $is_txn_exists = $wpdb->get_row($query);
    
    echo '<pre>';
    var_dump($is_txn_exists);exit();
    echo '</pre>';
    
}

/**
 * Check txn id
 * 
 * @global object $wpdb
 * @param int $id
 * @return bool
 */
function paypal_query_check_txn_id($id) {
    
    global $wpdb;
    
    $query = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wpdeals_sales WHERE transaction_id="'.$id.'"');
    $is_txn_exists = $wpdb->get_row($query);
    return !empty($is_txn_exists) ? true : false;
    
}

/**
 * Add sale data
 * @global $wpdb
 * @return bool|void
 */
function paypal_query_add_sales($args) {
    
    global $wpdb;
    
    if(!is_array($args) || empty($args)) {
        return false;
    }
    
    extract($args);
    
    $queryInputSales = 'INSERT INTO '.$wpdb->prefix.'wpdeals_sales 
        VALUES("","'.$user_id.'","'.$item_id.'",
            "'.$transaction_id.'","'.urldecode($post['payer_email']).'","'.$post['quantity'].'",
                "'.$post['payment_gross'].'","'.base64_encode(serialize($post)).'","'.$payment_status.'",
                    "'.$payment_date.'")';
    
    $queryInputSales = $wpdb->prepare($queryInputSales);
    $wpdb->query($queryInputSales);
    
}

/**
 * Add invoice data
 * 
 * @global object $wpdb
 * @param array $args
 * @return bool|void
 */
function paypal_query_add_invoice($args) {
    
    global $wpdb;
    
    if(!is_array($args) || empty($args)) {
        return false;
    }
    
    extract($args);
    
    $queryInputInvoice = 'INSERT INTO '.$wpdb->prefix.'wpdeals_invoices VALUES("","'.$sales_id.'",
                "'.$payment_status.'","'.date('c').'")';        
    $queryInputInvoice = $wpdb->prepare($queryInputInvoice);
    
    $wpdb->query($queryInputInvoice);
    
}

/**
 * Get latest sale id
 * 
 * @global $wpdb
 * @return int
 */
function paypal_query_get_latest_sales_id() {
    
    global $wpdb;
    
    $sales_id = $wpdb->get_var($wpdb->prepare('SELECT id FROM '.$wpdb->prefix.'wpdeals_sales ORDER BY id DESC LIMIT 1'));
    return $sales_id;
    
}