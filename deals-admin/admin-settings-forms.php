<?php
/**
 * Functions for outputting and updating settings pages
 * 
 * @author 	WP Deals
 * @category 	Admin
 * @package 	WP Deals
 */
 
/**
 * Update options
 * 
 * Updates the options on the wpdeals settings pages. Returns true if saved.
 */
function deals_update_options($options) {
    
    if(!isset($_POST) || !$_POST) return false;
    
    foreach ($options as $value) {
    	if (isset($value['id']) && $value['id']=='deals_tax_rates') :
    		
    		// Tate rates saving
    		$tax_classes = array();
    		$tax_countries = array();
    		$tax_rate = array();
    		$tax_rates = array();
    		$tax_shipping = array();
    		
			if (isset($_POST['tax_class'])) $tax_classes = $_POST['tax_class'];
			if (isset($_POST['tax_country'])) $tax_countries = $_POST['tax_country'];
			if (isset($_POST['tax_rate'])) $tax_rate = $_POST['tax_rate'];
			if (isset($_POST['tax_shipping'])) $tax_shipping = $_POST['tax_shipping'];
			
			for ($i=0; $i<sizeof($tax_classes); $i++) :
			
				if (isset($tax_classes[$i]) && isset($tax_countries[$i]) && isset($tax_rate[$i]) && is_numeric($tax_rate[$i])) :
					
					$rate = esc_attr(trim($tax_rate[$i]));
					if ($rate>100) $rate = 100;
					$rate = number_format($rate, 4, '.', '');
					
					$class = deals_clean($tax_classes[$i]);
					
					if (isset($tax_shipping[$i]) && $tax_shipping[$i]) $shipping = 'yes'; else $shipping = 'no';
					
					// Handle countries
					$counties_array = array();
					$countries = $tax_countries[$i];
					if ($countries) foreach ($countries as $country) :
						
						$country = deals_clean($country);
						$state = '*';
						
						if (strstr($country, ':')) :
							$cr = explode(':', $country);
							$country = current($cr);
							$state = end($cr);
						endif;
					
						$counties_array[trim($country)][] = trim($state);
						
					endforeach;
					
					$tax_rates[] = array(
						'countries' => $counties_array,
						'rate' => $rate,
						'shipping' => $shipping,
						'class' => $class
					); 
					
				endif;

			endfor;
			
			update_option($value['id'], $tax_rates);
		
		elseif (isset($value['type']) && $value['type']=='multi_select_countries') :
		
			// Get countries array
			if (isset($_POST[$value['id']])) $selected_countries = $_POST[$value['id']]; else $selected_countries = array();
			update_option($value['id'], $selected_countries);
		
		elseif ( isset($value['id']) && ( $value['id'] == 'deals_price_thousand_sep' || $value['id'] == 'deals_price_decimal_sep' ) ):
			
			// price separators get a special treatment as they should allow a spaces (don't trim)
			if( isset( $_POST[ $value['id'] ] )  ) {
				update_option($value['id'], $_POST[$value['id']] );
			} else {
                delete_option($value['id']);
            }
            
        elseif (isset($value['type']) && $value['type']=='checkbox') :
            
            if(isset($value['id']) && isset($_POST[$value['id']])) {
            	update_option($value['id'], $_POST[$value['id']]);
            } else {
                update_option($value['id'], 0);
            }
            
        elseif (isset($value['type']) && $value['type']=='image_width') :
            	
            if(isset($value['id']) && isset($_POST[$value['id'].'_width'])) {
              	update_option($value['id'].'_width', deals_clean($_POST[$value['id'].'_width']));
            	update_option($value['id'].'_height', deals_clean($_POST[$value['id'].'_height']));
				if (isset($_POST[$value['id'].'_crop'])) :
					update_option($value['id'].'_crop', 1);
				else :
					update_option($value['id'].'_crop', 0);
				endif;
            } else {
                update_option($value['id'].'_width', $value['std']);
            	update_option($value['id'].'_height', $value['std']);
            	update_option($value['id'].'_crop', 1);
            }	
            	
    	else :
		    
    		if(isset($value['id']) && isset($_POST[$value['id']])) {
            	update_option($value['id'], deals_clean($_POST[$value['id']]));
            } else {
                delete_option($value['id']);
            }
        
        endif;
        
    }
    return true;
}

/**
 * Admin fields
 * 
 * Loops though the wpdeals options array and outputs each field.
 */
