<?php

deals_secure();

class Payment_Paypal extends Payment_Gateway_Abstract {
    
    /**
     * @access public
     */
    public $id = 'paypal';
    
    /**
     * @access public     
     */
    public $name = 'Paypal';        
    
    /**
     * @access public
     */
    public $desc = 'Payment process using paypal payment gateway';
    
    public function get_payment_template() {
        return DEALS_PAYMENT_DIR.'default/paypal/paypal_payment_template.php';
    }
    
    public function get_path() {
        return DEALS_PAYMENT_DIR.'default/class-payment-paypal.php';
    }
    
    public function admin_options() {
        
        $options['paypal_payment_gateways'] = apply_filters('deals_paypal_payment_gateways', array(
		
            array(	'name' => __( 'Paypal Payment Gateways', 'wpdeals' ),
                  'type' => 'title',
                  'desc' => __('This section lets you manage your paypal configurations. ', 'wpdeals'),
                  'id' => 'deals_paypal_payment_gateways' ),
            
            array(
                'name' => __('Paypal Email','wpdeals'),                
                'desc' => __('Your paypal email', 'wpdeals'),
                'id' => 'deals_paypal_email',
                'std' =>'',
                'type' => 'text'
            ),                        
            
            array(                            
                'id' => 'form_sub_action',
                'std' => $this->id,
                'type' => 'hidden'
            ),
            
            array(
                'name' => __('Enable Test Mode','wpdeals'),                
                'desc' => __('enable paypal sandbox for testing payment transactions', 'wpdeals'),
                'id' => 'deals_paypal_sandbox',
                'std' =>'',
                'type' => 'checkbox'
            ),
            
            array(
                'type' => 'sectionend',
                'id' => 'deals_paypal_payment_gateways'
            )
        
        )); // End email settings
        
        deals_admin_fields($options['paypal_payment_gateways']);
        
    }
    
    public function save_options($data) {
        
        if(!empty($data) && is_array($data)) {
            
            foreach($data as $key => $value) {
                update_option($key,$value);
            }
            
            if(!array_key_exists('deals_paypal_sandbox',$data)) {
                update_option('deals_paypal_sandbox',0);
            }
            
        }
        
    }
    
}

// add the payment method transfer bank
add_filter('deals_payment_methods', 'gateway_paypal', 1);
function gateway_paypal($methods) {
    
    $paypal = new Payment_Paypal();
    
    $avai_payments = array();
    $avai_payments =  array(
        'name' => $paypal->name,
        'path' => $paypal->get_path()        
    );        
    
    $methods[$paypal->id] = $avai_payments;
    
    return $methods;
    
}