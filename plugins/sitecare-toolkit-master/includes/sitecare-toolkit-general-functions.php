<?php

/**
 * The file that defines the plugin's helper functions for general purposes.
 *
 * @link       https://www.sitecare.com
 * @since      0.0.1
 *
 * @package    SiteCare_Toolkit
 * @subpackage SiteCare_Toolkit/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Default jQuery
 * 
 * Update the default WordPress jQuery script with a custom URL.
 * 
 * @since 0.0.1
 */
if ( ! function_exists( 'sctk_modify_default_jquery' ) ) {
    function sctk_modify_default_jquery( $url = '', $ver = '' ) {
        if ( ! is_admin() ) {
            // Default URL.
            if ( ! isset( $url ) ) {
                $url = 'http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js';
            }
            // Default version.
            if ( ! isset( $ver ) ) {
                $ver = '1.12.4';
            }
            // Deregister the default jQuery library.
            wp_deregister_script( 'jquery' );
            // Register the custom jQuery library.
            wp_register_script( 'jquery', $url, false, $ver );
            // Enqueue the custom jQuery library.
            wp_enqueue_script( 'jquery' );
        }
    }
}

/**
 * Estimated reading time
 * 
 * This function adds an estimated reading time to the top of articles
 * 
 * @param int $min_words - the average words per minute you'd like to use.
 * @param int $post_id   - (optional) the ID you'd like to use
 * @return string
 * 
 * @since 0.0.1
 */
if ( ! function_exists( 'sctk_estimated_reading_time' ) ) {
    function sctk_estimated_reading_time( $min_words = 200, $post_id = false ) {
        if ( ! isset( $post_id ) ) {
            $post_id = get_the_ID();
        }
        $the_post = get_post( $post_id );
        $words    = $the_post->post_content;
        $word     = str_word_count( strip_tags( $words ) );
        $m        = floor( $word / $min_words );
        $min      = $m . ' minute' . ( $m == 1 ? '' : 's' );
        $s        = floor( $word % $min_words / ( $min_words / 60 ) );
        $sec      = $s . ' second' . ( $s == 1 ? '' : 's' );
        $est_time = apply_filters( 'sctk_estimated_reading_prefix', '' ) . apply_filters( 'sctk_estimated_reading_time_display', $min . ', '  . $sec, $min, $sec );
        $est      = '<p class="sitecare-est-reading">' . $est_time . '</p>';

        return apply_filters( 'sctk_estimated_reading_time', $est, $est_time );
    }
}


/**
 * Estimated reading time minutes
 * 
 * This function adds an estimated reading time to the top of articles
 * 
 * @param int $min_words - the average words per minute you'd like to use.
 * @param int $post_id - (optional) the ID you'd like to use
 * @param string $appended_content - allows you to add additional content after the read times
 * @return string
 * 
 * @since 0.0.1
 */
if ( ! function_exists( 'sctk_estimated_reading_time_minutes' ) ) {

    function sctk_estimated_reading_time_minutes( $min_words = 200, $post_id = false, $appended_content, $custom_words ) {
        if ( ! isset( $post_id ) ) {
            $post_id = get_the_ID();
        }
        $the_post = get_post( $post_id );
        $words    = ( $custom_words ? $custom_words : $the_post->post_content );
        $word     = str_word_count( strip_tags( $words ) );
        $m        = floor( $word / $min_words );
        $min      = $m . ' min';
        $est_time = apply_filters( 'sctk_estimated_reading_prefix', '' ) . apply_filters( 'sctk_estimated_reading_time_display', $min );
        $est      = '<span class="sitecare-est-reading">' . $est_time . $appended_content . '</span>';

        return apply_filters( 'sctk_estimated_reading_time', $est, $est_time );
    }

}

/**
 * Change URL domain
 * 
 * This function changes the domain name in a URL to your desired domain
 * 
 * @param  string $old_url - the URL you need to change the domain for.
 * @param  string $new_url - the domain you'd like to use in the URL (://www.example.com or ://example.com)
 * @return string
 * 
 * @since 0.0.1
 */
if ( ! function_exists( 'sctk_change_url_domain' ) ) {
    function sctk_change_url_domain( $old_url = false, $new_url = false ) {
        // Bail early?
        if ( empty( $old_url ) || empty( $new_url ) ) {
            return false;
        }
        // Old URL.
        $old_url = $old_url ? esc_url( $old_url ) : home_url();
        // Home URL.
        $home = str_replace( array( 'http://', 'https://' ), '://', home_url() );
        // New URL.
        $new_url = str_replace( $home, $new_url, $old_url );

        return esc_url( $new_url );
    }
}

/**
 * Creating a filterable table.
 * 
 * @param array  $thead  - Table head
 * @param array  $body   - Table body, an array of arrays
 * @param string $prefix - Filter name prefixes
 * @param string $class  - Table class(es) separated by space
 *
 * @return string
 */
