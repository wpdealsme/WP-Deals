<?php

global $deals_meta_box,$deals_product_meta_box;

$prefix_meta_box = '';
$deals_meta_box = array(
    'id'        => 'deals-detail-meta-box',
    'title'     => 'Deal Properties',
    'page'      => 'daily-deals',
    'context'   => 'side',
    'priority'  => 'high',
    'fields'    => array(
        array(
            'name'  => __('Base Price', 'wpdeals'),
            'desc'  => __('Price', 'wpdeals'),
            'id'    => $prefix_meta_box . 'base_price',
            'type'  => 'text',
            'std'   => 0
        ),
        array(
            'name'  => __('Discount Price', 'wpdeals'),
            'desc'  => __('Price', 'wpdeals'),
            'id'    => $prefix_meta_box . 'discount_price',
            'type'  => 'text',
            'std'   => 0
        ),
        array(
            'name'  => __('Stock', 'wpdeals'),
            'desc'  => __('Stock', 'wpdeals'),
            'id'    => $prefix_meta_box . 'stock',
            'type'  => 'text',
            'std'   => 0
        ),
        array(
            'name'  => __('End Time', 'wpdeals'),
            'desc'  => __('End Deals', 'wpdeals'),
            'id'    => $prefix_meta_box . 'end_time',
            'type'  => 'date',
            'std'   => ''
        ),
        array(
            'name'  => __('Set Expired', 'wpdeals'),
            'desc'  => __('', 'wpdeals'),
            'id'    => $prefix_meta_box . 'is_expired',
            'type'  => 'select',
            'std'   => 'no',
            'options'=> array('no','yes')
        )
    ),
);
$deals_product_meta_box = array(
    'id'        => 'deals-product-meta-box',
    'title'     => 'Deal Product',
    'page'      => 'daily-deals',
    'context'   => 'normal',
    'priority'  => 'high',
    'fields'    => array(
	array(
            'name' => __('Product', 'framework'),
            'desc' => __('Product Link', 'framework'),
            'id' => $prefix_meta_box . 'product_link',
            'type' => 'upload',
            'std' => ''
        ),
    ),
);

/**
 * Add custom meta box
 * @return void
 */
function deals_add_detail_meta() {
    global $deals_meta_box, $deals_product_meta_box;

    add_meta_box($deals_meta_box['id'], $deals_meta_box['title'], 'deals_meta_detail', $deals_meta_box['page'], $deals_meta_box['context'], $deals_meta_box['priority']);
    add_meta_box($deals_product_meta_box['id'], $deals_product_meta_box['title'], 'deals_meta_product', $deals_product_meta_box['page'], $deals_product_meta_box['context'], $deals_product_meta_box['priority']);
}

add_action('add_meta_boxes', 'deals_add_detail_meta');

/**
 * Get meta detail
 * 
 * @return void
 */
function deals_meta_detail() {

    global $deals_meta_box, $post;
    //echo '<p style="padding:10px 0 0 0;">'.__('Entry your link.', 'tokokoo').'</p><pre>';

    echo '<table class="form-table">';

    foreach ($deals_meta_box['fields'] as $field) {
        // get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);

        deals_the_metabox($field, $meta);
    }

    echo '</table>';
}

/**
 * Get meta product output
 * @return void
 */
function deals_meta_product() {

    global $deals_product_meta_box, $post;
    //echo '<p style="padding:10px 0 0 0;">'.__('Entry your link.', 'tokokoo').'</p><pre>';

    echo '<table class="form-table">';

    foreach ($deals_product_meta_box['fields'] as $field) {
        // get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);

        deals_the_metabox($field, $meta);
    }

    echo '</table>';
}

/**
 * Custom action to save all
 * custom meta value
 * 
 * @param int $post_id
 * @return void|int
 */
