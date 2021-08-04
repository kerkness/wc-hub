# woohub
WooCommerce HubSpot intergration

### Dependencies

Plugin has some composer dependencies.  
If installing from git or composer run `composer install` before activating 

### Setup

Add your HubSpot API Key in `Settings > WooHub` 

### Usage

Contacts will be pushed to HubSpot when a user registers, logs in or updates their account or billing address.

### Hooks

To modify what HubSpot Contact Properties are updates use the `woohub_hubspot_contact_properties` filter.

*Example*

Assuming you created the a Contact property in HubSpot called `Wordpress Username`  You could populate this field by using the filter.

```
function handle_hubspot_contact_props_filter( $properties, $user ) {

    $properties[] = [
        'property' => 'wordpress_username',
        'value' => $user->user_login
    ];

    return $properties;
}

add_filter( 'woohub_hubspot_contact_properties', 'handle_hubspot_contact_props_filter', 10, 2 );
```