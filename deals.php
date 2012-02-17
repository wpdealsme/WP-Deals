<?php

/*
  Plugin Name: WP Deals
  Plugin URI: http://wpdeals.me/
  Description: WP Deals is a special-for-deals plugin where you can make, post, and share deals all around the people just by downloading this plugin and make your own deals-site with it. WP Deals is one of Tokokoo’s network, so after downloading the plugin, then you can explore your site with the nice and gorgeous themes from WP Deals themes.
  Version: 1.1.1
  Author: WP Deals
  Author URI: http://wpdeals.me/
 */
ini_set('display_errors','On');

define('DEALS_VERSION', '1.1.1');

// defining here
define('DEALS_PLUGIN_PATH',     plugin_dir_path(__FILE__));
define('DEALS_PLUGIN_URL',      plugins_url(plugin_basename(dirname(__FILE__))));
define('DEALS_PLUGIN_FILE',     DEALS_PLUGIN_PATH   . 'deals.php');
define('DEALS_ADMIN_URL',       DEALS_PLUGIN_URL    . '/deals-admin/');
define('DEALS_ADMIN_DIR',       DEALS_PLUGIN_PATH   . '/deals-admin/');
define('DEALS_CLASSES',         DEALS_PLUGIN_PATH   . '/deals-classes/');
define('DEALS_ASSETS',          DEALS_PLUGIN_URL    . '/deals-assets/');
define('DEALS_ASSETS_PATH',     DEALS_PLUGIN_PATH   . '/deals-assets/');
define('DEALS_JS',              DEALS_ASSETS        . 'js/');
define('DEALS_CSS',             DEALS_ASSETS        . 'css/');
define('DEALS_IMG',             DEALS_ASSETS        . 'images/');
define('DEALS_TEMPLATE_DIR',    DEALS_PLUGIN_PATH   . 'deals-template/');
define('DEALS_TEMPLATE_URL',    DEALS_PLUGIN_URL    . 'deals-template/');
define('DEALS_FORM_DIR',        DEALS_TEMPLATE_DIR  . 'form/');
define('DEALS_PAYMENT_DIR',     DEALS_PLUGIN_PATH   . 'deals-payments/');
define('DEALS_LOG_DIR',         DEALS_PLUGIN_PATH   . 'deals-assets/logs/');
define('DEALS_VENDOR_DIR',      DEALS_PLUGIN_PATH   . 'deals-assets/vendors/');
define('DEALS_LIB_DIR',         DEALS_PLUGIN_PATH   . 'deals-assets/libs/');
define('DEALS_ENABLE_LOG',      false);

// load class
require_once DEALS_CLASSES.'class-deals-error.php';

global $deals_error;
$deals_error    = new Deals_Error();

require_once 'deals_functions.php';
require_once 'deals-admin/admin-functions.php';

// load file
if(is_admin()):
    require_once 'deals-admin/admin-init.php';    
else:
    require_once 'deals_templates.php';
    require_once 'deals_templates_actions.php';
    require_once 'deals_templates_functions.php';
    require_once 'deals-shortcodes/shortcode-init.php';    
endif;

require_once 'deals_init.php';
require_once 'deals-payments/class-payments.php';
require_once 'deals-widgets/widgets-init.php';


/**
 * Output generator to aid debugging
 **/
add_action('wp_head', 'deals_generator');

function deals_generator() {
	echo "\n\n" . '<!-- WP Deals Version -->' . "\n" . '<meta name="generator" content="WP Deals ' . DEALS_VERSION . '" />' . "\n\n";
}


/**
 * Wp-deals conditionals
 **/
function is_wp_deals() {
    // Returns true if on a page which uses WP-deals templates (cart and checkout are standard pages with shortcodes and thus are not included)
    if (is_deals_page() || is_deal_category() || is_deal_tag() || is_deal() || is_thanks() || is_history() || is_account_deal() || is_feature_deal())
        return true; 
    else
        return false;
}