function deals_save_detail_data($post_id) {

    global $deals_meta_box, $deals_product_meta_box;

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // check permissions
    if ($deals_meta_box['page'] == @$_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    // check permissions
    if ($deals_product_meta_box['page'] == @$_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    foreach ($deals_meta_box['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = @$_POST[$field['id']];

//        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
//        } elseif ('' == $new && $old) {
//            delete_post_meta($post_id, $field['id'], $old);
//        }
    }

    foreach ($deals_product_meta_box['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = @$_POST[$field['id']];

//        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
//        } elseif ('' == $new && $old) {
//            delete_post_meta($post_id, $field['id'], $old);
//        }
    }        
        
}
add_action('save_post', 'deals_save_detail_data');

/**
 * Custom action to send an email
 * when saving post
 * 
 * @param int $post_id
 * @return void
 */
function deals_publish($post_id){    
    
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    
    $posts = get_post($post_id);
    if($posts->post_type == 'daily-deals' && isset($_POST['publish']) && $_POST['publish'] == 'Publish'){
        
        deals_log($posts->post_type.' - '.$posts->post_status);

        deals_log('Start logging to sent mail to all subscribers');

        $subscribers = get_option('deals_subscribed_emails');
        $subscribers = maybe_unserialize($subscribers);

        if(!empty($subscribers) && is_array($subscribers)) {

            foreach($subscribers as $subscriber) {
                deals_log('Start sent mail subscribes to: '.$subscriber);
                $mail_subject = 'WP-Deals - '.$posts->post_title;
                $mail_message = 'Hello <br />';
                $mail_message .= 'For today, we announcing a new deal that is, '.$posts->post_title.'<br />';
                $mail_message .= 'Please go to our site to read more detail, '.get_permalink($post_id);

                $mail_header = "Content-Type: text/html" . "\r\n";
                $mail_header .= ' From: '.  get_bloginfo('name') . ' <'.  get_option('admin_email') .'>' . "\r\n";
                wp_mail($subscriber,$mail_subject,$mail_message,$mail_header);

            }

            deals_log('================================================');

        }else{
            deals_log('Empty subscribers when try to sent : '.$posts->post_title);
        }  

    }    
    
}
add_action('save_post', 'deals_publish');

/**
 * Set metabox output type
 * 
 * @param array $field [optional]
 * @param null|string $meta [optional]
 * @return void
 */
function deals_the_metabox($field = array(), $meta = null) {

    switch ($field['type']) {

                //If Text
                case 'text':

                        echo '<tr style="border-top:1px solid #eeeeee;">',
                                '<th style="width:25%"><label for="', $field['id'], '"><strong>', $field['name'], '</strong><span style=" display:block; color:#999; line-height: 20px; margin:5px 0 0 0;">'. $field['desc'].'</span></label></th>',
                                '<td>';
                        echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta , '" size="30" style="width:75%; margin-right: 20px; float:left;" />';

                    break;
                
                //If date
                case 'date':

                        echo '<tr style="border-top:1px solid #eeeeee;">',
                                '<th style="width:25%"><label for="', $field['id'], '"><strong>', $field['name'], '</strong><span style=" display:block; color:#999; line-height: 20px; margin:5px 0 0 0;">'. $field['desc'].'</span></label></th>',
                                '<td>';
                        $value  = $meta ? $meta : stripslashes(htmlspecialchars(( $field['std']), ENT_QUOTES));
                        
                        echo deals_get_meta_date($field['id'], $value );
                        //echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : stripslashes(htmlspecialchars(( $field['std']), ENT_QUOTES)), '" size="30" style="width:75%; margin-right: 20px; float:left;" />';
                        
                    break;

                //If Text
                case 'textarea':

                        echo '<tr style="border-top:1px solid #eeeeee;">',
                                '<th style="width:25%">
                                    <label for="', $field['id'], '"><strong>', $field['name'],
                                    '</strong><span style=" display:block; color:#999; line-height: 20px; margin:5px 0 0 0;">'. $field['desc'].'</span></label></th>',
                                '<td>';
                        echo '<textarea name="', $field['id'], '" id="', $field['id'], '" style="width:75%; margin-right: 20px; float:left; height:80px;" >', $meta ? $meta : stripslashes(htmlspecialchars(( $field['std']), ENT_QUOTES)),'</textarea>';

                    break;
                
                //If Button
                case 'button':
                        echo '<input style="float: left;" type="button" class="button" name="', $field['id'], '" id="', $field['id'], '"value="', $meta ? $meta : $field['std'], '" />';
                        echo 	'</td>',
                        '</tr>';

                    break;
                //If Select
                case 'select':

                        echo '<tr style="border-top:1px solid #eeeeee;">',
                        '<th style="width:25%"><label for="', $field['id'], '"><strong>', $field['name'], '</strong><span style=" display:block; color:#999; margin:5px 0 0 0;">'. $field['desc'].'</span></label></th>',
                        '<td>';

                        echo'<select name="'.$field['id'].'">';

                        foreach ($field['options'] as $option) {
                            echo $meta.'-'.$option;

                                echo'<option';
                                if ($meta == $option ) {
                                        echo ' selected="selected"';
                                }
                                echo'>'. $option .'</option>';

                        }

                        echo'</select>';

                    break;

                //If upload
                case 'upload':
?>
                        <script>
                        jQuery(document).ready(function() {
                                jQuery('#<?php echo $field['id']; ?>_button').click(function() {

                                        window.send_to_editor = function(html) {
                                                imgurl = jQuery('img',html).attr('src');
                                                jQuery('#<?php echo $field['id']; ?>').val(imgurl);
                                                tb_remove();
                                        }
                                        tb_show('', 'media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true');
                                        return false;
                                });
                        });
                        </script>
<?php
                        echo '<tr style="border-top:1px solid #eeeeee;">',
                        '<th style="width:25%"><label for="', $field['id'], '"><strong>', $field['name'], '</strong><span style=" display:block; color:#999; line-height: 20px; margin:5px 0 0 0;">'. $field['desc'].'</span></label></th>',
                        '<td>';
                        echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : stripslashes(htmlspecialchars(( $field['std']), ENT_QUOTES)), '" size="30" style="width:75%; margin-right: 20px; float:left;" />';
                        echo '<input style="float: left;" type="button" class="button" name="', $field['id'], '_button" id="', $field['id'], '_button" value="Browse" />';
                        echo 	'</td>',
                        '</tr>';

                break;

    }
}

/**
 * For displaying js date picker on metabox.
 */
function deals_meta_date_js(){
    global $post,$currentpage;
    
    //if($post->post_type == 'daily-deals'){
            
        wp_register_script('ui-custom', DEALS_JS . "jquery-ui-1.8.16.custom.min.js", '', array('jquery'));
        wp_register_script('datepicker-js', DEALS_JS . "jquery-ui-timepicker-addon.js", '', null);        
        
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'ui-custom' );
        wp_enqueue_script( 'datepicker-js' );    
    //}
    
}

/**
 * javascript for print web elements
 */
function deals_meta_print_js() {
    
    wp_register_script('jqprint',DEALS_JS.'jquery.jqprint.js','',null);
    wp_enqueue_script( 'jqprint' );    
    
}

add_action('admin_print_scripts','deals_meta_print_js');
add_action('admin_print_scripts','deals_meta_date_js');
//add_action('admin_print_scripts-post-new.php', 'deals_meta_date_js');
//add_action('admin_print_scripts-post.php', 'deals_meta_date_js');


/**
 * For displaying js date picker on metabox.
 */
function deals_meta_date_css(){
    global $post;
        
    //if($post->post_type == 'daily-deals'){
            
        wp_register_style( 'ui-custom-style', DEALS_CSS.'jquery-ui-1.8.16.custom.css');
        wp_register_style( 'ui-timepicker-style', DEALS_CSS.'jquery-ui-timepicker-addon.css');
        
        wp_enqueue_style( 'ui-custom-style' );
        wp_enqueue_style( 'ui-timepicker-style' );
    //}
    
}
add_action('admin_print_styles','deals_meta_date_css');
//add_action('admin_print_styles-post-new.php', 'deals_meta_date_css');
//add_action('admin_print_styles-post.php', 'deals_meta_date_css');


/**
 * display form for metabox date picker.
 *
 * @param type $id
 * @param type $value 
 */
function deals_get_meta_date($id, $value){
?>
        <script type="text/javascript">
        jQuery(document).ready(function() {
           jQuery('#<?php echo $id; ?>').datetimepicker({
				dateFormat: 'yy-mm-dd',
				timeFormat: 'hh:mm:ss'
		   });	
        });
        </script>
<?php    
    echo '<input type="text" name="'.$id.'" id="'.$id.'" value="'.$value.'" size="30" style="width:75%; margin-right: 20px; float:left;" />';
}