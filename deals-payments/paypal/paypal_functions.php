<?php

define('PAYPAL_ENABLE_LOG',false);

/**
 *
 * Confirm paypal transaction
 * 
 * @param array $post
 * @param string $base_url
 * @return bool 
 */
function paypal_check_transaction($post, $base_url) {
    
    if(empty($post) || is_null($post)) {
        return false;
    }

    $req = 'cmd=_notify-validate';
    foreach ($post as $key => $value) {
        $value = urlencode(stripslashes($value));
        $req .= "&$key=$value";
    }

    $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

    $fp = fsockopen($base_url, 443, $errno, $errstr, 30);

    if (!$fp) {
        return false;
    } else {

        fputs($fp, $header . $req);
        while (!feof($fp)) {
            $res = fgets($fp, 1024);
            if (strcmp($res, "VERIFIED") == 0) {
                return true;
                break;
            } else if (strcmp($res, "INVALID") == 0) {
                return false;
                break;
            }
        }                
        
    }
}

/**
 *
 * Create paypal file log
 * 
 * @global string $paypal_wp_url
 * @param string $messages 
 */
function paypal_log($messages) {
    
    global $paypal_wp_url;
    $log_path = $paypal_wp_url.'wp-content/plugins/wp-deals/deals-assets/logs/';
    $log_file_path = $log_path.'paypal-log';    
    
    if( PAYPAL_ENABLE_LOG ) {
        
        $fp = fopen($log_file_path,'a+');
        $messages = '[ '.date('d-m-Y H:i:s').' ] > '.$messages."\n"; 
        
        if($fp) {
            flock($fp,LOCK_EX);
            fwrite($fp,$messages);        
            flock($fp,LOCK_UN);
        }
        
        fclose($fp);
        
    }
    
}