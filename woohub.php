<?php

/**
 * Main plugin file.
 *
 * @package     Woohub
 * @author      Ryan Mayberry (@kerkness)
 * @license     GNU
 *
 * @wordpress-plugin
 * Plugin Name: Woohub
 * Plugin URI:  https://kerkness.ca/woohub
 * Description: WooCommerce & HubSpot Integration
 * Version:     1.0.0
 * Author:      Ryan Mayberry (@kerkness)
 * Author URI:  https://kerkness.ca
 * Text Domain: woohub
 * Domain Path: /languages
 * Requires at least: 5.4
 * Tested up to: 5.8
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html 
 */


//  Exit if accessed directly.
defined('ABSPATH') || exit;

// Include autoloader if plugin isn't running as a dependency
if (!class_exists('WooHub\WooHub')) {
    require_once( __DIR__ . '/lib/autoload.php');
}

use WooHub\WooHub;

/**
 * Gets this plugin's absolute directory path.
 *
 */
if(!function_exists('_get_woohub_plugin_directory')){
	function _get_woohub_plugin_directory() {
		return __DIR__;
	}	
}

/**
 * Gets this plugin's URL.
 */
if (!function_exists('_get_woohub_plugin_url')){
	function _get_woohub_plugin_url() {
		static $plugin_url;
	
		if ( empty( $plugin_url ) ) {
			$plugin_url = plugins_url( null, __FILE__ );
		}
	
		return $plugin_url;
	}	
}

/**
 * Get plugin base name
 */
if(!function_exists('_get_woohub_basename')) {
	function _get_woohub_basename() {
		return plugin_basename( __FILE__ );
	}
}

/**
 * Initalize the plugin
 */
WooHub::init();

/**
 * Create or update a HubSpot contact from WP_User object
 */
if(!function_exists('woohub_create_or_update_hubspot_contact')) {
	function woohub_create_or_update_hubspot_contact ( WP_User $user ) {
		WooHub::createOrUpdateHubspot( $user );
	}	
}

/**
 * Update specific values for a hubspot contact
 */
if(!function_exists('woohub_update_hubspot_contact')){
	function woohub_update_hubspot_contact( $email, $properties = []) {
		return WooHub::updateHubSpotContact($email, $properties);
	}	
}

/**
 * Get the current user's hubspot profile
 */
if(!function_exists('woohub_get_current_contact')){
	function woohub_get_current_contact() {
		return woohub_get_hubspot_contact(wp_get_current_user());
	}	
}

/**
 * Get a specific user's hubspot profile
 * Applies filter woohub_hubspot_get_contact_parameters
 */
if(!function_exists('woohub_get_hubspot_contact')){
	function woohub_get_hubspot_contact( WP_User $user ) {
		return WooHub::getHubSpotContact($user);
	}	
}
