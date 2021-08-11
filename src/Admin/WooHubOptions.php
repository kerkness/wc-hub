<?php

namespace WooHub\Admin;


/**
 * WooHub Admin Options
 * Create admin page and handle option updates
 */
class WooHubOptions
{
    /**
     * Initalize the plugin
     */
    public static function init()
    {
        $instance = new WooHubOptions();

        add_action('init', [$instance, 'woohub_register_settings'], 10, 0);
        add_action('admin_menu', [$instance, 'woohub_admin_settings_menu'], 10, 0);
        add_filter('plugin_action_links_' . _get_woohub_basename(), [$instance, 'woohub_settings_link'], 10, 1);
        add_filter('plugin_row_meta', [$instance, 'plugin_row_meta'], 10, 2);
    }

    /**
     * Add a settings link to the plugin listing
     */
    public function woohub_settings_link($links)
    {
        // Build and escape the URL.
        $url = esc_url(add_query_arg(
            'page',
            'woohub-options-page',
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
        if (strpos($file, 'woohub.php') !== false) {
            $new_links = [
                '<a href="https://github.com/kerkness/woohub" target="_blank">GitHub</a>',
                '<a href="https://github.com/kerkness/woohub/issues" target="_blank">Support</a>',
            ];

            $links = array_merge($links, $new_links);
        }

        return $links;
    }

    /**
     * Register Wordpress Options
     */
    public function woohub_register_settings()
    {
        register_setting(
            'woohub_settings',
            'woohub_hubspot_api_key',
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
    public function woohub_admin_settings_menu()
    {
        add_options_page('WooHub', 'WooHub', 'manage_options', 'woohub-options-page', [$this, 'woohub_options_page']);
    }

    /**
     * Render Options Page
     */
    public function woohub_options_page()
    {
?>
        <div class="wrap">
            <h1><?php echo __('WooHub Settings', 'woohub') ?></h1>

            <p><?php echo sprintf(__('To use this plugin you need to <a target="_blank" href="%s">create an HubSpot API key</a> and then enter the key below.', 'woohub'), 'https://knowledge.hubspot.com/integrations/how-do-i-get-my-hubspot-api-key') ?></p>

            <form method="post" action="options.php">
                <?php settings_fields('woohub_settings'); ?>
                <?php do_settings_sections('woohub_settings'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo __('HubSpot API Key', 'woohub') ?></th>
                        <td><input type="text" name="woohub_hubspot_api_key" value="<?php echo esc_attr(get_option('woohub_hubspot_api_key')); ?>" /></td>
                    </tr>

                </table>

                <?php submit_button(); ?>

            </form>

            <p><?php echo sprintf(__('Visit the <a target="_blank" href="%s">Github repository</a> to contribute to this plugin or submit any issues.', 'woohub'), 'https://github.com/kerkness/woohub') ?></p>
        </div>
<?php
    }
}
