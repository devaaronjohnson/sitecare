<?php
/**
 * Plugin Name: SiteCare Staging Tools
 * Plugin URI:  https://sitecare.com
 * Description: Sets up the site for staging and ensures required plugins are loaded.
 * Version:     1.2.1
 * Author:      SiteCare, LLC
 * Author URI:  https://sitecare.com
 * License:     MIT
 * License URI: http://wpsitecare.mit-license.org/
 */

// Bail if this was called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Disable the WordPress file editor
define( 'DISALLOW_FILE_EDIT', true );

// Define the list of required plugins
define( 'SITECARE_MUST_USE_REQUIRED_PLUGINS',  array(
    'restricted-site-access/restricted_site_access.php',
    'stop-emails/stop-emails.php',
    'blogvault-real-time-backup/blogvault.php',
) );

// Define the list of banned plugins
define( 'SITECARE_MUST_USE_BANNED_PLUGINS', array(
    'easy-wp-smtp/easy-wp-smtp.php',
    'wp-mail-smtp/wp_mail_smtp.php',
    'patchstack/patchstack.php',
    'better-wp-security/better-wp-security.php',
    'all-in-one-wp-security-and-firewall/wp-security.php',
    'wordfence/wordfence.php',
    'sucuri-scanner/sucuri.php',
    'updraftplus/updraftplus.php',
    'duplicator/duplicator.php',
    'all-in-one-wp-migration/all-in-one-wp-migration.php',
    'aryo-activity-log/aryo-activity-log.php',
    'wp-security-audit-log/wp-security-audit-log.php',
    'w3-total-cache/w3-total-cache.php',
    'wp-super-cache/wp-cache.php',
    'gravitysmtp/gravitysmtp.php',
) );

// Prepend "*STAGING* " to the site title
add_filter( 'option_blogname', function ( string $title ): string {
    return '*STAGING* ' . $title;
} );

// Remove the color scheme picker from the user profile screen
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

// Set "Ectoplasm" as the color scheme
add_filter( 'get_user_option_admin_color', function () {
    return 'ectoplasm';
} );

// Set the site as private if Restricted Site Access is activated
add_filter( 'pre_option_blog_public', function () {
    return defined( 'RSA_VERSION' ) ? 2 : 0;
} );

// Register license keys for premium plugins

// ACF Pro
if (!defined('ACF_PRO_LICENSE')) {
    // Define the constant
    define('ACF_PRO_LICENSE', 'b3JkZXJfaWQ9MzI1NDh8dHlwZT1kZXZlbG9wZXJ8ZGF0ZT0yMDE0LTA3LTA3IDEzOjM1OjM2');
}

// Gravity Forms
if (!defined('GF_LICENSE_KEY')) {
    // Define the constant
    define('GF_LICENSE_KEY', '065f8e19481ea71b4feb7f8d337c9cf6');
}

// WP Migrate
if (!defined('WPMDB_LICENCE')) {
    // Define the constant
    define('WPMDB_LICENCE', '8754891c-aeb6-4c74-ae79-e303a4cdfc79');
}

