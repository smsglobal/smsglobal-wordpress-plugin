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
        $title = Smsglobal_Utils::_('SMSGlobal');
        add_options_page($title, $title, 'manage_options',
            'smsglobal-settings', array($this, 'createAdminPage'));
    }

    public function createAdminPage()
    {
        ?>
        <div class="wrap">
            <h2><?php echo Smsglobal_Utils::_('SMSGlobal Settings') ?></h2>

            <div id="smsglobal-api-key-instructions">
                <h3><?php echo Smsglobal_Utils::_('How to Get Your API Key') ?></h3>
                <ol>
                    <li><?php echo Smsglobal_Utils::_('Get an SMSGlobal MXT account') ?></li>
                    <li><?php echo Smsglobal_Utils::_('Login to MXT') ?></li>
                    <li><?php echo Smsglobal_Utils::_('In the main menu, go to Tools') ?></li>
                    <li><?php echo Smsglobal_Utils::_('In the sub menu, go to API Keys') ?></li>
                    <li><?php echo Smsglobal_Utils::_('Create a new API Key. Give it a name like "Wordpress"') ?></li>
                    <li><?php echo Smsglobal_Utils::_('Copy the key and secret here') ?></li>
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
