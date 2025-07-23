<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.sitecare.com
 * @since      0.0.1
 *
 * @package    SiteCare_Toolkit
 * @subpackage SiteCare_Toolkit/public
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    SiteCare_Toolkit
 * @subpackage SiteCare_Toolkit/public
 * @author     Robert DeVore <robert@sitecare.com>
 */
class SiteCare_Toolkit_Public {

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
	 * @param    string    $plugin_name    The name of the plugin.
	 * @param    string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_styles() {
		// Access all Scripts Settings.
		$scripts = get_option( 'sitecare_toolkit_scripts' );

		// Check the slick.js script settings.
		if ( isset( $scripts['scripts_slickjs'] ) && 'off' !== $scripts['scripts_slickjs'] ) {
			wp_enqueue_style( $this->plugin_name . '-slick', plugin_dir_url( __FILE__ ) . 'css/slick.css', array(), $this->version, 'all' );
		} else {
			// Do nothing.
		}

		// Check the FontAwesome settings.
		if ( isset( $scripts['scripts_fontawesome'] ) && 'off' !== $scripts['scripts_fontawesome'] ) {
			wp_enqueue_style( $this->plugin_name . '-fontawesome', plugin_dir_url( __FILE__ ) . 'css/fontawesome/all.css', array(), $this->version, 'all' );
		} else {
			// Do nothing.
		}

		// Load the plugin's general public CSS.
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sitecare-toolkit-public.min.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {
		// Access all Scripts Settings.
		$scripts = get_option( 'sitecare_toolkit_scripts' );

		// Check the matchHeight script settings.
		if ( isset( $scripts['scripts_matchheight'] ) && 'off' !== $scripts['scripts_matchheight'] ) {
			wp_enqueue_script( $this->plugin_name . '-matchheight', plugin_dir_url( __FILE__ ) . 'js/jquery.matchHeight.min.js', array( 'jquery' ), $this->version, false );
		} else {
			// Do nothing.
		}

		// Check the slick.js script settings.
		if ( isset( $scripts['scripts_slickjs'] ) && 'off' !== $scripts['scripts_slickjs'] ) {
			wp_enqueue_script( $this->plugin_name . '-slick', plugin_dir_url( __FILE__ ) . 'js/slick.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name . '-slick-custom', plugin_dir_url( __FILE__ ) . 'js/slick-custom.js', array( 'jquery' ), $this->version, false );
		} else {
			// Do nothing.
		}

		// Load the plugin's general public JS.
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sitecare-toolkit-public.js', array( 'jquery' ), $this->version, false );
	}

}