// Ensure required plugins are installed and activated
add_action( 'init', function () {

    // Ready a list of missing plugins, which will crash the site if not empty
    $missing_plugins = array();

    // Load plugin functions, so we can handle required plugins the official way
    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    // Loops through required plugins and prevents them from being deactivated
    foreach ( SITECARE_MUST_USE_REQUIRED_PLUGINS as $plugin_path ) {

        $require_path = WP_PLUGIN_DIR . '/' . $plugin_path;

        // If the plugin is not installed then try to install it or add it to the missing list
        if ( ! file_exists( $require_path ) ) {

            // If we are not browsing a page then do not attempt to install and just add to the crash list
            if ( wp_doing_ajax() || wp_is_json_request() || is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
                $missing_plugins[] = $plugin_path;
                continue;
            }

            // We want to install the plugin asynchronously, so we are pretending this is an AJAX call
            require_once ABSPATH . 'wp-admin/includes/admin.php';
            require_once ABSPATH . 'wp-admin/includes/ajax-actions.php';
            include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

            $slug = substr( $plugin_path, 0, strpos( $plugin_path, '/' ) );

            // Fetch the plugin info from the WordPress plugin repo
            $api = plugins_api( 'plugin_information', array( 'slug' => $slug, 'fields' => array( 'sections' => false ) ) );

            // If the plugin does not exist on the repo then add it to the crash list
            if ( is_wp_error( $api ) ) {
                $missing_plugins[] = $plugin_path;
                continue;
            }

            // Try to install the plugin
            $skin = new WP_Ajax_Upgrader_Skin();
            $upgrader = new Plugin_Upgrader( $skin );
            $result = $upgrader->install( $api->download_link );

            // If the installation failed then add the plugin to the crash list
            if ( is_wp_error( $result ) || is_wp_error( $skin->result ) || $skin->get_errors()->has_errors() || is_null( $result ) ) {
                $missing_plugins[] = $plugin_path;
                continue;
            }
        }

        // Activate the plugin if it is not already activated
        if ( ! is_plugin_active( $plugin_path ) ) {
            activate_plugin( $plugin_path );
        }

        // Require the main plugin file in case activation failed
        require_once $require_path;

        // Remove the deactivate and delete action links from the plugin table
        foreach ( array( 'network_admin_plugin_action_links_%s', 'plugin_action_links_%s' ) as $format ) {
            $hook_name = sprintf( $format, plugin_basename( $require_path ) );
            add_filter( $hook_name, function ( $actions ) {
                unset( $actions['deactivate'] );
                unset( $actions['delete'] );
                return $actions;
            } );
        }
    }

    // Crash the site if there are missing plugins
    if ( ! empty( $missing_plugins ) ) {
        wp_die( sprintf( __( 'Required plugins are missing:<br>%s' ), implode( '<br>', $missing_plugins ) ) );
    }

} );

// Crash the site if a banned plugin is activated or installed
add_action( 'init', function () {

    // Attempt to deactivate banned plugins automatically and fetch an array of those not deactivated
    $banned_plugins = sitecare_must_use_deactivate_banned_plugins();

    // If a banned plugin is still activated, crash the site with a link to the plugins screen to deactivate it
    if ( ! is_admin() && ! is_login() && ! empty( $banned_plugins ) ) {
        wp_die( sprintf( __( '<strong>Banned plugins need to be <a href="%s">deactivated and deleted</a>:</strong><br>%s' ), admin_url( 'plugins.php' ), implode( '<br>', $banned_plugins ) ) );
    }
} );

// Crash admin if a banned plugin is activated or installed
add_action( 'current_screen', function () {

    // Get the current WordPress admin screen – only the plugins screen can be displayed
    $current_screen = get_current_screen();

    // Attempt to deactivate banned plugins automatically and fetch an array of those not deactivated
    $banned_plugins = sitecare_must_use_deactivate_banned_plugins();

    // If a banned plugin is still activated, display an error notice or crash admin
    if ( ! empty( $banned_plugins ) ) {

        // If we are on the plugins screen then just display an error notice
        if ( $current_screen instanceof WP_Screen && 'plugins' == $current_screen->id ) {
            add_action( 'admin_notices', function () use ( $banned_plugins ) {
                printf( '<div class="notice notice-error"><p><strong>Banned plugins need to be deactivated and deleted:</strong><br>%s</p></div>', implode( '<br>', $banned_plugins ) );
            } );
        }

        // If we are not on the plugins screen, crash admin with a link to the plugins screen to deactivate it
        else {
            wp_die( sprintf( __( '<strong>Banned plugins need to be <a href="%s">deactivated and deleted</a>:</strong><br>%s' ), admin_url( 'plugins.php' ), implode( '<br>', $banned_plugins ) ) );
        }
    }
} );

