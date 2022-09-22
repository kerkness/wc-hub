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
 * Version:     1.1.1
 * Requires at least: 5.4
 * Tested up to: 6.0.2
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

/**
 * Debug methods for dev. 
 */
if(!function_exists('moi_debug')) {
	function moi_debug($log) {
		if (WP_DEBUG === true) { 
			if (is_array($log) || is_object($log)) {
				error_log(print_r($log, true));
			} else {
				error_log($log);
			}	
		}
	}	
}
if(!function_exists('moi_dump')) {
	function moi_dump($args) {
		if(WP_DEBUG_DISPLAY) {
			if (is_array($args) || is_object($args)) {
				return '<pre>' . print_r($args, true) . '</pre>';
			} else {
				return '<pre>' . $args . '</pre>';
			}	
		}
	}	
}

/**
 * Get plugin base name
 */
if(!function_exists('wc_hub_integrator_plugin_basename')) {
	function wc_hub_integrator_plugin_basename() {
		return plugin_basename( __FILE__ );
	}
}

/**
 * Initalize the plugin
 */
\WCHub\WCHub::init();

/**
 * Create or update a HubSpot contact from WP_User object
 */
if(!function_exists('wc_hub_create_or_update_hubspot_contact')) {
	function wc_hub_create_or_update_hubspot_contact ( WP_User $user ) {
		\WCHub\WCHub::createOrUpdateHubspot( $user );
	}	
}

/**
 * Update specific values for a hubspot contact
 */
if(!function_exists('wc_hub_update_hubspot_contact')){
	function wc_hub_update_hubspot_contact( $email, $properties = []) {
		return \WCHub\WCHub::updateHubSpotContact($email, $properties);
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
		return \WCHub\WCHub::getHubSpotContact($user);
	}	
}
