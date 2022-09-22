# Integrator For HubSpot

Basic WooCommerce/WordPress & HubSpot intergration

### Dependencies

Plugin has some composer dependencies.  
If installing from git or composer run `composer install` before activating.

### Setup

Add your HubSpot Private App Access Token in `Settings > WCHub` 

### Usage

Woocommerce/Wordpress customers will automatically be pushed to HubSpot when a user **registers, logs in or updates** their account or billing address.

To manually update a Hubspot Contact use the function `wc_hub_update_hubspot_contact($email, $properties)`

*Functions*

```
// Create or update a hubspot contact from a WP_User object
$wc_hub_contact_id = wc_hub_create_or_update_hubspot_contact( $user );

// Get an existing WCHub Contact record for current user
$contact = wc_hub_get_current_contact();

// Get an existing WCHub Contact from a WP_User object
$contact = wc_hub_get_hubspot_contact( $user );

// Update an existing wc-hub contact
$properties = [
    ['firstname' => 'Sam'], 
    ['lastname' => 'Iam'],
    ['custom_property' => 'custom value']
]
wc_hub_update_hubspot_contact( $current_user->user_email, $properties);
```

*Actions*

```
// Called when a HubSpot Contact is Created or Updated
add_action('wc_hub_hubspot_contact_updated', 'handle_wc_hub_hubspot_contact_updated', 10, 2);

function handle_wc_hub_hubspot_contact_updated( $hubspot_id, $user ) { 
    //... 
}
```

*Filters*

```
// Modify default properties when a HubSpot contact is created
add_filter( 'wc_hub_hubspot_contact_properties', 'handle_hubspot_contact_props_filter', 10, 2 );

function handle_hubspot_contact_props_filter( $properties, $user ) {

    $properties = [
        'property_name' => 'property_value',
    ];

    return $properties;
}

```

```
// Modify what HubSpot Contact Properties are returned use the `wc_hub_hubspot_get_contact_parameters` filter.
add_filter( 'wc_hub_hubspot_get_contact_parameters', 'handle_wc_hub_hubspot_get_contact_parameters', 10, 1 );

function handle_wc_hub_hubspot_get_contact_parameters( $parameters ) {
    return array_merge($parameters, [
        'custom_property_key1',
        'custom_property_key2',
    ]);
}
```
