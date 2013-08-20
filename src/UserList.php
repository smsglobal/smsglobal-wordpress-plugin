<?php
class Smsglobal_UserList
{
    protected $arePostAlertsEnabled;

    function __construct()
    {
        add_action('manage_users_custom_column', array($this, 'getCustomField'), 15, 3);
        add_filter('manage_users_columns', array($this, 'addColumn'), 15, 1);
        add_filter('user_contactmethods', array($this, 'addField'));
    }

    protected function arePostAlertsEnabled()
    {
        if (null === $this->arePostAlertsEnabled) {
            $this->arePostAlertsEnabled = (bool) get_option('smsglobal_enable_post_alerts');
        }

        return $this->arePostAlertsEnabled;
    }

    function addField($profileFields)
    {
        // Add new fields
        $profileFields['mobile'] = Smsglobal::_('Mobile Number');

        if ($this->arePostAlertsEnabled()) {
            $profileFields['smsglobal_send_post_alerts'] = Smsglobal::_('Send Post Alerts');
        }

        return $profileFields;
    }

    function addColumn($defaults)
    {
        $defaults['mobile'] = Smsglobal::_('Mobile Number');

        if ($this->arePostAlertsEnabled()) {
            $defaults['smsglobal_send_post_alerts'] = Smsglobal::_('Send Post Alerts');
        }

        return $defaults;
    }

    function getCustomField($value, $column, $id)
    {
        if ('mobile' === $column || 'smsglobal_send_post_alerts' === $column) {
            $value = get_user_meta($id, $column);

            if ('smsglobal_send_post_alerts' === $column) {
                $value = (bool) $value;
                $value = $value ? 'Yes' : 'No';
            }
        }

        return $value;
    }
}
