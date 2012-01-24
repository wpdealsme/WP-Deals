<?php

/**
 * Register custom post type
 * daily-deals
 * 
 * @return void
 */
function deals_register_posttype() {

    //arguments for custom post type
    $labels = array(
        'name'          => __('Daily Deals'),
        'singular_name' => __('Daily Deal'),
        'add_new'       => __('Add Deal'),
        'add_new_item'  => __('Add Deal'),
        'edit_item'     => __('Edit Deal')
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'menu_position' => 5,
        'supports' => array(
            'title', 'editor', 'thumbnail', 'comments'
        ),
        'rewrite' => array(
            'slug' => 'deals'
        )
    );

    //register post_type for daily-deals
    register_post_type('daily-deals', $args);   
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
    $argCategories = array(
        'labels' => array(
            'name' => 'Deal Categories',
            'singular_name' => 'Deal Category',
            'search_items' => 'Deal Search Category',
            'all_items' => 'All Deal Categories',
            'edit_item' => 'Edit Deal Category',
            'update_item' => 'Update Deal Category',
            'add_new_item' => 'Add Deal Category',
            'new_item_name' => 'New Deal Category'
        ),
        'public' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'hierarchical' => true,
        'rewrite' => array(
            'slug' => 'deal-categories',
            'hierarchical' => true
        )
    );

    //arguments for custom taxonomy > deal tags
    $argTags = array(
        'labels' => array(
            'name' => 'Deal Tags',
            'singular_name' => 'Deal Tag',
            'search_items' => 'Deal Search Tag',
            'all_items' => 'All Deal Tags',
            'edit_item' => 'Edit Deal Tag',
            'update_item' => 'Update Deal Tag',
            'add_new_item' => 'Add Deal Tag',
            'new_item_name' => 'New Deal Tag'
        ),
        'public' => true,
        'show_ui' => true,
        'show_tagcloud' => true,
        'hierarchical' => false,
        'rewrite' => array(
            'slug' => 'deal-tags',
            'hierarchical' => false
        )
    );

    register_taxonomy('deal-categories', 'daily-deals', $argCategories);
    register_taxonomy('deal-tags', 'daily-deals', $argTags);
    
}


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
                    
    $columns['cb'] = '<input type="checkbox" />';
    $columns['title'] = __('Name', 'wpdeals');
    $columns['price'] = __('Price', 'wpdeals');
    $columns['discount'] = __('Discount Price', 'wpdeals');
    $columns['stock'] = __('Item Stocks', 'wpdeals');
    $columns['expired'] = __('Status', 'wpdeals');
    $columns['featured'] = __('Featured', 'wpdeals');

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
            echo get_post_meta($post->ID, 'base_price', true);

        break;

        case 'discount' :
            echo get_post_meta($post->ID, 'discount_price', true);
        break;

        case 'stock' :
            echo get_post_meta($post->ID, 'stock', true);
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


// register
add_action('init', 'deals_register_posttype'); //register custom post type
add_action('init', 'deals_register_taxonomy'); //register custom taxonomy

// modify coloumn
add_filter( 'manage_edit-daily-deals_columns', 'deals_additional_column_names' );
add_action( 'manage_posts_custom_column', 'deals_additional_column_data'); 