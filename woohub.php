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
 * Version:     1.0.0
 * Author:      Ryan Mayberry (@kerkness)
 * Author URI:  https://kerkness.ca
 * Text Domain: woohub
 * Domain Path: /languages
 */

//  Exit if accessed directly.
defined('ABSPATH') || exit;

// autoload our composer installed dependencies.
// require __DIR__ . '/lib/autoload.php';

// If we haven't loaded this plugin from Composer we need to add our own autoloader
if (!class_exists('WooHub\WooHub')) {
    // Get a reference to our PSR-4 Autoloader function that we can use to add our
    // Acme namespace
    require_once( __DIR__ . '/lib/autoload.php');

}

/**
 * Gets this plugin's absolute directory path.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @return string
 */
function _get_woohub_plugin_directory() {
	return __DIR__;
}

/**
 * Gets this plugin's URL.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @return string
 */

function _get_woohub_plugin_url() {
	static $plugin_url;

	if ( empty( $plugin_url ) ) {
		$plugin_url = plugins_url( null, __FILE__ );
	}

	return $plugin_url;
}

/**
 * Development debug log. remove from prod
 * 
 */
function _hublog( $log )
{
    if (is_array($log) || is_object($log)) {
	    $log =	print_r($log, true);
	} 

    error_log($log . "\n", 3, _get_woohub_plugin_directory().'/debug.log');
}


\WooHub\WooHub::init();

function woohub_create_or_update_hubspot_contact ( WP_User $user ) {
    \WooHub\WooHub::createOrUpdateHubspot( $user );
}