if (!function_exists('is_deals_page')) {

    function is_deals_page() {
        if (is_post_type_archive('daily-deals') || is_page(get_option('deals_page_post_id')))
            return true; 
        else
            return false;
    }

}
if (!function_exists('is_deal_category')) {

    function is_deal_category() {
        return is_tax('deal-categories');
    }

}
if (!function_exists('is_deal_tag')) {

    function is_deal_tag() {
        return is_tax('deal-tags');
    }

}
if (!function_exists('is_deal')) {

    function is_deal() {
        return is_singular('daily-deals');
    }

}
if (!function_exists('is_thanks')) {

    function is_thanks() {
        return is_page(get_option('deals_page_thanks_post_id'));
    }

}
if (!function_exists('is_history')) {

    function is_history() {
        if (is_page(get_option('deals_page_history_id')) )
            return true; 
        else
            return false;
    }

}
if (!function_exists('is_account_deal')) {

    function is_account_deal() {
        if (is_page(get_option('deals_page_profile_id')))
            return true; 
        else
            return false;
    }

}
if (!function_exists('is_feature_deal')) {

    function is_feature_deal() {
        if (is_page(get_option('deals_page_featured_id')))
            return true; 
        else
            return false;
    }

}


/**
 * Force SSL (if enabled)
 **/
if (!is_admin() && deals_get_option('force_ssl') == 1) add_action( 'wp', 'deals_force_ssl');

function deals_force_ssl() {
	if (!is_ssl()) :
		wp_safe_redirect( str_replace('http:', 'https:', get_permalink(get_option('deals_checkout_page_id'))), 301 );
		exit;
	endif;
}

/**
 * Force SSL for images
 **/
add_filter('post_thumbnail_html', 'deals_force_ssl_images');
add_filter('widget_text', 'deals_force_ssl_images');
add_filter('wp_get_attachment_url', 'deals_force_ssl_images');
add_filter('wp_get_attachment_image_attributes', 'deals_force_ssl_images');
add_filter('wp_get_attachment_url', 'deals_force_ssl_images');

function deals_force_ssl_images( $content ) {
	if (is_ssl()) :
		if (is_array($content)) :
			$content = array_map('deals_force_ssl_images', $content);
		else :
			$content = str_replace('http:', 'https:', $content);
		endif;
	endif;
	return $content;
}

/**
 * Force SSL for stylsheet/script urls etc. Modified code by Chris Black (http://cjbonline.org)
 **/
add_filter('option_siteurl', 'deals_force_ssl_urls');
add_filter('option_home', 'deals_force_ssl_urls');
add_filter('option_url', 'deals_force_ssl_urls');
add_filter('option_wpurl', 'deals_force_ssl_urls');
add_filter('option_stylesheet_url', 'deals_force_ssl_urls');
add_filter('option_template_url', 'deals_force_ssl_urls');
add_filter('script_loader_src', 'deals_force_ssl_urls');
add_filter('style_loader_src', 'deals_force_ssl_urls');

function deals_force_ssl_urls( $url ) {
	if (is_ssl()) :
		$url = str_replace('http:', 'https:', $url);
	endif;
	return $url;
}

/**
 * Currency
 **/
function get_deals_currency_symbol() {
	$currency = deals_get_option('currency');
	$currency_symbol = '';
	switch ($currency) :
		case 'EUR' : $currency_symbol = '&euro;'; break;
		case 'JPY' : $currency_symbol = '&yen;'; break;
		case 'TRY' : $currency_symbol = 'TL'; break;
		
		case 'CZK' :
		case 'DKK' :
		case 'HUF' :
		case 'ILS' :
		case 'MYR' :
		case 'NOK' :
		case 'PHP' :
		case 'PLN' :
		case 'SEK' :
		case 'CHF' :
		case 'TWD' :
		case 'THB' : $currency_symbol = $currency; break;
		
		case 'GBP' : $currency_symbol = '&pound;'; break;
		
		case 'IDR' : $currency_symbol = 'RP.'; break;
		
		case 'INR' : $currency_symbol = 'Rupee'; break;
		
		case 'AUD' :
		case 'BRL' :
		case 'CAD' :
		case 'MXN' :
		case 'NZD' :
		case 'HKD' :
		case 'SGD' :
		case 'USD' : 
		default    : $currency_symbol = '&#36;'; break;
	endswitch;
	return apply_filters('deals_currency_symbol', $currency_symbol, $currency);
}

/**
 * Flush rewrite rules in the end of process
 * triggered at activation and deactivation
 * 
 * @return void 
 */
function deals_final_flush_rules() {
    flush_rewrite_rules();
}
register_activation_hook(__FILE__,'deals_final_flush_rules');
register_activation_hook(__FILE__,'activate_deals');
register_deactivation_hook(__FILE__,'deals_final_flush_rules');
