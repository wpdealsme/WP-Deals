<?php

//secure included files
deals_secure();

/**
 * Output the errors and messages
 *
 * @param type $messages
 * @param type $errors
 * @return type 
 */
function _show_messages($messages='', $errors='') {

    if ((isset($errors)) && (count($errors) > 0)) :
        echo '<div class="wpdeals_error">' . $errors . '</div>';
        return true;
    elseif ((isset($messages)) && (count($messages) > 0)) :
        echo '<div class="wpdeals_message">' . $messages . '</div>';
        return true;
    else :
        return false;
    endif;
}

/**
 * http://net.tutsplus.com/tutorials/wordpress/quick-tip-making-a-fancy-wordpress-register-form-from-scratch/
 */
function _show_login_form() {
    ?>

    <h2><?php _e('Login', 'wpdeals'); ?></h2>
    <?php
    $args = array(
        'form_id' => 'deals-loginform" class="deals-user',
        'redirect' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] //home_url( '/'.$_SERVER['REQUEST_URI'] )
    );
    wp_login_form($args);
    ?>

    <h2><?php _e('Register', 'wpdeals'); ?></h2>
    <form method="post" class="deals-user" autocomplete="off" action="<?php echo site_url('wp-login.php?action=register', 'login_post') ?>">

        <p class="form-row form-row-first">
            <label for="reg_username"><?php _e('Username', 'wpdeals'); ?> <span class="required">*</span></label>
            <input type="text" class="input-text" name="user_login" id="user_login" value="<?php if (isset($_POST['user_login']))
        echo $_POST['user_login']; ?>" />
        </p>
        <p class="form-row form-row-last">
            <label for="reg_email"><?php _e('Email', 'wpdeals'); ?> <span class="required">*</span></label>
            <input type="email" class="input-text" name="user_email" id="user_email" <?php if (isset($_POST['user_email']))
                   echo $_POST['user_email']; ?> />
        </p>
        <div class="clear"></div>

        <p class="form-row">
            <?php do_action('register_form'); ?>  
            <input type="submit" class="button" name="register" value="<?php _e('Register', 'wpdeals'); ?>" />
        </p>

        <p class="statement">A password will be e-mailed to you.</p>

    </form>


    <?php
}

/**
 * Manage user profile
 * @return void 
 */
function deals_user_profile() {

    $user_id = get_current_user_id();

    $user_data = get_userdata($user_id);

//    echo '<pre>';
//    var_dump($user_data);
//    var_dump($_POST);
//    var_dump($errors);
//    echo '</pre>';

    if (is_user_logged_in()) :
        if ($_POST) :
            if ($user_id > 0 && wp_verify_nonce($_POST['deals_change_password'], 'deals_change_password_action')) :
                if ($_POST['password-1'] && $_POST['password-2']) :
                    if ($_POST['password-1'] == $_POST['password-2']) :
                        wp_update_user(array('ID' => $user_id, 'user_pass' => $_POST['password-1']));
                        $messages = __('Your password is updated successfully.', 'wpdeals');
                        wp_safe_redirect(get_permalink(get_option('deals_page_profile_id')));
                    else :
                        $errors = __('Passwords do not match.', 'wpdeals');
                    endif;                
                else:
                    $errors = __('Please enter your password.', 'wpdeals');
                endif;
            endif;
        endif;

        if(isset($messages)) {
            _show_messages($messages, $errors);    
        }
        
        ?>
        <form action="<?php echo esc_url(get_permalink(get_option('deals_page_profile_id'))); ?>" method="post" class="deals-user">

            <p class="form-row form-row-first">
                <label for="user_login"><?php _e('Username', 'wpdeals'); ?></label>
                <input type="text" class="input-text" name="user_login" id="user_login" value="<?php echo $user_data->user_login; ?>" readonly="readonly" />
            </p>
            <p class="form-row">
                <label for="display_name"><?php _e('Full Name', 'wpdeals'); ?></label>
                <input type="text" class="input-text" name="display_name" id="display_name" value="<?php echo $user_data->display_name; ?>" readonly="readonly" />
            </p>
            <p class="form-row">
                <label for="password-1"><?php _e('New password', 'wpdeals'); ?> <span class="required">*</span></label>
                <input type="password" class="input-text" name="password-1" id="password-1" />
            </p>
            <p class="form-row form-row-last">
                <label for="password-2"><?php _e('Re-enter new password', 'wpdeals'); ?> <span class="required">*</span></label>
                <input type="password" class="input-text" name="password-2" id="password-2" />
            </p>
            <div class="clear"></div>
            <?php wp_nonce_field('deals_change_password_action', 'deals_change_password') ?>
            <p><input type="submit" class="button" name="save_password" value="<?php _e('Save', 'wpdeals'); ?>" /></p>

        </form>
        <?php
    else :

//        wp_safe_redirect(home_url());
//        exit;
        _show_login_form();

    endif;
}

