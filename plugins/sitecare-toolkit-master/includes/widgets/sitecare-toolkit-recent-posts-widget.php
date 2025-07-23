<?php

/**
 * The file that defines the recent posts widget.
 *
 * @link       https://www.sitecare.com
 * @since      0.0.3
 *
 * @package    SiteCare_Toolkit
 * @subpackage SiteCare_Toolkit/includes/widgets
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Recent Posts widget
 *
 * @since 0.0.3
 */
class SiteCare_Toolkit_Recent_Posts_Widget extends WP_Widget {

    /**
     * Constructor
     *
     * @access      public
     * @since       0.0.3
     * @return      void
     */
	public function __construct() {

		parent::__construct(
			'sitecare_recent_posts_widget',
			__( 'SiteCare Recent Posts', 'sitecare-toolkit' ),
			array(
				'description' => esc_attr__( 'Display your recent posts', 'sitecare-toolkit' ),
				'classname'   => 'sitecare-toolkit-recent-posts-widget',
			)
		);

	}

    /**
     * Widget definition
     *
     * @access      public
     * @since       0.0.3
     * @see         WP_Widget::widget
     * @param       array $args Arguments to pass to the widget
     * @param       array $instance A given widget instance
     * @return      void
     */
    public function widget( $args, $instance ) {

		global $post;

        if( ! isset( $args['id'] ) ) {
            $args['id'] = 'sitecare_recent_posts_widget';
        }

        $title = apply_filters( 'widget_title', $instance['title'], $instance, $args['id'] );

        echo $args['before_widget'];

        if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        do_action( 'sitecare_toolkit_widget_recent_posts_before' );

			// Random order.
			$rand_order = '';

			// Set random order if selected by user.
			if (  isset( $instance['orderby'] ) && 'on' == $instance['orderby'] ) {
				$rand_order = 'rand';
            }

            // Post args.
			$details_array = array(
                'orderby'    => $rand_order,
                'image_size' => $instance['imagesize']
			);

            // Carousel wrapper.
            if ( 'on' == $instance['carousel'] ) {
                $details_array['class'] = 'carousel';
            }

            // Wrapper - default.
            $wrapper = 'div';

            // Wrapper - unordered list.
            if ( 'thumbnail' == $instance['imagesize'] ) {
                $wrapper = 'ul';
            }

            // Display recent posts.
            echo sctk_display_posts( $instance['post_type'], $instance['limit'], $wrapper, $details_array );

        do_action( 'sitecare_toolkit_widget_recent_posts_after' );

        echo $args['after_widget'];
    }


    /**
     * Update widget options
     *
     * @access      public
     * @since       0.0.3
     * @see         WP_Widget::update
     * @param       array $new_instance The updated options
     * @param       array $old_instance The old options
     * @return      array $instance The updated instance options
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['post_type'] = strip_tags( $new_instance['post_type'] );
        $instance['title']     = strip_tags( $new_instance['title'] );
        $instance['limit']     = strip_tags( $new_instance['limit'] );
        $instance['type']      = $new_instance['type'];
        $instance['orderby']   = $new_instance['orderby'];
        $instance['carousel']  = $new_instance['carousel'];
		$instance['imagesize'] = $new_instance['imagesize'];

        return $instance;
    }


    /**
     * Display widget form on dashboard
     *
     * @access      public
     * @since       0.0.3
     * @see         WP_Widget::form
     * @param       array $instance A given widget instance
     * @return      void
     */
    public function form( $instance ) {
        $defaults = array(
            'title'     => esc_attr__( 'Recent Posts', 'sitecare-toolkit' ),
            'limit'     => '5',
	        'type'      => '',
            'orderby'   => '',
			'carousel'  => '',
			'imagesize' => 'thumbnail',
        );

        $instance = wp_parse_args( (array) $instance, $defaults );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title:', 'sitecare-toolkit' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
        </p>

    	<p>
	        <label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>"><?php _e( 'Post type:', 'sitecare-toolkit' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" class="widefat" style="width:100%;">
                <option <?php if ( '' == $instance['type'] ) echo 'selected="selected"'; ?> value=""><?php _e( 'Select', 'sitecare-toolkit' ); ?></option>
                <?php
                $args = array(
                    'public'   => true,
                    '_builtin' => true
                );

                $output   = 'names'; // 'names' or 'objects' (default: 'names')
                $operator = 'and'; // 'and' or 'or' (default: 'and')

                $post_types = get_post_types( $args, $output, $operator );
                foreach ( $post_types as $post_type ) {
                ?>
                    <option <?php if ( $post_type == $instance['type'] ) echo 'selected="selected"'; ?> value="<?php echo $post_type; ?>"><?php echo $post_type; ?></option>
                <?php } ?>
			</select>
    	</p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php _e( 'Amount of posts to show:', 'sitecare-toolkit' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" type="number" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" min="1" max="999" value="<?php echo $instance['limit']; ?>" />
        </p>

	    <p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['orderby'], 'on' ); ?> id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php _e( 'Randomize output?', 'sitecare-toolkit' ); ?></label>
        </p>

	    <p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['carousel'], 'on' ); ?> id="<?php echo $this->get_field_id( 'carousel' ); ?>" name="<?php echo $this->get_field_name( 'carousel' ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'carousel' ) ); ?>"><?php _e( 'Display posts in carousel?', 'sitecare-toolkit' ); ?></label>
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'imagesize' ) ); ?>"><?php esc_html_e( 'Image size:', 'sitecare-toolkit' ); ?></label>
            <?php
                // Set featured image sizes.
				$image_sizes = get_intermediate_image_sizes();
                if ( $image_sizes ) {
                    printf( '<select name="%s" id="' . esc_html( $this->get_field_id( 'imagesize' ) ) . '" name="' . esc_html( $this->get_field_name( 'imagesize' ) ) . '" class="widefat">', esc_attr( $this->get_field_name( 'imagesize' ) ) );
					// Loop through each image size.
					foreach ( $image_sizes as $image ) {
                        if ( esc_html( $image ) != $instance['imagesize'] ) {
                            $image_selected = '';
                        } else {
                            $image_selected = 'selected="selected"';
                        }
                        printf( '<option value="%s" ' . esc_html( $image_selected ) . '>%s</option>', esc_html( $image ), esc_html( $image ) );
                    }
					print( '</select>' );
                }
            ?>
        </p>
		<?php
    }
}


/**
 * Register the new widget
 *
 * @since       0.0.3
 * @return      void
 */
function sitecare_toolkit_recent_posts_widget_register() {
    register_widget( 'SiteCare_Toolkit_Recent_Posts_Widget' );
}
add_action( 'widgets_init', 'sitecare_toolkit_recent_posts_widget_register' );

