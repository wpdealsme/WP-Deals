<?php

//secure included files
deals_secure();


/**
 * Register custom post type
 * daily-deals
 * 
 * @return void
 */
function deals_register_posttype() {

    /**
     * Post Types
     **/
    register_post_type( "daily-deals",
            array(
                    'labels' => array(
                            'name' 			=> __( 'Deals', 'wpdeals' ),
                            'singular_name' 		=> __( 'Deal', 'wpdeals' ),
                            'add_new' 			=> __( 'Add Deal', 'wpdeals' ),
                            'add_new_item' 		=> __( 'Add New Deal', 'wpdeals' ),
                            'edit' 			=> __( 'Edit', 'wpdeals' ),
                            'edit_item' 		=> __( 'Edit Deal', 'wpdeals' ),
                            'new_item' 			=> __( 'New Deal', 'wpdeals' ),
                            'view' 			=> __( 'View Deal', 'wpdeals' ),
                            'view_item' 		=> __( 'View Deal', 'wpdeals' ),
                            'search_items' 		=> __( 'Search Deals', 'wpdeals' ),
                            'not_found' 		=> __( 'No Deals found', 'wpdeals' ),
                            'not_found_in_trash' 	=> __( 'No Deals found in trash', 'wpdeals' ),
                            'parent' 			=> __( 'Parent Deal', 'wpdeals' )
                    ),
                    'description' 			=> __( 'This is where you can add new deals to your store.', 'wpdeals' ),
                    'public' 				=> true,
                    'show_ui' 				=> true,
                    'publicly_queryable'                => true,
                    'exclude_from_search'               => false,
                    'rewrite' 				=> array( 'slug' => 'deals' ),
                    'supports' 				=> array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields' ),
                    'show_in_nav_menus'                 => false,
                    'menu_icon'				=> DEALS_IMG . 'deals-icon.png'                    
            )
    );
    
    register_post_type( "deals-sales",
            array(
                    'labels' => array(
                            'name' 			=> __( 'Sales', 'wpdeals' ),
                            'singular_name' 		=> __( 'Sale', 'wpdeals' ),
                            'add_new' 			=> __( 'Add Sale', 'wpdeals' ),
                            'add_new_item' 		=> __( 'Add New Sale', 'wpdeals' ),
                            'edit' 			=> __( 'Edit', 'wpdeals' ),
                            'edit_item' 		=> __( 'Edit Sale', 'wpdeals' ),
                            'new_item' 			=> __( 'New Sale', 'wpdeals' ),
                            'view' 			=> __( 'View Sale', 'wpdeals' ),
                            'view_item' 		=> __( 'View Sale', 'wpdeals' ),
                            'search_items' 		=> __( 'Search Sales', 'wpdeals' ),
                            'not_found' 		=> __( 'No Sales found', 'wpdeals' ),
                            'not_found_in_trash' 	=> __( 'No Sales found in trash', 'wpdeals' ),
                            'parent' 			=> __( 'Parent Sale', 'wpdeals' )
                    ),
                    'description' 			=> __( 'Stored and manage all sales data transaction.', 'wpdeals' ),
                    'public' 				=> true,
                    'show_ui' 				=> true,
                    'publicly_queryable'                => false,
                    'capabilities' => array(
                            'publish_posts' 		=> 'manage_deals',
                            'edit_posts' 		=> 'manage_deals',
                            'edit_others_posts' 	=> 'manage_deals',
                            'delete_posts' 		=> 'manage_deals',
                            'delete_others_posts'	=> 'manage_deals',
                            'read_private_posts'	=> 'manage_deals',
                            'edit_post' 		=> 'manage_deals',
                            'delete_post' 		=> 'manage_deals',
                            'read_post' 		=> 'manage_deals'
                    ),
                    'exclude_from_search'               => true,
                    'show_ui'                           => true,
                    'show_in_menu'                      => 'wpdeals',
                    'show_in_nav_menus'                 => false,
                    'rewrite'                           => false,
                    'query_var'                         => true,
                    'has_archive'                       => false,
                    'capability_type'                   => 'post',
                    'hierarchical'                      => false,
                    'supports'                          => array( 'title' ),
                    'menu_icon'				=> DEALS_IMG . 'deals-icon.png' 
            )
    );
            
}

