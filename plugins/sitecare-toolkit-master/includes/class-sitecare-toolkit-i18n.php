<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
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
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.0.1
 * @package    SiteCare_Toolkit
 * @subpackage SiteCare_Toolkit/includes
 * @author     Robert DeVore <robert@sitecare.com>
 */
class SiteCare_Toolkit_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.0.1
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'sitecare-toolkit',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
