<?php

/**
 * The file that defines the plugin's helper functions for users.
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
 * Checks if a particular user has one or more roles.
 *
 * Returns true on first matching role. Returns false if no roles match.
 *
 * @uses get_userdata()
 * @uses wp_get_current_user()
 *
 * @param  array|string $roles Role name (or array of names).
 * @param  int          $user_id (Optional) The ID of a user. Defaults to the current user.
 * @return bool
 */
if ( ! function_exists( 'sctk_check_user_roles' ) ) {
    function sctk_check_user_roles( $roles, $user_id = null ) {
        // Default user ID.
        $user = wp_get_current_user();

        // Set user ID.
        if ( is_numeric( $user_id ) ) {
            $user = get_userdata( $user_id );
        }
        // Bail early?
        if ( empty( $user ) ) {
            return false;
        }
        // Get user roles.
        $user_roles = (array) $user->roles;
        // Loop through user roles.
        foreach ( (array) $roles as $role ) {
            if ( in_array( $role, $user_roles ) ) {
              // Role found.
              return true;
            }
        }
        // Role(s) not found.
        return false;
    }
}


/**
 * Capture user login and add it as timestamp in user meta data
 * 
 * @see sctk_user_last_login()
 */
function sctk_last_login( $user_login, $user ) {
    // Add last login to user meta.
    update_user_meta( $user->ID, 'sitecare_toolkit_last_login', time() );
}
add_action( 'wp_login', 'sctk_last_login', 10, 2 );


/**
 * Checks the last login time of a specific user
 *
 * @uses get_user_meta()
 * @uses sctk_last_login()
 *
 * @param  int       $user_id The user ID.
 * @param  bool      $human - returns a human readable time (example - 15 mins)
 * @return string
 */
if ( ! function_exists( 'sctk_user_last_login' ) ) {
    function sctk_user_last_login( $user_id = '', $human = true ) {
        // Bail early if no user is passed in.
        if ( ! $user_id ) {
            return;
        }

        // Get the last login.
        $last_login = get_user_meta( $user_id, 'sitecare_toolkit_last_login', true );

        // Human readable time.
        if ( $last_login && $human ) {
            // Create a human readable time.
            $last_login = human_time_diff( $last_login );
        }

        return $last_login;
    }
}