/**
 * Register custom taxonomy
 * deal categories
 * deal tags
 * 
 * @return void
 */
function deals_register_taxonomy() {
    
    //arguments for custom taxonomy > deal categories
    register_taxonomy('deal-categories', 'daily-deals', array(
            'labels'            => array(
            'name'              => __( 'Deal Categories', 'wpdeals' ),
            'singular_name'     => __( 'Deal Category', 'wpdeals' ),
            'search_items'      => __( 'Deal Search Category', 'wpdeals' ),
            'all_items'         => __( 'All Deal Categories', 'wpdeals' ),
            'edit_item'         => __( 'Edit Deal Category', 'wpdeals' ),
            'update_item'       => __( 'Update Deal Category', 'wpdeals' ),
            'add_new_item'      => __( 'Add Deal Category', 'wpdeals' ),
            'new_item_name'     => __( 'New Deal Category', 'wpdeals' )
        ),
        'public'                => true,
        'show_ui'               => true,
        'show_tagcloud'         => true,
        'hierarchical'          => true,
        'rewrite'               => array(
            'slug'              => 'deal-categories',
            'hierarchical'      => true
        )
    ));

    //arguments for custom taxonomy > deal tags
    register_taxonomy('deal-tags', 'daily-deals', array(
            'labels'            => array(
            'name'              => __( 'Deal Tags', 'wpdeals' ),
            'singular_name'     => __( 'Deal Tag', 'wpdeals' ),
            'search_items'      => __( 'Deal Search Tag', 'wpdeals' ),
            'all_items'         => __( 'All Deal Tags', 'wpdeals' ),
            'edit_item'         => __( 'Edit Deal Tag', 'wpdeals' ),
            'update_item'       => __( 'Update Deal Tag', 'wpdeals' ),
            'add_new_item'      => __( 'Add Deal Tag', 'wpdeals' ),
            'new_item_name'     => __( 'New Deal Tag', 'wpdeals' )
        ),
        'public'                => true,
        'show_ui'               => true,
        'show_tagcloud'         => true,
        'hierarchical'          => false,
        'rewrite'               => array(
            'slug'              => 'deal-tags',
            'hierarchical'      => false
        )
    ));
    
}


/**
 * Set up Roles & Capabilities
 **/
function deals_init_roles() {
	global $wp_roles;

	if (class_exists('WP_Roles')) if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();	
	
	if (is_object($wp_roles)) :
		
		// Customer role
		add_role('customer', __('Customer', 'wpdeals'), array(
		    'read' 					=> true,
		    'edit_posts' 				=> false,
		    'delete_posts' 				=> false
		));
	
		// Shop manager role
		add_role('deals_manager', __('Deals Manager', 'wpdeals'), array(
		    'read' 				=> true,
		    'read_private_pages'		=> true,
		    'read_private_posts'		=> true,
		    'edit_posts' 			=> true,
		    'edit_pages' 			=> true,
		    'edit_published_posts'		=> true,
		    'edit_published_pages'		=> true,
		    'edit_private_pages'		=> true,
		    'edit_private_posts'		=> true,
		    'edit_others_posts' 		=> true,
		    'edit_others_pages' 		=> true,
		    'publish_posts' 			=> true,
		    'publish_pages'			=> true,
		    'delete_posts' 			=> true,
		    'delete_pages' 			=> true,
		    'delete_private_pages'		=> true,
		    'delete_private_posts'		=> true,
		    'delete_published_pages'            => true,
		    'delete_published_posts'            => true,
		    'delete_others_posts' 		=> true,
		    'delete_others_pages' 		=> true,
		    'manage_categories' 		=> true,
		    'manage_links'			=> true,
		    'moderate_comments'			=> true,
		    'unfiltered_html'			=> true,
		    'upload_files'			=> true,
		   	'export'			=> true,
			'import'			=> true,
			'manage_deals'                  => true
		));
		
		// Main Shop capabilities for admin
		$wp_roles->add_cap( 'administrator', 'manage_deals' );
	endif;
}

