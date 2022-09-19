<?php

namespace WCHub\Admin;


/**
 * WCHub Admin Options
 * Create admin page and handle option updates
 */
class WCHubOptions
{
    /**
     * Initalize the plugin
     */
    public static function init()
    {
        $instance = new WCHubOptions();

        register_activation_hook( wc_hub_integrator_plugin_basename(), [$instance, 'wc_hub_activation_hook'] );

        add_action('init', [$instance, 'wc_hub_register_settings'], 10, 0);
        add_action('admin_menu', [$instance, 'wc_hub_admin_settings_menu'], 10, 0);
        add_filter('plugin_action_links_' . wc_hub_integrator_plugin_basename(), [$instance, 'wc_hub_settings_link'], 10, 1);
        add_filter('plugin_row_meta', [$instance, 'plugin_row_meta'], 10, 2);
    }

    /**
     * Plugin Activation Hook
     */
    public function wc_hub_activation_hook()
    {
        register_uninstall_hook(wc_hub_integrator_plugin_basename(), [$this, 'wc_hub_deactivation_hook'] );
    }

    /**
     * Plugin deactivation hook
     */
    public function wc_hub_deactivation_hook()
    {
        delete_option( 'wc_hub_hubspot_access_token' );
        delete_site_option('wc_hub_hubspot_access_token');
    }

    /**
     * Add a settings link to the plugin listing
     */
    public function wc_hub_settings_link($links)
    {
        // Build and escape the URL.
        $url = esc_url(add_query_arg(
            'page',
            'wc-hub-options-page',
            get_admin_url() . 'admin.php'
        ));
        // Create the link.
        $settings_link = "<a href='$url'>" . __('Settings') . '</a>';
        // Adds the link to the end of the array.
        array_push(
            $links,
            $settings_link
        );
        return $links;
    }

    /**
     * Add description links to plugin description
     */
    public function plugin_row_meta($links, $file)
    {
        if (strpos($file, 'wc-hub.php') !== false) {
            $new_links = [
                '<a href="https://github.com/kerkness/wc-hub" target="_blank">GitHub</a>',
                '<a href="https://github.com/kerkness/wc-hub/issues" target="_blank">Support</a>',
            ];

            $links = array_merge($links, $new_links);
        }

        return $links;
    }

    /**
     * Register Wordpress Options
     */
    public function wc_hub_register_settings()
    {
        register_setting(
            'wc_hub_settings',
            'wc_hub_hubspot_access_token',
            array(
                'type'         => 'string',
                'show_in_rest' => false,
                'default'      => '',
            )
        );
    }

    /**
     * Add Options Page to Dashboard
     */
    public function wc_hub_admin_settings_menu()
    {
        add_options_page('WCHub', 'WCHub', 'manage_options', 'wc-hub-options-page', [$this, 'wc_hub_options_page']);
    }

    /**
     * Render Options Page
     */
    public function wc_hub_options_page()
    {
?>
        <div class="wrap">
            <h1><?php echo __('WCHub Settings', 'wc-hub') ?></h1>

            <p><?php echo sprintf(__('To use this plugin you need to create a private app with permissions to read/write contacts and <a target="_blank" href="%s">copy your private app acccess-token</a> and then enter the token below.', 'wc-hub'), 'https://developers.hubspot.com/docs/api/migrate-an-api-key-integration-to-a-private-app') ?></p>

            <form method="post" action="options.php">
                <?php settings_fields('wc_hub_settings'); ?>
                <?php do_settings_sections('wc_hub_settings'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo __('HubSpot App Access Token', 'wc-hub') ?></th>
                        <td><input type="text" name="wc_hub_hubspot_access_token" value="<?php echo esc_attr(get_option('wc_hub_hubspot_access_token')); ?>" /></td>
                    </tr>

                </table>

                <?php submit_button(); ?>

            </form>

            <p><?php echo sprintf(__('Visit the <a target="_blank" href="%s">Github repository</a> to contribute to this plugin or submit any issues.', 'wc-hub'), 'https://github.com/kerkness/wc-hub') ?></p>
        </div>
<?php
    }
}
