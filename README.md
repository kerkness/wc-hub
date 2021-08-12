# woohub
Basic WooCommerce & HubSpot intergration

### Dependencies

Plugin has some composer dependencies.  
If installing from git or composer run `composer install` before activating 

### Setup

Add your HubSpot API Key in `Settings > WooHub` 

### Usage

Woocommerce/Wordpress customers will automatically be pushed to HubSpot when a user **registers, logs in or updates** their account or billing address.

To manually update a Hubspot Contact use the function `woohub_update_hubspot_contact($email, $properties)`

*Functions*

```
// Create or update a hubspot contact from a WP_User object
$woohub_contact_id = woohub_create_or_update_hubspot_contact( $user );

// Get an existing WooHub Contact record for current user
// @see https://developers.hubspot.com/docs/methods/contacts/get_contact_by_email
$contact = woohub_get_current_contact();

// Get an existing WooHub Contact from a WP_User object
// @see https://developers.hubspot.com/docs/methods/contacts/get_contact_by_email
$contact = woohub_get_hubspot_contact( $user );

// Update an existing woohub contact
// @see https://legacydocs.hubspot.com/docs/methods/contacts/update_contact-by-email 
$properties = [
    ['property' => 'firstname', 'value' => 'Sam'], 
    ['property' => 'lastname', 'value' => 'Iam'],
    ['property' => 'custom_property', 'value' => 'custom value']
]
woohub_update_hubspot_contact( $current_user->user_email, $properties);
```

*Actions*

```
// Called when a HubSpot Contact is Created or Updated
add_action('woohub_hubspot_contact_updated', 'handle_woohub_hubspot_contact_updated', 10, 2);

function handle_woohub_hubspot_contact_updated( $hubspot_id, $user ) { 
    //... 
}
```

*Filters*

```
// Modify default properties when a HubSpot contact is created
add_filter( 'woohub_hubspot_contact_properties', 'handle_hubspot_contact_props_filter', 10, 2 );

function handle_hubspot_contact_props_filter( $properties, $user ) {

    $properties[] = [
        'property' => 'wordpress_username',
        'value' => $user->user_login
    ];

    return $properties;
}

```

```
// Modify what HubSpot Contact Properties are returned use the `woohub_hubspot_get_contact_parameters` filter.
// See https://developers.hubspot.com/docs/methods/contacts/get_contact_by_email  for more details.
add_filter( 'woohub_hubspot_get_contact_parameters', 'handle_woohub_hubspot_get_contact_parameters', 10, 1 );

function handle_woohub_hubspot_get_contact_parameters( $parameters ) {

    $parameters = ['showListMemberships' => 1, 'property' => 'lastname'];

    return $parameters;
}
```
