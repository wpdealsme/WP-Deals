<?php
/**
 * template for loop deals.
 */

$deals_columns = apply_filters('loop_deals_columns', 3);

$i = 1;

?>

<?php do_action('deals_before_loop'); ?>

<div <?php deals_container_class(); ?>>

        <?php
        // Looping through the posts and building the HTML structure.  
        if (have_posts()):
            
                while (have_posts()): the_post();
        
                $class_item = ($i % $deals_columns == 1)? ' first-deals-item' : '';
                $class_item .= ($i % $deals_columns == 0)? ' last-deals-item' : '';
        
                ?>

                <article id="post-<?php the_ID() ?>" <?php post_class($class_item) ?>>

                            <?php do_action('deals_before_loop_item_title'); ?>

                            <h2 class="recent-deal-title entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                            <?php do_action('deals_after_loop_item_title'); ?>
                    
                </article>
    
                <?php $i++; ?>
    
                <?php endwhile; ?>

        <?php else : ?>

                <?php deals_get_template('form/subscribe-form.php'); ?>

        <?php endif; ?>

</div>

<?php do_action('deals_pagination'); ?>

<?php do_action('deals_after_loop'); ?>