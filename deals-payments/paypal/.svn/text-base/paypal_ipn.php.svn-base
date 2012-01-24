<?php

global $paypal_wp_url;
    
// Get the path to the root.
$full_path = __FILE__;
$path_bits = explode( 'wp-content', $full_path );    
$paypal_wp_url = $path_bits[0];

// Require WordPress bootstrap.
require_once( $paypal_wp_url . '/wp-load.php' );

//paypal functions
require_once 'paypal_functions.php';

//load paypal query
require_once 'paypal_query.php';

//get paypal test option
$options = get_option('dealoptions');
$paypal_test = isset($options['paypal_is_test']) && !empty($options['paypal_is_test']) ? true : false;

/*
 * set paypal base url
 */
if($paypal_test) {
    $paypalBaseUrl = 'ssl://www.sandbox.paypal.com';    
}else{
    $paypalBaseUrl = 'ssl://www.paypal.com';    
}

//get verification from paypal
$confirm_paypal_transaction = paypal_check_transaction($_POST, $paypalBaseUrl);
$log_confirm = $confirm_paypal_transaction == true ? 'transaction confirmed' : 'transaction invalid';
paypal_log($log_confirm);

if($confirm_paypal_transaction) {//if transaction valid and verified
    
    $transaction_id = $_POST['txn_id'];
    $item_id = $_GET['item_id'];
    $user_id = $_GET['user_id'];
    
    paypal_log($item_id.'-'.$user_id.'-'.$transaction_id);
    
    $is_transaction_exists = paypal_query_check_txn_id($transaction_id);
    
    if(!$is_transaction_exists) {
        
        paypal_log('start save transaction - '.$item_id);
        
        $payment_status = $_POST['payment_status'] == 'Completed' ? 1 : 0;
        $convert_time = strtotime(urldecode($_POST['payment_date']));
        $payment_date = date('c',$convert_time);
        
        paypal_query_add_sales(array(
            'item_id' => $item_id,
            'user_id' => $user_id,
            'transaction_id' => $transaction_id,
            'payment_status' => $payment_status,
            'payment_date' => $payment_date,
            'post' => $_POST
        ));
        
        $sales_id = paypal_query_get_latest_sales_id();
        
        paypal_query_add_invoice(array(
            'sales_id' => $sales_id,
            'payment_status' => $payment_status
        ));
        
        deals_minus_inventory($item_id);
        
        paypal_log('decrease inventory for - '.$item_id);
        paypal_log('end save transaction - '.$item_id);
        
    }else{
        paypal_log('transaction id : '.$transaction_id.' has been requested twice, at :'.date('c'));
    }
    
}

paypal_log('transaction end for '.$_GET['item_id']);
paypal_log('=======================================');


