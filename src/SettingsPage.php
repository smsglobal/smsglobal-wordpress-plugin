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

        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'saveEnablePostAlerts'));

        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'savePostAlertsOrigin'));

        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'saveShoppEnabled'));

        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'saveShoppOrigin'));

        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'saveShoppDestination'));

        add_settings_section(
            'smsglobal_settings_api_key',
            Smsglobal::_('API Key Settings'),
            array($this, 'getSectionInfo'),
            'smsglobal'
        );

        add_settings_field(
            'api_key',
            Smsglobal::_('API Key'),
            array($this, 'getApiKeyHtml'),
            'smsglobal',
            'smsglobal_settings_api_key'
        );

        add_settings_field(
            'api_secret',
            Smsglobal::_('API Secret'),
            array($this, 'getApiSecretHtml'),
            'smsglobal',
            'smsglobal_settings_api_key'
        );

        add_settings_section(
            'smsglobal_settings_post_alerts',
            Smsglobal::_('Post Alerts'),
            array($this, 'getSectionInfo'),
            'smsglobal'
        );

        add_settings_field(
            'enable_post_alerts',
            Smsglobal::_('Post alerts'),
            array($this, 'getEnablePostAlertsHtml'),
            'smsglobal',
            'smsglobal_settings_post_alerts'
        );

        add_settings_field(
            'post_alerts_origin',
            Smsglobal::_('Send SMS from'),
            array($this, 'getPostAlertsOriginHtml'),
            'smsglobal',
            'smsglobal_settings_post_alerts'
        );

        add_settings_section(
            'smsglobal_settings_shopp',
            Smsglobal::_('Shopp Integration'),
            array($this, 'getSectionInfo'),
            'smsglobal'
        );

        add_settings_field(
            'shopp_enabled',
            Smsglobal::_('Order alerts'),
            array($this, 'getShoppEnabledHtml'),
            'smsglobal',
            'smsglobal_settings_shopp'
        );

        add_settings_field(
            'shopp_origin',
            Smsglobal::_('SMS comes from'),
            array($this, 'getShoppOriginHtml'),
            'smsglobal',
            'smsglobal_settings_shopp'
        );

        add_settings_field(
            'shopp_destination',
            Smsglobal::_('SMS goes to'),
            array($this, 'getShoppDestinationHtml'),
            'smsglobal',
            'smsglobal_settings_shopp'
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

    public function saveEnablePostAlerts($input)
    {
        $value = (bool) $input['enable_post_alerts'];

        if (get_option('smsglobal_enable_post_alerts') === false) {
            add_option('smsglobal_enable_post_alerts', $value);
        } else {
            update_option('smsglobal_enable_post_alerts', $value);
        }

        return $input;
    }

    public function savePostAlertsOrigin($input)
    {
        $value = $input['post_alerts_origin'];

        if (get_option('smsglobal_post_alerts_origin') === false) {
            add_option('smsglobal_post_alerts_origin', $value);
        } else {
            update_option('smsglobal_post_alerts_origin', $value);
        }

        return $input;
    }

    public function saveShoppEnabled($input)
    {
        $value = (bool) $input['shopp_enabled'];

        if (get_option('smsglobal_shopp_enabled') === false) {
            add_option('smsglobal_shopp_enabled', $value);
        } else {
            update_option('smsglobal_shopp_enabled', $value);
        }

        return $input;
    }

    public function saveShoppOrigin($input)
    {
        $value = $input['shopp_origin'];

        if (get_option('smsglobal_shopp_origin') === false) {
            add_option('smsglobal_shopp_origin', $value);
        } else {
            update_option('smsglobal_shopp_origin', $value);
        }

        return $input;
    }

    public function saveShoppDestination($input)
    {
        $value = $input['shopp_destination'];

        if (get_option('smsglobal_shopp_destination') === false) {
            add_option('smsglobal_shopp_destination', $value);
        } else {
            update_option('smsglobal_shopp_destination', $value);
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

    public function getEnablePostAlertsHtml()
    {
        $checked = (bool) get_option('smsglobal_enable_post_alerts');
        ?><label for="smsglobal-enable-post-alerts">
            <input<?php if ($checked): ?> checked="checked"<?php endif ?> type="checkbox" id="smsglobal-enable-post-alerts" name="array_key[enable_post_alerts]" value="1">
            <?php echo Smsglobal::_('Enable') ?>
        </label><?php
    }

    public function getPostAlertsOriginHtml()
    {
        ?><input type="text" id="smsglobal-post-alerts-origin" name="array_key[post_alerts_origin]" value="<?php echo get_option('smsglobal_post_alerts_origin'); ?>"><?php
    }

    public function getShoppEnabledHtml()
    {
        $checked = (bool) get_option('smsglobal_shopp_enabled');
        ?>
        <label for="smsglobal-shopp-enabled">
        <input<?php if ($checked): ?> checked="checked"<?php endif ?> type="checkbox" id="smsglobal-shopp-enabled" name="array_key[shopp_enabled]" value="1">
        <?php echo Smsglobal::_('Enable') ?>
        </label><?php
    }

    public function getShoppOriginHtml()
    {
        ?><input type="text" id="smsglobal-shopp-origin" name="array_key[shopp_origin]" value="<?php echo get_option('smsglobal_shopp_origin'); ?>"><?php
    }

    public function getShoppDestinationHtml()
    {
        ?><input type="text" id="smsglobal-shopp-origin" name="array_key[shopp_destination]" value="<?php echo get_option('smsglobal_shopp_destination'); ?>"><?php
    }
}
