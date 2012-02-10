<?php
// metabox for detail deals
function deals_product_data_box(){
    global $post;
    
        wp_nonce_field( basename( __FILE__ ), 'deals_product_data_box_nonce' );

        echo '<div class="detail-deals">';
        
                deals_wp_text_input( array( 'id' => '_base_price', 'label' => __('Regular Price (value)', 'wpdeals') ) );
                deals_wp_text_input( array( 'id' => '_discount_price', 'label' => __('Discount Price (value)', 'wpdeals') ) );
                deals_wp_text_input( array( 'id' => '_stock', 'label' => __('Stock (value)', 'wpdeals') ) );
                deals_wp_date( array( 'id' => '_end_time', 'label' => __('End Time', 'wpdeals') ) );
                
                // Set expired
                deals_wp_select( array( 'id' => '_is_expired', 'label' => __('Set Expired', 'wpdeals'), 
                    'options' => array(
                        'no' => __('No', 'wpdeals'),
                        'yes' => __('Yes', 'wpdeals')
                ) ) );
                                
                // Do action for product data box
                do_action( 'deals_product_data_box_after', $post );
                
        echo '</div>';
}

/* Save the meta box's post metadata. */
function deals_product_data_save( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['deals_product_data_box_nonce'] ) || !wp_verify_nonce( $_POST['deals_product_data_box_nonce'], basename( __FILE__ ) ) ) return $post_id;
        if ( !current_user_can( 'edit_post', $post_id )) return $post_id;

        
	update_post_meta( $post_id, '_base_price', stripslashes( $_POST['_base_price'] ) );
	update_post_meta( $post_id, '_discount_price', stripslashes( $_POST['_discount_price'] ) );
	update_post_meta( $post_id, '_stock', stripslashes( $_POST['_stock'] ) );
	update_post_meta( $post_id, '_end_time', stripslashes( $_POST['_end_time'] ) );
	update_post_meta( $post_id, '_is_expired', stripslashes( $_POST['_is_expired'] ) );
                
	// Do action for product save box
	do_action( 'deals_product_data_save_after', $post_id );

}
add_action( 'save_post', 'deals_product_data_save', 10, 2 );



/**
 * metabox for detail deals
 *
 * @global type $post 
 */
function deals_product_file_box(){
    global $post;
    
        wp_nonce_field( basename( __FILE__ ), 'deals_product_file_box_nonce' );

        echo '<div class="file-deals">';
                        
                deals_wp_upload( array( 'id' => '_product_link', 'label' => __('File path', 'wpdeals') ) );
                
                // Do action for product data box
                do_action( 'deals_product_file_box_after', $post );
                
        echo '</div>';
}


/* Save the meta box's post metadata. */
function deals_product_file_save( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['deals_product_file_box_nonce'] ) || !wp_verify_nonce( $_POST['deals_product_file_box_nonce'], basename( __FILE__ ) ) ) return $post_id;      
        if ( !current_user_can( 'edit_post', $post_id )) return $post_id;
        
	update_post_meta( $post_id, '_product_link', stripslashes( $_POST['_product_link'] ) );
                
	// Do action for product save box
	do_action( 'deals_product_file_save_after', $post_id );

}
add_action( 'save_post', 'deals_product_file_save', 10, 2 );