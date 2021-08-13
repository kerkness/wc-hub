<?php

/**
 * Main plugin file.
 *
 * @package     WCHub
 * @author      Kerkness
 * @license     GNU
 *
 * @wordpress-plugin
 * Plugin Name: Integrator For HubSpot
 * Plugin URI:  https://kerkness.ca/wc-hub
 * Description: Automatically creates and updates HubSpot records from Woocommerce or WordPress events
 * Version:     1.0.0
 * Requires at least: 5.4
 * Tested up to: 5.8
 * Requires PHP: 7.2
 * Author:      Kerkness

 * Author URI:  https://kerkness.ca
 * Text Domain: wc-hub
 * Domain Path: /languages
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html 
 */


//  Exit if accessed directly.
defined('ABSPATH') || exit;

// Include autoloader if plugin isn't running as a dependency
if (!class_exists('WCHub\WCHub')) {
    require_once( __DIR__ . '/lib/autoload.php');
}

use WCHub\WCHub;

/**
 * Gets this plugin's absolute directory path.
 *
 */
if(!function_exists('_get_wc_hub_plugin_directory')){
	function _get_wc_hub_plugin_directory() {
		return __DIR__;
	}	
}

/**
 * Gets this plugin's URL.
 */
if (!function_exists('_get_wc_hub_plugin_url')){
	function _get_wc_hub_plugin_url() {
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
if(!function_exists('_get_wc_hub_basename')) {
	function _get_wc_hub_basename() {
		return plugin_basename( __FILE__ );
	}
}

/**
 * Initalize the plugin
 */
WCHub::init();

/**
 * Create or update a HubSpot contact from WP_User object
 */
if(!function_exists('wc_hub_create_or_update_hubspot_contact')) {
	function wc_hub_create_or_update_hubspot_contact ( WP_User $user ) {
		WCHub::createOrUpdateHubspot( $user );
	}	
}

/**
 * Update specific values for a hubspot contact
 */
if(!function_exists('wc_hub_update_hubspot_contact')){
	function wc_hub_update_hubspot_contact( $email, $properties = []) {
		return WCHub::updateHubSpotContact($email, $properties);
	}	
}

/**
 * Get the current user's hubspot profile
 */
if(!function_exists('wc_hub_get_current_contact')){
	function wc_hub_get_current_contact() {
		return wc_hub_get_hubspot_contact(wp_get_current_user());
	}	
}

/**
 * Get a specific user's hubspot profile
 * Applies filter wc_hub_hubspot_get_contact_parameters
 */
if(!function_exists('wc_hub_get_hubspot_contact')){
	function wc_hub_get_hubspot_contact( WP_User $user ) {
		return WCHub::getHubSpotContact($user);
	}	
}
