<?php

/**
 * The SiteCare Toolkit Admin Settings
 *
 * @link       https://www.sitecare.com
 * @since      0.0.1
 *
 * @package    SiteCare_Toolkit
 * @subpackage SiteCare_Toolkit/admin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP-OOP-Settings-API Initializer
 *
 * Initializes the WP-OOP-Settings-API.
 *
 * @since   0.0.1
 */

/**
 * Actions/Filters
 *
 * Related to all settings API.
 *
 * @since  0.0.1
 */
if ( class_exists( 'SiteCare_Toolkit_Settings' ) ) {
	/**
	 * Object Instantiation.
	 *
	 * Object for the class `SiteCare_Toolkit_Settings`.
	 */
	$sitecare_toolkit_obj = new SiteCare_Toolkit_Settings();

	// Section: Basic Settings.
	$sitecare_toolkit_obj->add_section(
		array(
			'id'    => 'sitecare_toolkit_scripts',
			'title' => esc_attr__( 'Scripts', 'sitecare-toolkit' ),
		)
	);

	// Field: Slick.js.
	$sitecare_toolkit_obj->add_field(
		'sitecare_toolkit_scripts',
		array(
			'id'   => 'scripts_slickjs',
			'type' => 'checkbox',
			'name' => esc_attr__( 'Slick.js', 'sitecare-toolkit' ),
			'desc' => esc_attr__( 'Enable the Slick.js script', 'sitecare-toolkit' ),
		)
	);

	// Field: matchHeight.js.
	$sitecare_toolkit_obj->add_field(
		'sitecare_toolkit_scripts',
		array(
			'id'   => 'scripts_matchheight',
			'type' => 'checkbox',
			'name' => esc_attr__( 'machHeight.js', 'sitecare-toolkit' ),
			'desc' => esc_attr__( 'Enable the matchHeight.js script', 'sitecare-toolkit' ),
		)
	);

	// Field: FontAwesome.
	$sitecare_toolkit_obj->add_field(
		'sitecare_toolkit_scripts',
		array(
			'id'   => 'scripts_fontawesome',
			'type' => 'checkbox',
			'name' => esc_attr__( 'FontAwesome', 'sitecare-toolkit' ),
			'desc' => esc_attr__( 'Enable the FontAwesome v5.15.2 CSS', 'sitecare-toolkit' ),
		)
	);

    // Make sure Simple History is active.
    if ( class_exists( 'SimpleHistory' ) ) {
		// Field: Simple History log purge days.
		$sitecare_toolkit_obj->add_field(
			'sitecare_toolkit_scripts',
			array(
				'id'      => 'scripts_simple_history_log_purge_days',
				'type'    => 'text',
				'name'    => esc_attr__( 'Simple History log purge days', 'sitecare-toolkit' ),
				'default' => '14'
			)
		);
	}

}
