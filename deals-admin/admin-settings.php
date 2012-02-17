<?php
/**
 * Functions for outputting and updating settings pages
 * 
 * @author 	WP Deals
 * @category 	Admin
 * @package 	WP Deals
 */


if (!function_exists('add_action')) {
    echo "Hi there!  I'm just a little plugin, don't mind me.";
    exit;
}

/**
 * Define settings for the WP Deals settings pages
 */
global $deals_settings;

$deals_settings['general'] = apply_filters('deals_general_settings', array(

	array( 'name' => __( 'General Options', 'wpdeals' ), 'type' => 'title', 'desc' => '', 'id' => 'general_options' ),
	    
	array(  
		'name' => __( 'Currency', 'wpdeals' ),
		'desc' 		=> __("This controls what currency prices are listed at in the catalog and which currency gateways will take payments in.", 'wpdeals' ),
		'tip' 		=> '',
		'id' 		=> 'deals_currency',
		'css' 		=> 'min-width:300px;',
		'std' 		=> 'USD',
		'type' 		=> 'select',
		'class'		=> 'chosen_select',
		'options' => array_unique(apply_filters('deals_currencies', array( 
			'USD' => __( 'US Dollars (&#36;)', 'wpdeals' ),
			'EUR' => __( 'Euros (&euro;)', 'wpdeals' ),
			'GBP' => __( 'Pounds Sterling (&pound;)', 'wpdeals' ),
			'AUD' => __( 'Australian Dollars (&#36;)', 'wpdeals' ),
			'BRL' => __( 'Brazilian Real (&#36;)', 'wpdeals' ),
			'CAD' => __( 'Canadian Dollars (&#36;)', 'wpdeals' ),
			'CZK' => __( 'Czech Koruna (&#75;&#269;)', 'wpdeals' ),
			'DKK' => __( 'Danish Krone', 'wpdeals' ),
			'HKD' => __( 'Hong Kong Dollar (&#36;)', 'wpdeals' ),
			'HUF' => __( 'Hungarian Forint', 'wpdeals' ),
			'ILS' => __( 'Israeli Shekel', 'wpdeals' ),
			'IDR' => __( 'Indonesia Rupiah (Rp.)', 'wpdeals' ),
			'INR' => __( 'India Rupee', 'wpdeals' ),
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
			'TRY' => __( 'Turkish Lira (TL)', 'wpdeals' ),
			'ZAR' => __( 'South African rand (R)', 'wpdeals' ),
			))
		)
	),
            
        array( "name"   => __( 'Security', 'wpdeals' ),
                "desc"  => __( 'Force <abbr title="Secure Sockets Layer, a computing protocol that ensures the security of data sent via the Internet by using encryption">SSL</abbr>/HTTPS (an SSL Certificate is required)', 'wpdeals' ),
                "id"    => "deals_force_ssl",
                "std"   => "",						
                "type"  => "checkbox"
        ),
						
	array(  "name"      => __( 'View type', 'wpdeals' ),
                "desc"      => __( 'Select type for your view deals.', 'wpdeals'),
                "id"        => "deals_view_type",
                "std"       => "list",
                "type"      => "select",
		'class'     => 'chosen_select',
		'css'       => 'min-width:300px;',
		'options'   => array(  
			'list'  => __( 'List View', 'wpdeals' ),
			'grid'  => __( 'Grid View', 'wpdeals' ),
		)
        ),
            
        array( "name"   => __( 'Styling', 'wpdeals' ),
                "desc"  => __( 'Enable WP Deals CSS styles', 'wpdeals' ),
                "id"    => "deals_use_style",
                "std"   => 'yes',						
                "type"  => "checkbox"
        ),
    
	array( 'type' => 'sectionend', 'id' => 'general_options'),    
	
	array( 'name' => __( 'Share Button', 'wpdeals' ), 'type' => 'title', 'desc' => '', 'id' => 'share_button' ),

	array(  
		'name'      => __( 'Display share button?', 'wpdeals' ),
		'desc'      => __( 'Displaying share button.', 'wpdeals' ),
		'id'        => 'deals_share',
		'std'       => 'yes',
                "type"      => "select",
                'css'       => 'min-width:100px;',
                'options'   => array( 
                    'no'    => __( 'No', 'wpdeals' ),
                    'yes'   => __( 'Yes', 'wpdeals' )
                )
	),
	
	array( 'type' => 'sectionend', 'id' => 'share_button'),
    

)); // End general settings



