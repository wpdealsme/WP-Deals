<?php
// Looping through the posts and building the HTML structure.  
if (have_posts()):
        $i = 1;
        while (have_posts()): the_post();

                // default
                $is_expired     = deals_is_expired();
                $item_stock     = get_post_meta(get_the_ID(), 'stock', true);

                // convert date and time to array
                $deals_end      = deals_get_end_date(get_the_ID());

                // get image size
                $default_image      = deals_get_option('default_image', array('width' => 100, 'height' => 100));
                $default_img_width  = $default_image['width'];
                $default_img_height = $default_image['height'];
                ?>

                <article id="post-<?php the_ID() ?>" <?php post_class() ?>>

                        <div class="recent-deal-wrap<?php if ($i % 3 == 0) echo ' last-per-row'?>">

                                <span class="recent-image-wrap">
                                        <a href="<?php the_permalink(); ?>">
                                                <?php deals_image( 'width='.$default_img_width.'&height='.$default_img_height.'&class=deal-thumbnail'); ?>
                                        </a>				
                                </span>

                                <div class="recent-deal-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </div>

                                <div class="another-timer-block">
                                        <?php if ($is_expired == 0) : ?>
                                                <script type="text/javascript">
                                                        // jQuery Countdown
                                                        jQuery(document).ready(function() {
                                                            var austDay = new Date(
                                                                    <?php echo $deals_end[0]; // year  ?>,
                                                                    <?php echo ($deals_end[1] - 1); // month - 1 ?>,
                                                                    <?php echo $deals_end[2]; // date  ?>,  
                                                                    <?php echo $deals_end[3]; // hour  ?>,
                                                                    <?php echo $deals_end[4]; // minute  ?>)
                                                            jQuery("#deal_ends_<?php the_ID(); ?>").countdown({until: austDay,onExpiry:dealExpired<?php the_ID();?>,
                                                                expiryText:'<div class="deal_expired"><?php _e('Deals Expired', 'wpdeals'); ?></div>'});

                                                            checkZero = jQuery('.hasCountdown').find('span.countdown_amount').text();

                                                            if(checkZero < 1) {                        
                                                                jQuery('#deal_ends_<?php the_ID(); ?>').find('span').remove();
                                                                jQuery('#deal_ends_<?php the_ID(); ?>').append('<div class="deal_expired"><?php _e('Deals Expired', 'wpdeals'); ?></div>');

                                                            }

                                                        });
                                                        function dealExpired<?php the_ID(); ?>() {
                                                            var postID = <?php the_ID(); ?>;                                                            
                                                            jQuery.post(
                                                               '<?php echo admin_url("admin-ajax.php"); ?>', 
                                                               {
                                                                  'action':'deals_ajax_post_action',
                                                                  'type': 'expired',
                                                                  'data': postID
                                                               }, 
                                                               function(response){                            
                                                                    //pass
                                                               }
                                                            );
                                                        }
                                                </script>
                                                <div id="deal_ends_<?php the_ID(); ?>"></div>
                                        <?php else: ?>
                                                <div class="deal_expired">Deals Expired</div>
                                        <?php endif; ?>
                                </div> <!-- timer block -->

                                <?php if ($is_expired == 0) : ?>
                                
                                        <?php if( deals_is_free() ) : ?>
                                        <div class="deal_expired"><?php _e('Free', 'wpdeals'); ?></div>
                                        <?php else: ?>
                                        <div class="deal-discount-block">
                                                <span class="list-price-title"><?php _e('Value', 'wpdeals'); ?><br/><span class="price-num"><?php echo deals_price(); ?></span></span>
                                                <span class="list-price-title"><?php _e('Discount', 'wpdeals'); ?><br/><span class="price-num"><?php echo deals_save_percent(); ?></span></span>
                                                <span class="list-price-title"><?php _e('You Save', 'wpdeals'); ?><br/><span class="price-num"><?php echo deals_save_price(); ?></span></span>							
                                        </div>
                                        <?php endif; ?>
                                
                                <?php endif; ?>

                                <div class="deal-description"><?php the_excerpt(); ?></div>
                        </div>
                </article>
                <?php $i++; ?>
        <?php endwhile; ?>

<?php else : ?>

        <?php deals_get_template_part('form/subscribe_form'); ?>

<?php endif; ?>

<?php if (  $loop->max_num_pages > 1 ) : ?>
        <div class="navigation clear">
                <div class="nav-next"><?php next_posts_link( __( 'Next <span class="meta-nav">&rarr;</span>', 'dailydeals' ),$loop->max_num_pages ); ?></div>
                <div class="nav-previous"><?php previous_posts_link( __( '<span class="meta-nav">&larr;</span> Previous', 'dailydeals' ) ); ?></div>
        </div>
<?php endif; ?>