<?php get_header("deals"); ?>

<?php do_action('deals_before_main_content'); // <div id="container"><div id="content" role="main"> ?>

        <?php while (have_posts()) : the_post(); ?>		

                <?php deals_get_template_part('loop-single', 'deals'); ?>

        <?php endwhile; // end of the loop.  ?>

<?php do_action('deals_after_main_content'); // </div></div> ?>

<?php do_action('deals_sidebar'); ?>

<?php get_footer("deals"); ?>
