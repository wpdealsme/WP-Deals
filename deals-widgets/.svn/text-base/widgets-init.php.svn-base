<?php
/**
 * Widgets init
 * 
 * Init the widgets.
 *
 * @package	WP-Deals
 * @category	Widgets
 * @author	WP-Deals Team
 */
 
include_once('widget-featured.php');
include_once('widget-free.php');
include_once('widget-randoms.php');
include_once('widget-recents.php');
include_once('widget-categories.php');
include_once('widget-tags.php');

/**
 * Register widget
 * @return void
 */
function deals_register_widgets() {
	register_widget('Deals_Recent_Widget');
	register_widget('Deals_Random_Widget');
	register_widget('Deals_Free_Widget');
	register_widget('Deals_Featured_Widget');
	register_widget('Deals_Categories_Widget');
	register_widget('Deals_Tags_Widget');
}
add_action('widgets_init', 'deals_register_widgets');