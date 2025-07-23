<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.sitecare.com
 * @since             0.0.1
 * @package           SiteCare_Toolkit
 *
 * @wordpress-plugin
 * Plugin Name:       SiteCare Toolkit
 * Plugin URI:        https://www.sitecare.com
 * Description:       A set of helper functions and widgets for theme development.
 * Version:           99.0.2.0
 * Author:            SiteCare
 * Author URI:        https://www.sitecare.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sitecare-toolkit
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin version.
define( 'SITECARE_TOOLKIT_VERSION', '0.2.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sitecare-toolkit-activator.php
 */
function activate_sitecare_toolkit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sitecare-toolkit-activator.php';
	SiteCare_Toolkit_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sitecare-toolkit-deactivator.php
 */
function deactivate_sitecare_toolkit() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sitecare-toolkit-deactivator.php';
	SiteCare_Toolkit_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sitecare_toolkit' );
register_deactivation_hook( __FILE__, 'deactivate_sitecare_toolkit' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sitecare-toolkit.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.1
 */
function run_sitecare_toolkit() {

	$plugin = new SiteCare_Toolkit();
	$plugin->run();

}
run_sitecare_toolkit();
