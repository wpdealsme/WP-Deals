<?php get_header("deals"); ?>

<?php do_action('deals_before_main_content'); // <div id="container"><div id="content" role="main"> ?>
                
        <?php deals_featured(); ?>
        <?php if(have_posts()): while (have_posts()) : the_post(); ?>		

                <?php deals_get_template_part('loop-single', 'deals'); ?>

        <?php endwhile; // end of the loop.  ?>
        <?php wp_reset_query(); ?>
        <?php else: ?>

                <h1 class="entry-title"><?php _e('Sorry No deals found', 'wpdeals') ?></h1>

                <?php deals_get_template('form/subscribe-form.php'); ?>

        <?php endif; ?>

<?php do_action('deals_after_main_content'); // </div></div> ?>

<?php do_action('deals_sidebar'); ?>

<?php get_footer("deals"); ?>