// Remove the activate action link from the plugins table
add_action( 'admin_init', function () {

    // Loop through banned plugins and prevent them from being activated
    foreach ( SITECARE_MUST_USE_BANNED_PLUGINS as $plugin_path ) {

        $require_path = WP_PLUGIN_DIR . '/' . $plugin_path;

        // If the plugin is not installed then we are in luck and can move on
        if ( ! file_exists( $require_path ) ) {
            continue;
        }

        // Remove the activate action link from the plugins table
        foreach ( array( 'network_admin_plugin_action_links_%s', 'plugin_action_links_%s' ) as $format ) {
            $hook_name = sprintf( $format, plugin_basename( $require_path ) );
            add_filter( $hook_name, function ( $actions ) {
                unset( $actions['activate'] );
                return $actions;
            } );
        }
    }
} );

// Define the active staging alert options
define( 'SITECARE_MUST_USE_ACTIVE_STAGING_OPTIONS', get_option( 'sitecare_active_staging', array( 'enabled' => false ) ) );

// Define whether the active staging alert is enabled
define( 'SITECARE_MUST_USE_ACTIVE_STAGING_ENABLED', sitecare_must_use_active_staging_is_enabled() );

// Define the various settings available for the active staging alert
define( 'SITECARE_MUST_USE_ACTIVE_STAGING_SETTINGS', array(
    'enabled' => array(
        'section' => 'main',
        'label' => __( 'Enabled', 'sitecare' ),
        'type' => 'checkbox'
    ),
    'color' => array(
        'section' => 'main',
        'label' => __( 'Color', 'sitecare' ),
        'type' => 'select',
        'options' => array(
            '#72aee6' => 'Blue',
            '#00a32a' => 'Green',
            '#dba617' => 'Orange',
            '#d63638' => 'Red',
        ),
        'default' => '#d63638',
    ),
    'name' => array(
        'section' => 'person',
        'label' => __( 'Name', 'sitecare' ),
        'type' => 'text'
    ),
    'organization' => array(
        'section' => 'person',
        'label' => __( 'Organization', 'sitecare' ),
        'type' => 'text'
    ),
    'email' => array(
        'section' => 'person',
        'label' => __( 'Email', 'sitecare' ),
        'type' => 'email'
    ),
    'phone' => array(
        'section' => 'person',
        'label' => __( 'Phone', 'sitecare' ),
        'type' => 'tel'
    ),
    'slack' => array(
        'section' => 'person',
        'label' => __( 'Slack', 'sitecare' ),
        'type' => 'url',
        'sensitive' => true,
    ),
    'task' => array(
        'section' => 'context',
        'label' => __( 'ClickUp Task Link', 'sitecare' ),
        'type' => 'url',
        'sensitive' => true
    ),
    'internal' => array(
        'section' => 'context',
        'label' => __( 'Slack Thread Link', 'sitecare' ),
        'type' => 'url',
        'sensitive' => true
    ),
    'external' => array(
        'section' => 'context',
        'label' => __( 'Front Thread Link', 'sitecare' ),
        'type' => 'url',
        'sensitive' => true
    ),
    'notes' => array(
        'section' => 'context',
        'label' => __( 'Additional Notes', 'sitecare' ),
        'type' => 'text'
    ),
    'start' => array(
        'section' => 'dates',
        'label' => __( 'Start Date', 'sitecare' ),
        'type' => 'date'
    ),
    'end' => array(
        'section' => 'dates',
        'label' => __( 'End Date', 'sitecare' ),
        'type' => 'date'
    ),
) );

/**
 * Identify installed banned plugins and returns them in an array.
 *
 * @return array
 */
function sitecare_must_use_deactivate_banned_plugins(): array {

    // Load plugin functions, so we can handle required plugins the official way
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    include_once ABSPATH . 'wp-admin/includes/file.php';

    // Ready a list of banned plugins that are installed
    $banned_plugins = array();

    // Loops through banned plugins and adds them to the crash list
    foreach ( SITECARE_MUST_USE_BANNED_PLUGINS as $plugin_path ) {

        $require_path = WP_PLUGIN_DIR . '/' . $plugin_path;

        // If the plugin is installed, add it to the crash list
        if ( file_exists( $require_path ) ) {
            $banned_plugins[] = $plugin_path;
        }
    }

    return $banned_plugins;
}

