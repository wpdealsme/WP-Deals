<?php

deals_secure();

class Payment_Options {
    
    private $_payment_options = array();
    private $_payment_handlers = array();
    
    public function register_hook_options() {
                
        $payments = apply_filters('deals_payment_methods',$avai_payments);
        
        /*
         Check if extension has been disabled then rebuild used payments
         */
        $used_payments = get_option('deals_payments_used');
        if(!empty($used_payments)) {
            
            $rebuild_used_payments = array();
            foreach($used_payments as $used) {
                
                if(array_key_exists($used,$payments)) {
                    $rebuild_used_payments[] = $used;
                }else{
                    $option_gateway = 'deals_payment_gateway_'.$used;
                    update_option($option_gateway,0);
                }
                
            }
            
            //rebuild database
            update_option('deals_payments_used',$rebuild_used_payments);            
            
        }                        
        
        $options['payment_gateways'] = apply_filters('deals_payment_gateways', array(
		
            array(	'name' => __( 'Payment Gateways', 'wpdeals' ),
                  'type' => 'title',
                  'desc' => __('This section lets you manage your payment gateways. ', 'wpdeals'),
                  'id' => 'deals_payment_gateways_options' ),                        
        
        )); // End email settings
                
        $total_payments = count($payments);
        $i = 0;
        
        foreach($payments as $payment_id => $payment_data) {
        
            $i++;
            
            if( 1 == $i) {
            
                $options['payment_gateways'][] = array(
                    'name' => __('Available Payments','wpdeals'),                
                    'desc' => $payment_data['name'],
                    'id' => 'deals_payment_gateway_'.$payment_id,
                    'checkboxgroup' => 'start',
                    'type' => 'checkbox'
                );
                
            }else{
                
                if($i == $total_payments) {
                
                    $options['payment_gateways'][] = array(
                        'name' => __('Available Payments','wpdeals'),                
                        'desc' => $payment_data['name'],
                        'id' => 'deals_payment_gateway_'.$payment_id,
                        'checkboxgroup' => 'end',
                        'type' => 'checkbox'
                    );
                    $end_payment_id = 'deals_payment_gateway_'.$payment_id;
                    
                }else{
                    $options['payment_gateways'][] = array(
                        'name' => __('Available Payments','wpdeals'),                
                        'desc' => $payment_data['name'],
                        'id' => 'deals_payment_gateway_'.$payment_id,
                        'checkboxgroup' =>'',
                        'type' => 'checkbox'
                    );
                }
                
            }                                 
            
        }
        
        $options['payment_gateways'][] = array(
            'type' => 'sectionend',
            'id' => $end_payment_id
        );                
        
        $this->_payment_options = $options;
        $this->_payment_handlers = $payments;
        
        update_option('deals_payments',$this->_payment_handlers);
        add_filter('deals_settings_rebuild',array($this,'hook_options'),10,1);
        add_action('deals_settings_tabs_payment_gateways',array($this,'hook_option_display'));            
        add_action('deals_settings_before_tabs_payment_gateways',array($this,'hook_before_tab_content'));
        
        $this->_hook_option_update();
        
    }
    
    public function hook_before_tab_content() {
        
        $used_payments = get_option('deals_payments_used');
        
        ?>
        <div id="subtab-options">
            <span>
                <a id="gateways" href="<?php echo admin_url('/admin.php?page=wpdeals&tab=payment_gateways#gateways'); ?>" class="sub-section">Select Gateways</a> |
                <?php
                
                if(!empty($used_payments) && is_array($used_payments)) {
                    
                    $payments = get_option('deals_payments');
                    foreach($used_payments as $payment_id) {
                        
                        $payment_name = $payments[$payment_id]['name'];
                        
                        ?>
                        <a id="gateway-<?php echo $payment_id;?>" href="#gateway-<?php echo $payment_id;?>" class="sub-section">
                            <?php echo $payment_name; ?>
                        </a> |
                        <?php
                        
                    }
                    
                }
                
                ?>
            </span>
        </div>
        <?php
        
    }
    
    public function hook_option_updated() {
        
        global $deals_settings;        
        
        $current_tab = $_GET['tab'];
        $sub_action = isset($_POST['form_sub_action']) ? $_POST['form_sub_action'] : null;        
        
        if(is_null($sub_action)) {
            
            deals_update_options($deals_settings[$current_tab]);
        
            /*
             Rebuild used payments
             */
            $avai_payments = get_option('deals_payments');
            if(!empty($avai_payments) && is_array($avai_payments)) {
                
                $payments = array();
                foreach($avai_payments as $payment_id => $payment_data) {
                    $payments[] = 'deals_payment_gateway_'.$payment_id;
                }
                
                $used_payments = array();
                foreach($payments as $payment) {
                    if(array_key_exists($payment,$_POST)) {
                        $payment_id = str_replace('deals_payment_gateway_','',$payment);
                        $used_payments[] = trim($payment_id);
                    }
                }
                            
                //used payments
                update_option('deals_payments_used',$used_payments);
                                
            }
                        
        }else{
            
            $data = array();
            $default = array('_wpnonce','_wp_http_referer','save','subtab','form_sub_action');
            foreach($_POST as $post_key => $post_value) {
                
                if(!in_array($post_key,$default)) {
                    $data[$post_key] = $post_value;
                }
                
            }
            
            $payments = get_option('deals_payments');
            $payment_file = $payments[$sub_action]['path'];
            
            $payment_class = $this->_get_payment_class($sub_action,$payment_file);
            $payment_class->save_options($data);
            
            wp_redirect(admin_url('/admin.php?page=wpdeals&tab=payment_gateways&saved=true&sub='.$sub_action));
            exit();
            
        }        
        
    }        
    
    public function hook_option_display() {
        
        global $deals_settings;
        
        $used_payments = get_option('deals_payments_used');
        $payments = get_option('deals_payments');
        
        ?>
        <div id="subtab">
            
            <div id="sub-gateways">
                <?php deals_admin_fields($deals_settings['payment_gateways']); ?>
            </div>
            
            <?php
            
            if(!empty($used_payments) && is_array($used_payments)) {
                
                foreach($used_payments as $payment_id) {
                
                    $payment_filepath = $payments[$payment_id]['path'];
                    $payment_class = $this->_get_payment_class($payment_id,$payment_filepath);
                    
                    echo '<div id="sub-gateway-'.$payment_id.'" style="display:none">';
                    $payment_class->admin_options();
                    echo '</div>';
                    
                }
                
            }
            
            ?>                        
            
        </div>
        <?php
        
    }
    
    public function hook_options($content) {
        return array_merge($content,$this->_payment_options);
    }
    
    private function _get_payment_class($id,$filepath) {
        
        $payment_class = null;
        if(file_exists($filepath)) {
            
            require_once($filepath);
            $payment_class_name = ucwords(str_replace('_',' ',strtolower($id)));
            $payment_class_name = str_replace(' ','_',$payment_class_name);
            
            $class_name = 'Payment_'.$payment_class_name;
            
            if(class_exists($class_name)) {
                
                $payment_class = new $class_name();
                if(!is_subclass_of($payment_class,'Payment_Gateway_Abstract')) {
                    $payment_class = null;
                }
                
            }
            
        }
        
        return $payment_class;
        
    }
    
    private function _hook_option_update() {
                
        $used_payments = get_option('deals_payments_used');
        add_action('deals_update_options_payment_gateways',array($this,'hook_option_updated'));        
        
    }
    
}