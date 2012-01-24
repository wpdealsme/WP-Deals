<?php

if ( !function_exists( 'deal_init' ) ) {

    /*-----------------------------------------------------------------------------------*/
    /* deals options defined variables
    /*-----------------------------------------------------------------------------------*/

    define('DEALS_FRAMEWORK_URL', ABSPATH . 'wp-content/plugins/wp-deals/deals-admin/');
    define('DEALS_FRAMEWORK_DIRECTORY',plugins_url('wp-deals/deals-admin/'));

    require_once (DEALS_FRAMEWORK_URL . 'deals_settings.php');

}

add_action('deals_settings_custom_scripts', 'deals_settings_custom_scripts');

function deals_settings_custom_scripts() { ?>

    <script type="text/javascript">
    jQuery(document).ready(function() {

            jQuery('#example_showhidden').click(function() {
                    jQuery('#section-example_text_hidden').fadeToggle(400);
            });

            if (jQuery('#example_showhidden:checked').val() !== undefined) {
                    jQuery('#section-example_text_hidden').show();
            }

    });
    </script>

    <?php

}