/**
 * Determines whether the active staging alert is enabled. If it is enabled and the end date is set but past then
 * consider it disabled.
 *
 * @return bool
 */
function sitecare_must_use_active_staging_is_enabled(): bool {

    $enabled = SITECARE_MUST_USE_ACTIVE_STAGING_OPTIONS['enabled'] ?? false;
    $end = SITECARE_MUST_USE_ACTIVE_STAGING_OPTIONS['end'] ?? false;

    if ( $enabled && $end ) {

        try {

            $timezone = new DateTimeZone( 'America/New_York' );
            $end_date = new DateTime( $end, $timezone );
            $now_date = new DateTime( 'now', $timezone );

            if ( $end_date < $now_date ) {
                return false;
            }

        } catch ( Exception $e ) {
            trigger_error( $e->getMessage() );
            return false;
        }

    }

    return $enabled;
}

/**
 * Checks whether the current user is a SiteCare team member
 *
 * @return bool
 */
function sitecare_must_use_is_personnel(): bool {

    foreach ( array( 'sitecare.com', 'wpsitecare.com', 'southernweb.com' ) as $domain ) {

        $pattern = sprintf( '/@%s$/', preg_quote( $domain ) );
        $subject = wp_get_current_user()->user_email;

        if ( 1 === preg_match( $pattern, $subject ) ) {
            return true;
        }
    }

    return false;
}

// Registers the active staging alert options and settings
add_action( 'admin_init', function () {

    register_setting( 'sitecare_active_staging', 'sitecare_active_staging' );

    $page = 'sitecare_active_staging';

    $sections = array(
        'main' => __( 'Enabled', 'sitecare' ),
        'person' => __( 'Person Staging', 'sitecare' ),
        'context' => __( 'Staging Context', 'sitecare' ),
        'dates' => __( 'Start &amp; End', 'sitecare' ),
    );

    foreach ( $sections as $section_name => $section_title ) {
        $section_id = 'sitecare_active_staging_section_' . $section_name;
        add_settings_section(
            $section_id,
            $section_title,
            function ( $args ) {},
            $page
        );
    }

    foreach ( SITECARE_MUST_USE_ACTIVE_STAGING_SETTINGS as $setting_name => $setting_config ) {

        if ( ! sitecare_must_use_is_personnel() && ( ! isset( $setting_config['sensitive'] ) || $setting_config['sensitive'] ?? false ) ) {
            continue;
        }

        $setting_id = 'sitecare_active_staging_field_' . $setting_name;
        $section_id = 'sitecare_active_staging_section_' . $setting_config['section'];

        add_settings_field(
            $setting_id,
            $setting_config['label'],
            function ( $args ) {

                $name = $args['name'];
                $type = $args['type'];
                $id = $args['id'];
                $options = $args['options'];
                $stored_value = SITECARE_MUST_USE_ACTIVE_STAGING_OPTIONS[ $name ] ?? $args['default'];

                if ( 'color' == $name ) {
                    foreach ( $options as $value => $label ) {
                        printf( '<div><label for="%2$s_%3$s"><input name="sitecare_active_staging[%1$s]" type="radio" id="%2$s_%3$s" value="%3$s"%5$s><span class="dashicons dashicons-warning" style="color: %3$s;"></span> %4$s</label></div>', $name, $id, $value, $label, $value == $stored_value ? 'checked' : '' );
                    }
                    return;
                }

                switch ( $type ) {

                    case 'select':
                        $options = array_map( function ( $value, $label ) use ( $name, $stored_value ) {
                            return sprintf( '<option value="%1$s"%3$s>%2$s</option>', $value, $label, $value == $stored_value ? ' selected' : '' );
                        }, array_keys( $options ), array_values( $options ) );
                        printf( '<select name="sitecare_active_staging[%1$s]" id="%2$s">%3$s</select>', $name, $id, implode( '', $options ) );
                        break;

                    case 'radio':
                        foreach ( $options as $value => $label ) {
                            printf( '<div><label for="%2$s_%3$s"><input name="sitecare_active_staging[%1$s]" type="radio" id="%2$s_%3$s" value="%4$s"%5$s>%4$s</label></div>', $name, $id, $value, $label, $value == $stored_value ? ' checked' : '' );
                        }
                        break;

                    case 'checkbox':
                        printf( '<input name="sitecare_active_staging[%1$s]" type="checkbox" id="%2$s" value="true" %3$s>', $name, $id, 'true' == $stored_value ?? 'false' ? ' checked' : '' );
                        break;

                    default:
                        printf( '<input name="sitecare_active_staging[%1$s]" type="%2$s" id="%3$s" value="%4$s" class="regular-text">', $name, $type, $id, $stored_value ?? '' );
                        break;

                }

            },
            $page,
            $section_id,
            array(
                'name' => $setting_name,
                'label' => $setting_config['label'],
                'type' => $setting_config['type'],
                'options' => $setting_config['options'] ?? array(),
                'default' => $setting_config['default'] ?? '',
                'id' => $setting_id,
            )
        );

    }

} );

