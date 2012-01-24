<?php

//secure included files
deals_secure();

/*
 set global status    
 */
global $deals_featured_page_id, $deals_page_post_id, $deals_thanks_page_post_id, $deals_user_history_id, $deals_user_profile_id;
global $pageAuto,$pagesAutoExcluded;
    
/*
 * option page/post id
 */
$deals_page_post_id = get_option('deals_page_post_id');
$deals_thanks_page_post_id = get_option('deals_page_thanks_post_id');
$deals_user_profile_id = get_option('deals_page_profile_id');
$deals_user_history_id = get_option('deals_page_history_id');
$deals_featured_page_id = get_option('deals_page_featured_id');

/*
 * create pages :
 * - profile
 * - dailydeals
 * - history
 * - featured
 * - thanks
 */
$pageAuto = array(
    'wpdeals_page' => array(
        'post_option_id' => $deals_page_post_id,
        'post_option_name' => 'deals_page_post_id',
        'title' => 'Daily Deals',
        'content' => '[wpdeals]'),
    'wpdeals_user_profile' => array(
        'post_option_id' => $deals_user_profile_id,
        'post_option_name' => 'deals_page_profile_id',
        'title' => 'My User Profile',
        'content' => '[wpdeals_user_profile]'
    ),
    'wpdeals_user_history' => array(
        'post_option_id' => $deals_user_history_id,
        'post_option_name' => 'deals_page_history_id',
        'title' => 'My History',
        'content' => '[wpdeals_user_history]'
    ),
    'wpdeals_featured_page' => array(
        'post_option_id' => $deals_featured_page_id,
        'post_option_name' => 'deals_page_featured_id',
        'title' => 'Featured Deal',
        'content' => ''
    ),
    'wpdeals_thanks' => array(
        'post_option_id' => $deals_thanks_page_post_id,
        'post_option_name' => 'deals_page_thanks_post_id',
        'title' => 'Your Deal Transaction',
        'content' => '[thanksdeal]'));

$pagesAutoExcluded = array(
    'deals_page_thanks_post_id'
);

/**
 *
 * Dailydeals page management
 * 
 * @global int $wpdeals_featured_page_id
 * @global int $wpdeals_page_post_id
 * @global int $wpdeals_thanks_page_post_id
 * @global int $wpdeals_user_history_id
 * @global int $wpdeals_user_profile_id
 * @global array $pageAuto
 * @param string $post_status 
 */
function deals_init_page($post_status='publish') {
    global $deals_featured_page_id, $deals_page_post_id, $deals_thanks_page_post_id, $deals_user_history_id, $deals_user_profile_id;
    global $pageAuto;

    if (empty($post_status)) {
        $post_status = 'publish';
    }

    foreach ($pageAuto as $autoPostName => $autoPostValue) {

        $post_id = $autoPostValue['post_option_id'];

        if ($post_id) {//if exists then update
            //check if saved post id is really exists
            $check_post = get_post($post_id);

            if ($check_post) {

                $post = array(
                    'ID' => $post_id,
                    'post_status' => $post_status,
                    'comment_status' => 'closed'
                );

                wp_update_post($post);
            } else {//reset if real page not exists, delete > create
                delete_option($autoPostValue['post_option_name']);

                //create
                $post = array(
                    'post_title' => $autoPostValue['title'],
                    'post_type' => 'page',
                    'post_content' => $autoPostValue['content'],
                    'post_status' => $post_status,
                    'comment_status' => 'closed'
                );

                $post_id = wp_insert_post($post);

                if ($post_id) {
                    //set option to save post id (page)
                    add_option($autoPostValue['post_option_name'], $post_id, '', 'no');
                }
            }
        } else {//insert new post
            $post = array(
                'post_title' => $autoPostValue['title'],
                'post_type' => 'page',
                'post_content' => $autoPostValue['content'],
                'post_status' => 'publish',
                'comment_status' => 'closed'
            );

            $post_id = wp_insert_post($post);

            if ($post_id) {
                //set option to save post id (page)
                add_option($autoPostValue['post_option_name'], $post_id, '', 'no');
            }
        }
    }
}

/**
 * Disable all page created by wp-deals
 * 
 * @return void
 */
function deals_init_disable_page() {
    deals_init_page('draft');
}

/**
 *
 * Create tables on activating plugin
 * 
 * @global object $wpdb 
 * @return void
 */