/**
 * Shortcode for thanks page
 * @return string
 */
function deals_thanks_deal(){
    
    global $wpdb;
    
    if (is_page(get_option('deals_page_thanks_post_id')) ) {        
        if (!isset($_REQUEST['txn_id'])) {            
            return 'Invalid Request';            
        } else {                   
            
            $item_id = $_GET['item_id'];
            $user_id = $_GET['user_id'];
            $user_first_name = $_REQUEST['first_name'];
            $user_last_name = $_REQUEST['last_name'];
            $txn_id = $_REQUEST['txn_id'];                        
            
            global $checkVerify;
            $checkVerify = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'wpdeals_sales WHERE transaction_id="' . $txn_id . '" 
                AND user_id="'.$user_id.'" AND item_id="'.$item_id.'"');
            
            //get current user login info
            $wp_user = wp_get_current_user();
            
            if( !($wp_user instanceof WP_User) ) {
                $mail_to = $_REQUEST['payer_email'];
            }else{
                $mail_to = $wp_user->user_email;
            }
            
            $mail_subject = '[SUCCEED] Your Deals Transaction For ' . $_REQUEST['item_name'];                        
            
            if(!empty($checkVerify)) {
                
                $template = DEALS_TEMPLATE_DIR . 'form/mail_invoice.php';
                deals_log('Start payment verification for item: '.$item_id.' buyer_id: '.$user_id.' txn_id: '.$txn_id);
                
                if(file_exists($template)) {
                    
                    global $invoice_options, $invoice_data,$checkVerify;
                    
                    //create barcode
                    deals_image_create_barcode($txn_id,$txn_id.'.png');
                    $img_barcode_url = DEALS_IMG.'barcodes/'.$txn_id.'.png';
                    
                    $invoice_options = array(
                        'info'      => deals_get_option('invoice_desc'),
                        'logo_url'  => deals_get_option('invoice_logo_url'),
                        'store_name'=> deals_get_option('store_name'),
                        'footer'    => deals_get_option('invoice_footer'),
                        'barcode' => $img_barcode_url
                    );

                    $item_raw = get_post($item_id);
                    
                    $invoice_data = array(
                        'title' => $item_raw->post_title,   
                        'link' => home_url('/my-history'),
                        'user_name' => $user_first_name.' '.$user_last_name
                    );
                    
                    deals_log('Generated invoice data : '.addslashes(serialize($invoice_data)));
                    
                    ob_start();

                    load_template($template);

                    $mail_content = ob_get_clean();
                    $headers = "Content-Type: text/html" . "\r\n";
                    $headers .= ' From: '.  get_bloginfo('name') . ' <'.  get_option('admin_email') .'>' . "\r\n";
                    //$sent_email = wp_mail($mail_to, $mail_subject, $mail_content, $headers);
                    $sent_email_status = (wp_mail($mail_to, $mail_subject, $mail_content, $headers) == true )? 'sent' : 'error';  
                    
                    deals_log('Invoice for '.$user_first_name.'-'.$user_last_name.' :'.$sent_email_status);

                    //sent to admin
                    $admin_email = get_option('admin_email');
                    $admin_mail_subject = 'Deal Transaction - Invoice - '.$_REQUEST['item_name'];
                    //$admin_sent_email = wp_mail($admin_email,$admin_mail_subject,$mail_content,$headers);
                    $admin_mail_status = (wp_mail($admin_email,$admin_mail_subject,$mail_content,$headers) == true )? 'send':'error';
                    
                    deals_log('Invoice '.$txn_id.' for administrator :'.$admin_mail_status);
                    
                    //deals_minus_inventory($item_id);
                    
                    deals_log('End verification and invoices process for transaction_id: '.$txn_id);
                    deals_log('===================================================================');
                    return 'Thank you for your buying, please check your user profile page to download your link. We have been sent an invoice to your email address : '.$mail_to;

                }else{
                    deals_log('Mail template not exists');                    
                }
                
            }else{
                
                deals_log('Transaction ID : '.$txn_id.' failed for item id : '.$item_id.' and user : '.$user_id);
                
                if(is_array($checkVerify)) {
                    deals_log(serialize($checkVerify),'deals-log-'.$txn_id.'-failed');
                }
                
                return 'We are sorry, but it seems your transaction is not valid or verified, please check your paypal account and contact us.';
            }
            
        }
    }    
    
}

