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
            <?php screen_icon() ?>
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
            array($this, 'saveEnableAuth'));

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

        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'saveEcommerceEnabled'));

        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'saveEcommerceOrigin'));

        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'saveEcommerceDestination'));

        add_settings_section(
            'smsglobal_settings_api_key',
            Smsglobal_Utils::_('API Key Settings'),
            array($this, 'getSectionInfo'),
            'smsglobal'
        );

        add_settings_field(
            'api_key',
            Smsglobal_Utils::_('API Key'),
            array($this, 'getApiKeyHtml'),
            'smsglobal',
            'smsglobal_settings_api_key'
        );

        add_settings_field(
            'api_secret',
            Smsglobal_Utils::_('API Secret'),
            array($this, 'getApiSecretHtml'),
            'smsglobal',
            'smsglobal_settings_api_key'
        );

        add_settings_section(
            'smsglobal_settings_auth',
            Smsglobal_Utils::_('2 Factor Authentication'),
            array($this, 'getSectionInfo'),
            'smsglobal'
        );

        add_settings_field(
            'enable_auth',
            Smsglobal_Utils::_('Require SMS code for admin panel'),
            array($this, 'getEnableAuthHtml'),
            'smsglobal',
            'smsglobal_settings_auth'
        );

        add_settings_field(
            'auth_origin',
            Smsglobal_Utils::_('SMS comes from'),
            array($this, 'getAuthOriginHtml'),
            'smsglobal',
            'smsglobal_settings_auth'
        );

        add_settings_section(
            'smsglobal_settings_post_alerts',
            Smsglobal_Utils::_('Post Alerts'),
            array($this, 'getSectionInfo'),
            'smsglobal'
        );

        add_settings_field(
            'enable_post_alerts',
            Smsglobal_Utils::_('Post alerts'),
            array($this, 'getEnablePostAlertsHtml'),
            'smsglobal',
            'smsglobal_settings_post_alerts'
        );

        add_settings_field(
            'post_alerts_origin',
            Smsglobal_Utils::_('Send SMS from'),
            array($this, 'getPostAlertsOriginHtml'),
            'smsglobal',
            'smsglobal_settings_post_alerts'
        );

        add_settings_section(
            'smsglobal_settings_shopp',
            Smsglobal_Utils::_('Shopp Integration'),
            array($this, 'getSectionInfo'),
            'smsglobal'
        );

        add_settings_field(
            'shopp_enabled',
            Smsglobal_Utils::_('Order alerts'),
            array($this, 'getShoppEnabledHtml'),
            'smsglobal',
            'smsglobal_settings_shopp'
        );

        add_settings_field(
            'shopp_origin',
            Smsglobal_Utils::_('SMS comes from'),
            array($this, 'getShoppOriginHtml'),
            'smsglobal',
            'smsglobal_settings_shopp'
        );

        add_settings_field(
            'shopp_destination',
            Smsglobal_Utils::_('SMS goes to'),
            array($this, 'getShoppDestinationHtml'),
            'smsglobal',
            'smsglobal_settings_shopp'
        );

        add_settings_section(
            'smsglobal_settings_ecommerce',
            Smsglobal_Utils::_('e-Commerce Integration'),
            array($this, 'getSectionInfo'),
            'smsglobal'
        );

        add_settings_field(
            'ecommerce_enabled',
            Smsglobal_Utils::_('Order alerts'),
            array($this, 'getEcommerceEnabledHtml'),
            'smsglobal',
            'smsglobal_settings_ecommerce'
        );

        add_settings_field(
            'ecommerce_origin',
            Smsglobal_Utils::_('SMS comes from'),
            array($this, 'getEcommerceOriginHtml'),
            'smsglobal',
            'smsglobal_settings_ecommerce'
        );

        add_settings_field(
            'ecommerce_destination',
            Smsglobal_Utils::_('SMS goes to'),
            array($this, 'getEcommerceDestinationHtml'),
            'smsglobal',
            'smsglobal_settings_ecommerce'
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

    public function saveEnableAuth($input)
    {
        $value = (bool) $input['enable_auth'];

        if (get_option('smsglobal_enable_auth') === false) {
            add_option('smsglobal_enable_auth', $value);
        } else {
            update_option('smsglobal_enable_auth', $value);
        }

        return $input;
    }

    public function saveAuthOrigin($input)
    {
        $value = $input['auth_origin'];

        if (get_option('smsglobal_auth_origin') === false) {
            add_option('smsglobal_auth_origin', $value);
        } else {
            update_option('smsglobal_auth_origin', $value);
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

    public function saveEcommerceEnabled($input)
    {
        $value = (bool) $input['ecommerce_enabled'];

        if (get_option('smsglobal_ecommerce_enabled') === false) {
            add_option('smsglobal_ecommerce_enabled', $value);
        } else {
            update_option('smsglobal_ecommerce_enabled', $value);
        }

        return $input;
    }

    public function saveEcommerceOrigin($input)
    {
        $value = $input['ecommerce_origin'];

        if (get_option('smsglobal_ecommerce_origin') === false) {
            add_option('smsglobal_ecommerce_origin', $value);
        } else {
            update_option('smsglobal_ecommerce_origin', $value);
        }

        return $input;
    }

    public function saveEcommerceDestination($input)
    {
        $value = $input['ecommerce_destination'];

        if (get_option('smsglobal_ecommerce_destination') === false) {
            add_option('smsglobal_ecommerce_destination', $value);
        } else {
            update_option('smsglobal_ecommerce_destination', $value);
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

    public function getEnableAuthHtml()
    {
        $checked = (bool) get_option('smsglobal_enable_auth');
        ?>
        <label for="smsglobal-auth-enabled">
        <input<?php if ($checked): ?> checked="checked"<?php endif ?> type="checkbox" id="smsglobal-auth-enabled" name="array_key[enable_auth]" value="1">
        <?php echo Smsglobal_Utils::_('Enable') ?>
        </label><?php
    }

    public function getAuthOriginHtml()
    {
        ?><input type="text" id="smsglobal-auth-origin" name="array_key[auth_origin]" value="<?php echo get_option('smsglobal_auth_origin'); ?>"><?php
    }

    public function getEnablePostAlertsHtml()
    {
        $checked = (bool) get_option('smsglobal_enable_post_alerts');
        ?><label for="smsglobal-enable-post-alerts">
            <input<?php if ($checked): ?> checked="checked"<?php endif ?> type="checkbox" id="smsglobal-enable-post-alerts" name="array_key[enable_post_alerts]" value="1">
            <?php echo Smsglobal_Utils::_('Enable') ?>
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
        <?php echo Smsglobal_Utils::_('Enable') ?>
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

    public function getEcommerceEnabledHtml()
    {
        $checked = (bool) get_option('smsglobal_ecommerce_enabled');
        ?>
        <label for="smsglobal-ecommerce-enabled">
        <input<?php if ($checked): ?> checked="checked"<?php endif ?> type="checkbox" id="smsglobal-ecommerce-enabled" name="array_key[ecommerce_enabled]" value="1">
        <?php echo Smsglobal_Utils::_('Enable') ?>
        </label><?php
    }

    public function getEcommerceOriginHtml()
    {
        ?><input type="text" id="smsglobal-ecommerce-origin" name="array_key[ecommerce_origin]" value="<?php echo get_option('smsglobal_ecommerce_origin'); ?>"><?php
    }

    public function getEcommerceDestinationHtml()
    {
        ?><input type="text" id="smsglobal-ecommerce-origin" name="array_key[ecommerce_destination]" value="<?php echo get_option('smsglobal_ecommerce_destination'); ?>"><?php
    }
}
