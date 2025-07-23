<?php

/**
 * The file that defines the author box widget.
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
 * Author Box widget
 *
 * @since 0.0.3
 */
class SiteCare_Author_Box_Widget extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @access      public
	 * @since       4.0.0
	 * @return      void
	 */
	public function __construct() {

		parent::__construct(
			'sitecare_author_box_widget',
			__( 'SiteCare Author Box', 'sitecare-toolkit' ),
			array(
				'description' => esc_attr__( 'Add an author box to your site', 'sitecare-toolkit' ),
				'classname'   => 'sitecare-author-box-widget',
			)
		);

	}

	/**
	 * Widget definition
	 *
	 * @access      public
	 * @since       4.0.0
	 * @see         WP_Widget::widget
	 * @param       array $args Arguments to pass to the widget.
	 * @param       array $instance A given widget instance.
	 * @return      void
	 */
	public function widget( $args, $instance ) {
		if ( ! isset( $args['id'] ) ) {
			$args['id'] = 'sitecare_author_box_widget';
		}

        // Widget title.
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $args['id'] );

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
		}

		do_action( 'sitecare_author_box_widget_before' );

		echo '<div class="sitecare-author-box-widget">';

        do_action( 'sitecare_author_box_widget_before_form' );

        // Author image.
        if ( NULL !== $instance['author_image'] ) {
            echo '<div class="author-box-image"><img src="' . $instance['author_image'] . '" alt="" /></div>';
        }

        // Author name.
        if ( NULL !== $instance['author_name'] ) {
            echo '<h3 class="author-box-name">' . $instance['author_name'] . '</h3>';
        }

        // Author bio.
        if ( NULL !== $instance['author_bio'] ) {
            echo '<p class="author-bio">' . $instance['author_bio'] . '</p>';
        }

        // Author button.
        if ( NULL !== $instance['author_page'] ) {
            echo '<p class="author-button"><a href="' . sctk_get_permalink_by_slug( $instance['author_page'] ) . '">' . $instance['author_button'] . '</a></p>';
        }

        do_action( 'sitecare_author_box_widget_after_form' );

		'</div>';

		do_action( 'sitecare_author_box_widget_after' );

		echo $args['after_widget'];
	}


	/**
	 * Update widget options
	 *
	 * @access      public
	 * @since       4.0.0
	 * @see         WP_Widget::update
	 * @param       array $new_instance The updated options.
	 * @param       array $old_instance The old options.
	 * @return      array $instance The updated instance options
	 */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title']         = $new_instance['title'];
        $instance['author_image']  = $new_instance['author_image'];
        $instance['author_name']   = $new_instance['author_name'];
        $instance['author_bio']    = $new_instance['author_bio'];
        $instance['author_page']   = $new_instance['author_page'];
        $instance['author_button'] = $new_instance['author_button'];

        return $instance;
    }


	/**
	 * Display widget form on dashboard
	 *
	 * @access      public
	 * @since       4.0.0
	 * @see         WP_Widget::form
	 * @param       array $instance A given widget instance.
	 * @return      void
	 */
	public function form( $instance ) {
        $defaults = array(
            'title'         => esc_attr__( 'Author Box', 'sitecare-toolkit' ),
            'author_image'  => '',
            'author_name'   => '',
            'author_bio'    => '',
            'author_page'   => '',
            'author_button' => '',
        );

        $instance = wp_parse_args( (array) $instance, $defaults );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget title:', 'sitecare-toolkit' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
        </p>

        <p class="sitecare-toolkit-image-wrapper">
            <?php if ( $instance['author_image'] ) { ?>
                <img class="<?php echo $this->id ?>_img" src="<?php echo $instance['author_image']; ?>" />
            <?php } ?>
            <input type="text" class="widefat" id="<?php echo $this->id; ?>_url" name="<?php echo $this->get_field_name( 'author_image' ); ?>" value="<?php echo $instance['author_image']; ?>" style="margin-top:5px;" />
            <input type="button" id="<?php echo $this->id ?>" class="button button-primary js_custom_upload_media" value="<?php _e( 'Select Image', 'sitecare-toolkit' ); ?>" style="margin-top:5px;" />
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'author_name' ) ); ?>"><?php _e( 'Author name:', 'sitecare-toolkit' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'author_name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'author_name' ) ); ?>" type="text" value="<?php echo $instance['author_name']; ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'author_bio' ) ); ?>"><?php _e( 'Author bio:', 'sitecare-toolkit' ); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'author_bio' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'author_bio' ) ); ?>"><?php echo $instance['author_bio']; ?></textarea>
        </p>

        <p>
	        <label for="<?php echo esc_attr( $this->get_field_id( 'author_page' ) ); ?>"><?php _e( 'Author page:', 'sitecare-toolkit' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'author_page' ); ?>" name="<?php echo $this->get_field_name( 'author_page' ); ?>" class="widefat" style="width:100%;">
                <option <?php if ( empty( $instance['author_page'] ) ) echo 'selected="selected"'; ?> value=""><?php _e( 'Select a page', 'sitecare-toolkit' ); ?></option>
                <?php
                    // Args for pages.
                    $args = array(
                        'sort_order'   => 'asc',
                        'sort_column'  => 'post_title',
                        'hierarchical' => 1,
                        'exclude'      => '',
                        'include'      => '',
                        'meta_key'     => '',
                        'meta_value'   => '',
                        'authors'      => '',
                        'child_of'     => 0,
                        'parent'       => -1,
                        'exclude_tree' => '',
                        'number'       => '',
                        'offset'       => 0,
                        'post_type'    => 'page',
                        'post_status'  => 'publish'
                    );
                    // Filter the args.
                    $args = apply_filters( 'sitecare_toolkit_author_page_args', $args );

                    // Get all pages.
                    $pages = get_pages( $args );

                    // Only if pages exist.
                    if ( ! empty( $pages ) ) {
                        // Loop through pages.
                        foreach ( $pages as $page ) { ?>
                            <option <?php if ( $page->post_name == $instance['author_page'] ) echo 'selected="selected"'; ?> value="<?php echo $page->post_name; ?>"><?php echo $page->post_title; ?></option>
                        <?php }
                    }

                    echo $instance['author_page'];
                ?>
			</select>
    	</p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'author_button' ) ); ?>"><?php _e( 'Button text:', 'sitecare-toolkit' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'author_button' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'author_button' ) ); ?>" type="text" value="<?php echo $instance['author_button']; ?>" />
        </p>

    <?php }
}

/**
 * Register the new widget
 *
 * @since       0.0.3
 * @return      void
 */
function sitecare_author_box_register_widget() {
	register_widget( 'SiteCare_Author_Box_Widget' );
}
add_action( 'widgets_init', 'sitecare_author_box_register_widget' );