// Adds the active staging alert options page but only if the user is a SiteCare team member
add_action( 'admin_menu', function () {

    if ( ! sitecare_must_use_is_personnel() ) {
        return;
    }

    add_options_page(
        __( 'Active Staging Notice Settings', 'sitecare' ),
        'Staging',
        'manage_options',
        'sitecare_active_staging',
        function () {

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            if ( isset( $_GET['settings-updated'] ) ) {
                add_settings_error( 'sitecare_active_staging_messages', 'sitecare_active_staging_message', __( 'Settings saved.', 'sitecare' ), 'updated' );
            }

            settings_errors();

            ?>
            <div class="wrap">
                <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <form action="options.php" method="post">
                    <?php

                    settings_fields( 'sitecare_active_staging' );

                    foreach ( SITECARE_MUST_USE_ACTIVE_STAGING_SETTINGS as $setting_name => $setting_config ) {
                        if ( $setting_config['sensitive'] ?? false ) {
                            printf( '<input name="sitecare_active_staging[%1$s]" type="hidden" id="sitecare_active_staging_field_%1$s" value="%2$s">', $setting_name, SITECARE_MUST_USE_ACTIVE_STAGING_OPTIONS[ $setting_name ] ?? '' );
                        }
                    }

                    do_settings_sections( 'sitecare_active_staging' );

                    submit_button( 'Save Settings' );

                    ?>
                </form>
            </div>
            <?php

        }
    );

} );

// Adds a start date to the active staging alert if omitted
add_filter( 'pre_update_option', function ( $value, $option ) {

    if ( 'sitecare_active_staging' == $option ) {

        $enabled = $value['enabled'] ?? 'false';
        $start = $value['start'] ?? '';

        if ( 'true' == $enabled && empty( $start ) ) {
            $value['start'] = date( 'Y-m-d' );
        }

    }

    return $value;

}, 10, 2 );

