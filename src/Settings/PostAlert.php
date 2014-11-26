<?php
class Smsglobal_Settings_PostAlert
{
    /**
     * Constructor
     * Registers new post alert settings with WordPress
     */
    public function __construct()
    {

        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'saveEnablePostAlerts'));

        register_setting('smsglobal_option_group', 'array_key',
            array($this, 'savePostAlertsOrigin'));

        add_settings_section(
            'smsglobal_settings_post_alerts',
            __('Post Alerts', SMSGLOBAL_TEXT_DOMAIN),
            array($this, 'getSectionPostAlertsInfo'),
            'smsglobal'
        );

        add_settings_field(
            'enable_post_alerts',
            __('Post alerts', SMSGLOBAL_TEXT_DOMAIN),
            array($this, 'getEnablePostAlertsHtml'),
            'smsglobal',
            'smsglobal_settings_post_alerts'
        );

        add_settings_field(
            'post_alerts_origin',
            __('Send SMS from', SMSGLOBAL_TEXT_DOMAIN),
            array($this, 'getPostAlertsOriginHtml'),
            'smsglobal',
            'smsglobal_settings_post_alerts'
        );
    }

    /**
     * Display new post alert section description
     *
     * @return void
     */
    public function getSectionPostAlertsInfo()
    {
        if(get_option('smsglobal_enable_post_alerts')) {
            _e('All subscribers / users receive an SMS alert of a new post with a link to the post.', SMSGLOBAL_TEXT_DOMAIN);
        } else {
            _e('Enable post alerts to send an SMS alert of a new post to subscribers / users with a link to the post.', SMSGLOBAL_TEXT_DOMAIN);
        }
    }

    /**
     * Display the Enable / Disable checkbox html
     *
     * @return void
     */
    public function getEnablePostAlertsHtml()
    {
        $checked = (bool) get_option('smsglobal_enable_post_alerts');
        ?><label for="smsglobal-enable-post-alerts">
        <input<?php if ($checked): ?> checked="checked"<?php endif ?> type="checkbox" id="smsglobal-enable-post-alerts" name="array_key[enable_post_alerts]" value="1">
        <?php _e('Enable', SMSGLOBAL_TEXT_DOMAIN) ?>
        </label><?php
    }

    /**
     * Display the post alert origin string input html
     *
     * @return void
     */
    public function getPostAlertsOriginHtml()
    {
        ?><input type="text" id="smsglobal-post-alerts-origin" name="array_key[post_alerts_origin]" value="<?php echo get_option('smsglobal_post_alerts_origin'); ?>"><?php
    }

    /**
     * Save if new post alert is enabled through WP Options
     *
     * @param string $input
     * @return string
     */
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

    /**
     * Save Shopp SMS origin string through WP Options
     *
     * @param string $input
     * @return string
     */
    public function savePostAlertsOrigin($input)
    {
        $value = $input['post_alerts_origin'];

        if(!is_numeric($value) && strlen($value) > 11) {
            $value = substr($value, 0, 11);
        }

        if (get_option('smsglobal_post_alerts_origin') === false) {
            add_option('smsglobal_post_alerts_origin', $value);
        } else {
            update_option('smsglobal_post_alerts_origin', $value);
        }

        return $input;
    }
}
