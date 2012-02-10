<?php
// default
global $post;

// get image size
$single_img_width   = deals_get_option('single_image_width', 300);
$single_img_height  = deals_get_option('single_image_height', 250);
$single_img_crop    = (deals_get_option('single_image_crop', 1) == 1)? true:false;

?>

<h1 class="entry-title"><?php the_title(); ?></h1>

<?php do_action('deals_before_single_content', $post); ?>

<div class="deal-wrapper buy-wrap">
    
        <?php do_action('deals_before_top_button'); ?>

        <!-- deal button -->
        <?php deals_button(); ?>

        <?php do_action('deals_after_top_button'); ?>

        <div class="clr"></div>      

</div> <!-- end of deal-wrapper -->	


<!-- display thumbnail deal -->
<figure class="deal-image-wrap">
    
        <?php if(has_post_thumbnail()): ?>
    
                <a href="<?php echo deals_get_thumb_image_url(get_the_ID()); ?>" class="fancy">
                        <?php deals_image( 'width='.$single_img_width.'&height='.$single_img_height.'&crop='.$single_img_crop.'&class=single-deal-thumbnail&alt='. get_the_title() ); ?>
                </a>
    
        <?php else: ?>
    
                <img src="<?php echo DEALS_IMG.'default-deals.jpg'; ?>" width="<?php echo $single_img_width; ?>" height="<?php echo $single_img_height; ?>" class="single-deal-thumbnail no-image" alt="No Image"/>
        
        <?php endif; ?>
                
</figure>

<div class="clr"></div>

<!-- display the content -->
<div id="deal-details entry-content">

        <div class="description-info">

                <?php do_action('deals_before_description',$post); ?>

                <?php the_content(); ?>       

                <?php do_action('deals_after_description',$post); ?>  

        </div>

</div><!-- end of deal details -->

<?php do_action('deals_after_single_content', $post); ?>