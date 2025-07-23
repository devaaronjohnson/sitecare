<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.sitecare.com
 * @since      0.0.1
 *
 * @package    SiteCare_Toolkit
 * @subpackage SiteCare_Toolkit/admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    SiteCare_Toolkit
 * @subpackage SiteCare_Toolkit/admin
 * @author     Robert DeVore <robert@sitecare.com>
 */
class SiteCare_Toolkit_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param    string    $plugin_name    The name of this plugin.
	 * @param    string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_styles() {
		// Load the plugin's general admin CSS.
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sitecare-toolkit-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {
		// Disabled until needed - Load the plugin's general admin JS.
		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sitecare-toolkit-admin.js', array( 'jquery' ), $this->version, false );

		// Get current screen.
		$screen = get_current_screen();
		// Only add on widgets.php screen.
		if ( 'widgets' == $screen->id ) {
			wp_enqueue_media();
			wp_enqueue_script( 'sitercare-toolkit-author-box-widget', plugin_dir_url( __FILE__ ) . '/js/sitecare-toolkit-author-box-widget.js', false, '1.0.0', true );
		}
	}

}
