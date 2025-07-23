<?php

/**
 * The file that defines the hide email shortcode.
 *
 * @link       https://www.sitecare.com
 * @since      0.2.0
 *
 * @package    SiteCare_Toolkit
 * @subpackage SiteCare_Toolkit/includes/shortcodes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'sctk_hide_email_shortcode' ) ) {
    /**
     * Hide email from Spam Bots using a shortcode.
     *
     * @param array  $atts    Shortcode attributes. Not used.
     * @param string $content The shortcode content. Should be an email address.
     *
     * @return string The obfuscated email address.
     * @since  0.2.0
     */
    function sctk_hide_email_shortcode( $atts , $content = null ) {
        if ( ! is_email( $content ) ) {
            return;
        }

        $content = antispambot( $content );

        $email_link = sprintf( 'mailto:%s', $content );

        return sprintf( '<a href="%s">%s</a>', esc_url( $email_link, array( 'mailto' ) ), esc_html( $content ) );
    }
    add_shortcode( 'hide_email', 'sctk_hide_email_shortcode' );
}
