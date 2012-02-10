<article id="post-<?php the_ID() ?>" <?php post_class($class_item) ?>>

        <?php
        global $post;
        if(is_page('Featured Deal') || is_page('Daily Deals')) {
                $addthis_url = get_permalink($post->ID);
                $addthis_title = $post->post_title;
        }
        ?>
        <img class="nodeal" src="<?php echo DEALS_IMG . 'nodeal.png'; ?>" />
        <p class="nodeal_notif"><?php _e('Oops, it looks like you just missed out on our Deals. Dang. Sign up below to stay tuned for our next bundle!', 'wpdeals'); ?></p>

        <?php deals_form_subscribe(array('idform' => 'subacribe-deals')); ?>

        <div id="social" class="nodeal_share">

                <p><?php _e('Share to the world !', 'wpdeals'); ?></p>

                <!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_default_style " addthis:url="<?php echo home_url('/'); ?>" addthis:title="<?php echo get_bloginfo('name').' &raquo; '.get_bloginfo('description'); ?>">
                <a class="addthis_button_preferred_1"></a>
                <a class="addthis_button_preferred_2"></a>
                <a class="addthis_button_preferred_3"></a>
                <a class="addthis_button_preferred_4"></a>
                <a class="addthis_button_compact"></a>
                <a class="addthis_counter addthis_bubble_style"></a>
                </div>
                <script type="text/javascript"
                        src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4ec345ab0933201f">			
                </script>
                <!-- AddThis Button END -->

        </div>
        
</article>