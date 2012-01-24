<?php get_header('deals'); ?>

<?php do_action('deals_before_main_content'); // <div id="container"><div id="content" role="main"> ?>

<?php $container_class = (deals_get_option('view_type', 'list') == 'list') ? 'wp-deals-list' : 'wp-deals-grid'; ?>

<h1 class="entry-title"><?php the_title(); ?></h1>

<div class="<?php echo $container_class; ?>">

    <?php
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

	// Creating custom query to fetch the daily-deals type custom post.  
	query_posts(array(
                'post_type' => 'daily-deals',
                'deal-tags' => get_query_var($wp_query->query_vars['taxonomy'])
        ));

        deals_get_template_part( 'loop', 'deals' );
    ?>
    
</div>

<?php do_action('deals_after_main_content'); // </div></div> ?>

<?php do_action('deals_sidebar'); ?>

<?php get_footer('deals'); ?>
