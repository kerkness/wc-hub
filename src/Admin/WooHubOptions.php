<?php

namespace WooHub\Admin;

class WooHubOptions
{
    public static function init()
    {
        $instance = new WooHubOptions();

        add_action('init', [$instance, 'woohub_register_settings']);
        add_action('admin_menu', [$instance, 'woohub_admin_settings_menu']);

    }

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

    public function woohub_admin_settings_menu()
    {
        add_options_page('WooHub', 'WooHub', 'manage_options', 'woohub-options-page', [$this, 'woohub_options_page']);
    }

    public function woohub_options_page()
    {
?>
        <div class="wrap">
            <h1><?php echo __('WooHub Settings', 'woohub') ?></h1>

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
        </div>
<?php
    }
}
