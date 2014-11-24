<?php
class Smsglobal_SettingsPage
{

    public function __construct()
    {
        if (is_admin()) {
            add_action('admin_menu', array($this, 'addSettingsPage'));
            add_action('admin_init', array($this, 'pageInit'));
        }
    }

    public function addSettingsPage()
    {
        $title = __('SMSGlobal', SMSGLOBAL_TEXT_DOMAIN);
        add_options_page($title, $title, 'manage_options',
            'smsglobal-settings', array($this, 'createAdminPage'));
    }

    public function createAdminPage()
    {
        ?>
        <div class="wrap">
            <h2><?php _e('SMSGlobal Settings', SMSGLOBAL_TEXT_DOMAIN) ?></h2>

            <div id="smsglobal-api-key-instructions">
                <h3><?php _e('How to Get Your API Key', SMSGLOBAL_TEXT_DOMAIN) ?></h3>
                <ol>
                    <li><?php _e('Get an SMSGlobal MXT account', SMSGLOBAL_TEXT_DOMAIN) ?></li>
                    <li><?php _e('Login to MXT', SMSGLOBAL_TEXT_DOMAIN) ?></li>
                    <li><?php _e('In the main menu, go to Tools', SMSGLOBAL_TEXT_DOMAIN) ?></li>
                    <li><?php _e('In the sub menu, go to API Keys', SMSGLOBAL_TEXT_DOMAIN) ?></li>
                    <li><?php _e('Create a new API Key. Give it a name like "Wordpress"', SMSGLOBAL_TEXT_DOMAIN) ?></li>
                    <li><?php _e('Copy the key and secret here', SMSGLOBAL_TEXT_DOMAIN) ?></li>
                </ol>
            </div>

            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('smsglobal_option_group');
                do_settings_sections('smsglobal');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function pageInit()
    {
        new Smsglobal_Settings_ApiKeys();
        new Smsglobal_Settings_Authentication();
        new Smsglobal_Settings_PostAlert();
        new Smsglobal_Settings_Shopp();
        new Smsglobal_Settings_WPecommerce();
    }

}
