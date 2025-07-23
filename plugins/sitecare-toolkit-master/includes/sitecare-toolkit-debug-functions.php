<?php

/**
 * The file that defines the plugin's helper functions for debugging purposes.
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
 * Add PHP variables to the console log
 *
 * Passes a PHP variable to the browser's console so that it can be inspected like a JS variable.
 *
 * @param array|string $output = the variable or function to be checked within the browser console.
 * @param boolean      $with_script_tags (Optional) true will render the results in a <script> tag.
 * @param boolean      $table (Optional) true will use console_table instead of console_log
 * @return array|string|int
 */
if ( ! function_exists( 'sctk_console_log' ) ) {
    function sctk_console_log( $output, $with_script_tags = true, $table = false ) {
        if ( $table ) {
            $js_code = 'console.table(' . json_encode( $output, JSON_HEX_TAG ) . ');';
        } else {
            $js_code = 'console.log(' . json_encode( $output, JSON_HEX_TAG ) . ');';
        }

        if ( $with_script_tags ) {
            $js_code = '<script>' . $js_code . '</script>';
        }

        echo $js_code;
    }
}


/**
 * Calculate code execution time
 *
 * Checks the time that a specific function takes to execute.
 *
 * @param array|string $function = the function name you would like to check the execution time of.
 * @param bool         $in_console = (optional) output the execution time in the console.
 * @return int
 */
if ( ! function_exists( 'sctk_calculate_execution_time' ) ) {
    function sctk_calculate_execution_time( $function = '', $in_console = false ) {
        if ( $function ) {
            // Start time.
            $starttime = microtime( true );
            // Function.
            $function;
            // End time.
            $endtime = microtime( true );
            // Calculate total time taken.
            $duration = $endtime - $starttime;
            // Output in console.
            if ( $in_console ) {
                sctk_console_log( 'Execution time: ' . $duration );
            }

            return $duration;
        }
    }
}


/**
 * Get all the registered image sizes along with their dimensions
 *
 * @global array $_wp_additional_image_sizes
 * @return array $image_sizes The image sizes
 */
if ( ! function_exists( 'sctk_get_all_image_sizes' ) ) {
    function sctk_get_all_image_sizes() {
        global $_wp_additional_image_sizes;

        $default_image_sizes = get_intermediate_image_sizes();

        foreach ( $default_image_sizes as $size ) {
            $image_sizes[ $size ][ 'width' ]  = intval( get_option( "{$size}_size_w" ) );
            $image_sizes[ $size ][ 'height' ] = intval( get_option( "{$size}_size_h" ) );
            $image_sizes[ $size ][ 'crop' ]   = get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
        }

        if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
            $image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
        }

        return $image_sizes;
    }
}
