<?php

// Content Wrapper Archive
add_action('deals_before_main_content', 'deals_output_before_main_content');
add_action('deals_after_main_content', 'deals_output_after_main_content');

// Content Wrapper Single
add_action('deals_before_single_content', 'deals_output_before_single_content');
add_action('deals_after_single_content', 'deals_output_after_single_content');

// sidebar action
add_action('deals_sidebar', 'deals_output_sidebar');