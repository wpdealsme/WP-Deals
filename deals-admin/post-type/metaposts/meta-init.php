<?php
/**
 * Init the meta boxes
 * 
 * @author 	WP Deals
 * @category 	Admin Write Meta
 * @package 	WP Deals
 */

include_once( 'meta-daily-deals.php' ); // meta post for post type daily-deals
include_once( 'meta-deals-sales.php' ); // meta post for post type deals-sales

/**
 * Init the meta boxes
 * 
 * Inits the write panels for both products and orders. Also removes unused default write panels.
 */
add_action( 'add_meta_boxes', 'deals_meta_boxes' );

function deals_meta_boxes() {
	
	// Daily Deals
	add_meta_box( 'deals-product-data-box', __('Deals Detail', 'wpdeals'), 'deals_product_data_box', 'daily-deals', 'side', 'high' );
	add_meta_box( 'deals-product-file-box', __('Deals File', 'wpdeals'), 'deals_product_file_box', 'daily-deals', 'normal', 'high' );
    
	//deal sales
	add_meta_box( 'deals-sales-change-status', __('Change Status', 'wpdeals'), 'deals_sales_status_box', 'deals-sales', 'side', 'core');
	add_meta_box( 'deals-sales-submit', __('Save Transaction', 'wpdeals'), 'deals_sales_save_box', 'deals-sales', 'side', 'high');
	add_meta_box( 'deals-sales-payment', __('Payment Methods', 'wpdeals'), 'deals_sales_payments_box', 'deals-sales', 'side', 'high');
	add_meta_box( 'deals-sales-detail', __('Transaction Details', 'wpdeals'), 'deals_sales_detail_box', 'deals-sales', 'normal', 'high');
	
	remove_meta_box( 'submitdiv', 'deals-sales', 'side');
	remove_meta_box( 'slugdiv', 'deals-sales' , 'normal' );
	remove_meta_box( 'commentstatusdiv', 'deals-sales', 'normal' );	
	
}

/**
 * Title boxes
 */
add_filter('enter_title_here', 'deals_enter_title_here', 1, 2);

function deals_enter_title_here( $text, $post ) {
    
	if ($post->post_type=='daily-deals') return __('Deals name', 'wpdeals');
	if ($post->post_type=='deals-sales') return __('Deals Sales', 'wpdeals');
        
	return $text;
}


/**
 * Output write panel form elements
 */
function deals_wp_text_input( $field ) {
	global $thepostid, $post;
	
	if (!$thepostid) $thepostid = $post->ID;
	if (!isset($field['placeholder'])) $field['placeholder'] = '';
	if (!isset($field['class'])) $field['class'] = 'short';
	if (!isset($field['value'])) $field['value'] = get_post_meta($thepostid, $field['id'], true);
	
	echo '<p class="form-field '.$field['id'].'_field"><label for="'.$field['id'].'">'.$field['label'].'</label><input type="text" class="'.$field['class'].'" name="'.$field['id'].'" id="'.$field['id'].'" value="'.esc_attr( $field['value'] ).'" placeholder="'.$field['placeholder'].'" /> ';
	
	if (isset($field['description'])) echo '<span class="description">' .$field['description'] . '</span>';
		
	echo '</p>';
}

function deals_wp_upload( $field ) {
	global $thepostid, $post;
	
	if (!$thepostid) $thepostid = $post->ID;
	if (!isset($field['placeholder'])) $field['placeholder'] = '';
	if (!isset($field['class'])) $field['class'] = 'short';
	if (!isset($field['value'])) $field['value'] = get_post_meta($thepostid, $field['id'], true);
?>
        <script>
        jQuery(document).ready(function() {
            
                // Uploading files
                var file_path_field;

                window.send_to_editor_default = window.send_to_editor;

                jQuery('.upload_file_button').live('click', function(){

                        file_path_field = jQuery(this).parent().find('.file_path');

                        formfield = jQuery(file_path_field).attr('name');

                        window.send_to_editor = window.send_to_download_url;

                        tb_show('', 'media-upload.php?post_id=' + <?php echo $post->ID; ?> + '&amp;type=downloadable_product&amp;from=wc01&amp;TB_iframe=true');
                        return false;
                });

                window.send_to_download_url = function(html) {

                        file_url = jQuery(html).attr('href');
                        if (file_url) {
                                jQuery(file_path_field).val(file_url);
                        }
                        tb_remove();
                        window.send_to_editor = window.send_to_editor_default;

                }
        });
        </script>
<?php

        // File URL
        $file_path = get_post_meta($post->ID, $field['id'], true);
        echo '<p class="form-field"><label for="'.$field['id'].'">'.$field['label'].':</label>
                <input type="text" class="short file_path" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$file_path.'" placeholder="'.__('File path/URL', 'wpdeals').'" style="width:50%;"  />
                <input type="button"  class="upload_file_button button" value="'.__('Upload a file', 'wpdeals').'" />
        </p>';
}