$deals_settings['deals'] = apply_filters('deals_item_settings', array(

	array( 'name' => __( 'Deals Options', 'wpdeals' ), 'type' => 'title', 'desc' => '', 'id' => 'deals_options' ),
                
        array( 'name' => __( 'Deals per page', 'wpdeals' ),
                'desc' 		=> __( 'Set your max deals item per page.', 'wpdeals' ),
                'id' 		=> 'deals_items_per_page',
                'std' 		=> 9,
                'type' 		=> 'text'
        ),
    
        array( 'name' => __( 'Random deals on featured', 'wpdeals' ),
                'desc' 		=> __( 'Check if you want make deals random on featured page.', 'wpdeals' ),
                'id' 		=> 'deals_featured_rand',
                'std' 		=> 'no',
                "type" => "select",
                'css' 	=> 'min-width:150px;',
                'options' => array( 
                    'no' => __( 'No', 'wpdeals' ),
                    'yes' => __( 'Yes', 'wpdeals' )
                )
        ),
    
	array( 'type' => 'sectionend', 'id' => 'deals_options'),
	        
	array( 'name' => __( 'Currency', 'wpdeals' ), 'type' => 'title', 'desc' => '', 'id' => 'currency_options' ),
    
	array( "name" => "Currency Position",
                "desc" => "This controls the position of the currency symbol.",
                "id" => "deals_currency_pos",
                "std" => "left",
                "type" => "select",
                'css' 	=> 'min-width:150px;',
                'options' => array( 
                    'left' => __( 'Left', 'wpdeals' ),
                    'right' => __( 'Right', 'wpdeals' ),
                    'left_space' => __( 'Left (with space)', 'wpdeals' ),
                    'right_space' => __( 'Right (with space)', 'wpdeals' )
                )
        ),
        
        array( 'name' => __( 'Thousand separator', 'wpdeals' ),
                'desc' 		=> __( 'This sets the thousand separator of displayed prices.', 'wpdeals' ),
                'id' 		=> 'deals_price_thousand_sep',
                'std' 		=> ',',
                'type' 		=> 'text'
        ),
        
        array( 'name' => __( 'Decimal separator', 'wpdeals' ),
                'desc' 		=> __( 'This sets the decimal separator of displayed prices.', 'wpdeals' ),
                'id' 		=> 'deals_price_decimal_sep',
                'std' 		=> '.',
                'type' 		=> 'text'
        ),
        
        array( 'name' => __( 'Number of decimals', 'wpdeals' ),
                'desc' 		=> __( 'This sets the number of decimal points shown in displayed prices.', 'wpdeals' ),
                'id' 		=> 'deals_price_num_decimals',
                'std' 		=> '2',
                'type' 		=> 'text'
        ),
	
	array( 'type' => 'sectionend', 'id' => 'currency_options'),
    
	array(	'name' => __( 'Image Options', 'wpdeals' ), 'type' => 'title','desc' => '', 'id' => 'image_options' ),
	
	array(  
		'name' => __( 'Default Deal Image Size (List)', 'wpdeals' ),
		'desc' 		=> __('This size is usually used in deals listings (List)', 'wpdeals'),
		'id' 		=> 'deals_default_image',
		'css' 		=> '',
		'type' 		=> 'image_width',
		'std' 		=> '150'
	),
	
	array(  
		'name' => __( 'Default Deal Image Size (Grid)', 'wpdeals' ),
		'desc' 		=> __('This size is usually used in deals listings (Grid)', 'wpdeals'),
		'id' 		=> 'deals_default_image_grid',
		'css' 		=> '',
		'type' 		=> 'image_width',
		'std' 		=> '150'
	),

	array(  
		'name' => __( 'Single Product Image', 'wpdeals' ),
		'desc' 		=> __('This is the size used by the main image on the deals page.', 'wpdeals'),
		'id' 		=> 'deals_single_image',
		'css' 		=> '',
		'type' 		=> 'image_width',
		'std' 		=> '275'
	),
	
	array( 'type' => 'sectionend', 'id' => 'image_options' ),
    

)); // End Deals settings


