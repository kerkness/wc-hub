<?php

/**
 * Main plugin file.
 *
 * @package     Woohub
 * @author      Ryan Mayberry (@kerkness)
 * @license     MIT
 *
 * @wordpress-plugin
 * Plugin Name: Woohub
 * Description: WooCommerce & HubSpot Integration
 * Version:     0.1.2
 * Author:      Ryan Mayberry (@kerkness)
 * Author URI:  https://kerkness.ca
 * Text Domain: woohub
 * Domain Path: /languages
 */

//  Exit if accessed directly.
defined('ABSPATH') || exit;

// Include autoloader if plugin isn't running as a dependency
if (!class_exists('WooHub\WooHub')) {
    require_once( __DIR__ . '/lib/autoload.php');
}

/**
 * Gets this plugin's absolute directory path.
 *
 */
function _get_woohub_plugin_directory() {
	return __DIR__;
}

/**
 * Gets this plugin's URL.
 */
function _get_woohub_plugin_url() {
	static $plugin_url;

	if ( empty( $plugin_url ) ) {
		$plugin_url = plugins_url( null, __FILE__ );
	}

	return $plugin_url;
}

/**
 * Debug method. Writes to plugin log file.
 * Do not use in production
 *
 */
function _hublog( $log )
{
    if (is_array($log) || is_object($log)) {
	    $log =	print_r($log, true);
	} 

    error_log($log . "\n", 3, _get_woohub_plugin_directory().'/debug.log');
}

/**
 * Initalize the plugin
 */
\WooHub\WooHub::init();

/**
 * Create or update a HubSpot contact from WP_User object
 */
function woohub_create_or_update_hubspot_contact ( WP_User $user ) {
    \WooHub\WooHub::createOrUpdateHubspot( $user );
}
