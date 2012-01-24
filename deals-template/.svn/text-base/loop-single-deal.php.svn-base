<?php
// default
global $post;
$is_expired     = deals_is_expired(get_the_ID());
$item_stock     = get_post_meta(get_the_ID(), 'stock', true);

// convert date and time to array
$deals_end      = deals_get_end_date(get_the_ID());

// get image size
$single_image       = deals_get_option('single_image');
$single_img_width   = $single_image['width'];
$single_img_height  = $single_image['height'];  

?>

<h1 class="deal-title"><?php the_title(); ?></h1>

<?php do_action('deals_top_content'); ?>

<div class="deal-wrapper buy-wrap">

    
    <?php deals_button(); ?>
    <!-- price block -->
    
                    
    <!-- popup form -->    
    <div style="display: none;">
		<div id="subscribe_deals">
			<div id="modal-overlay"></div>

			<div id="modal-container">
				<div id="modal-bg">
					<div id="modal-close"><a href="">Close</a></div>
					<h2 class="modal-title">Deals</h2>
					<h3 class="modal-tagline">Subscribe to Premium Pixels and get notified about each release.</h3>

					<div id="subs-container" class="clearfix">
						<div class="modal-side left">
							<div class="modal-icon">
								<span class="email">Subscribe by Email</span>
							</div>
							<h4>Subscribe by Email</h4>
							<h5>Get sent a nice little alert directly to your inbox after each release. Oooo, handy!</h5> 
							<?php deals_form_subscribe('top'); ?>
						</div><div id="modal-or">OR</div>
						<div class="modal-side right">
							<div class="modal-icon">
								<span class="rss">Subscribe by Email</span>
							</div>
							<h4>Subscribe by RSS</h4>
							<h5>Add the RSS to your feedreader and get news of deals releases as they happen!</h5> 
							<a class="sub" href="<?php echo deals_get_option('rss_link'); ?>">Subscribe</a>
						</div>
					</div> 
					<a class="destroy" href="">Hey, I'm already subscribed to this awesome site, there's no need to show me this ever again.</a>
				</div>
			</div>
		</div>
    </div>    
    
    
    <!-- display price and detail -->
    <?php if(deals_is_free()) : ?>
        <div class="deal_expired" style="background-color: #50B25F;border:none;color:#efefef"><?php _e('Free', 'wpdeals'); ?></div>
    <?php else: ?>
        <div class="deal-discount-block">
            <span class="tiny-price-title"><?php _e('Value', 'wpdeals'); ?><br/><span class="price-num"><?php echo deals_price(); ?></span></span>
            <span class="tiny-price-title"><?php _e('Discount', 'wpdeals'); ?><br/><span class="price-num"><?php echo deals_save_percent(); ?></span></span>
            <span class="tiny-price-title"><?php _e('You Save', 'wpdeals'); ?><br/><span class="price-num"><?php echo deals_save_price(); ?></span></span>							
            <div class="clr"></div>
        </div>
    <?php endif; ?>

    <!-- display countdown deal -->
    <?php if ($is_expired == 0) : ?>
        <div id="timer-block">
            <script type="text/javascript">
                // jQuery Countdown
                jQuery(document).ready(function() {
                    var austDay = new Date(
                            <?php echo $deals_end[0]; // year  ?>,
                            <?php echo ($deals_end[1] - 1); // month - 1 ?>,
                            <?php echo $deals_end[2]; // date  ?>,  
                            <?php echo $deals_end[3]; // hour  ?>,
                            <?php echo $deals_end[4]; // minute  ?>)
                    jQuery("#deal_ends_<?php the_ID(); ?>").countdown({until: austDay,
                        onExpiry:dealExpired,expiryText:''});

                    checkZero = jQuery('.hasCountdown').find('span.countdown_amount').text();
                    
                    if(checkZero < 1) {                        
                        jQuery('#deal_ends_<?php the_ID(); ?>').find('span').remove();
                        jQuery('#bottom-price-block').remove();
                        //jQuery('#deal_ends_<?php the_ID(); ?>').append('<div class="deal_expired"><?php _e('Deals Expired', 'wpdeals'); ?></div>');
                        
                    }
                    

                });
                                
                function dealExpired() {
                    var postID = <?php the_ID(); ?>;
                    
                    jQuery.post(
                       '<?php echo admin_url("admin-ajax.php"); ?>', 
                       {
                          'action':'deals_ajax_post_action',
                          'type': 'expired',
                          'data': postID
                       }, 
                       function(response){                            
                            jQuery('a.buy-button').remove();
                            jQuery('#timer-block').remove();
                            jQuery('#bottom-price-block').remove();
                            jQuery('#price-block').append('<span class="expired-button"><span class="buy-label"><?php _e('Deals Expired', 'wpdeals'); ?></span></span>');                            
                       }
                    );
                }
                
                
            </script>
            <div class="timer-msg">
                <?php _e('This deal will end in:', 'wpdeals'); ?>
            </div> 
            <div id="deal_ends_<?php the_ID(); ?>"></div>                        
        </div>
    <?php endif; ?>


    <?php if(deals_get_option('share') == 'yes'): ?>
        <!-- display share deal -->
        <div id="deal-share-block">
                <!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_default_style " addthis:url="<?php the_permalink(); ?>" addthis:title="<?php the_title(); ?>">
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
    <?php endif; ?>
    
    <div class="clr"></div>      

</div> <!-- end of deal-wrapper -->	


<!-- display thumbnail deal -->
<div class="deal-image-wrap">
        <?php $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'full'); ?>
        <a href="<?php echo $image[0]; ?>" class="fancy">                
            <?php deals_image('width=' . $single_img_width . '&height=' . $single_img_height . '&class=single-deal-thumbnail'); ?>
        </a>
</div>

<div class="clr"></div>

<!-- display the content -->
<div id="deal-details">

    <div class="description-info">

        <?php do_action('deals_before_description',$post); ?>
        <?php the_content(); ?>       
        <?php do_action('deals_after_description',$post); ?>
        
        <?php
        
            do_action('deals_before_bottom_price_block',$post);
            $args = array(
                    'container_open'    => '<div id="bottom-price-block">',
                    'container_close'   => '</div>'
            );
            deals_button($args);
            do_action('deals_after_bottom_price_block',$post);
            
        ?>

    </div>

</div><!-- end of deal details -->

<!-- display footer deal -->
<div id="deal-footer">
    <span class="deal-categories">
        <?php echo get_the_term_list(get_the_ID(), 'deal-categories', __('Posted in: ', 'wpdeals'), ', ', ''); ?> 
    </span>
    <span class="deal-tags">
        <?php echo get_the_term_list(get_the_ID(), 'deal-tags', __('Tags: ', 'wpdeals)'), ', ', ''); ?> 
    </span>
</div>
<?php do_action('deals_bottom_content', $post); ?>