$deals_settings['invoice'] = apply_filters('deals_invoice_settings', array(
		
	array(	'name' => __( 'Invoice template', 'wpdeals' ),
		  'type' => 'title',
		  'desc' => __('This section lets you customise the WP Deals invoices. ', 'wpdeals'),
		  'id' => 'invoice_template_options' ),
	
	array(  
		'name' => __( 'Store Name', 'wpdeals' ),
		'desc' 		=> __( 'Your deal store name.', 'wpdeals' ),
		'id' 		=> 'deals_store_name',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> get_bloginfo('name')
	),
    
	array(  
		'name' => __( 'Store Logo', 'wpdeals' ),
		'desc' 		=> sprintf(__( 'Enter a URL to an image you want to show in the invoice\'s header. Upload your image using the <a href="%s">media uploader</a>.', 'wpdeals' ), admin_url('media-new.php')),
		'id' 		=> 'deals_invoice_logo_url',
		'type' 		=> 'text',
		'css' 		=> 'min-width:300px;',
		'std' 		=> ''
	),
	
	array(  
		'name' => __( 'Store Description', 'wpdeals' ),
		'desc' 		=> __( 'Your deal store description.', 'wpdeals' ),
		'id' 		=> 'deals_invoice_desc',
		'css' 		=> 'width:100%; height: 75px;',
		'type' 		=> 'textarea',
		'std' 		=> get_bloginfo('description')
	),
	
	array(  
		'name' => __( 'Invoice\'s Footer Text', 'wpdeals' ),
		'desc' 		=> __( 'The text to appear in the footer of WP Deals invoices.', 'wpdeals' ),
		'id' 		=> 'deals_invoice_footer',
		'css' 		=> 'width:100%; height: 75px;',
		'type' 		=> 'textarea',
		'std' 		=> get_bloginfo('name') . ' - ' . __('Powered by WP Deals', 'wpdeals')
	),
		
	array( 'type' => 'sectionend', 'id' => 'email_template_options' ),

)); // End email settings

/**
 * Settings page
 * 
 * Handles the display of the main wpdeals settings page in admin.
 */
