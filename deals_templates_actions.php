<?php

// Content Wrapper Archive
add_action('deals_before_main_content', 'deals_output_before_main_content');
add_action('deals_after_main_content', 'deals_output_after_main_content');

// sidebar action
add_action('deals_sidebar', 'deals_output_sidebar');

// before loop deals
add_action('deals_before_loop', 'deals_view_type');

// pagination
add_action('deals_pagination', 'deals_pagination');

// loop deals
add_action('deals_before_loop_item_title', 'deals_loop_thumb');
add_action('deals_after_loop_item_title', 'deals_loop_countdown');
add_action('deals_after_loop_item_title', 'deals_table_price');
add_action('deals_after_loop_item_title', 'deals_loop_description');

// single content
add_action('deals_before_single_content', 'deals_before_single_content');
add_action('deals_after_top_button', 'deals_table_price');
add_action('deals_after_top_button', 'deals_loop_countdown');
add_action('deals_after_top_button', 'deals_share_button');
add_action('deals_after_description', 'deals_button_bottom_desc');
add_action('deals_after_description', 'deals_single_meta_content');
add_action('deals_after_single_content', 'deals_after_single_content');

// user info
add_action('deals_before_user_account', 'deals_user_info');
add_action('deals_before_user_history', 'deals_user_info');