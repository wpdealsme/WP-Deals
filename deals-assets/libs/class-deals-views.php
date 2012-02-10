<?php

//secure included files
deals_secure();

/**
 * @author WPDeals (wpdeals.me)
 * @package Deals Lib
 * @version 1.0
 * @link http://wpdeals.me
 */
class Deals_Views {
    
    /**
     * Get view template
     * 
     * @param string $filepath
     * @param array $vars
     * @return null|string
     */
    static public function get($filepath,$vars=array()) {
        
        $ob_contents = null;
        if(file_exists($filepath)) {
            
            ob_start();
            
            if(is_array($vars) && !empty($vars)) : extract($vars); endif;
            require_once $filepath;
            $ob_contents = ob_get_contents();
            ob_end_flush();
            
        }
        
        return $ob_contents;
        
    }
    
}