if (!function_exists('deals_settings')) {
function deals_settings() {
    global $wpdeals, $deals_settings;
    
	$deals_settings = apply_filters('deals_settings_rebuild',$deals_settings);	
    $current_tab = (isset($_GET['tab'])) ? $_GET['tab'] : 'general';	
    
    if( isset( $_POST ) && $_POST ) :
    	if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpdeals-settings' ) ) die( __( 'Action failed. Please refresh the page and retry.', 'wpdeals' ) ); 
    	
                switch ( $current_tab ) :
			case "general" :
			case "deals" :
			case "invoice" :
				deals_update_options( $deals_settings[$current_tab] );
			break;
		endswitch;
		
		do_action( 'deals_update_options' );		
		do_action( 'deals_update_options_' . $current_tab );				
		
		flush_rewrite_rules( false );
		wp_redirect( add_query_arg( 'subtab', esc_attr(str_replace('#', '', $_POST['subtab'])), add_query_arg( 'saved', 'true', admin_url( 'admin.php?page=wpdeals&tab=' . $current_tab ) )) );
    endif;
    
    if (isset($_GET['saved']) && $_GET['saved']) :
		do_action('deals_update_options_saved');
    	echo '<div id="message" class="updated fade"><p><strong>' . __( 'Your settings have been saved.', 'wpdeals' ) . '</strong></p></div>';
        flush_rewrite_rules( false );
    endif;
        
    ?>
	<div class="wrap wpdeals">
		<form method="post" id="mainform" action="">
                        <div id="icon-themes" class="icon32"><br></div><h2 class="nav-tab-wrapper deals-nav-tab-wrapper">
				<?php
					$tabs = array(
						'general' => __( 'General', 'wpdeals' ),
						'deals' => __( 'Deals', 'wpdeals' ),
						'payment_gateways' => __( 'Payment Gateways', 'wpdeals' ),
						'invoice' => __( 'Invoice', 'wpdeals' ),
					);
					
					$tabs = apply_filters('deals_settings_tabs_array', $tabs);
					
					foreach ($tabs as $name => $label) :
						echo '<a href="' . admin_url( 'admin.php?page=wpdeals&tab=' . $name ) . '" class="nav-tab ';
						if( $current_tab==$name ) echo 'nav-tab-active';
						echo '">' . $label . '</a>';
					endforeach;
					
					do_action( 'deals_settings_tabs' ); 
				?>
			</h2>
			<?php wp_nonce_field( 'wpdeals-settings', '_wpnonce', true, true ); ?>
			<?php
				
				do_action('deals_settings_before_tabs_'.$current_tab);
				
                                switch ($current_tab) :
                                        case "general" :
                                        case "deals" :										
                                        case "invoice" :
                                                deals_admin_fields( $deals_settings[$current_tab] );
                                        break;                                        
                                        default :												
                                                do_action( 'deals_settings_tabs_' . $current_tab );
                                        break;
                                endswitch;
			?>
	        <p class="submit">
	        	<input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'wpdeals' ); ?>" />
	        	<input type="hidden" name="subtab" id="last_tab" />				
	        </p>
		</form>
		
		<script type="text/javascript">
                    
			jQuery(window).load(function(){
				
				// Edit prompt
				jQuery(function(){
					var changed = false;
					
					jQuery('input, textarea, select, checkbox').change(function(){
						changed = true;
					});
					
					jQuery('.deals-nav-tab-wrapper a').click(function(){
						if (changed) {
							window.onbeforeunload = function() {
							    return '<?php echo __( 'The changes you made will be lost if you navigate away from this page.', 'wpdeals' ); ?>';
							}
						} else {
							window.onbeforeunload = '';
						}
					});
					
					jQuery('.submit input').click(function(){
						window.onbeforeunload = '';
					});
				});
				
			});    
        
                        var achanged = false;

                        jQuery(document).ready(function() {

                            if(!achanged) {
                                jQuery('#gateways').addClass('active');
                                jQuery('#subtab').find('div').hide();
                                jQuery('#sub-gateways').show();
                            }
							
                            <?php if(isset($_GET['sub'])) : ?>
                                sub = '<?php echo $_GET['sub']; ?>';
                                jQuery('#subtab').find('div').hide();
                                jQuery('#sub-gateway-'+sub).show();
                                jQuery('#subtab-options').find('a').removeClass('active');
                                jQuery('a#gateway-'+sub).addClass('active');
                            <?php endif; ?>

                            jQuery('.sub-section').click(function(e) {

                                e.preventDefault();
                                aid = jQuery(this).attr('id');
                                jQuery('#subtab').find('div').hide();
                                jQuery('#sub-'+aid).show();
                                jQuery('#subtab-options').find('a').removeClass('active');                    

                                achanged = true;
                                jQuery(this).addClass('active');

                            });
							
                            jQuery('#mainform').submit(function() {

                                    jQuery('#mainform div:hidden').remove();
                                    return true;
                            });

                        });
                        
		</script>
		
	</div>
	<?php
}
}