<?php

namespace WooHub;

use WooHub\Admin\WooHubOptions;
use WP_User;
use SevenShores\Hubspot\Factory as Hub;


class WooHub
{
    public static function init()
    {
        // If a hubspot api key has been added
        if (get_option('woohub_hubspot_api_key')) {
            $instance = new WooHub();

            add_action('user_register', [$instance, 'user_register_action'], 10, 1);
            add_action('wp_login', [$instance, 'wp_login_action'], 10, 2);
            add_action('woocommerce_after_edit_account_form', [$instance, 'disable_edit_email_address']);
            add_action('woocommerce_save_account_details_errors', [$instance, 'prevent_user_update_email'], 10, 2);
            add_action('woocommerce_save_account_details', [$instance, 'action_woocommerce_save_account_details'], 10, 1);
            add_action('woocommerce_customer_save_address', [$instance, 'action_woocommerce_customer_save_address'], 10, 2);
        }

        // Admin Page
        WooHubOptions::init();
    }

    /**
     * Action to perform after a user has registered.
     */
    public function user_register_action($user_id)
    {
        $user = get_user_by('ID', $user_id);

        $result = $this->createOrUpdateHubspot($user);
    }

    /**
     * Action to perform after a user has logged in.
     */
    public function wp_login_action($user_login, WP_User $user)
    {
        $result = $this->createOrUpdateHubspot($user);
    }

    public function action_woocommerce_save_account_details($user_id)
    {
        $user = get_user_by('ID', $user_id);

        $result = $this->createOrUpdateHubspot($user);
    }

    public function action_woocommerce_customer_save_address($user_id, $load_address)
    {
        if ($load_address !== 'billing') return;

        $user = get_user_by('ID', $user_id);

        $result = $this->createOrUpdateHubspot($user);
    }

    /**
     * Create or Update Hubspot User
     */
    public function createOrUpdateHubspot(WP_User $user)
    {
        $hub = Hub::create(get_option('woohub_hubspot_api_key'));

        $meta = get_user_meta($user->ID);

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
                'value' => $this->meta_value('billing_company', $meta)
            ],
            [
                'property' => 'phone',
                'value' => $this->meta_value('billing_phone', $meta)
            ],
            [
                'property' => 'address',
                'value' => $this->meta_value('billing_address_1', $meta)
            ],
            [
                'property' => 'city',
                'value' => $this->meta_value('billing_city', $meta)
            ],
            [
                'property' => 'state',
                'value' => $this->meta_value('billing_state', $meta)
            ],
            [
                'property' => 'zip',
                'value' => $this->meta_value('billing_postcode', $meta)
            ],
            [
                'property' => 'country',
                'value' => $this->meta_value('billing_country', $meta)
            ],

        ];

        // Apply filter to properties allowing people to customize details that are pushed to hubspot
        $properties = apply_filters( 'woohub_hubspot_contact_properties', $base_properties, $user );

        $response = $hub->contacts()->createOrUpdate($user->user_email, $properties);


        return ($response->data && $response->data->vid) ? $response->data->vid : false;
    }

    public function meta_value($key, $meta)
    {
        return $meta[$key] && $meta[$key][0] ? $meta[$key][0] : '';
    }

    /**
     * Auto disable field for editing email address
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

    public function prevent_user_update_email(&$error, &$user)
    {
        $current_user = get_user_by('id', $user->ID);
        $current_email = $current_user->user_email;
        if ($current_email !== $user->user_email) {
            $error->add('error', 'E-mail cannot be updated.');
        }
    }
}
