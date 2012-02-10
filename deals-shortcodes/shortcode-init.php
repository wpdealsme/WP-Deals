<?php
/**
 * Shortcodes init
 * 
 * Init main shortcodes, and add a few others such as recent products.
 *
 * @package     WP Deals
 * @category	Shortcode
 * @author	WP Deals
 */

//secure included files
deals_secure();

// include file
include_once('shortcode-user_account.php');
include_once('shortcode-user_history.php');


// shorcode
add_shortcode('wpdeals_user_history','get_deals_user_history');
add_shortcode('wpdeals_user_profile', 'get_deals_user_account');
add_shortcode('thanksdeal', 'get_deals_thanks_deal');