/**
 * Shortcode for user history
 * @return void
 */
function deals_user_history() {
    
    $user_id = get_current_user_id();

    if (is_user_logged_in()) :
        
        global $wpdb;

        $sql = "SELECT p.ID,s.id,s.buy_date, p.post_title, pm.meta_value " .
                "FROM " . $wpdb->prefix . "wpdeals_sales AS s, " .
                $wpdb->prefix . "posts AS p, " . $wpdb->prefix . "postmeta AS pm " .
                "WHERE s.item_id = p.ID AND s.item_id = pm.post_id AND pm.meta_key = 'product_link' " .
                "AND s.user_id = " . $user_id;

        $rows = $wpdb->get_results($sql);
        
        if(isset($_GET['invid'])) {
            
            $inv_id = intval($_GET['invid']);
            if(is_int($inv_id) && $inv_id > 0) {
                
                $sales_id = $wpdb->get_var('SELECT sales_id FROM '.$wpdb->prefix.'wpdeals_invoices WHERE id="'.$inv_id.'"');            
                $item_id = $wpdb->get_var('SELECT item_id FROM '.$wpdb->prefix.'wpdeals_sales WHERE id="'.$sales_id.'"');            
                $txn_id = $wpdb->get_var('SELECT transaction_id FROM '.$wpdb->prefix.'wpdeals_sales WHERE item_id="'.$item_id.'"');        
                $user_id = $wpdb->get_var('SELECT user_id FROM '.$wpdb->prefix.'wpdeals_sales WHERE id="'.$sales_id.'"');        
                $user_name = $wpdb->get_var('SELECT user_nicename FROM '.$wpdb->prefix.'users WHERE ID="'.$user_id.'"');
                
                //create barcode
                deals_image_create_barcode($txn_id,$txn_id.'.png');
                $img_barcode_url = DEALS_IMG.'barcodes/'.$txn_id.'.png';
                
                $invoice_options = array(
                    'info' => deals_get_option('invoice_desc'),
                    'logo_url' => deals_get_option('invoice_logo_url'),
                    'store_name' => deals_get_option('store_name'),
                    'footer' => deals_get_option('invoice_footer'),
                    'barcode' => $img_barcode_url
                );
        
                $item_raw = get_post($item_id);
                $invoice_data = array(
                    'title' => $item_raw->post_title,
                    'link' => home_url('/my-history'),
                    'user_name' => $user_name
                );
        
                $checkVerify = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'wpdeals_sales WHERE id="'.$sales_id.'"');
                                
                require_once DEALS_PLUGIN_PATH.'deals-assets/libs/class-deals-invoice-pdf.php';
                $pdf = new Deals_Invoice_Pdf(compact('invoice_options','item_raw','invoice_data','checkVerify'));
                $pdf->make();                
                
                $pdf_download = DEALS_PLUGIN_PATH.'deals-assets/invoices/invoice-'.$checkVerify->transaction_id.'.pdf';                
                $pdf_name = 'invoice-'.$checkVerify->transaction_id.'.pdf';                
                
                ob_start();
                header("Cache-Control: no-cache, must-revalidate");                
                header("Content-Disposition: attachment; filename=$pdf_name");
                header("Content-Type: application/pdf");                
                readfile($pdf_download);
                ob_end_flush();
                
            }
        }
        ?>

        <h2>Transaction History</h2>
        <table class="deals_table">

            <thead>
                <tr>
                    <th>Buy Date</th>
                    <th>Deal Items</th>
                    <th>Download Link</th>
                    <th>Invoices</th>
                </tr>
            </thead>

            <tbody>
        <?php
        foreach ($rows as $row) :
        
            /*
             hook to download link post meta             
            */
            $link_download = apply_filters('deals_download_link',$row->meta_value,$row->ID);
            $sql2 = 'SELECT id FROM '.$wpdb->prefix.'wpdeals_invoices WHERE sales_id="'.$row->id.'"';
            $inv_id = $wpdb->get_var($sql2);
            
            ?>
                    <tr>
                        <td><?php echo $row->buy_date; ?></td>
                        <td><?php echo $row->post_title; ?></td>
                        <td><a href="<?php echo $link_download; ?>">Download</a></td>
                        <td><a href="?invid=<?php echo $inv_id;?>">Download</a></td>
                    </tr>
            <?php
        endforeach;
        ?>
            </tbody>

        </table>


        <?php
    else :
        _show_login_form();
    endif;
    
}

// shorcode
add_shortcode('wpdeals_user_history','deals_user_history');
add_shortcode('wpdeals_user_profile', 'deals_user_profile');
add_shortcode('thanksdeal', 'deals_thanks_deal');
