<?php
/**
 * Tags Products Widget
 * 
 * @package	WP-Deals
 * @category	Widgets
 * @author	WP-Deals Team
 */

class Deals_Tags_Widget extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function Deals_Tags_Widget() {
		$widget_ops = array( 'classname' => 'widget_deals_tags', 'description' => __( 'Use this widget to get all your deal tags', 'wpdeals' ) );
		$this->WP_Widget( 'widget_deals_tags', __( 'Deals - Tags', 'wpdeals' ), $widget_ops );
		$this->alt_option_name = 'widget_deals_tags';

		add_action( 'save_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache' ) );
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array An array of standard parameters for widgets in this theme
	 * @param array An array of settings for this widget instance
	 * @return void Echoes it's output
	 **/
	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_deals_tags', 'widget' );

		if ( !is_array( $cache ) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = null;

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args, EXTR_SKIP );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Tags Deals', 'wpdeals' ) : $instance['title'], $instance, $this->id_base);

		if ( ! isset( $instance['number'] ) )
			$instance['number'] = '3';

		if ( ! $number = absint( $instance['number'] ) )
 			$number = 3;				

		$terms = wp_tag_cloud(array(
            'number' => $number,
            'format' => 'flat',
            'echo' => 0,
            'taxonomy' => 'deal-tags'
        ));
		
		if ( !empty($terms) ) ://has terms
			
			echo $before_widget;
			echo $before_title;
			echo $title; // Can set this with a widget option, or omit altogether
			echo $after_title;
			
			?>
			<ul>
				<?php echo $terms; ?>				
			</ul>
			<?php
            echo $after_widget;
			
		else://no terms
			
			echo 'No tags';
			
		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_deals_tags', $cache, 'widget' );
	}

	/**
	 * Deals with the settings when they are saved by the admin. Here is
	 * where any validation should be dealt with.
	 **/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_deals_tags'] ) )
			delete_option( 'widget_deals_tags' );

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_deals_tags', 'widget' );
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 **/
	function form( $instance ) {
		$title  = isset( $instance['title']) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 10;		
?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'wpdeals' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of tags to show:', 'wpdeals' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
                        			
		<?php
	}
}