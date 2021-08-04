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
        if ( get_option('woohub_hubspot_api_key') ) {
            $instance = new WooHub();

            add_action('user_register', [$instance, 'user_register_action'], 10, 1);
            add_action('wp_login', [$instance, 'wp_login_action'], 10, 2);
            add_action( 'woocommerce_after_edit_account_form', [$instance, 'disable_edit_email_address'] );
            add_action( 'woocommerce_save_account_details_errors', [$instance, 'prevent_user_update_email'], 10, 2 );
            add_action( 'woocommerce_save_account_details', [$instance, 'action_woocommerce_save_account_details'], 10, 1 ); 

        }

        // Admin Page
        WooHubOptions::init();
    }

    /**
     * Action to perform after a user has registered.
     */
    public function user_register_action($user_id)
    {
        // New user registered with USER ID
        _hublog("Someone registered");
        _hublog($user_id);

        $user = get_user_by('ID', $user_id);
        _hublog($user);

    }

    /**
     * Action to perform after a user has logged in.
     */
    public function wp_login_action($user_login, WP_User $user)
    {
        //
        _hublog("Someone logged in");
        _hublog($user_login);
        _hublog($user->user_email);
        _hublog([
            $user->first_name,
            $user->last_name,
        ]);
        _hublog($user->ID);
        _hublog($user->user_login);

        _hublog($user);

    }

    public function action_woocommerce_save_account_details( $user_id ) { 
        // make action magic happen here... 

                // New user registered with USER ID
        _hublog("Someone updated their account details");
        _hublog($user_id);

        $user = get_user_by('ID', $user_id);
        _hublog($user);
        _hublog($user->user_email);
        _hublog([
            $user->first_name,
            $user->last_name,
        ]);

    }

    /**
     * Create or Update Hubspot User
     */
    public function createOrUpdateHubspot( WP_User $user )
    {
        $hub = Hub::create(get_option('woohub_hubspot_api_key'));

        $properties = [
            [
                'property' => 'firstname',
                'value' => $user->FirstName
            ],
            [
                'property' => 'lastname',
                'value' => $user->LastName
            ],
            [
                'property' => 'company',
                'value' => $user->Company
            ],
            [
                'property' => 'phone',
                'value' => $user->PhoneNumber
            ],
            [
                'property' => 'address',
                'value' => $user->AddressStreet
            ],
            [
                'property' => 'city',
                'value' => $user->AddressCity
            ],
            [
                'property' => 'state',
                'value' => $user->AddressProvince
            ],
            [
                'property' => 'zip',
                'value' => $user->AddressPostal
            ],
            [
                'property' => 'country',
                'value' => $user->AddressCountry
            ],
            [
                'property' => 'website_paddle_number',
                'value' => $user->id
            ],
            [
                'property' => 'website_username',
                'value' => $user->username
            ],

        ];

        // 
        $response = $hub->contacts()->createOrUpdate( $user->user_email, $properties );


        return ($response->data && $response->data->vid) ? $response->data->vid : false;


    }

    /**
     * Auto disable field for editing email address
     */
    public function disable_edit_email_address($user) {
        $script = '<script type="text/javascript">'.
            'var account_email = document.getElementById("account_email");'.
            'if(account_email) { '.
            '     account_email.readOnly = true; '.
            '     account_email.className += " disable-input";'.
            '}'.
        '</script>';
        echo $script;
    }

    public function prevent_user_update_email( &$error, &$user ){
        $current_user = get_user_by( 'id', $user->ID );
        $current_email = $current_user->user_email;
        if( $current_email !== $user->user_email){
            $error->add( 'error', 'E-mail cannot be updated.');
        }
    }

}