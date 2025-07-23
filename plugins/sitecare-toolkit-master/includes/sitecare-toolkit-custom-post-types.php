<?php

/**
 * The file that defines the plugin's custom post types.
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
 * Custom Post Type generator
 * 
 * @param mixed $cpt Array with the following content:
 *     0 singular,
 *     1 plural,
 *     2 slug,
 *     3 rewrite,
 *     4 capabilities,
 *     5 args,
 *     6 menu icon
 * 
 * @since 0.0.1
 */
if ( ! function_exists( 'sctk_create_cpt' ) ) {
    function sctk_create_cpt( $cpt = array() ) {
        // Error message.
        $error_message = apply_filters( 'sctk_create_cpt_error_message', '<strong>Error:</strong> ' . esc_attr__( 'It is required to pass the single, plural and slug strings to sctk_create_cpt', 'sitecare-toolkit' ) );

        if ( ! is_array( $cpt ) ) {
            wp_die( $error_message );
        }

        if ( ! isset( $cpt[0], $cpt[1], $cpt[2] ) ) {
            wp_die( $error_message );
        }

        if ( ! is_string( $cpt[0] ) || ! is_string( $cpt[1] ) || ! is_string( $cpt[2] ) ) {
            wp_die( $error_message );
        }

        // Menu icon.
        if ( isset( $cpt[6] ) ) {
            // Custom icon.
            $menu_icon = $cpt[6];
        } else {
            // Default icon.
            $menu_icon = 'dashicons-arrow-right-alt2';
        }

        // Labels.
        $labels = array(
            'name'                  => $cpt[1],
            'singular_name'         => $cpt[0],
            'menu_name'             => sprintf( esc_attr__( '%s', 'sitecare-toolkit' ), $cpt[1] ),
            'name_admin_bar'        => $cpt[1],
            'archives'              => sprintf( esc_attr__( '%s Archives', 'sitecare-toolkit' ), $cpt[0] ),
            'attributes'            => sprintf( esc_attr__( '%s Attributes', 'sitecare-toolkit' ), $cpt[0] ),
            'parent_item_colon'     => sprintf( esc_attr__( 'Parent %s:', 'sitecare-toolkit' ), $cpt[0] ),
            'all_items'             => sprintf( esc_attr__( 'All %s', 'sitecare-toolkit' ), $cpt[1] ),
            'add_new_item'          => sprintf( esc_attr__( 'Add New %s', 'sitecare-toolkit' ), $cpt[0] ),
            'add_new'               => esc_attr__( 'Add New', 'sitecare-toolkit' ),
            'new_item'              => sprintf( esc_attr__( 'New %s', 'sitecare-toolkit' ), $cpt[0] ),
            'edit_item'             => sprintf( esc_attr__( 'Edit %s', 'sitecare-toolkit' ), $cpt[0] ),
            'update_item'           => sprintf( esc_attr__( 'Update %s', 'sitecare-toolkit' ), $cpt[0] ),
            'view_item'             => sprintf( esc_attr__( 'View %s', 'sitecare-toolkit' ), $cpt[0] ),
            'view_items'            => sprintf( esc_attr__( 'View %s', 'sitecare-toolkit' ), $cpt[1] ),
            'search_items'          => sprintf( esc_attr__( 'Search %s', 'sitecare-toolkit' ), $cpt[0] ),
            'not_found'             => esc_attr__( 'Not found', 'sitecare-toolkit' ),
            'not_found_in_trash'    => esc_attr__( 'Not found in Trash', 'sitecare-toolkit' ),
            'featured_image'        => esc_attr__( 'Featured Image', 'sitecare-toolkit' ),
            'set_featured_image'    => esc_attr__( 'Set featured image', 'sitecare-toolkit' ),
            'remove_featured_image' => esc_attr__( 'Remove featured image', 'sitecare-toolkit' ),
            'use_featured_image'    => esc_attr__( 'Use as featured image', 'sitecare-toolkit' ),
            'insert_into_item'      => sprintf( esc_attr__( 'Insert into %s', 'sitecare-toolkit' ), $cpt[0] ),
            'uploaded_to_this_item' => sprintf( esc_attr__( 'Uploaded to this %s', 'sitecare-toolkit' ), $cpt[0] ),
            'items_list'            => sprintf( esc_attr__( '%s list', 'sitecare-toolkit' ), $cpt[1] ),
            'items_list_navigation' => sprintf( esc_attr__( '%s list navigation', 'sitecare-toolkit' ), $cpt[1] ),
            'filter_items_list'     => sprintf( esc_attr__( 'Filter %s list', 'sitecare-toolkit' ), $cpt[1] ),
        );

        // Default rewrite.
        $rewrite = array(
            'slug'       => $cpt[2],
            'with_front' => true,
            'pages'      => true,
            'feeds'      => true,
        );

        // Custom rewrite.
        if ( ! empty( $cpt[3] ) ) {
            // Custom array.
            $rewrite = wp_parse_args( $cpt[3], $rewrite );
        }

        // Default capabilities.
        $capabilities = array(
            'edit_post'          => 'edit_post',
            'read_post'          => 'read_post',
            'delete_post'        => 'delete_post',
            'edit_posts'         => 'edit_posts',
            'edit_others_posts'  => 'edit_others_posts',
            'publish_posts'      => 'publish_posts',
            'read_private_posts' => 'read_private_posts',
        );

        // Custom capabilities.
        if ( ! empty( $cpt[4] ) ) {
            // Custom array.
            $capabilities = wp_parse_args( $cpt[4], $capabilities );
        }

        // Default args.
        $args = array(
            'label'               => $cpt[1],
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'author', 'thumbnail' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_icon'           => $menu_icon,
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => true,
            'publicly_queryable'  => true,
            'rewrite'             => $rewrite,
            'capability_type'     => 'post',
            'show_in_rest'        => true,
        );

        // Custom args.
        if ( ! empty( $cpt[5] ) ) {
            // Custom array.
            $args = wp_parse_args( $cpt[5], $args );
        }

        // Register post type.
        register_post_type( $cpt[1], $args );
    }
}
