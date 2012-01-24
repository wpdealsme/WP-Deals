<?php get_header(); ?>

<div>
    <?php 
    
    wp_login_form(array(
        'echo' => true,
        'redirect' => $_SERVER['HTTP_REFERER'],
        'remember' => false,
		'form_id' => 'deals-loginform" class="deals-user" '
    ));
    
    ?>
</div>
<div style="margin-top: -10px;margin-bottom: 10px;">
    <?php wp_register('','','Register'); ?>
</div>
<?php get_footer(); ?>