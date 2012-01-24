<?php

function deals_option_name() {	
	
	$deals_settings = get_option('dealoptions');
	$deals_settings['id'] = 'dealoptions';
	update_option('dealoptions', $deals_settings);
        
}

function deals_options() {
	
	// Test data
	$test_array = array("one" => "One","two" => "Two","three" => "Three","four" => "Four","five" => "Five");
	
	// Multicheck Array
	$multicheck_array = array("one" => "French Toast", "two" => "Pancake", "three" => "Omelette", "four" => "Crepe", "five" => "Waffle");
	
	// Multicheck Defaults
	$multicheck_defaults = array("one" => "1","five" => "1");
	
	// Background Defaults
	
	$background_defaults = array('color' => '', 'image' => '', 'repeat' => 'repeat','position' => 'top center','attachment'=>'scroll');
	
	
	// Pull all the categories into an array
	$options_categories = array();  
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
    	$options_categories[$category->cat_ID] = $category->cat_name;
	}
	
	// Pull all the pages into an array
	$options_pages = array();  
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
    	$options_pages[$page->ID] = $page->post_title;
	}
		
	// If using image radio buttons, define a directory path
	//$imagepath =  get_bloginfo('stylesheet_directory') . '/images/';
		
	$options = array();
        
        $options[] = array( "name" => "General",
						"type" => "heading");
        
        $options[] = array( "name" => "Force SSL/HTTPS?",
						"desc"  => "an SSL Certificate is required.",
						"id"    => "force_ssl",
						"std"   => "",						
						"type"  => "checkbox"
						);
        
	$options[] = array( "name" => "Currency",
						"desc" => __("This controls what currency prices are listed at in the catalog and which currency gateways will take payments in.", 'wpdeals' ),
						"id" => "currency",
						"std" => "USD",
						"type" => "select",
						"class" => "mini", //mini, tiny, small						
						'options' => apply_filters('deals_currencies', array( 
                                                    'USD' => __( 'US Dollars (&#36;)', 'wpdeals' ),
                                                    'EUR' => __( 'Euros (&euro;)', 'wpdeals' ),
                                                    'GBP' => __( 'Pounds Sterling (&pound;)', 'wpdeals' ),
                                                    'AUD' => __( 'Australian Dollars (&#36;)', 'wpdeals' ),
                                                    'BRL' => __( 'Brazilian Real (&#36;)', 'wpdeals' ),
                                                    'CAD' => __( 'Canadian Dollars (&#36;)', 'wpdeals' ),
                                                    'CZK' => __( 'Czech Koruna', 'wpdeals' ),
                                                    'DKK' => __( 'Danish Krone', 'wpdeals' ),
                                                    'HKD' => __( 'Hong Kong Dollar (&#36;)', 'wpdeals' ),
                                                    'HUF' => __( 'Hungarian Forint', 'wpdeals' ),
                                                    'ILS' => __( 'Israeli Shekel', 'wpdeals' ),
                                                    'JPY' => __( 'Japanese Yen (&yen;)', 'wpdeals' ),
                                                    'MYR' => __( 'Malaysian Ringgits', 'wpdeals' ),
                                                    'MXN' => __( 'Mexican Peso (&#36;)', 'wpdeals' ),
                                                    'NZD' => __( 'New Zealand Dollar (&#36;)', 'wpdeals' ),
                                                    'NOK' => __( 'Norwegian Krone', 'wpdeals' ),
                                                    'PHP' => __( 'Philippine Pesos', 'wpdeals' ),
                                                    'PLN' => __( 'Polish Zloty', 'wpdeals' ),
                                                    'SGD' => __( 'Singapore Dollar (&#36;)', 'wpdeals' ),
                                                    'SEK' => __( 'Swedish Krona', 'wpdeals' ),
                                                    'CHF' => __( 'Swiss Franc', 'wpdeals' ),
                                                    'TWD' => __( 'Taiwan New Dollars', 'wpdeals' ),
                                                    'THB' => __( 'Thai Baht', 'wpdeals' ), 
                                                    'TRY' => __( 'Turkish Lira (TL)', 'wpdeals' )
                                                    ))
                                                );
						
	$options[] = array( "name" => "View type",
						"desc" => "Select type for your view deals.",
						"id" => "view_type",
						"std" => "list",
						"type" => "select",
						"class" => "mini", //mini, tiny, small
						"options" => array('list', 'grid'));
        
