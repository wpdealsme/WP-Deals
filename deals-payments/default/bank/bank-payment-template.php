<?php 
global $wp_rewrite;

$item_id    = get_query_var('deal_buy_id');
$user_id    = get_current_user_id();
$sale_id    = get_option('_deals_sales_used_'.$item_id.'_'.$user_id.'_bank');
$link       = get_permalink(get_option('deals_page_thanks_post_id'));  
    
if ( !$wp_rewrite->using_permalinks() ) {
    $link   = $link . '&deal_buy_id='.$item_id.'&user_id='.$user_id.'&payment_method=transfer';
} elseif ( $wp_rewrite->using_permalinks() ) {
    $link   = $link . '?deal_buy_id='.$item_id.'&user_id='.$user_id.'&payment_method=transfer';
}

/*
Send invoices to customers
*/
//require_once DEALS_PAYMENT_DIR.'abstract-payment-gateway.php';
//require_once DEALS_PAYMENT_DIR.'default/class-payment-bank.php';

$bankGateway = new Payment_Bank();
$bankGateway->update_transaction_data($sale_id, 'pending');
//$bankGateway->send_invoice();

wp_safe_redirect($link);
exit;
?>