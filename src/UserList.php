<?php
class Smsglobal_UserList
{
    /**
     * Cached setting for whether post alerts are enabled
     * @var bool
     */
    protected $arePostAlertsEnabled;

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('manage_users_custom_column', array($this, 'getCustomField'), 15, 3);
        add_filter('manage_users_columns', array($this, 'addCustomColumns'), 15, 1);
        add_filter('user_contactmethods', array($this, 'addCustomColumns'));
    }

    /**
     * Gets whether post alerts are enabled. Caches setting for performance
     *
     * @return bool
     */
    protected function arePostAlertsEnabled()
    {
        if (null === $this->arePostAlertsEnabled) {
            $this->arePostAlertsEnabled = (bool) get_option('smsglobal_enable_post_alerts');
        }

        return $this->arePostAlertsEnabled;
    }

    /**
     * Adds custom columns for mobile number and post alerts (if enabled)
     *
     * @param array $profileFields
     * @return array
     */
    public function addCustomColumns(array $profileFields)
    {
        // Add new fields
        $profileFields['mobile'] = __('Mobile Number', SMSGLOBAL_TEXT_DOMAIN);
        $profileFields['smsglobal_send_post_alerts'] = __('Send Post Alerts', SMSGLOBAL_TEXT_DOMAIN);

        return $profileFields;
    }

    /**
     * Gets the value for the custom fields:
     * - mobile
     * - smsglobal_send_post_alerts
     *
     * @param mixed $value
     * @param string $column
     * @param int $id
     * @return string
     */
    public function getCustomField($value, $column, $id)
    {
        if ('mobile' === $column || 'smsglobal_send_post_alerts' === $column) {
            $value = get_user_meta($id, $column, true);

            if ('smsglobal_send_post_alerts' === $column) {
                $value = (bool) $value;
                $value = __($value ? 'Yes' : 'No', SMSGLOBAL_TEXT_DOMAIN);
            }
        }

        return $value;
    }
}
