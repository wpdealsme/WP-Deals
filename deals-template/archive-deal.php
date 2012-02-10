<?php get_header('deals'); ?>

<?php do_action('deals_before_main_content'); // <div id="container"><div id="content" role="main"> ?>
	
    <?php if (is_search()) : ?>		
            <h1 class="entry-title"><?php _e('Search Results:', 'wpdeals'); ?> &ldquo;<?php the_search_query(); ?>&rdquo; <?php if (get_query_var('paged')) echo ' &mdash; Page '.get_query_var('paged'); ?></h1>
    <?php else : ?>
            <h1 class="entry-title"><?php echo apply_filters('the_title', get_the_title()); ?></h1>
    <?php endif; ?>
    
    <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        // Creating custom query to fetch the wp-deals type custom post.  
        query_posts(array(
            'post_type' => 'daily-deals', 
            'paged' => $paged,
            'order' => 'DESC',
            'meta_key' => '_is_expired',
            'meta_value' => 'no',
            'posts_per_page' => deals_get_option('items_per_page', 9)
        ));

        deals_get_template_part( 'loop', 'deals' );
    ?>    

<?php do_action('deals_after_main_content'); // </div></div> ?>

<?php do_action('deals_sidebar'); ?>

<?php get_footer('deals'); ?>