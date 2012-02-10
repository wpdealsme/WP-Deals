<?php
/**
 * Functions for handling WordPress import to make it compatable with WP Deals
 * 
 * WordPress import should work - however, it fails to import custom deals attribute taxonomies.
 * This code grabs the file before it is imported and ensures the taxonomies are created.
 *
 * @package	WP Deals
 * @category	Admin
 * @author	Onnay Okheng
 */


function deals_import_start() {
	
	global $wpdb;
	
	$id = (int) $_POST['import_id'];
	$file = get_attached_file( $id );

	$parser = new WXR_Parser();
	$import_data = $parser->parse( $file );

	if (isset($import_data['posts'])) :
		$posts = $import_data['posts'];
		
		if ($posts && sizeof($posts)>0) foreach ($posts as $post) :
			
			if ($post['post_type']=='daily-deals') :
				
				if ($post['terms'] && sizeof($post['terms'])>0) :
					
					foreach ($post['terms'] as $term) :
						
						$domain = $term['domain'];
						
						if (strstr($domain, 'pa_')) :
							
							// Make sure it exists!
							if (!taxonomy_exists( $domain )) :
								
								$nicename = strtolower(sanitize_title(str_replace('pa_', '', $domain)));
																
								// Register the taxonomy now so that the import works!
								register_taxonomy( $domain,
							        array('daily-deals'),
							        array(
							            'hierarchical' => true,
							            'labels' => array(
							                    'name' => $nicename,
							                    'singular_name' => $nicename,
							                    'search_items' =>  __( 'Search', 'wpdeals') . ' ' . $nicename,
							                    'all_items' => __( 'All', 'wpdeals') . ' ' . $nicename,
							                    'parent_item' => __( 'Parent', 'wpdeals') . ' ' . $nicename,
							                    'parent_item_colon' => __( 'Parent', 'wpdeals') . ' ' . $nicename . ':',
							                    'edit_item' => __( 'Edit', 'wpdeals') . ' ' . $nicename,
							                    'update_item' => __( 'Update', 'wpdeals') . ' ' . $nicename,
							                    'add_new_item' => __( 'Add New', 'wpdeals') . ' ' . $nicename,
							                    'new_item_name' => __( 'New', 'wpdeals') . ' ' . $nicename
							            ),
							            'show_ui' => false,
							            'query_var' => true,
							            'rewrite' => array( 'slug' => strtolower(sanitize_title($nicename)), 'with_front' => false, 'hierarchical' => true ),
							        )
							    );
								
							endif;
							
						endif;
						
					endforeach;
					
				endif;
				
			endif;
			
		endforeach;
		
	endif;

}

add_action('import_start', 'deals_import_start');