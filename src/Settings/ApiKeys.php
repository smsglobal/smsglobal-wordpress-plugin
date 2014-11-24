<?php
class Smsglobal_Settings_ApiKeys
{
    /**
     * Constructor
     * Registers API Key & Secret key settings with WordPress
     */
    public function __construct()
    {
        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'saveApiKey'));

        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'saveApiSecret'));

        add_settings_section(
            'smsglobal_settings_api_key',
            __('API Key Settings', SMSGLOBAL_TEXT_DOMAIN),
            array($this, 'getSectionInfo'),
            'smsglobal'
        );

        add_settings_field(
            'api_key',
            __('API Key', SMSGLOBAL_TEXT_DOMAIN),
            array($this, 'getApiKeyHtml'),
            'smsglobal',
            'smsglobal_settings_api_key'
        );

        add_settings_field(
            'api_secret',
            __('API Secret', SMSGLOBAL_TEXT_DOMAIN),
            array($this, 'getApiSecretHtml'),
            'smsglobal',
            'smsglobal_settings_api_key'
        );
    }

    /**
     * Display API key section description
     *
     * @return void
     */
    public function getSectionInfo()
    {
        return;
    }

    /**
     * Display the API string Key input html
     *
     * @return void
     */
    public function getApiKeyHtml()
    {
        ?><input type="text" id="smsglobal-api-key" name="array_key[api_key]" value="<?php echo get_option('smsglobal_api_key'); ?>"><?php
    }

    /**
     * Display the API Secret string input html
     *
     * @return void
     */
    public function getApiSecretHtml()
    {
        ?><input type="text" id="smsglobal-api-secret" name="array_key[api_secret]" value="<?php echo get_option('smsglobal_api_secret'); ?>"><?php
    }

    /**
     * Save API Key string through WP Options
     *
     * @param string $input
     * @return string
     */
    public function saveApiKey($input)
    {
        $value = trim($input['api_key']);

        if (get_option('smsglobal_api_key') === false) {
            add_option('smsglobal_api_key', $value);
        } else {
            update_option('smsglobal_api_key', $value);
        }

        return $input;
    }

    /**
     * Save API Secret string through WP Options
     *
     * @param string $input
     * @return string
     */
    public function saveApiSecret($input)
    {
        $value = trim($input['api_secret']);

        if (get_option('smsglobal_api_secret') === false) {
            add_option('smsglobal_api_secret', $value);
        } else {
            update_option('smsglobal_api_secret', $value);
        }

        return $input;
    }
}
