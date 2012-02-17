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

//get paypal test option
$option_sandbox = get_option('deals_paypal_sandbox');
$paypal_test = $option_sandbox == 1 ? true : false;
//$paypal_test = isset($options['paypal_is_test']) && !empty($options['paypal_is_test']) ? true : false;

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
$log_confirm = $confirm_paypal_transaction == true ? 'completed' : 'invalid';
paypal_log($log_confirm);

if($confirm_paypal_transaction) {
        
    require_once DEALS_PAYMENT_DIR.'abstract-payment-gateway.php';
    require_once DEALS_PAYMENT_DIR.'default/class-payment-paypal.php';
        
    $user_id = $_GET['user_id'];
    $item_id = $_GET['item_id'];
    
    $sale_id = get_option('_deals_sales_used_'.$item_id.'_'.$user_id.'_paypal');    
    paypal_log($sale_id);    
    
    $paypalClass = new Payment_Paypal();
    
    $check_class = is_a($paypalClass,'Payment_Paypal') ? '$paypalClass is Payment_Paypal' : '$paypalClass not object';
    paypal_log($check_class);
    
    $paypalClass->update_transaction_data($sale_id);
    $paypalClass->send_invoice();
    
    delete_option('_deals_sales_used_'.$item_id.'_'.$user_id.'_paypal');
    
}