function deals_admin_fields($options) {
	global $wpdeals;

    foreach ($options as $value) :
    	if (!isset( $value['name'] ) ) $value['name'] = '';
    	if (!isset( $value['class'] )) $value['class'] = '';
    	if (!isset( $value['css'] )) $value['css'] = '';
    	if (!isset( $value['std'] )) $value['std'] = '';
        switch($value['type']) :
            case 'title':
            	if (isset($value['name']) && $value['name']) echo '<h3>'.$value['name'].'</h3>'; 
            	if (isset($value['desc']) && $value['desc']) echo wpautop(wptexturize($value['desc']));
            	echo '<table class="form-table">'. "\n\n";
            	if (isset($value['id']) && $value['id']) do_action('deals_settings_'.sanitize_title($value['id']));
            break;
            case 'sectionend':
            	if (isset($value['id']) && $value['id']) do_action('deals_settings_'.sanitize_title($value['id']).'_end');
            	echo '</table>';
            	if (isset($value['id']) && $value['id']) do_action('deals_settings_'.sanitize_title($value['id']).'_after');
            break;
            case 'text':
            	?><tr valign="top">
					<th scope="row" class="titledesc"><?php echo $value['name']; ?></th>
                    <td class="forminp"><input name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" type="<?php echo esc_attr( $value['type'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>" value="<?php if ( get_option( $value['id'] ) !== false && get_option( $value['id'] ) !== null ) { echo esc_attr( stripslashes( get_option($value['id'] ) ) ); } else { echo esc_attr( $value['std'] ); } ?>" /> <span class="description"><?php echo $value['desc']; ?></span></td>
                </tr><?php
            break;
			case 'hidden':
				?><tr valign="top">					
                    <td class="forminp"><input name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" type="hidden" style="<?php echo esc_attr( $value['css'] ); ?>" value="<?php if ( get_option( $value['id'] ) !== false && get_option( $value['id'] ) !== null ) { echo esc_attr( stripslashes( get_option($value['id'] ) ) ); } else { echo esc_attr( $value['std'] ); } ?>" /></td>
                </tr><?php
			break;
            case 'color' :
            	?><tr valign="top">
					<th scope="row" class="titledesc"><?php echo $value['name']; ?></th>
                    <td class="forminp"><input name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" type="text" style="<?php echo esc_attr( $value['css'] ); ?>" value="<?php if ( get_option( $value['id'] ) !== false && get_option( $value['id'] ) !== null ) { echo esc_attr( stripslashes( get_option($value['id'] ) ) ); } else { echo esc_attr( $value['std'] ); } ?>" class="colorpick" /> <span class="description"><?php echo $value['desc']; ?></span> <div id="colorPickerDiv_<?php echo esc_attr( $value['id'] ); ?>" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div></td>
                </tr><?php
            break;
            case 'image_width' :
            	?><tr valign="top">
					<th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                    <td class="forminp">
                    	
                    	<?php _e('Width', 'wpdeals'); ?> <input name="<?php echo esc_attr( $value['id'] ); ?>_width" id="<?php echo esc_attr( $value['id'] ); ?>_width" type="text" size="3" value="<?php if ( $size = get_option( $value['id'].'_width') ) echo stripslashes($size); else echo $value['std']; ?>" /> 
                    	
                    	<?php _e('Height', 'wpdeals'); ?> <input name="<?php echo esc_attr( $value['id'] ); ?>_height" id="<?php echo esc_attr( $value['id'] ); ?>_height" type="text" size="3" value="<?php if ( $size = get_option( $value['id'].'_height') ) echo stripslashes($size); else echo $value['std']; ?>" /> 
                    	
                    	<label><?php _e('Hard Crop', 'wpdeals'); ?> <input name="<?php echo esc_attr( $value['id'] ); ?>_crop" id="<?php echo esc_attr( $value['id'] ); ?>_crop" type="checkbox" <?php if (get_option( $value['id'].'_crop')!='') checked(get_option( $value['id'].'_crop'), 1); else checked(1); ?> /></label> 
                    	
                    	<span class="description"><?php echo $value['desc'] ?></span></td>
                </tr><?php
            break;
            case 'select':
            	?><tr valign="top">
					<th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                    <td class="forminp"><select name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>" class="<?php if (isset($value['class'])) echo $value['class']; ?>">
                        <?php
                        foreach ($value['options'] as $key => $val) {
                        ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php if (get_option($value['id']) == $key) { ?> selected="selected" <?php } ?>><?php echo ucfirst($val) ?></option>
                        <?php
                        }
                        ?>
                       </select> <span class="description"><?php echo $value['desc'] ?></span>
                    </td>
                </tr><?php
            break;
            case 'checkbox' :
            
            	if (!isset($value['checkboxgroup']) || (isset($value['checkboxgroup']) && $value['checkboxgroup']=='start')) :
            		?>
            		<tr valign="top">
					<th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
					<td class="forminp">
					<?php
            	endif;
            	
            	?>
	            <fieldset><legend class="screen-reader-text"><span><?php echo $value['name'] ?></span></legend>
					<label for="<?php echo $value['id'] ?>">
					<input name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" type="checkbox" value="1" <?php checked(get_option($value['id']), 1); ?> />
					<?php echo $value['desc'] ?></label><br>
				</fieldset>
				<?php
				
				if (!isset($value['checkboxgroup']) || (isset($value['checkboxgroup']) && $value['checkboxgroup']=='end')) :
					?>
						</td>
					</tr>
					<?php
				endif;
				
            break;
            case 'textarea':
            	?><tr valign="top">
					<th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                    <td class="forminp">
                        <textarea <?php if ( isset($value['args']) ) echo $value['args'] . ' '; ?>name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>"><?php if (get_option($value['id'])) echo esc_textarea(stripslashes(get_option($value['id']))); else echo esc_textarea( $value['std'] ); ?></textarea> <span class="description"><?php echo $value['desc'] ?></span>
                    </td>
                </tr><?php
            break;
            case 'single_select_page' :
            	$page_setting = (int) get_option($value['id']);
            	
            	$args = array( 'name'				=> $value['id'],
            				   'id'					=> $value['id'],
            				   'sort_column' 		=> 'menu_order',
            				   'sort_order'			=> 'ASC',
            				   'show_option_none' 	=> ' ',
            				   'class'				=> $value['class'],
            				   'echo' 				=> false,
            				   'selected'			=> $page_setting);
            	
            	if( isset($value['args']) ) $args = wp_parse_args($value['args'], $args);
            	
            	?><tr valign="top" class="single_select_page">
                    <th scope="row" class="titledesc"><?php echo $value['name'] ?></th>
                    <td class="forminp">
                            <?php echo str_replace(' id=', " data-placeholder='".__('Select a page...', 'wpdeals')."' style='".$value['css']."' class='".$value['class']."' id=", wp_dropdown_pages($args)); ?> <span class="description"><?php echo $value['desc'] ?></span>        
                    </td>
               	</tr><?php	
            break;
        endswitch;
    endforeach;
}