// Displays the active staging alert notice banner
add_action( 'all_admin_notices', function () {

    if ( ! SITECARE_MUST_USE_ACTIVE_STAGING_ENABLED ) {
        return;
    }

    $heading = __( 'Active Staging', 'sitecare' );

    $options = SITECARE_MUST_USE_ACTIVE_STAGING_OPTIONS;

    $name = $options['name'] ?? false;
    $organization = $options['organization'] ?? false;
    $email = $options['email'] ?? false;
    $phone = $options['phone'] ?? false;
    $slack = $options['slack'] ?? false;
    $task = $options['task'] ?? false;
    $internal = $options['internal'] ?? false;
    $external = $options['external'] ?? false;
    $notes = $options['notes'] ?? false;
    $start = $options['start'] ?? false;
    $end = $options['end'] ?? false;

    $avatar = $email ? get_avatar( $email ) : false;

    if ( ! sitecare_must_use_is_personnel() ) {
        $task = false;
        $internal = false;
        $external = false;
    }

    $message_opening = __( 'This site is being actively staged.' );

    if ( $name && $organization ) {
        $message_opening = sprintf( __( 'This site is being actively staged by %1$s from %2$s.' ), esc_html( $name ), esc_html( $organization ) );
    } else if ( $name || $organization ) {
        $message_opening = sprintf( __( 'This site is being actively staged by %1$s.' ), esc_html( $name ) ?: esc_html( $organization ) );
    }

    $message_related = '';
    $message_dates = '';
    $message_contact = '';

    $related_options = array();

    if ( $task ) {
        $related_options[] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $task ), __( 'this ClickUp task', 'sitecare' ) );
    }

    if ( $internal ) {
        $related_options[] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $internal ), __( 'this Slack thread', 'sitecare' ) );
    }

    if ( $external ) {
        $related_options[] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $external ), __( 'this Front thread', 'sitecare' ) );
    }

    if ( count( $related_options ) ) {

        switch ( count( $related_options ) ) {

            case 3:
                $message_related = sprintf( __( 'This is in relation to %1$s, %2$s, and %3$s.' ), ...$related_options );
                break;

            case 2:
                $message_related = sprintf( __( 'This is in relation to %1$s, and %2$s.' ), ...$related_options );
                break;

            case 1:
                $message_related = sprintf( __( 'This is in relation to %1$s.' ), ...$related_options );
                break;

            default:
                break;

        }

    }

    try {

        $timezone = new DateTimeZone( 'America/New_York' );
        $now_date = new DateTime( 'now', $timezone );
        $date_format = get_option( 'date_format' ) ?: 'F j, Y';

        if ( $start && $end ) {

            $start_date = new DateTime( $start, $timezone );
            $end_date = new DateTime( $end, $timezone );

            $format = $start_date <= $now_date ? _x( 'Staging started on %1$s', 'start past both', 'sitecare' ) : _x( 'Staging will start on %1$s', 'start future both', 'sitecare' );
            $format .= $end_date <= $now_date ? _x( ', and ended on %2$s.', 'end past both', 'sitecare' ) : _x( ', and will end on %2$s.', 'end future both', 'sitecare' );

            $message_dates = sprintf( $format, $start_date->format( $date_format ), $end_date->format( $date_format ) );

        }

        if ( $start && ! $end ) {

            $start_date = new DateTime( $start, $timezone );

            $format = $start_date <= $now_date ? _x( 'Staging started on %1$s.', 'start past', 'sitecare' ) : _x( 'Staging will start on %1$s.', 'start future', 'sitecare' );

            $message_dates = sprintf( $format, $start_date->format( $date_format ) );

        }

        if ( $end && ! $start ) {

            $end_date = new DateTime( $end, $timezone );

            $format = $end_date <= $now_date ? _x( 'Staging ended on %1$s.', 'end past', 'sitecare' ) : _x( 'Staging will end on %1$s.', 'end future', 'sitecare' );

            $message_dates = sprintf( $format, $end_date->format( $date_format ) );

        }

    } catch ( Exception $e ) {
        trigger_error( $e->getMessage() );
    }

    $contact_options = array();

    if ( $email ) {
        $format = $name ? _x( 'at <a href="mailto:%1$s">%2$s</a>', 'at email', 'sitecare' ) : _x( 'email <a href="mailto:%1$s">%1$s</a>', 'send email', 'sitecare' );
        $contact_options[] = sprintf( $format, esc_attr( $email ), esc_html( $email ) );
    }

    if ( $phone ) {
        $format = $name ? _x( 'on <a href="tel:%1$s">%2$s</a>', 'on phone', 'sitecare' ) : _x( 'phone <a href="tel:%1$s">%1$s</a>', 'call phone', 'sitecare' );
        $contact_options[] = sprintf( $format, esc_attr( $phone ), esc_html( $phone ) );
    }

    if ( $slack ) {
        $format = $name ? _x( 'on <a href="%1$s">Slack</a>', 'on Slack', 'sitecare' ) : _x( 'use <a href="%1$s">Slack</a>', 'use Slack', 'sitecare' );
        $contact_options[] = sprintf( $format, esc_url( $slack ) );
    }

    if ( count( $contact_options ) ) {

        $contact_options = implode( __( ' or ', 'sitecare' ), $contact_options );

        $format = $name ? __( 'Please contact %1$s %2$s for queries.', 'sitecare' ) : __( 'Please %1$s for queries.', 'sitecare' );
        $values = $name ? array( esc_html( $name ), $contact_options ) : array( $contact_options );

        $message_contact = sprintf( $format, ...$values );

    }

    ?>
    <div id="sitecare_active_staging">
        <div id="sitecare_active_staging_icon">
            <svg viewBox="0 0 96 96" width="96" height="96" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg">
                <g transform="matrix(6,0,0,6,-12,-12)">
                    <path fill="currentColor" d="m 10,2 c 4.42,0 8,3.58 8,8 0,4.42 -3.58,8 -8,8 C 5.58,18 2,14.42 2,10 2,5.58 5.58,2 10,2 Z m 1.13,9.38 0.35,-6.46 H 8.52 l 0.35,6.46 z m -0.09,3.36 c 0.24,-0.23 0.37,-0.55 0.37,-0.96 0,-0.42 -0.12,-0.74 -0.36,-0.97 -0.24,-0.23 -0.59,-0.35 -1.06,-0.35 -0.47,0 -0.82,0.12 -1.07,0.35 -0.25,0.23 -0.37,0.55 -0.37,0.97 0,0.41 0.13,0.73 0.38,0.96 0.26,0.23 0.61,0.34 1.06,0.34 0.45,0 0.8,-0.11 1.05,-0.34 z"/>
                </g>
            </svg>
        </div>
        <div id="sitecare_active_staging_message">
            <h1><?php echo $heading; ?></h1>
            <p><?php echo implode( ' ', array( $message_opening, $message_related, $message_dates, $message_contact ) ); ?></p>
            <?php echo $notes ? sprintf( '<p>%s</p>', esc_html( $notes ) ) : ''; ?>
        </div>
        <div id="sitecare_active_staging_avatar">
            <?php echo $avatar ?: ''; ?>
        </div>
    </div>
    <?php

} );

