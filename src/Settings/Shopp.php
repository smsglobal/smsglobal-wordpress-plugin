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
        }

        add_settings_section(
            'smsglobal_settings_shopp',
            Smsglobal_Utils::_('Shopp Integration'),
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
            print 'This plugin supports "Shopp" e-commerce plugin. You can receive SMS alert of new order placed on "Shopp"';
        } else {
            if(!$this->vm->isAvailable('shopp')) {
                print 'Installed version of "Shopp" e-commerce plugin is not supported. Please upgrade plugin to latest version to continue using this feature.';
            } else {
                if(get_option( 'smsglobal_shopp_enabled')) {
                    print 'Sends an SMS alert of a new order placed through "Shopp" e-commerce plugin with new order information.';
                } else {
                    print 'Enable to receive an SMS alert of a new order placed through "Shopp" e-commerce plugin with new order information.';
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
        <?php echo Smsglobal_Utils::_('Enable') ?>
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