function deals_init_create_tables() {

    global $wpdb;

    $sql_invoices = 'CREATE TABLE ' . $wpdb->prefix . 'wpdeals_invoices(
                                id BIGINT(150) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                sales_id BIGINT(15) NOT NULL,
                                invoice_status TINYINT(3) NOT NULL,
                                created DATETIME NOT NULL, 
                                INDEX(sales_id,invoice_status)
                        );';

    $sql_sales = 'CREATE TABLE ' . $wpdb->prefix . 'wpdeals_sales(
                                id BIGINT(150) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                user_id BIGINT(150) NOT NULL,
                                item_id BIGINT(150) NOT NULL,
                                transaction_id VARCHAR(255) NOT NULL,
                                buyer_email VARCHAR(255) NOT NULL,
                                quantity BIGINT(150) NOT NULL,
                                total_price FLOAT NOT NULL,
                                payment_data TEXT NOT NULL,
                                payment_status TINYINT(3) NOT NULL,
                                buy_date DATETIME NOT NULL, 
                                INDEX(user_id,item_id,payment_status)
                        );';

    $sql_download = 'CREATE TABLE ' . $wpdb->prefix . 'wpdeals_download(
                                id BIGINT(150) NOT NULL AUTO_INCREMENT PRIMARY KEY,                                
                                item_id BIGINT(150) NOT NULL,
                                download_date DATETIME NOT NULL,
                                INDEX(item_id)
                        );';
    
    $tables = array(
        $wpdb->prefix.'wpdeals_invoices' => $sql_invoices,
        $wpdb->prefix.'wpdeals_sales' => $sql_sales,
        $wpdb->prefix.'wpdeals_download' => $sql_download
    );
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); // include upgrade wpdb.
    foreach ($tables as $tableName => $tableSql) {
        
        if (($wpdb->get_var("SHOW TABLES LIKE '" . $tableName ."'")) != $tableName) {
            //dbDelta($tableSql);
            $wpdb->query($tableSql);
        }
    }    
}

/*
 run at activation plugin for :
 - init page
 - create table 
 */
register_activation_hook(DEALS_PLUGIN_FILE,'deals_init_page');
register_activation_hook(DEALS_PLUGIN_FILE,'deals_init_create_tables');
/*
 run at deactivation plugin
 */
register_deactivation_hook(DEALS_PLUGIN_FILE,'deals_init_disable_page');



/**
 * For enqueue js
 */
function deals_js(){
        
    if(is_admin()){
        // register
        wp_register_script('wp-deals-js', DEALS_JS . "wp-deals.js", '', null);
        wp_register_script('jlivequery', DEALS_JS . "jquery.livequery.js", '', null);
        wp_register_script('jqplot-excanvas', DEALS_JS . "excanvas.min.js", '', null);
        wp_register_script('jqplot-date', DEALS_JS . "jqplot.dateAxisRenderer.min.js", '', null);
        wp_register_script('jqplot-log-axis', DEALS_JS . "jqplot.logAxisRenderer.min.js", '', null);
        wp_register_script('jqplot-canvas-axis', DEALS_JS . "jqplot.canvasAxisLabelRenderer.min.js", '', null);
        wp_register_script('jqplot-canvas-text', DEALS_JS . "jqplot.canvasTextRenderer.min.js", '', null);
        wp_register_script('jqplot-canvas-tick', DEALS_JS . "jqplot.canvasAxisTickRenderer.min.js", '', null);
        wp_register_script('jqplot', DEALS_JS . "jquery.jqplot.min.js", '', null);
        
        // enqueue
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jlivequery' );        
        wp_enqueue_script( 'wp-deals-js' );
        wp_enqueue_script( 'jqplot' );
        wp_enqueue_script( 'jqplot-excanvas' );
        wp_enqueue_script( 'jqplot-date' );
        wp_enqueue_script( 'jqplot-log-axis' );
        wp_enqueue_script( 'jqplot-canvas-axis' );
        wp_enqueue_script( 'jqplot-canvas-text' );
        wp_enqueue_script( 'jqplot-canvas-tick' );
        
    } else {
        
        // register
        wp_register_script('jquery-countdown', DEALS_JS . "jquery.countdown.min.js", '', null);
        wp_register_script('wp-deals-front', DEALS_JS . "wp-deals-front.js", '', null, true);
        wp_register_script('fancybox', DEALS_JS . "jquery.fancybox-1.3.4.pack.js", '', null, true);
        
        // enqueue
        wp_enqueue_script( 'jquery' ); 
        wp_enqueue_script( 'jquery-countdown' );      
        wp_enqueue_script( 'wp-deals-front' );
        wp_enqueue_script( 'fancybox' ); 
    }
    
}
add_action('init', 'deals_js', 10);

/**
 *Starting output buffering
 *@return void
 */
function deals_output_buffering() {
    
    ob_start();
    
}
add_action('init','deals_output_buffering');


/**
 * For enqueue styles
 */
function deals_styles(){
        
    if(!is_admin()){
        // register
        wp_register_style('wp-deals', DEALS_CSS . "wp-deals.css", '', null);
        wp_register_style('fancybox', DEALS_CSS . "jquery.fancybox-1.3.4.css", '', null);        
        
        // enqueue
        wp_enqueue_style( 'wp-deals' ); 
        wp_enqueue_style( 'fancybox' );        
        
    }else{
        
        wp_register_style('wp-deals-admin', DEALS_CSS . "wp-deals-admin.css", '', null);
        wp_register_style('jquery.jqplot', DEALS_CSS . "jquery.jqplot.css", '', null);
        
        wp_enqueue_style( 'wp-deals-admin' );
        wp_enqueue_style( 'jquery.jqplot' );
        
    }
    
}
add_action('init', 'deals_styles');

/**
 * register admin menus
 *
 * @return void
 */
