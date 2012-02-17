<?php

deals_secure();

class Payment_Bank extends Payment_Gateway_Abstract {
    
    public $id = 'bank';
    public $name = 'Bank';
    public $desc = 'payment gateway using manual bank transfer';
    
    public function get_path() {
        return DEALS_PAYMENT_DIR.'default/class-payment-bank.php';
    }
    
    public function get_payment_template() {
        return DEALS_PAYMENT_DIR.'default/bank/bank-payment-template.php';
    }
    
    public function admin_options() {
        
        $options['bank_payment_gateways'] = apply_filters('deals_bank_payment_gateways', array(
		
            array(	
                  'name' => __( 'Bank Payment Gateways', 'wpdeals' ),
                  'type' => 'title',
                  'desc' => __('This section lets you manage your bank transfer configurations. ', 'wpdeals'),
                  'id' => 'deals_bank_payment_gateways' 
            ),
			
            array(
                'name' => __('Bank Name','wpdeals'),                
                'desc' => __('Your bank name', 'wpdeals'),
                'id' => 'deals_bank_name',
                'std' =>'',
                'type' => 'text'
            ),
		
            array(	
                  'name' => __( 'Payment Description', 'wpdeals' ),
                  'desc' => __('This section lets costumers see your bank transfer descriptions.', 'wpdeals'),
                  'id' => 'deals_bank_payment_desc',
                  'std' => __('Please transfer a nominal amount of this item, then please give us a confirmation using our confirmation page, and do not loose your Sales ID data', 'wpdeals'),
                  'type' => 'textarea',
                  'css' => 'width: 500px; height:70px;'
            ),
            
            array(
                'name' => __('Account Name','wpdeals'),                
                'desc' => __('Your bank account name', 'wpdeals'),
                'id' => 'deals_bank_account_name',
                'std' =>'',
                'type' => 'text'
            ),
            
            array(
                'name' => __('Account Number','wpdeals'),                
                'desc' => __('Your bank account number', 'wpdeals'),
                'id' => 'deals_bank_account_number',
                'std' =>'',
                'type' => 'text'
            ),
            
            array(
                'name' => __('IBAN','wpdeals'),                
                'desc' => __('For international payments', 'wpdeals'),
                'id' => 'deals_bank_account_iban',
                'std' =>'',
                'type' => 'text'
            ),
            
            array(
                'name' => __('BIC (formerly Swift)','wpdeals'),                
                'desc' => __('For international payments', 'wpdeals'),
                'id' => 'deals_bank_account_bic',
                'std' =>'',
                'type' => 'text'
            ), 
            
            array(                            
                'id' => 'form_sub_action',
                'std' => $this->id,
                'type' => 'hidden'
            ),                        
            
            array(
                'type' => 'sectionend',
                'id' => 'deals_bank_payment_gateways'
            )
        
        )); // End email settings
        
        deals_admin_fields($options['bank_payment_gateways']);
        
    }
    
    public function save_options($data) {
        
        if(!empty($data) && is_array($data)) {
            
            foreach($data as $key => $value) {
                update_option($key,$value);
            }                        
            
        }
        
    }
    
}


// add the payment method transfer bank
add_filter('deals_payment_methods', 'gateway_bank', 1);
function gateway_bank($methods) {
    
    $bank = new Payment_Bank();
    
    $avai_payments = array();
    $avai_payments =  array(
        'name' => $bank->name,
        'path' => $bank->get_path()        
    );        
    
    $methods[$bank->id] = $avai_payments;
    
    return $methods;
    
}

// hook shortcode [thanksdeals]
add_filter('deals_payment_message', 'gateway_bank_message', 1);
function gateway_bank_message($method){
    
    if($method == 'transfer'){
    
        $item_id = get_query_var('deal_buy_id');
        $user_id = get_current_user_id();
        $sale_id = get_option('_deals_sales_used_'.$item_id.'_'.$user_id.'_bank');
        $itemdata = get_post($item_id);
		
        $output  = '';
        $output .= '<h3>' . sprintf(__('Your Sales ID #%d', 'wpdeals') , $sale_id) . '</h3>';

        $output .= '<h4>' . __('Item Details', 'wpdeals') . '</h4>';
        $output .= '<p>'. sprintf(__('Your Item : <a href="%s">%s</a>', 'wpdeals'), get_permalink($itemdata->ID), $itemdata->post_title) . '<br/>' .
                          sprintf(__('Your Item Price : %s', 'wpdeals'), deals_discount(true, $item_id)) .'</p>';

        $output .= '<h4>' . __('Our Bank Details', 'wpdeals') . '</h4>';
        $output .= '<p>'. sprintf(__('Bank Name : %s', 'wpdeals'), deals_get_option('bank_name')) . '<br/>' .
                          sprintf(__('Bank Account Name : %s', 'wpdeals'), deals_get_option('bank_account_name')) . '<br/>' .
                          sprintf(__('Bank Account Number : %s', 'wpdeals'), deals_get_option('bank_account_number')) . '<br/>' .
                          sprintf(__('IBAN : %s', 'wpdeals'), deals_get_option('bank_account_iban')) . '<br/>' .
                          sprintf(__('BIC : %s', 'wpdeals'), deals_get_option('bank_account_bic')) .'</p>';

        $output .= '<h4>' . __('Todo Next', 'wpdeals') . '</h4>';
        $output .= '<p>' . deals_get_option('bank_payment_desc') . '</p>';

        return $output;
        
    }
    
    return __('Compelete', 'wpdeals');
        
}