<?php

/**
 * The file that defines the plugin's helper functions for posts.
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
 * Display posts in customized loops.
 *
 * @param  string $post_type     - the post type to get posts from
 * @param  init   $limit         - the amount of posts to show
 * @param  string $wrapper       - the type of wrapper around the posts
 * @param  array  $details_array - what details from each post to display
 *
 * @return string
 */
if ( ! function_exists( 'sctk_display_posts' ) ) {
    function sctk_display_posts( $post_type = 'post', $limit = 10, $wrapper = 'ul', $details_array = array() ) {
        // Default post order.
        if ( ! isset( $details_array['orderby'] ) ) {
          $details_array['orderby'] = '';
        }
        // Post args.
        $args = array(
            'numberposts' => $limit,
            'orderby'     => $details_array['orderby'],
            'post_type'   => $post_type
        );
        // Filter args.
        $args = apply_filters( 'sctk_display_posts_args', $args );

        // Get posts.
        $posts = get_posts( $args );

        // Default image_size.
        if ( ! isset( $details_array['image_size'] ) ) {
          $details_array['image_size'] = 'thumbnail';
        }
        // Default featured_image.
        if ( ! isset( $details_array['featured_image'] ) ) {
          $details_array['featured_image'] = 'show';
        }
        // Default post_date.
        if ( ! isset( $details_array['post_date'] ) ) {
          $details_array['post_date'] = 'show';
        }
        // Default categories.
        if ( ! isset( $details_array['categories'] ) ) {
          $details_array['categories'] = 'show';
        }
        // Default excerpt.
        if ( ! isset( $details_array['excerpt'] ) ) {
          $details_array['excerpt'] = 'show';
        }
        // Default excerpt.
        if ( ! isset( $details_array['read_more'] ) ) {
          $details_array['read_more'] = 'show';
        }
        // Default class name.
        if ( isset( $details_array['class'] ) ) {
          $class_names = ' ' . $details_array['class'];
        } else {
          $class_names = '';
        }

        // Display posts as unordered list.
        if ( 'ul' === $wrapper ) {
            $str = '<ul class="sitecare-toolkit-display-posts' . $class_names . '">';
            // Loop through posts.
            foreach ( $posts as $post ) {
                // Post thumbnail.
                $post_thumbnail = get_the_post_thumbnail( $post->ID, $details_array['image_size'] );
                // String.
                $str .= '<li><a class="sitecare-toolkit-image" href="' . get_permalink( $post->ID ) . '">' . $post_thumbnail . '</a><a href="' . get_permalink( $post->ID ) . '">' . get_the_title( $post->ID ) . '</a></li>';
            }
            $str .= '</ul>';
        }

        // Display posts as div blocks.
        if ( 'div' === $wrapper ) {
            // Loop through posts.
            $str  = '<div class="sitecare-toolkit-display-posts' . $class_names . '">';
            foreach ( $posts as $post ) {
                // Post thumbnail.
                $post_thumbnail = get_the_post_thumbnail( $post->ID, $details_array['image_size'] );
                // Post date.
                $post_date = get_the_date( '', $post->ID );
                // Post categories.
                $post_categories = apply_filters( 'sctk_display_posts_categories', get_the_term_list( $post->ID, 'category', '', ' ', '' ), $post->ID );
                // Create post.
                $str .= '<div class="sitecare-toolkit-post">';
                  // Add thumbnail?
                  if ( 'show' == $details_array['featured_image'] && $post_thumbnail ) {
                      $str .= '<div class="sitecare-toolkit-image"><a href="' . get_permalink( $post->ID ) . '">' . $post_thumbnail . '</a></div>';
                  }
                  // Add content.
                  $str .= '<div class="sitecare-toolkit-content">';
                  // Add title.
                  $str .= '<h3><a href="' . get_permalink( $post->ID ) . '">' . get_the_title( $post->ID ) . '</a></h3>';
                  // Add categories?
                  if ( 'show' == $details_array['categories'] && $post_categories ) {
                    $str .= '<span class="categories">' . $post_categories . '</span>';
                  }
                  // Add post date?
                  if ( 'show' == $details_array['post_date'] ) {
                    $str .= '<span class="date">' . $post_date . '</span>';
                  }
                  // Add post excerpt?
                  if ( 'show' == $details_array['excerpt'] ) {
                    $str .= '<span class="excerpt">' . sctk_the_excerpt( $post->ID, 20 ) . '</span>';
                  }
                  // Add read-more link?
                  if ( 'show' == $details_array['read_more'] ) {
                    $str .= '<span class="read-more"><a href="' . get_permalink( $post->ID ) . '">' . esc_attr__( 'Read More', 'sitecare-toolkit' ) . '</a></span>';
                  }
                  // End content.
                  $str .= '</div>';
                // Finish post.
                $str .= '</div>';
            }
            $str .= '</div>';
        }

        return $str;
    }
}


