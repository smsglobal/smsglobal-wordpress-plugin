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
        $title = Smsglobal::_('SMSGlobal');
        add_options_page($title, $title, 'manage_options',
            'smsglobal-settings', array($this, 'createAdminPage'));
    }

    public function createAdminPage()
    {
        ?>
        <div class="wrap">
            <?php screen_icon() ?>
            <h2><?php echo Smsglobal::_('SMSGlobal Settings') ?></h2>

            <div id="smsglobal-api-key-instructions">
                <h3>How to get your API Key</h3>
                <ol>
                    <li>Get an SMSGlobal MXT account</li>
                    <li>Login to MXT</li>
                    <li>Go to Settings</li>
                    <li>Go to API Keys</li>
                    <li>Create a new API Key. Give it a name like 'Wordpress'</li>
                    <li>Copy the key and secret here</li>
                </ol>
            </div>

            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('smsglobal_option_group');
                do_settings_sections('smsglobal');
                submit_button()
                ?>
            </form>
        </div>
        <?php
    }

    public function getSectionInfo()
    {
        return;
    }

    public function pageInit()
    {
        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'saveApiKey'));

        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'saveApiSecret'));

        add_settings_section(
            'setting_section_id',
            Smsglobal::_('API Key Settings'),
            array($this, 'getSectionInfo'),
            'smsglobal'
        );

        add_settings_field(
            'api_key',
            Smsglobal::_('API Key'),
            array($this, 'getApiKeyHtml'),
            'smsglobal',
            'setting_section_id'
        );

        add_settings_field(
            'api_secret',
            Smsglobal::_('API Secret'),
            array($this, 'getApiSecretHtml'),
            'smsglobal',
            'setting_section_id'
        );

    }

    public function saveApiKey($input)
    {
        $value = $input['api_key'];

        if (get_option('smsglobal_api_key') === false) {
            add_option('smsglobal_api_key', $value);
        } else {
            update_option('smsglobal_api_key', $value);
        }

        return $input;
    }

    public function saveApiSecret($input)
    {
        $value = $input['api_secret'];

        if (get_option('smsglobal_api_secret') === false) {
            add_option('smsglobal_api_secret', $value);
        } else {
            update_option('smsglobal_api_secret', $value);
        }

        return $input;
    }

    public function getApiKeyHtml()
    {
        ?><input type="text" id="smsglobal-api-key" name="array_key[api_key]" value="<?php echo get_option('smsglobal_api_key'); ?>"><?php
    }

    public function getApiSecretHtml()
    {
        ?><input type="text" id="smsglobal-api-secret" name="array_key[api_secret]" value="<?php echo get_option('smsglobal_api_secret'); ?>"><?php
    }
}
