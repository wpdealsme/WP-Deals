<?php
/**
 * Free Products Widget
 * 
 * @package	WP-Deals
 * @category	Widgets
 * @author	WP-Deals Team
 */

class Deals_Free_Widget extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function Deals_Free_Widget() {
		$widget_ops = array( 'classname' => 'widget_deals_freebies', 'description' => __( 'Use this widget to list your free deals', 'wpdeals' ) );
		$this->WP_Widget( 'widget_deals_freebies', __( 'Deals - Freebies', 'wpdeals' ), $widget_ops );
		$this->alt_option_name = 'widget_deals_freebies';

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
		$cache = wp_cache_get( 'widget_deals_freebies', 'widget' );

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

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Frees Deals', 'wpdeals' ) : $instance['title'], $instance, $this->id_base);

		if ( ! isset( $instance['number'] ) )
			$instance['number'] = '3';

		if ( ! $number = absint( $instance['number'] ) )
 			$number = 3;

		if ( ! isset( $instance['width'] ) )
			$instance['width'] = '100';

		if ( ! $width = absint( $instance['width'] ) )
 			$width = 100;

		if ( ! isset( $instance['height'] ) )
			$instance['height'] = '100';

		if ( ! $height = absint( $instance['height'] ) )
 			$height = 100;

		$freebies_args = array(
			'order'             => 'ASC',
			'posts_per_page'    => $number,
			'no_found_rows'     => true,
			'post_status'       => 'publish',
                        'post_type'         => 'daily-deals',
                        'meta_query' => array(
                                array(
                                        'key'       => '_is_expired',
                                        'value'     => 'no',
                                        'compare'   => '='
                                ),
                                array(
                                        'key'       => '_discount_price',
                                        'value'     => 0,
                                        'type'      => 'numeric',
                                        'compare'   => '='
                                ),
                                array(
                                        'key'       => '_base_price',
                                        'value'     => 0,
                                        'type'      => 'numeric',
                                        'compare'   => '='
                                )
                        )
		);
		$freebies = new WP_Query( $freebies_args );

		if ( $freebies->have_posts() ) :

		echo $before_widget;
		echo $before_title;
		echo $title; // Can set this with a widget option, or omit altogether
		echo $after_title;

		?>
		<ul>
		<?php while ( $freebies->have_posts() ) : $freebies->the_post(); ?>

			<li class="widget-deal-item">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                        <span class="free-image-wrap image-wrap">                                                
                                                <?php deals_image( 'width='.$width.'&height='.$height.'&class=deal-thumbnail'); ?>
                                        </span>
                                <?php endif; ?>

                                <div class="free-deal-title">
                                        <?php the_title(); ?>
                                        <?php echo get_post_meta(get_the_ID(), '_discount_price', true); ?>
                                </div>
                            </a>
			</li>

		<?php endwhile; ?>
		</ul>
		<?php

		echo $after_widget;

		// Reset the post globals as this query will have stomped on it
		wp_reset_postdata();
                
                else:
                    
                    // echo '<p>Sorry, no other deals for now.</p>';

		// end check for freebiesl posts
		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_deals_freebies', $cache, 'widget' );
	}

	/**
	 * Deals with the settings when they are saved by the admin. Here is
	 * where any validation should be dealt with.
	 **/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = (int) $new_instance['number'];
		$instance['width'] = (int) $new_instance['width'];
		$instance['height'] = (int) $new_instance['height'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['widget_deals_freebies'] ) )
			delete_option( 'widget_deals_freebies' );

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_deals_freebies', 'widget' );
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 **/
	function form( $instance ) {
		$title  = isset( $instance['title']) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 10;
		$width  = isset( $instance['width'] ) ? absint( $instance['width'] ) : 100;
		$height = isset( $instance['height'] ) ? absint( $instance['height'] ) : 100;
?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'wpdeals' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of posts to show:', 'wpdeals' ); ?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" /></p>
                        
			<p>
                            <label for="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>"><?php _e( 'Width:', 'wpdeals' ); ?></label>
                            <input id="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>" size="3" />
                            <label for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>"><?php _e( 'Height:', 'wpdeals' ); ?></label>
                            <input id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" type="text" value="<?php echo esc_attr( $height ); ?>" size="3" />
                        </p>
		<?php
	}
}