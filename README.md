# woohub
Basic WooCommerce & HubSpot intergration

### Dependencies

Plugin has some composer dependencies.  
If installing from git or composer run `composer install` before activating 

### Setup

Add your HubSpot API Key in `Settings > WooHub` 

### Usage

Woocommerce/Wordpress users will automatically be pushed to HubSpot when a user **registers, logs in or updates** their account or billing address.

To manually update a Hubspot Contact use the function `woohub_update_hubspot_contact($email, $properties)`

*Example*

```
$current_user = wp_get_current_user();

woohub_create_or_update_hubspot_contact($current_user);
```

### Actions

To access the HubSpot contact ID or informed when the contact has been updated use the action hook `woohub_hubspot_contact_updated`

*Example*

```
add_action('woohub_hubspot_contact_updated', 'handle_woohub_hubspot_contact_updated', 10, 2);

function handle_woohub_hubspot_contact_updated( $hubspot_id, $user ) {
    
    //...

}

```

### Filters

To modify what HubSpot Contact Properties are updated use the `woohub_hubspot_contact_properties` filter.

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

Modify what HubSpot Contact Properties are returned use the `woohub_hubspot_get_contact_parameters` filter.
See https://developers.hubspot.com/docs/methods/contacts/get_contact_by_email  for more details.

*Example*

```
function handle_woohub_hubspot_get_contact_parameters( $parameters ) {

    $parameters = ['showListMemberships' => 1, 'property' => 'lastname'];

    return $parameters;
}

add_filter( 'woohub_hubspot_get_contact_parameters', 'handle_woohub_hubspot_get_contact_parameters', 10, 1 );
```
