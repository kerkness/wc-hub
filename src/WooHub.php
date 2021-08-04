<?php

namespace WooHub;

use WooHub\Admin\WooHubOptions;
use WP_User;
use SevenShores\Hubspot\Factory as Hub;

/**
 * WooHub 
 * Handle updating contact details in HubSpot
 */
class WooHub
{
    /**
     * Initialize Plugin 
     */
    public static function init()
    {
        // If a hubspot api key has been added then register actions
        if (get_option('woohub_hubspot_api_key')) {
            $instance = new WooHub();

            add_action('user_register', [$instance, 'user_register_action'], 10, 1);
            add_action('wp_login', [$instance, 'wp_login_action'], 10, 2);
            add_action('woocommerce_after_edit_account_form', [$instance, 'disable_edit_email_address']);
            add_action('woocommerce_save_account_details_errors', [$instance, 'prevent_user_update_email'], 10, 2);
            add_action('woocommerce_save_account_details', [$instance, 'action_woocommerce_save_account_details'], 10, 1);
            add_action('woocommerce_customer_save_address', [$instance, 'action_woocommerce_customer_save_address'], 10, 2);
        }

        // Initialize Admin Page
        WooHubOptions::init();

        return $instance;
    }

    /**
     * Create or update Hubspot Contact when user registers
     */
    public function user_register_action($user_id)
    {
        $user = get_user_by('ID', $user_id);

        $result = $this->createOrUpdate($user);
    }

    /**
     * Create or update Hubspot Contact when user logs in
     */
    public function wp_login_action($user_login, WP_User $user)
    {
        $result = $this->createOrUpdate($user);
    }

    /**
     * Create or update Hubspot Contact when accout details are saved
     */
    public function action_woocommerce_save_account_details($user_id)
    {
        $user = get_user_by('ID', $user_id);

        $result = $this->createOrUpdate($user);
    }

    /**
     * Create or update Hubspot Contact when billing address is saved
     */
    public function action_woocommerce_customer_save_address($user_id, $load_address)
    {
        if ($load_address !== 'billing') return;

        $user = get_user_by('ID', $user_id);

        $result = $this->createOrUpdate($user);
    }

    /**
     * Class method to Create or Update Hubspot User
     */
    public function createOrUpdate(WP_User $user) {
        WooHub::createOrUpdateHubspot($user);
    }

    /**
     * Static Method to Create or Update Hubspot User
     */
    public static function createOrUpdateHubspot(WP_User $user)
    {
        // Create HubSpot Client
        $hub = Hub::create(get_option('woohub_hubspot_api_key'));

        // Get all the user meta data
        $meta = get_user_meta($user->ID);

        // Build default HubSpot properties
        $base_properties = [
            [
                'property' => 'firstname',
                'value' => $user->first_name
            ],
            [
                'property' => 'lastname',
                'value' => $user->last_name
            ],
            [
                'property' => 'company',
                'value' => WooHub::meta_value('billing_company', $meta)
            ],
            [
                'property' => 'phone',
                'value' => WooHub::meta_value('billing_phone', $meta)
            ],
            [
                'property' => 'address',
                'value' => WooHub::meta_value('billing_address_1', $meta)
            ],
            [
                'property' => 'city',
                'value' => WooHub::meta_value('billing_city', $meta)
            ],
            [
                'property' => 'state',
                'value' => WooHub::meta_value('billing_state', $meta)
            ],
            [
                'property' => 'zip',
                'value' => WooHub::meta_value('billing_postcode', $meta)
            ],
            [
                'property' => 'country',
                'value' => WooHub::meta_value('billing_country', $meta)
            ],

        ];

        // Apply filter to properties allowing people to customize details that are pushed to hubspot
        $properties = apply_filters( 'woohub_hubspot_contact_properties', $base_properties, $user );

        // Call the HubSpot API
        $response = $hub->contacts()->createOrUpdate($user->user_email, $properties);

        // If Successful call action with HubSpot ID and WP_User object
        if ($response->data && $response->data->vid) {
            do_action('woohub_hubspot_contact_updated', $response->data->vid, $user);
            
            return $response->data->vid;
        }

        // IF failed return false.
        return false;
    }

    /**
     * Helper function for pulling meta value from wp get_user_meta() function
     */
    public static function meta_value($key, $meta)
    {
        return $meta[$key] && $meta[$key][0] ? $meta[$key][0] : '';
    }

    /**
     * Auto disable fields for editing email address
     */
    public function disable_edit_email_address($user)
    {
        $script = '<script type="text/javascript">' .
            'var account_email = document.getElementById("account_email");' .
            'if(account_email) { ' .
            '     account_email.readOnly = true; ' .
            '     account_email.className += " disable-input";' .
            '}' .
            '</script>';
        echo $script;
    }

    /**
     * Prevent user from updating email address
     */
    public function prevent_user_update_email(&$error, &$user)
    {
        $current_user = get_user_by('id', $user->ID);
        $current_email = $current_user->user_email;
        if ($current_email !== $user->user_email) {
            $error->add('error', 'E-mail cannot be updated.');
        }
    }
}
