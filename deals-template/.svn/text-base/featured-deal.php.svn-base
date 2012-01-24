<?php get_header('deals'); ?>

<?php do_action('deals_before_single_content'); // <div id="container"><div id="content" role="main"> ?>

    <div id="deal-content-wrap">
            <div id="deal-content">
                
                <?php if(deals_is_featured_exists()): ?>
                        <?php deals_featured(); ?>
                        <?php if(have_posts()): while (have_posts()) : the_post(); ?>		

                                <?php deals_get_template_part('loop', 'single-deal'); ?>

                        <?php endwhile; // end of the loop.  ?>
                        <?php wp_reset_query(); endif; ?>
                <?php else: ?>
                        <?php deals_get_template_part('form/subscribe_form'); ?>
                <?php endif; ?>
                
            </div>
            <div class="clr"></div>
    </div>
		
<?php do_action('deals_after_single_content'); // </div></div> ?>

<?php do_action('deals_sidebar'); ?>

<?php get_footer('deals'); ?>
