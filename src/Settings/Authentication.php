<?php
class Smsglobal_Settings_Authentication
{
    /**
     * Constructor
     * Registers 2 factor authentication settings with WordPress
     */
    public function __construct()
    {
        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'saveEnableAuth'));

        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'saveAuthOrigin'));

        add_settings_section(
            'smsglobal_settings_auth',
            __('2 Factor Authentication', SMSGLOBAL_TEXT_DOMAIN),
            array($this, 'getSection2FactorAuthInfo'),
            'smsglobal'
        );

        add_settings_field(
            'enable_auth',
            __('Require SMS code for admin panel', SMSGLOBAL_TEXT_DOMAIN),
            array($this, 'getEnableAuthHtml'),
            'smsglobal',
            'smsglobal_settings_auth'
        );

        add_settings_field(
            'auth_origin',
            __('SMS comes from', SMSGLOBAL_TEXT_DOMAIN),
            array($this, 'getAuthOriginHtml'),
            'smsglobal',
            'smsglobal_settings_auth'
        );
    }

    /**
     * Display 2 factor authentication section description
     *
     * @return void
     */
    public function getSection2FactorAuthInfo($section = '')
    {
        if(get_option('smsglobal_enable_auth')) {
            print 'Controls access to your WordPress administration panel by sending a verification code to your mobile phone when you try and login.';
        } else {
            print 'Enable 2 factor authentication to control access to your WordPress administration panel by sending a verification code to your mobile phone when you try and
            login.';
        }
    }

    /**
     * Display the 2 factor authentication settings section description
     *
     * @return void
     */
    public function getEnableAuthHtml()
    {
        $checked = (bool) get_option('smsglobal_enable_auth');
        ?>
        <label for="smsglobal-auth-enabled">
        <input<?php if ($checked): ?> checked="checked"<?php endif ?> type="checkbox" id="smsglobal-auth-enabled" name="array_key[enable_auth]" value="1">
        <?php _e('Enable', SMSGLOBAL_TEXT_DOMAIN) ?>
        </label><?php
    }

    /**
     * Display the SMS origin string input html
     *
     * @return void
     */
    public function getAuthOriginHtml()
    {
        ?><input type="text" id="smsglobal-auth-origin" name="array_key[auth_origin]" value="<?php echo get_option('smsglobal_auth_origin'); ?>"><br>
        <span style="font-size: 10px">Letters (4-11 chars) or Valid mobile #</span><?php
    }

    /**
     * Save if 2 factor authentication is enabled through WP Options
     *
     * @param string $input
     * @return string
     */
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

    /**
     * Save 2 factor authentication SMS origin string through WP Options
     *
     * @param string $input
     * @return string
     */
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
}
