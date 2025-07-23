<?php

/**
 * Astra Child Theme functions and definitions
 *
 * @package Astra_Child
 *
 * @link  https://developer.wordpress.org/themes/basics/theme-functions/
 * @since 1.0.0
 */

use ParagonIE\Sodium\Core\Curve25519\Ge\P2;

/**
 * Theme version
 *
 * @return int $theme_ver
 */
function sitecare_theme_ver()
{
    // Get the URL.
    $url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

    // Set theme version number.
    $theme_ver = wp_get_theme()->Version;

    // Check if we're on the staging website.
    if (strpos($url, 'wpsitecarepro.com') || strpos($url, 'sitecare.pro') || strpos($url, 'sitecare.dev')) {
        // Update theme version number.
        $theme_ver = time();
    }

    return $theme_ver;
}

// Define the theme version.
define('THEME_VER', sitecare_theme_ver());



/**
 * Function to log PHP vars to the console
 */

 function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';

    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }

    echo $js_code;
}

if (class_exists('Astra_Ext_Nav_Menu_Loader')) {

    add_action('wp_enqueue_scripts', function() {
        $megamenu_instance = Astra_Ext_Nav_Menu_Loader::get_instance();
        $megamenu_instance->megamenu_style();
    } );
    add_action('wp_footer', function() {
        $megamenu_instance = Astra_Ext_Nav_Menu_Loader::get_instance();
        remove_action( 'wp_footer', array( $megamenu_instance, 'megamenu_style' ) );
    }, 1);

}

/**
 * Enqueue styles
 */
function child_enqueue_styles()
{

    wp_enqueue_style('astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), THEME_VER, 'all');
    wp_enqueue_style('astra-child-main', get_stylesheet_directory_uri() . '/main.css', array('astra-child-theme-css'), THEME_VER, 'all');
}
add_action('wp_enqueue_scripts', 'child_enqueue_styles', 15);

/**
 * Register custom block categories.
 */
function grillseeker_block_categories( $categories )
{
    $category_slugs = wp_list_pluck( $categories, 'slug' );

    return in_array( 'grillseeker-blocks', $category_slugs, true ) ? $categories : array_merge(
        $categories,
        array(
            array(
                'slug'  => 'grillseeker-blocks',
                'title' => __( 'Grillseeker Blocks' )
            ),
        )
    );
}
add_filter( 'block_categories_all', 'grillseeker_block_categories', 10, 2 );

/**
 * Enqueue block editor only assets.
 */
function grillseeker_block_editor_assets()
{
    wp_enqueue_style(
        'grillseeker-blocks',
        get_stylesheet_directory_uri() . '/blocks.min.css',
        array(),
        THEME_VER,
        'all'
    );

    $script_path = '/grillseeker-blocks.min.js';

    wp_enqueue_script(
        'grillseeker-blocks',
        get_stylesheet_directory_uri() . $script_path,
        array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ),
        filemtime( get_stylesheet_directory_uri() . $script_path ),
        true
    );

    wp_localize_script(
        'grillseeker-blocks',
        'grillseeker',
        array(
            // 'nonce' => wp_create_nonce( 'wp_rest' ),
            'categories' => grillseeker_categories(),
        )
    );
}
add_action( 'enqueue_block_editor_assets', 'grillseeker_block_editor_assets' );

/**
 * Get all categories
 */
function grillseeker_categories( $ids = array() )
{
    $args = array(
        'taxonomy' => 'category',
        'hide_empty' => false,
        'order' => 'ASC',
        'orderby' => 'name',
    );

    if ( ! empty( $ids ) ) {
        $args['include'] = $ids;
        $args['orderby'] = 'include';
    }

    $categories = get_terms( $args );

    $categories_data = array();

    foreach ( $categories as $category ) {
        // Get the category icon.
        $attachment_id = get_term_meta( $category->term_id, 'category_icon' ) ? get_term_meta( $category->term_id, 'category_icon' )[0] : '';

        $category->icon = $attachment_id ? wp_get_attachment_image_src( $attachment_id, 'full' )[0] : get_stylesheet_directory_uri() . '/img/default-category-icon.png';

        $categories_data[] = $category;
    }

    return $categories_data;
}

/**
 * Require category block
 */
require_once get_stylesheet_directory() . '/js/blocks/category/category-block.php';

/**
 * Robots updates
 */
add_filter("wpseo_robots", function ($robots) {
    if (is_paged()) {
        return 'noindex,follow';
    } else {
        return $robots;
    }
});

/**
 * Unset Comment URL field
 *
 * @param object $fields
 *
 * @return array
 */
function sitecare_unset_url_field($fields)
{
    if (isset($fields['url']))
        unset($fields['url']);
    return $fields;
}
add_filter('comment_form_default_fields', 'sitecare_unset_url_field');


$image_count_number = 0;


// Add remove-lazy-loading class to featured images
function add_class_to_featured_image($attr)
{
    global $image_count_number;

    // Set the check to 5 in order to account for the four logo images in the header
    if ($image_count_number < 5) {
        $attr['class'] .= ' remove-lazy-loading';
    }


    $image_count_number++;

    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'add_class_to_featured_image', 10, 1);


// Remove lazy loading from featured post images
function change_attachement_image_attributes($attr, $attachment)
{

    $classes = $attr['class'] ? explode(' ', $attr['class']) : array();

    if (in_array('remove-lazy-loading', $classes)) {
        unset($attr['loading']);
    }


    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'change_attachement_image_attributes', 20, 2);


add_filter('wp_img_tag_add_loading_optimization_attrs', '__return_false');

// Remove the decoding attribute from featured images and the Post Image block.
add_filter( 'wp_get_attachment_image_attributes', function( $attributes ) {
    unset( $attributes['decoding'] );
    return $attributes;
} );

/**
 * Remove the decoding attribute from images with the classname "grillseeker-category-icon-image"
 */

add_filter( 'wp_get_attachment_image_attributes', function( $attributes ) {
    if ( isset( $attributes['class'] ) && false !== strpos( $attributes['class'], 'grillseeker-category-icon-image' ) ) {
        unset( $attributes['decoding'] );
    }
    return $attributes;
} );


add_action('acf/init', 'register_custom_acf_block');
function register_custom_acf_block() {
    if (function_exists('acf_register_block_type')) {
        acf_register_block_type(array(
            'name'              => 'post-date',
            'title'             => __('Post Date'),
            'description'       => __('Displays published and updated dates.'),
            'render_template'   => 'template-parts/blocks/post-date.php',
            'category'          => 'formatting',
            'icon'              => 'calendar-alt',
            'keywords'          => array('date', 'post', 'time'),
            'post_types'        => array('post'),
            'mode'              => 'preview',
            'supports'          => array(
                'align' => true,
                'mode' => true,
                'jsx' => true,
            )
        ));
    }
}