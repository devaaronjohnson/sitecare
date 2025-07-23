<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
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
 * The plugin updater class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      0.1
 * @package    SiteCare_Toolkit
 * @subpackage SiteCare_Toolkit/includes
 * @author     Robert DeVore <robert@sitecare.com>
 */
class SiteCare_Update_Checker {

	public function plugin_update_checker() {
		$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://sitecare.com/sitecare-toolkit/updates/details.json',
			plugin_dir_path( dirname( __FILE__ ) ) . 'sitecare-toolkit.php',
			'sitecare-toolkit'
		);
	}

}

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.0.1
 * @package    SiteCare_Toolkit
 * @subpackage SiteCare_Toolkit/includes
 * @author     Robert DeVore <robert@sitecare.com>
 */
class SiteCare_Toolkit {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      SiteCare_Toolkit_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {
		$this->plugin_name = 'sitecare-toolkit';
		$this->version     = '0.2.0';
		// Check if this is defined elsewhere.
		if ( defined( 'SITECARE_TOOLKIT_VERSION' ) ) {
			$this->version = SITECARE_TOOLKIT_VERSION;
		}

		$this->load_dependencies();
		$this->set_locale();
		$this->update_checker();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - SiteCare_Toolkit_Loader. Orchestrates the hooks of the plugin.
	 * - SiteCare_Toolkit_i18n. Defines internationalization functionality.
	 * - SiteCare_Toolkit_Admin. Defines all hooks for the admin area.
	 * - SiteCare_Toolkit_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sitecare-toolkit-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sitecare-toolkit-i18n.php';

		/**
		 * The file responsible for including the plugin update checker functionality.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/plugin-update-checker/plugin-update-checker.php';

		/**
		 * The class responsible for defining the WP OOP API admin settings.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sitecare-toolkit-admin-settings.php';

		/**
		 * The file responsible for defining the custom WP OOP API admin settings.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/sitecare-toolkit-admin-settings-init.php';

		/**
		 * The file responsible for defining all custom post types.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/sitecare-toolkit-custom-post-types.php';

		/**
		 * The file responsible for defining all general helper functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/sitecare-toolkit-general-functions.php';

		/**
		 * The file responsible for defining all posts related helper functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/sitecare-toolkit-posts-functions.php';

		/**
		 * The file responsible for defining all pages related helper functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/sitecare-toolkit-pages-functions.php';

		/**
		 * The file responsible for defining all user related helper functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/sitecare-toolkit-user-functions.php';

		/**
		 * The file responsible for defining all debug related helper functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/sitecare-toolkit-debug-functions.php';

		/**
		 * The file responsible for defining all string replacement related helper functions.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/sitecare-toolkit-string-replace-functions.php';

		/**
		 * The file responsible for defining the hide email shortcode.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/shortcodes/sitecare-toolkit-hide-email-shortcode.php';

		/**
		 * The class responsible for defining the Author Box widget.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets/sitecare-toolkit-author-box-widget.php';

		/**
		 * The class responsible for defining the Recent Posts widget.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets/sitecare-toolkit-recent-posts-widget.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sitecare-toolkit-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sitecare-toolkit-public.php';

		$this->loader = new SiteCare_Toolkit_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the SiteCare_Toolkit_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new SiteCare_Toolkit_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Plugin update checker
	 * 
	 * @since    0.1
	 * @access   private
	 */
	private function update_checker() {

		$update_checker = new SiteCare_Update_Checker();

		$this->loader->add_action( 'plugins_loaded', $update_checker, 'plugin_update_checker' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new SiteCare_Toolkit_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new SiteCare_Toolkit_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.0.1
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.0.1
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.0.1
	 * @return    SiteCare_Toolkit_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.0.1
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