if ( ! function_exists( 'sctk_table_builder' ) ) {
    function sctk_table_builder( $thead = array(), $tbody = array(), $prefix = '', $class = '' ) {
        // Filter the table head.
        $thead = apply_filters( $prefix . '_thead_content', $thead );

        // Filter the table body.
        $tbody = apply_filters( $prefix . '_tbody_content', $tbody );

        // Create empty vars.
        $thead_content = '';
        $tbody_content = '';

        // Create thead content.
        foreach ( $thead as $value ) {
            $thead_content .= '<td>' . $value . '</td>';
        }

        // Create tbody content.
        foreach ( $tbody as $item ) {
            $tbody_content .= '<tr>';
            foreach ( $item as $td ) {
                $tbody_content .= '<td>' . $td . '</td>';
            }
            $tbody_content .= '</tr>';
        }

        // Build the table.
        $table = '<table class="' . $class . '"><thead>' . $thead_content . '</thead>' . '<tbody>' . $tbody_content . '</tbody></table>';

        return $table;
    }
}

/**
 * Returns the permalink for a page based on the incoming slug.
 *
 * @param   string  $slug   The slug of the page to which we're going to link.
 * @return  string          The permalink of the page
 * @since   1.0.3
 */
if ( ! function_exists( 'sctk_get_permalink_by_slug' ) ) {
    function sctk_get_permalink_by_slug( $slug, $post_type = 'page', $output = OBJECT ) {
        global $wpdb;
        $result = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s", $slug, $post_type ) );
        if ( $result ) {
            $get_result = get_page( $result, $output );
            return get_permalink( $get_result->ID );
        }
        return null;
    }
}

/**
 * Returns a customized search form.
 *
 * @param     string    $action_url  - The slug of the page to which we're going to link.
 * @param     string    $post_type   - The post type used in the search form (hidden field)
 * @param     string    $form_title  - The title displayed above the form (defaults to "Search")
 * @param     string    $placeholder - Search input field placeholder text (defaults to "Search...")
 * @param     string    $submit_text - Submit input field placeholder text (defaults to "Search")
 * @return    string    $form        - Search form HTML string
 * @since     1.0.4
 */
if ( ! function_exists( 'sctk_search_form' ) ) {
    function sctk_search_form( $action_url = '', $post_type = '', $form_title = '', $placeholder = 'Search...', $submit_text = 'Search' ) {

        // Default Action URL.
        if ( ! isset( $action_url ) || '' == $action_url ) {
            $action_url = site_url( '/' );
        }

        // Form title (if any).
        if ( ! empty( $form_title ) ) {
            $form_title = '<h3>' . $form_title . '</h3>';
        }

        // Build the form.
        $form = ' <div class="sitecare-toolkit-search-form">
            ' . $form_title . '
            <form role="search" action="' . $action_url . '" method="get" id="searchform">
            <input type="text" name="s" placeholder="' . $placeholder . '" />
            <input type="hidden" name="post_type" value="' . $post_type . '" />
            <input type="submit" alt="Search" value="' . $submit_text . '" />
            </form>
        </div>';

        return apply_filters( 'sctk_search_form', $form, $action_url, $post_type, $placeholder, $submit_text );
    }
}

if ( ! function_exists( 'sctk_get_primary_categories' ) ) {
    /**
     * Get Primary Categories for each post
     * 
     * @param  int    $post_type
     * @param  int    $post_count
     * @return array|string
     * @since  1.0.4
     */
    function sctk_get_primary_categories( $post_type = array( 'post' ), $post_count = -1, $return_table = NULL ) {
        // Bail early?
        if ( ! is_array( $post_type ) ) { return; }

        // Create empty list.
        $categories = array();

        // Run for each post type
        foreach ( $post_type as $post_type ) {
            // Post args.
            $args = array(
                'post_type'   => $post_type,
                'numberposts' => $post_count,
                'orderby'     => 'title',
                'order'       => 'ASC',
            );
            // Filter args.
            $args = apply_filters( 'sctk_the_name_args', $args );

            // Get posts.
            $posts = get_posts( $args );

            // Loop through posts.
            foreach ( $posts as $the_post ) {
                $categories[$the_post->ID] = sctk_get_post_primary_category( $the_post->ID );
            }
        }

        // Prefix.
        $prefix = 'sitecare';
        // Table class(es).
        $class = 'primary-categories';
        // Table head.
        $thead = array(
            esc_attr__( 'Post ID', 'sitecare-toolkit' ),
            esc_attr__( 'Category ID', 'sitecare-toolkit' ),
            esc_attr__( 'Category Name', 'sitecare-toolkit' ),
            esc_attr__( 'Category Link', 'sitecare-toolkit' )
        );
        // Table body.
        $tbody = array();

        // Build table data.
        foreach ( $categories as $key => $value ) {
            if ( ! empty( $value ) ) {
                $cat_array = $value['primary_category'];
                // Get the URL of this category.
                $category_link = get_category_link( $cat_array->term_id );
                // Add to table data.
                $tbody[] = array( $key, $cat_array->term_id, $cat_array->name, $category_link );
            } else {
                // No primary category is set.
                $tbody[] = array( $key, '-', '-', '-' );
            }
        }

        // Return data as table.
        if ( $return_table ) {
            return sctk_table_builder( $thead, $tbody, $prefix, $class );;
        }
        // Return data as array.
        return $categories;
    }
}

