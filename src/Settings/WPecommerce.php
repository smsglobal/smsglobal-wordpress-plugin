<?php
class Smsglobal_Settings_WPecommerce
{
    /**
     * Constructor
     * Registers WP eCommerce plugin related settings with WordPress
     */
    public function __construct()
    {
        $this->vm = SMSGlobal\VersionManager\VersionManager::getInstance();
        if(is_plugin_active('wp-e-commerce/wp-shopping-cart.php') && $this->vm->isAvailable('wp-e-commerce')) {
            register_setting('smsglobal_option_group', 'array_key',
                array($this, 'saveEcommerceEnabled'));

            register_setting('smsglobal_option_group', 'array_key',
                array($this, 'saveEcommerceOrigin'));

            register_setting('smsglobal_option_group', 'array_key',
                array($this, 'saveEcommerceDestination'));

            add_settings_field(
                'ecommerce_enabled',
                __('Order alerts', SMSGLOBAL_TEXT_DOMAIN),
                array($this, 'getEcommerceEnabledHtml'),
                'smsglobal',
                'smsglobal_settings_ecommerce'
            );

            add_settings_field(
                'ecommerce_origin',
                __('SMS comes from', SMSGLOBAL_TEXT_DOMAIN),
                array($this, 'getEcommerceOriginHtml'),
                'smsglobal',
                'smsglobal_settings_ecommerce'
            );

            add_settings_field(
                'ecommerce_destination',
                __('SMS goes to', SMSGLOBAL_TEXT_DOMAIN),
                array($this, 'getEcommerceDestinationHtml'),
                'smsglobal',
                'smsglobal_settings_ecommerce'
            );
        }

        add_settings_section(
            'smsglobal_settings_ecommerce',
            __('WP eCommerce Integration', SMSGLOBAL_TEXT_DOMAIN),
            array($this, 'getSectionWPCommerceInfo'),
            'smsglobal'
        );
    }

    /**
     * Display the WP eCommerce plugin settings section description
     *
     * @return void
     */
    public function getSectionWPCommerceInfo()
    {
        if(!is_plugin_active('wp-e-commerce/wp-shopping-cart.php')) {
            _e('This plugin supports "WP eCommerce" plugin. You can receive SMS alert of new order placed on "WP eCommerce" and send order status updates to your customers.', SMSGLOBAL_TEXT_DOMAIN);
        } else {
            if(!$this->vm->isAvailable('wp-e-commerce')) {
                _e('Installed version of "WP eCommerce" plugin is not supported. Please upgrade plugin to latest version to continue using this feature.', SMSGLOBAL_TEXT_DOMAIN);
            } else {
                if(get_option( 'smsglobal_ecommerce_enabled')) {
                    _e('Sends an SMS alert of a new order placed through "WP eCommerce" plugin with new order information.', SMSGLOBAL_TEXT_DOMAIN);
                } else {
                    _e('Enable to receive an SMS alert of a new order placed through "WP eCommerce" plugin with new order information.', SMSGLOBAL_TEXT_DOMAIN);
                }
            }
        }
    }

    /**
     * Display the Enable / Disable checkbox html
     *
     * @return void
     */
    public function getEcommerceEnabledHtml()
    {
        $checked = (bool) get_option('smsglobal_ecommerce_enabled');
        ?>
        <label for="smsglobal-ecommerce-enabled">
        <input<?php if ($checked): ?> checked="checked"<?php endif ?> type="checkbox" id="smsglobal-ecommerce-enabled" name="array_key[ecommerce_enabled]" value="1">
        <?php _e('Enable', SMSGLOBAL_TEXT_DOMAIN) ?>
        </label><?php
    }

    /**
     * Display the SMS origin string input html
     *
     * @return void
     */
    public function getEcommerceOriginHtml()
    {
        ?><input type="text" id="smsglobal-ecommerce-origin" name="array_key[ecommerce_origin]" value="<?php echo get_option('smsglobal_ecommerce_origin'); ?>"><?php
    }

    /**
     * Display the SMS Destination string input html
     *
     * @return void
     */
    public function getEcommerceDestinationHtml()
    {
        ?><input type="text" id="smsglobal-ecommerce-origin" name="array_key[ecommerce_destination]" value="<?php echo get_option('smsglobal_ecommerce_destination'); ?>"><?php
    }

    /**
     * Save if integration with WP eCommerce plugin is enabled through WP Options
     *
     * @param string $input
     * @return string
     */
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

    /**
     * Save WP eCommerce SMS origin string through WP Options
     *
     * @param string $input
     * @return string
     */
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

    /**
     * Save WP eCommerce SMS destination string through WP Options
     *
     * @param string $input
     * @return string
     */
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

}
