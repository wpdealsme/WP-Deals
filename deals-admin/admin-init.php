<?php
/**
 * WP Deals Admin
 * 
 * Main admin file which loads all settings panels and sets up admin menus.
 * 
 * @author 	WP Deals
 * @category 	Admin
 * @package 	WP Deals
 */

require_once 'admin-install.php';

function deals_admin_init() {
	require_once 'admin-settings-forms.php';
	require_once 'admin-settings.php';
	require_once 'admin-import.php';
	require_once 'admin-stats.php';
	require_once 'admin-subscribers.php';
    require_once 'post-type/deals-posts-init.php';	
}
add_action('admin_init', 'deals_admin_init');


/**
 * Admin Menus
 * 
 * Sets up the admin menus in wordpress.
 */
function deals_admin_menu() {		
		
	add_menu_page(__('WP Deals Settings', 'wpdeals'), __('WP Deals', 'wpdeals'), 'manage_options', 'wpdeals' , 'deals_settings', DEALS_IMG.'deals-menu-setting.png', 45);
	add_submenu_page('wpdeals', __('WP Deals Settings', 'wpdeals'),  __('Settings', 'wpdeals') , 'manage_options', 'wpdeals', 'deals_settings');
	//add_submenu_page('wpdeals', __('Deals Statistics', 'wpdeals'), __('Reports', 'wpdeals'), 'manage_options', 'deal-stats', 'deals_stats');	        
	add_submenu_page('wpdeals', __('Deals Subscribers', 'wpdeals'), __('Subscribers', 'wpdeals'), 'manage_options', 'deal-subscribers', 'deals_subscribers');
	add_submenu_page(null,'Deal Invoice Preview','Invoice Preview','manage_options','deal-invoice-preview','deals_invoice_preview');
    
}
add_action('admin_menu', 'deals_admin_menu', 1);
