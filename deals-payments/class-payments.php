<?php

deals_secure();

require_once 'abstract-payment-gateway.php';
require_once 'class-payment-options.php';
require_once 'class-payment-gateways.php';

class Payments {
    
    /**
     * @access private
     * @staticvar null,object
     */
    static private $_object = null;
    
    /**
     * @access private
     */
    private function __construct() {
        //pass
    }
    
    /**
     * Create object class - Singleton
     *
     * @access public
     * @return null|object
     * @static
     */
    static public function get_instance() {
        
        if(is_null(self::$_object)) {
            self::$_object = new Payments();
        }
        
        return self::$_object;
        
    }        
    
    /**
     * Get core classes
     *
     * @access public
     * @param string $core
     * @return object|null
     */
    public function core($core) {
        
        $core_class = 'Payment_'.ucfirst(strtolower($core));
        if(class_exists($core_class)) {
            return new $core_class();
        }
        
    }
    
}


//create payment object
$payment = Payments::get_instance();

//only load in admin page
if(is_admin()) {
    
    //load payments option hook
    $payment->core('Options')->register_hook_options();
    
}