// Adds the active staging alert banner admin styling
add_action( 'admin_head', function () {

    if ( ! SITECARE_MUST_USE_ACTIVE_STAGING_ENABLED ) {
        return;
    }

    ?>
    <style>
        #sitecare_active_staging {
            box-sizing: border-box;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            column-gap: 20px;
            row-gap: 0;
            width: calc(100% + 20px);
            min-height: calc(96px + 20px + 20px);
            margin: 0 0 0 -20px;
            padding: 20px;
            color: #fff;
            background-color: <?php echo esc_html( SITECARE_MUST_USE_ACTIVE_STAGING_OPTIONS['color'] ?? '#d63638' ); ?>;
        }

        #sitecare_active_staging_icon {
            flex-basis: 96px;
            flex-shrink: 1;
            flex-grow: 0;
        }

        #sitecare_active_staging_icon svg {
            width: 96px;
            height: 96px;
        }

        #sitecare_active_staging_message {
            display: flex;
            flex-direction: column;
            align-self: center;
            flex-basis: calc(100% - 96px - 96px - 20px - 20px);
            flex-grow: 1;
            flex-shrink: 0;
        }

        #sitecare_active_staging_message h1 {
            margin: 0 0 16px 0;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-weight: 700;
            font-size: 26px;
            line-height: 25px;
            letter-spacing: normal;
            color: #fff;
        }

        #sitecare_active_staging_message p {
            margin: 0 0 16px 0;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-weight: 400;
            font-size: 16px;
            line-height: 24px;
            color: #fff;
        }

        #sitecare_active_staging_message a {
            color: #fff;
        }

        #sitecare_active_staging_message *:last-child {
            margin-bottom: 0;
        }

        #sitecare_active_staging_avatar {
            flex-basis: 96px;
            flex-shrink: 1;
            flex-grow: 0;
        }

        #sitecare_active_staging_avatar img {
            box-sizing: border-box;
            width: 96px;
            height: 96px;
            border: solid 6px #fff;
            border-radius: 50%;
        }
    </style>
    <?php

} );
