<?php

deals_secure();

require_once 'abstract-payment-gateway.php';
require_once 'class-payment-options.php';
require_once 'class-payment-gateways.php';
require_once 'default/class-payment-bank.php';
require_once 'default/class-payment-paypal.php';

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

$payments = Payments::get_instance();
$payments->core('Options')->register_hook_options();