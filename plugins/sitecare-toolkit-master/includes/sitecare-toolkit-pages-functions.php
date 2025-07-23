<?php

/**
 * The file that defines the plugin's helper functions for pages.
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
 * Check if the page is a child page.
 *
 * @param  int $page_id - the page ID.
 * 
 * @return bool
 */
if ( ! function_exists( 'sctk_is_child' ) ) {
    function sctk_is_child( $page_id = '' ) {
        if ( isset( $page_id ) ) {
            $post = get_post( $page_id );
        }
        if ( is_page() && ( $post->post_parent != 0 ) && ( $post->post_parent != $page_id ) ) {
            return true;
        } else {
            return false;
        }
    }
}


/**
 * Check if the page is an ancestor page.
 *
 * @param  string $page_id - the page ID.
 * 
 * @return bool
 */
if ( ! function_exists( 'sctk_is_ancestor' ) ) {
    function sctk_is_ancestor( $post_id = '' ) {
        global $wp_query;
        $ancestors = $wp_query->post->ancestors;
        if ( in_array( $post_id, $ancestors ) ) {
            return true;
        } else {
            return false;
        }
    }
}