//        $options[] = array( "name" => "Deals",
//						"type" => "heading");
        							
	$options[] = array( "name" => "Pricing options",
						"type" => "section");
						
	$options[] = array( "name" => "Currency Position",
						"desc" => "This controls the position of the currency symbol.",
						"id" => "currency_pos",
						"std" => "left",
						"type" => "select",
						"class" => "mini", //mini, tiny, small
						'options' => array( 
                                                            'left' => __( 'Left', 'wpdeals' ),
                                                            'right' => __( 'Right', 'wpdeals' ),
                                                            'left_space' => __( 'Left (with space)', 'wpdeals' ),
                                                            'right_space' => __( 'Right (with space)', 'wpdeals' )
                                                        )
                                        );
        
        $options[] = array( 'name' => __( 'Thousand separator', 'wpdeals' ),
                                                'desc' 		=> __( 'This sets the thousand separator of displayed prices.', 'wpdeals' ),
                                                'id' 		=> 'price_thousand_sep',
                                                'std' 		=> ',',
                                                'class'         => 'mini',
                                                'type' 		=> 'text',);
        
        $options[] = array( 'name' => __( 'Decimal separator', 'wpdeals' ),
                                                'desc' 		=> __( 'This sets the decimal separator of displayed prices.', 'wpdeals' ),
                                                'id' 		=> 'price_decimal_sep',
                                                'std' 		=> '.',
                                                'class'         => 'mini',
                                                'type' 		=> 'text',);
        
        $options[] = array( 'name' => __( 'Number of decimals', 'wpdeals' ),
                                                'desc' 		=> __( 'This sets the number of decimal points shown in displayed prices.', 'wpdeals' ),
                                                'id' 		=> 'price_num_decimals',
                                                'std' 		=> '2',
                                                'class'         => 'mini',
                                                'type' 		=> 'text',);
        
        							
	$options[] = array( "name" => "Image Setting",
						"type" => "section");
							
	$options[] = array( "name" => "Deafult Deal Image Size",
						"desc" => "",
						"id" => "default_image",
						"std" => 0,
						"class" => "size",
						"type" => "size");
							
	$options[] = array( "name" => "Single Deal Image Size",
						"desc" => "",
						"id" => "single_image",
						"std" => 0,
						"class" => "size",
						"type" => "size");
        
        $options[] = array( "name" => "PayPal",
						"type" => "heading");
        
        $options[] = array( "name" => "Paypal Email",
						"desc" => "Your paypal email for receive money.",
						"id" => "paypal_email",
						"std" => "",
						"class" => "mini",
						"type" => "text");
        
        $options[] = array( "name" => "Paypal Sandbox Email",
						"desc" => "Your paypal sandbox account email to test payment.",
						"id" => "paypal_sandbox_email",
						"std" => "",
						"class" => "mini",
						"type" => "text");
        
        $options[] = array( "name" => "Enable Paypal Test",
						"desc" => "Enable paypal sandbox.",
						"id" => "paypal_is_test",
						"std" => "",
						"type" => "checkbox");
        
	$options[] = array( "name" => "Description",
						"type" => "section");
							
	$options[] = array( "name" => "About Paypal Sandbox",
						"desc" => "Paypal sandbox account is an account to test payment transaction, use this feature if you want to test this plugin payment transaction using paypal. Read more detail about",
						"type" => "info");
							
	$options[] = array( "name" => "About Paypal IPN",
						"desc" => "Use paypal IPN to get notification for your deal transaction. For more detail please read about <a href='https://www.paypal.com/ipn' target='_blank'>Paypal IPN</a>.",
						"type" => "info");
        
        $options[] = array( "name" => "Invoice",
						"type" => "heading");
							
	$options[] = array( "name" => "Store Name",
						"desc" => "Your deal store name.",
						"id" => "store_name",
						"std" => get_bloginfo('name'),
						"class" => "mini",
						"type" => "text");
        
        $options[] = array( "name" => "Logo",
						"desc" => "Ex:http://yoursite.com/yourlogo.jpg.",
						"id" => "invoice_logo_url",
						"std" => "",						
						"type" => "text");
        
        $options[] = array( "name" => "Store Description",
						"desc" => "Your deal store description.",
						"id" => "invoice_desc",
						"std" => "",						
						"type" => "textarea");
        
        $options[] = array( "name" => "Footer Text",
						"desc" => "Your invoice footer text.",
						"id" => "invoice_footer",
						"std" => "",						
						"type" => "textarea");
        
        
        $options[] = array( "name" => "Account",
						"type" => "heading");
        $options[] = array( "name" => "Twitter",
						"desc"  => "Your twitter account.",
						"id"    => "twitter",
						"std"   => "",				
						"class" => "mini", //mini, tiny, small		
						"type"  => "text");
        $options[] = array( "name" => "Facebook",
						"desc"  => "Your facebook link.",
						"id"    => "facebook",
						"std"   => "",				
						"class" => "mini", //mini, tiny, small		
						"type"  => "text");
        $options[] = array( "name" => "RSS/FEED",
						"desc"  => "Your RSS/FEED link.",
						"id"    => "rss_link",
						"std"   => "",				
						"class" => "mini", //mini, tiny, small		
						"type"  => "text");
        $options[] = array( "name" => "Show share button?",
						"desc"  => "Displaying share button.",
						"id"    => "share",
						"std"   => "yes",						
						"type"  => "select",
						"class" => "mini", //mini, tiny, small
						"options" => array('no' => 'No', 'yes' => 'Yes'));
        			
	return apply_filters('deals_plugin_options', $options);
}