/**
 * Get the 'primary' category for a specific post
 *
 * @todo - write better docs for this function :)
 * 
 * @param  int    $post_id - the post ID to get the primary category from
 * @param  string $term - the taxonomy term
 * @param  bool   $return_all_categories - returns all of the categories, not just the primary one
 * @param  bool   $return_link - returns <a href> link for the primary category
 *
 * @return object|string
 */
if ( ! function_exists( 'sctk_get_post_primary_category' ) ) {
    function sctk_get_post_primary_category( $post_id, $term = 'category', $return_all_categories = false, $return_link = false ) {
        $return = array();
        if ( class_exists( 'WPSEO_Primary_Term' ) ) {
            // Show Primary category by Yoast if it is enabled & set.
            $wpseo_primary_term = new WPSEO_Primary_Term( $term, $post_id );
            $primary_term       = get_term( $wpseo_primary_term->get_primary_term() );
            if ( ! is_wp_error( $primary_term ) ) {
                $return['primary_category'] = $primary_term;
            }
        }
        if ( empty( $return['primary_category'] ) || $return_all_categories ) {
            $categories_list = get_the_terms( $post_id, $term );
            if ( empty( $return['primary_category']) && ! empty( $categories_list ) ) {
                $return['primary_category'] = $categories_list[0];
            }
            if ( $return_all_categories ) {
                $return['all_categories'] = array();
                if ( ! empty( $categories_list ) ) {
                    foreach( $categories_list as &$category ) {
                        $return['all_categories'][] = $category->term_id;
                    }
                }
            }
        }
        if ( $return_link ) {
          $cat_array = $return['primary_category'];
          // Get the URL of this category.
          $category_link = get_category_link( $cat_array->term_id );
          return '<a class="sitecare-toolkit-primary-category" href="' . esc_url( $category_link ) . '" title="' . $cat_array->name. '">' . $cat_array->name. '</a>';
        }
        return $return;
    }
}


/**
 * Limit the excerpt
 * 
 * @param $post_id - the post ID you want to get the excerpt of
 * @param $limit   - the amount of words you'd like to return for the excerpt
 * 
 * @return string
 * 
 * @since 0.0.1
 */
if ( ! function_exists( 'sctk_the_excerpt' ) ) {
  function sctk_the_excerpt( $post_id, $limit = '40' ) {
      $limit = $limit + 1;
      $excerpt_text = explode( ' ', get_the_excerpt( $post_id ), $limit );
      array_pop( $excerpt_text );
      $excerpt_text = implode( ' ', $excerpt_text );
      // Check if excerpt exists.
      if ( '' != $excerpt_text ) {
          // New excerpt.
          return apply_filters( 'sctk_the_excerpt', '<p>' . $excerpt_text . '</p>', $excerpt_text );
      } else {
          // Do nothing.
      }
  }
}


/**
 * Get post thumbnail
 * 
 * @param  int $post_id - the post ID
 * @param  string $thumb_size - the post thumbnail size you'd like to use
 * @param  string $class - custom class added to <a> tag
 * 
 * @return string
 * @since 0.0.1
 */
if ( ! function_exists( 'sctk_get_post_thumbnail' ) ) {
  function sctk_get_post_thumbnail( $post_id = '', $thumb_size = '', $class = '' ) {
    // Check for post ID and thumbnail.
    if ( ! isset( $post_id ) || ! has_post_thumbnail( $post_id ) ) { return; }
    // Thumbnail size.
    if ( isset( $thumb_size ) ) {
      $thumb_size = $thumb_size;
    } else {
      $thumb_size = 'medium';
    }
    // Create thumbnail.
    $thumbnail  = '';
    $thumbnail .= '<a class="sitecare-toolkit-post-thumbnail ' . $class . '" href="' . get_the_permalink( $post_id ) . '">';
    $thumbnail .= get_the_post_thumbnail( $post_id, $thumb_size, array( 'class' => 'img-resp' ) );
    $thumbnail .= '</a>';

    return $thumbnail;
  }
}

/**
 * Delete posts by URL.
 *
 * @param   array    $source_urls - Array of post links that you would like removed
 * @return  bool     $delete      - permalink of the page
 * @since   1.0.4
 */
if ( ! function_exists( 'sitecare_delete_posts_by_url' ) ) {
  function sitecare_delete_posts_by_url( $source_urls = array(), $delete = false ) {
    // Start deletion process by appending "?move_posts_to_trash=true" to any URL on the site
    $trash_var = filter_input( INPUT_GET, 'move_posts_to_trash' );

    if ( $trash_var ) {
      // Source URL's.
      $source_urls = $urls;

      // Loop through source URL's.
      foreach( $source_urls as $url ) {
        // Get post object by permalink https://developer.wordpress.org/reference/functions/get_page_by_path/
        $url_post_object = get_page_by_path( $url, OBJECT, 'post' );
        // Delete or trash?
        if ( $delete ) {
          wp_delete_post( $url_post_object->ID, false );
        } else {
          wp_trash_post( $url_post_object->ID );
        }
      }
    }
  }
}
