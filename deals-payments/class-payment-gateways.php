<?php

deals_secure();

class Payment_Gateways {
    
    private $_payments = array();
    private $_active_payments = array();
    
    public function __construct() {
        
        $this->_active_payments = get_option('deals_payments_used');
        $this->_payments = get_option('deals_payments');
        
    }
    
    public function get_available() {
        return $this->_active_payments;
    }
    
    public function has_multiple() {
        
        $total_active_payments = count($this->_active_payments);
        if($total_active_payments > 1) {
            return true;
        }else{
            return false;
        }
        
    }        
    
    public function get_class($id) {
        
        $filepath = $this->_payments[$id]['path'];
        if(file_exists($filepath)) {
            
            require_once($filepath);
            $payment_class_name = 'Payment_'.ucwords(str_replace('_',' ',strtolower($id)));
            $payment_class_name = str_replace(' ','_',$payment_class_name);
            
            if(class_exists($payment_class_name)) {
                
                $payment_class = new $payment_class_name();
                if(is_subclass_of($payment_class,'Payment_Gateway_Abstract')) {
                    return $payment_class;
                }
                
            }
            
        }
        
        return null;
        
    }
    
    public function choosen($id) {
        
        if(array_key_exists($id,$this->_payments)) {
            return $this->get_class($id);
        }
        
        return null;
        
    }
    
}