if ( ! function_exists( 'sctk_simple_history_log_purge_days' ) ) {
    /**
     * Filter to modify number of days of history to keep.
     * Default is 60 days.
     * 
     * @return int
     * @since  1.0.4
     */
    function sctk_simple_history_log_purge_days( $days ) {
        // Access all Scripts Settings.
        $scripts = get_option( 'sitecare_toolkit_scripts' );

        // Check the simple history log purge days setting.
        if ( isset( $scripts['scripts_simple_history_log_purge_days'] ) ) {
            // Override purge days interval.
            $days = $scripts['scripts_simple_history_log_purge_days'];
        }

        return $days;
    }
}

if ( ! function_exists( 'sctk_simple_history_db_purge_days_interval' ) ) {
    /**
     * Simple History database purge days interval
     * 
     * @return void
     * @since  1.0.4
     */
    function sctk_simple_history_db_purge_days_interval() {
        // Make sure Simple History is active.
        if ( class_exists( 'SimpleHistory' ) ) {
            // Access all Scripts Settings.
            $scripts = get_option( 'sitecare_toolkit_scripts' );

            // Check the simple history log purge days setting.
            if ( isset( $scripts['scripts_simple_history_log_purge_days'] ) ) {
                add_filter( 'simple_history/db_purge_days_interval', 'sctk_simple_history_log_purge_days' );
            }
        }
    }
}

if ( ! function_exists( 'sctk_hex2rgba' ) ) {
    /**
     * HEX to RGB(A) converter
     * 
     * @return string
     * @since  1.1
     */
    function sctk_hex2rgba( $color, $opacity = false ) {

        // Default.
        $default = 'rgb(0,0,0)';
    
        // Return default if no color provided.
        if ( empty( $color ) ) {
            return $default; 
        }
    
        // Sanitize $color if "#" is provided.
        if ( '#' == $color[0] ) {
            $color = substr( $color, 1 );
        }

        // Check if color has 6 or 3 characters and get values.
        if ( 6 == strlen( $color ) ) {
            $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        } elseif ( 3 == strlen( $color ) ) {
            $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        } else {
            return $default;
        }

        // Convert hexadec to rgb.
        $rgb = array_map( 'hexdec', $hex );

        // Check if opacity is set(rgba or rgb).
        if ( $opacity ) {
            if ( abs( $opacity ) > 1 ) { $opacity = 1.0; }
            $output  = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
        } else {
            $output = 'rgb(' . implode( ',', $rgb ) . ')';
        }

        // Return rgb(a) color string
        return $output;
    }
}

if ( ! function_exists( 'sctk_enqueue_google_fonts' ) ) {
    /**
     * Enqueue Google Fonts
     * 
     * @return void
     * @since  0.2.0
     */
    function sctk_enqueue_google_fonts() {
        $fonts = '';
        $fonts = apply_filters( 'sctk_enqueue_google_fonts', $fonts );
        if ( $fonts ) {
            wp_enqueue_style( 'sctk-google-fonts', urldecode( 'https://fonts.googleapis.com/css2?' . $fonts . '&display=swap' ) );
        }
    }
    add_action( 'wp_enqueue_scripts', 'sctk_enqueue_google_fonts' );
}

if ( ! function_exists( 'sctk_the_component' ) ) {
    /**
     * Adds a helper function to retrieve a component by a slug.
     * 
     * Example: sctk_the_component( 'header/header' );
     *
     * @param  string    $slug The slug of the component.
     * @param  array     $params An array of data passed to component.
     *
     * @since  0.2.0
     * @return void
     */
    function sctk_the_component( string $slug, array $params = array() ) {

        $templates = array();

        if ( '' !== $slug ) {
            $templates[] = "components/{$slug}.php";
        }

        $templates[] = "{$slug}.php";
        $template    = locate_template( $templates, false, false );

        if ( $template ) {
            if ( $params ) {
                foreach ( $params as $key => $variable ) {
                    $$key = $variable;
                }
            }

            include $template;
        }
    }
}

if ( ! function_exists( 'sctk_get_the_component' ) ) {
    /**
     * Adds a helper function to retrieve a component by a slug.
     * 
     * Example: sctk_get_the_component( 'header/header' );
     *
     * @param  string    $slug
     * @param  array     $params
     *
     * @since  0.2.0
     * @return false|string
     */
    function sctk_get_the_component( string $slug, array $params = array() ) {

        ob_start();

        sctk_the_component( $slug, $params );

        return ob_get_clean();
    }
}