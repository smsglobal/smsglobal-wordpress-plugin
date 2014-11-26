<?php
class Smsglobal_Settings_Shopp
{
    /**
     * Constructor
     * Registers Shopp plugin related settings with WordPress
     */
    public function __construct()
    {
        $this->vm = SMSGlobal\VersionManager\VersionManager::getInstance();
        if(is_plugin_active('shopp/Shopp.php') && $this->vm->isAvailable('shopp')) {
            register_setting('smsglobal_option_group', 'array_key',
                array($this, 'saveShoppEnabled'));

            register_setting('smsglobal_option_group', 'array_key',
                array($this, 'saveShoppOrigin'));

            register_setting('smsglobal_option_group', 'array_key',
                array($this, 'saveShoppDestination'));

            add_settings_field(
                'shopp_enabled',
                __('Order alerts', SMSGLOBAL_TEXT_DOMAIN),
                array($this, 'getShoppEnabledHtml'),
                'smsglobal',
                'smsglobal_settings_shopp'
            );

            add_settings_field(
                'shopp_origin',
                __('SMS comes from', SMSGLOBAL_TEXT_DOMAIN),
                array($this, 'getShoppOriginHtml'),
                'smsglobal',
                'smsglobal_settings_shopp'
            );

            add_settings_field(
                'shopp_destination',
                __('SMS goes to', SMSGLOBAL_TEXT_DOMAIN),
                array($this, 'getShoppDestinationHtml'),
                'smsglobal',
                'smsglobal_settings_shopp'
            );
        }

        add_settings_section(
            'smsglobal_settings_shopp',
            __('Shopp Integration', SMSGLOBAL_TEXT_DOMAIN),
            array($this, 'getSectionShoppInfo'),
            'smsglobal'
        );
    }

    /**
     * Display the Shopp plugin settings section description
     *
     * @return void
     */
    public function getSectionShoppInfo()
    {
        if(!is_plugin_active('shopp/Shopp.php')) {
            _e('This plugin supports "Shopp" e-commerce plugin. You can receive SMS alert of new order placed on "Shopp"', SMSGLOBAL_TEXT_DOMAIN);
        } else {
            if(!$this->vm->isAvailable('shopp')) {
                _e('Installed version of "Shopp" e-commerce plugin is not supported. Please upgrade plugin to latest version to continue using this feature.', SMSGLOBAL_TEXT_DOMAIN);
            } else {
                if(get_option( 'smsglobal_shopp_enabled')) {
                    _e('Sends an SMS alert of a new order placed through "Shopp" e-commerce plugin with new order information.', SMSGLOBAL_TEXT_DOMAIN);
                } else {
                    _e('Enable to receive an SMS alert of a new order placed through "Shopp" e-commerce plugin with new order information.', SMSGLOBAL_TEXT_DOMAIN);
                }
            }
        }
    }

    /**
     * Display the Enable / Disable checkbox html
     *
     * @return void
     */
    public function getShoppEnabledHtml()
    {
        $checked = (bool) get_option('smsglobal_shopp_enabled');
        ?>
        <label for="smsglobal-shopp-enabled">
        <input<?php if ($checked): ?> checked="checked"<?php endif ?> type="checkbox" id="smsglobal-shopp-enabled" name="array_key[shopp_enabled]" value="1">
        <?php _e('Enable', SMSGLOBAL_TEXT_DOMAIN) ?>
        </label><?php
    }

    /**
     * Display the SMS origin string input html
     *
     * @return void
     */
    public function getShoppOriginHtml()
    {
        ?><input type="text" id="smsglobal-shopp-origin" name="array_key[shopp_origin]" value="<?php echo get_option('smsglobal_shopp_origin'); ?>"><?php
    }

    /**
     * Display the SMS Destination string input html
     *
     * @return void
     */
    public function getShoppDestinationHtml()
    {
        ?><input type="text" id="smsglobal-shopp-origin" name="array_key[shopp_destination]" value="<?php echo get_option('smsglobal_shopp_destination'); ?>"><?php
    }

    /**
     * Save if integration with Shopp plugin is enabled through WP Options
     *
     * @param string $input
     * @return string
     */
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

    /**
     * Save Shopp SMS origin string through WP Options
     *
     * @param string $input
     * @return string
     */
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

    /**
     * Save Shopp SMS destination string through WP Options
     *
     * @param string $input
     * @return string
     */
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

}
