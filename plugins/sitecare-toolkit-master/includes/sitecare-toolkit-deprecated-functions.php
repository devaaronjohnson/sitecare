<?php

/**
 * The file that defines the plugin's deprecated functions.
 *
 * @link       https://www.sitecare.com
 * @since      0.1
 *
 * @package    SiteCare_Toolkit
 * @subpackage SiteCare_Toolkit/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'sitecare_toolkit_check_user_roles' ) ) {
    function sitecare_toolkit_check_user_roles( $roles, $user_id = null ) {
        // Pass args to new function.
        return sctk_check_user_roles( $roles, $user_id );
    }
}

if ( ! function_exists( 'sitecare_toolkit_user_last_login' ) ) {
    function sitecare_toolkit_user_last_login( $user_id = '', $human = true ) {
        // Pass args to new function.
        return sctk_user_last_login( $user_id = '', $human );
    }
}

if ( ! function_exists( 'sitecare_toolkit_replace_phone' ) ) {
    function sitecare_toolkit_replace_phone( $input, $replacement = '' ) {
        // Pass args to new function.
        return sctk_replace_phone( $input, $replacement );
    }
}

if ( ! function_exists( 'sitecare_toolkit_replace_state_name' ) ) {
    function sitecare_toolkit_replace_state_name( $name ) {
        // Pass args to new function.
        return sctk_replace_state_name( $name );
    }
}

if ( ! function_exists( 'sitecare_toolkit_replace_country_name' ) ) {
    function sitecare_toolkit_replace_country_name( $name ) {
        // Pass args to new function.
        return sctk_replace_country_name( $name );
    }
}

if ( ! function_exists( 'sitecare_toolkit_display_posts' ) ) {
    function sitecare_toolkit_display_posts( $post_type = 'post', $limit = 10, $wrapper = 'ul', $details_array = array() ) {
        // Pass args to new function.
        return sctk_display_posts( $post_type, $limit, $wrapper, $details_array );
    }
}

if ( ! function_exists( 'sitecare_toolkit_get_post_primary_category' ) ) {
    function sitecare_toolkit_get_post_primary_category( $post_id, $term = 'category', $return_all_categories = false, $return_link = false ) {
        // Pass args to new function.
        return sctk_get_post_primary_category( $post_id, $term, $return_all_categories, $return_link );
    }
}

if ( ! function_exists( 'sitecare_toolkit_the_excerpt' ) ) {
    function sitecare_toolkit_the_excerpt( $post_id, $limit = '40' ) {
        // Pass args to new function.
        return sctk_the_excerpt( $post_id, $limit );
    }
}

if ( ! function_exists( 'sitecare_toolkit_get_post_thumbnail' ) ) {
    function sitecare_toolkit_get_post_thumbnail( $post_id = '', $thumb_size = '', $class = '' ) {
        // Pass args to new function.
        return sctk_get_post_thumbnail( $post_id, $thumb_size, $class );
    }
}

if ( ! function_exists( 'sitecare_toolkit_is_child' ) ) {
    function sitecare_toolkit_is_child( $page_id = '' ) {
        // Pass args to new function.
        return sctk_is_child( $page_id );
    }
}

if ( ! function_exists( 'sitecare_toolkit_is_ancestor' ) ) {
    function sitecare_toolkit_is_ancestor( $page_id = '' ) {
        // Pass args to new function.
        return sctk_is_ancestor( $page_id );
    }
}

if ( ! function_exists( 'sitecare_toolkit_modify_default_jquery' ) ) {
    function sitecare_toolkit_modify_default_jquery( $url = '', $ver = '' ) {
        // Pass args to new function.
        return sctk_modify_default_jquery( $url, $ver );
    }
}

if ( ! function_exists( 'sitecare_toolkit_estimated_reading_time' ) ) {
    function sitecare_toolkit_estimated_reading_time( $min_words = 200, $post_id = false ) {
        // Pass args to new function.
        return sctk_estimated_reading_time( $min_words, $post_id );
    }
}

if ( ! function_exists( 'sitecare_toolkit_estimated_reading_time_minutes' ) ) {
    function sitecare_toolkit_estimated_reading_time_minutes( $min_words = 200, $post_id = false, $appended_content, $custom_words ) {
        // Pass args to new function.
        return sctk_estimated_reading_time_minutes( $min_words, $post_id, $appended_content, $custom_words );
    }
}

if ( ! function_exists( 'sitecare_toolkit_change_url_domain' ) ) {
    function sitecare_toolkit_change_url_domain( $old_url = false, $new_url = false ) {
        // Pass args to new function.
        return sctk_change_url_domain( $old_url, $new_url );
    }
}

if ( ! function_exists( 'sitecare_toolkit_table_builder' ) ) {
    function sitecare_toolkit_table_builder( $thead = array(), $tbody = array(), $prefix = '', $class = '' ) {
        // Pass args to new function.
        return sctk_table_builder( $thead, $tbody, $prefix, $class );
    }
}

if ( ! function_exists( 'sitecare_toolkit_get_permalink_by_slug' ) ) {
    function sitecare_toolkit_get_permalink_by_slug( $slug, $post_type = 'page', $output = OBJECT ) {
        // Pass args to new function.
        return sctk_get_permalink_by_slug( $slug, $post_type, $output );
    }
}

if ( ! function_exists( 'sitecare_toolkit_console_log' ) ) {
    function sitecare_toolkit_console_log( $output, $with_script_tags = true, $table = false ) {
        // Pass args to new function.
        return sctk_console_log( $output, $with_script_tags , $table );
    }
}

if ( ! function_exists( 'sitecare_toolkit_calculate_execution_time' ) ) {
    function sitecare_toolkit_calculate_execution_time( $function = '', $in_console = false ) {
        // Pass args to new function.
        return sctk_calculate_execution_time( $function , $in_console );
    }
}

if ( ! function_exists( 'sitecare_toolkit_get_all_image_sizes' ) ) {
    function sitecare_toolkit_get_all_image_sizes() {
        // Pass args to new function.
        return sctk_get_all_image_sizes();
    }
}

if ( ! function_exists( 'sitecare_toolkit_create_cpt' ) ) {
    function sitecare_toolkit_create_cpt( $cpt = array() ) {
        // Pass args to new function.
        return sctk_create_cpt( $cpt );
    }
}

if ( ! function_exists( 'sitecare_toolkit_get_primary_categories' ) ) {
    function sitecare_toolkit_get_primary_categories( $post_type = array( 'post' ), $post_count = -1, $return_table = NULL ) {
        return sctk_get_primary_categories( $post_type, $post_count, $return_table );
    }
}
