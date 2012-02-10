<?php get_header('deals'); ?>

<?php do_action('deals_before_main_content'); // <div id="container"><div id="content" role="main"> ?>

    <?php $term = get_term_by( 'slug', get_query_var($wp_query->query_vars['taxonomy']), $wp_query->query_vars['taxonomy']); ?>

    <h1 class="entry-title"><?php echo wptexturize($term->name); ?></h1>

    <?php deals_get_template_part( 'loop', 'deals' ); ?>
    
<?php do_action('deals_after_main_content'); // </div></div> ?>

<?php do_action('deals_sidebar'); ?>

<?php get_footer('deals'); ?>