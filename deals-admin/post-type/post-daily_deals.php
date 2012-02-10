<?php

/**
 * deals_additional_column_names function.
 *
 * @access public
 * @param (array) $columns
 * @return (array) $columns
 *
 */
function deals_additional_column_names( $columns ){
    $columns = array();
                    
    $columns['cb']          = '<input type="checkbox" />';
    $columns['title']       = __('Deals', 'wpdeals');
    $columns['price']       = __('Price', 'wpdeals');
    $columns['discount']    = __('Discount Price', 'wpdeals');
    $columns['stock']       = __('Item Stocks', 'wpdeals');
    $columns['expired']     = __('Status', 'wpdeals');
    $columns['featured']    = __('Featured', 'wpdeals');

    return $columns;
}


/**
 * deals_additional_column_data.
 *
 * @access public
 * @param (array) $column
 * @return void
 * @todo Need to check titles / alt tags ( I don't think thumbnails have any in this code )
 * @desc Switch function to generate columns the right way...no more UI hacking!
 *
 */
function deals_additional_column_data( $column ) {
    global $post;

    switch ( $column ) :

        case 'price' :
            if(deals_price(false) == 0 OR deals_price(false) == '')
                _e('Free', 'wpdeals');
            else
                echo deals_price();

        break;

        case 'discount' :
            if(deals_discount(false) == 0 OR deals_discount(false) == '')
                _e('Free', 'wpdeals');
            else
                echo deals_discount();
        break;

        case 'stock' :
            $stock  = get_post_meta($post->ID, '_stock', true);
            $status = ($stock == 0 OR !isset($stock) )? __('Empty', 'wpdeals'): $stock ;
            echo $stock;
        break;

        case 'expired' :
            $status = (deals_is_expired($post->ID) == 1)? '<span style="color: red;">Expired</span>':'<span style="color: #21759B;">Available</span>';
            echo $status;
        break;

        case 'featured' :
            $featured_deal_url = wp_nonce_url( "index.php?deals_admin_action=update_featured_deal&amp;deal_id=$post->ID", 'feature_deal_' . $post->ID);
?>
                <a class="deals_featured_deal_toggle featured_toggle_<?php echo $post->ID; ?>" href='<?php echo $featured_deal_url; ?>' >
                    <?php if ( in_array( $post->ID, (array)get_option( 'sticky_deals' ) ) ) : ?>
                        <img src='<?php echo DEALS_IMG.'unset-featured.png'; ?>' alt='Unset Featured' title='Unset Featured'/>
                    <?php else: ?>
                        <img src='<?php echo DEALS_IMG.'set-featured.png'; ?>' alt='Set Featured' title='Set Featured'/>
                    <?php endif; ?>
                </a>
                <?php

        break;

    endswitch;

}

// modify coloumn
add_filter( 'manage_edit-daily-deals_columns', 'deals_additional_column_names' );
add_action( 'manage_posts_custom_column', 'deals_additional_column_data'); 