function deals_wp_checkbox( $field ) {
	global $thepostid, $post;
	
	if (!$thepostid) $thepostid = $post->ID;
	if (!isset($field['class'])) $field['class'] = 'checkbox';
	if (!isset($field['wrapper_class'])) $field['wrapper_class'] = '';
	if (!isset($field['value'])) $field['value'] = get_post_meta($thepostid, $field['id'], true);
	
	echo '<p class="form-field '.$field['id'].'_field '.$field['wrapper_class'].'"><label for="'.$field['id'].'">'.$field['label'].'</label><input type="checkbox" class="'.$field['class'].'" name="'.$field['id'].'" id="'.$field['id'].'" ';
	
	checked($field['value'], 'yes');
	
	echo ' /> ';
	
	if (isset($field['description'])) echo '<span class="description">' .$field['description'] . '</span>';
		
	echo '</p>';
}

function deals_wp_select( $field ) {
	global $thepostid, $post;
	
	if (!$thepostid) $thepostid = $post->ID;
	if (!isset($field['class'])) $field['class'] = 'select short';
	if (!isset($field['value'])) $field['value'] = get_post_meta($thepostid, $field['id'], true);
	
	echo '<p class="form-field '.$field['id'].'_field"><label for="'.$field['id'].'">'.$field['label'].'</label><select id="'.$field['id'].'" name="'.$field['id'].'" class="'.$field['class'].'">';
	
	foreach ($field['options'] as $key => $value) :
		
		echo '<option value="'.$key.'" ';
		selected($field['value'], $key);
		echo '>'.$value.'</option>';
		
	endforeach;
	
	echo '</select> ';
	
	if (isset($field['description'])) echo '<span class="description">' .$field['description'] . '</span>';
		
	echo '</p>';
}


/**
 * display form for metabox date picker.
 *
 * @param type $id
 * @param type $value 
 */
function deals_wp_date($field){
	global $thepostid, $post;
	
	if (!$thepostid) $thepostid = $post->ID;
	if (!isset($field['placeholder'])) $field['placeholder'] = '';
	if (!isset($field['class'])) $field['class'] = 'short';
	if (!isset($field['value'])) $field['value'] = get_post_meta($thepostid, $field['id'], true); ?>
        
        <script type="text/javascript">
        jQuery(document).ready(function() {
           jQuery('#<?php echo $field['id']; ?>').datetimepicker({
				dateFormat: 'yy-mm-dd',
				timeFormat: 'hh:mm:ss'
		   });	
        });
        </script>
        
        <?php echo '<p class="form-field '.$field['id'].'_field"><label for="'.$field['id'].'">'.$field['label'].'</label><input type="text" class="'.$field['class'].'" name="'.$field['id'].'" id="'.$field['id'].'" value="'.esc_attr( $field['value'] ).'" placeholder="'.$field['placeholder'].'" style="width:130px;" /> ';
	
	if (isset($field['description'])) echo '<span class="description">' .$field['description'] . '</span>';
		
	echo '</p>';
}


/**
 * For displaying js date picker on metabox.
 */
function deals_meta_date_js(){
        global $post, $currentpage;

        if($post->post_type == 'daily-deals'){

                wp_register_script('ui-custom', DEALS_JS . "jquery-ui-1.8.16.custom.min.js", '', array('jquery'));
                wp_register_script('datepicker-js', DEALS_JS . "jquery-ui-timepicker-addon.js", '', null);        

                wp_enqueue_script( 'jquery' );
                wp_enqueue_script( 'ui-custom' );
                wp_enqueue_script( 'datepicker-js' );    

        }
        
}

/**
 * For displaying js date picker on metabox.
 */
function deals_meta_date_css(){
        global $post;

        if($post->post_type == 'daily-deals'){

                wp_register_style( 'ui-custom-style', DEALS_CSS.'jquery-ui-1.8.16.custom.css');
                wp_register_style( 'ui-timepicker-style', DEALS_CSS.'jquery-ui-timepicker-addon.css');

                wp_enqueue_style( 'ui-custom-style' );
                wp_enqueue_style( 'ui-timepicker-style' );

        }
    
}


/**
 * Directory for uploads
 */
function deals_downloads_upload_dir( $pathdata ) {

	if (isset($_POST['type']) && $_POST['type'] == 'downloadable') :
		
		// Uploading a downloadable file
		$subdir = '/deals_uploads'.$pathdata['subdir'];
	 	$pathdata['path'] = str_replace($pathdata['subdir'], $subdir, $pathdata['path']);
	 	$pathdata['url'] = str_replace($pathdata['subdir'], $subdir, $pathdata['url']);
		$pathdata['subdir'] = str_replace($pathdata['subdir'], $subdir, $pathdata['subdir']);
		return $pathdata;
		
	endif;
	
	return $pathdata;
}
function deals_media_upload_downloadable_product() {
	do_action('media_upload_file');
}

// add action
add_action('admin_print_styles','deals_meta_date_css');
add_action('admin_print_scripts','deals_meta_date_js');
add_filter('upload_dir', 'deals_downloads_upload_dir');
add_action('media_upload_downloadable_product', 'deals_media_upload_downloadable_product');