// register
add_action('init', 'deals_register_posttype'); //register custom post type
add_action('init', 'deals_register_taxonomy'); //register custom taxonomy
add_action('init', 'deals_init_roles');

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
        'content' => '[wpdeals_featured]'
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
        
        if (($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE '" . $tableName ."'"))) != $tableName) {
            //dbDelta($tableSql);
            $wpdb->query($wpdb->prepare($tableSql));
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
    global $pagenow;
        
    if(is_admin()){
        // register
        wp_register_script('deals-js', DEALS_JS . "wp-deals.js", '', null);
        wp_register_script('deals-jlivequery', DEALS_JS . "jquery.livequery.js", '', null);
        
        // enqueue
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'deals-jlivequery' );        
        wp_enqueue_script( 'deals-js' );
        
        // just reports page
        if(is_admin() AND $pagenow == 'deal-stats'){
                wp_register_script('deals-jqplot-excanvas', DEALS_JS . "excanvas.min.js", '', null);
                wp_register_script('deals-jqplot-date', DEALS_JS . "jqplot.dateAxisRenderer.min.js", '', null);
                wp_register_script('deals-jqplot-log-axis', DEALS_JS . "jqplot.logAxisRenderer.min.js", '', null);
                wp_register_script('deals-jqplot-canvas-axis', DEALS_JS . "jqplot.canvasAxisLabelRenderer.min.js", '', null);
                wp_register_script('deals-jqplot-canvas-text', DEALS_JS . "jqplot.canvasTextRenderer.min.js", '', null);
                wp_register_script('deals-jqplot-canvas-tick', DEALS_JS . "jqplot.canvasAxisTickRenderer.min.js", '', null);
                wp_register_script('deals-jqplot', DEALS_JS . "jquery.jqplot.min.js", '', null);
                
                wp_enqueue_script( 'deals-jqplot' );
                wp_enqueue_script( 'deals-jqplot-excanvas' );
                wp_enqueue_script( 'deals-jqplot-date' );
                wp_enqueue_script( 'deals-jqplot-log-axis' );
                wp_enqueue_script( 'deals-jqplot-canvas-axis' );
                wp_enqueue_script( 'deals-jqplot-canvas-text' );
                wp_enqueue_script( 'deals-jqplot-canvas-tick' );
        }
        
    } else {
        
        // register
        wp_register_script('deals-jquery-countdown', DEALS_JS . "jquery.countdown.min.js", '', null);
        wp_register_script('deals-front', DEALS_JS . "wp-deals-front.js", '', null, true);
        wp_register_script('deals-fancybox', DEALS_JS . "jquery.fancybox-1.3.4.pack.js", '', null, true);
        
        // enqueue
        wp_enqueue_script( 'jquery' ); 
        wp_enqueue_script( 'deals-jquery-countdown' );      
        wp_enqueue_script( 'deals-front' );
        wp_enqueue_script( 'deals-fancybox' ); 
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
        wp_register_style('deals', DEALS_CSS . "wp-deals.css", '', null);
        wp_register_style('deals-fancybox', DEALS_CSS . "jquery.fancybox-1.3.4.css", '', null);        
        
        // enqueue
        if(deals_get_option('use_style', 1) == 1) wp_enqueue_style( 'deals' ); 
        wp_enqueue_style( 'deals-fancybox' );        
        
    }else{
        
        wp_register_style('deals-admin', DEALS_CSS . "wp-deals-admin.css", '', null);
        wp_register_style('deals-jquery.jqplot', DEALS_CSS . "jquery.jqplot.css", '', null);
        
        wp_enqueue_style( 'deals-admin' );
        wp_enqueue_style( 'deals-jquery.jqplot' );
        
    }
    
}
add_action('init', 'deals_styles');


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


//install pear
deals_install_path(DEALS_VENDOR_DIR.'pear');
deals_install_path(DEALS_VENDOR_DIR.'pear/PEAR');
deals_install_path(DEALS_VENDOR_DIR.'pear/PEAR/Image');
deals_install_path(DEALS_VENDOR_DIR.'pear/PEAR/Image/Barcode');

//install libs path
deals_install_path(DEALS_PLUGIN_PATH.'deals-assets/libs');

//install fpdf
deals_install_path(DEALS_VENDOR_DIR.'fpdf');