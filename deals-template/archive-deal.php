<?php get_header('deals'); ?>

<?php do_action('deals_before_main_content'); // <div id="container"><div id="content" role="main"> ?>

<?php $container_class = (deals_get_option('view_type', 'list') == 'list') ? 'wp-deals-list' : 'wp-deals-grid'; ?>

<h1 class="entry-title"><?php the_title(); ?></h1>

<div class="<?php echo $container_class; ?> wp-deals-wrapper">

    <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        // Creating custom query to fetch the wp-deals type custom post.  
        query_posts(array(
            'post_type' => 'daily-deals', 
            'paged' => $paged,
            'orderby' => 'meta_value',
            'meta_key' => 'is_expired',
            'meta_value' => 'no',
            'order' => 'DESC'
        ));

        deals_get_template_part( 'loop', 'deals' );
    ?>
    
</div>


<?php do_action('deals_after_main_content'); // </div></div> ?>

<?php do_action('deals_sidebar'); ?>

<?php get_footer('deals'); ?>