function deals_register_admin_menus() {
    
    if(is_admin()) {
        
        require_once 'deals_sales_report.php';
        require_once 'deals_invoice_report.php';
        require_once 'deals_subscribers.php';
        require_once 'deals-stats/deals_stats.php';
        
        add_submenu_page('edit.php?post_type=daily-deals',
                'Deal Sales','Sales','manage_options','deal-sales','deals_sales_report');
        add_submenu_page('edit.php?post_type=daily-deals',
                'Deal Invoices','Invoices','manage_options','deal-invoices','deals_invoice_report');
        add_submenu_page(null,
                'Deal Sales Detail','Sale Details','manage_options','deal-sales-detail','deals_sales_detail');
        add_submenu_page(null,
                'Deal Invoice Detail','Invoice Details','manage_options','deal-invoice-detail','deals_invoice_detail');
        add_submenu_page(null,
                'Deal Invoice Preview','Invoice Preview','manage_options','deal-invoice-preview','deals_invoice_preview');
        add_submenu_page('edit.php?post_type=daily-deals',
                'Deal Subscribers','Subscribers','manage_options','deal-subscribers','deals_subscribers');
        add_submenu_page('edit.php?post_type=daily-deals',
                'Deal Statistics','Reports','manage_options','deal-stats','deals_stats');                                
        
    }
    
}
add_action('admin_menu','deals_register_admin_menus');

/**
 *
 * Reset loaded pages
 * 
 * @global array $pagesAutoExcluded
 * @param array $pages
 * @return array 
 */
function deals_exclude_pages($pages) {
    
    global $pagesAutoExcluded;
    
    $page_ids = array();
    foreach($pagesAutoExcluded as $pageAutoOptionName) {
        $page_ids[] = get_option($pageAutoOptionName);
    }
    
    $reset_pages = array();
    foreach($pages as $page) {
        
        if(!in_array($page->ID,$page_ids)) {
            $reset_pages[] = $page;
        }
        
    }
    
    return $reset_pages;
    
}
add_filter('get_pages','deals_exclude_pages');


/**
 * WP Deals Templates
 * 
 * Handles template usage so that we can use our own templates instead of the theme's.
 *
 * Templates are in the 'deals-templates' folder. deals looks for theme 
 * overides in /theme/deals/ by default  but this can be overwritten with DEALS_DIR
 *
 * @package	WP Deals
 * @category	Core
 */
function deals_template_loader( $template ) {
	
	$buy_id = get_query_var('deal_buy_id');
        
        if($buy_id){
            
		$wp_nonce = $_REQUEST['_wpnonce'];
		
                $plugin_template_buy            = DEALS_TEMPLATE_DIR . 'form/wp-deals-buy.php';
		$plugin_template_buy_invalid    = DEALS_TEMPLATE_DIR . 'form/wp-deals-buy-invalid.php';
                $plugin_template_form           = DEALS_TEMPLATE_DIR . 'form/wp-deals-form.php';
                
                if(is_user_logged_in())
		
			if(wp_verify_nonce($wp_nonce,'buy-button')) {
				$template   = $plugin_template_buy;
			}else{
				$template = $plugin_template_buy_invalid;
			}
			
                else
                    $template   = $plugin_template_form;
        
        }elseif ( is_single() && get_post_type() == 'daily-deals' ) {
            		
		$template = locate_template( array( 'single-deal.php', DEALS_TEMPLATE_DIR . 'single-deal.php' ) );
		
		if ( ! $template ) $template = DEALS_TEMPLATE_DIR . 'single-deal.php';
		
	}elseif ( is_tax('deal-categories') ) {
		
		$template = locate_template(  array( 'taxonomy-deal_cat.php', DEALS_TEMPLATE_DIR . 'taxonomy-deal_cat.php' ) );
		
		if ( ! $template ) $template = DEALS_TEMPLATE_DIR . 'taxonomy-deal_cat.php';
	}elseif ( is_tax('deal-tags') ) {
		
		$template = locate_template( array( 'taxonomy-deal_tag.php', DEALS_TEMPLATE_DIR . 'taxonomy-deal_tag.php' ) );
		
		if ( ! $template ) $template = DEALS_TEMPLATE_DIR . 'taxonomy-deal_tag.php';
	}elseif (is_page( get_option('deals_page_featured_id') )) { // get page id featured deal

		$template = locate_template( array( 'featured-deal.php', DEALS_TEMPLATE_DIR . 'featured-deal.php' ) );
		
		if ( ! $template ) $template = DEALS_TEMPLATE_DIR . 'featured-deal.php';
                
        }elseif ( is_post_type_archive('daily-deals') ||  is_page( get_option('deals_page_post_id') )) {

		$template = locate_template( array( 'archive-deal.php', DEALS_TEMPLATE_DIR . 'archive-deal.php' ) );
		
		if ( ! $template ) $template = DEALS_TEMPLATE_DIR . 'archive-deal.php';
		
	}
        
        return $template;

}
add_filter( 'template_include', 'deals_template_loader' );

// admin bar not showing for user default (just administrator).
add_filter('show_admin_bar', '__return_false');