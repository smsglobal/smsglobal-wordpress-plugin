<?php
class Smsglobal
{
    protected static $roles;

    protected static $restClient;

    public static function getRestClient()
    {
        if (null === self::$restClient) {
            $apiKey = new Smsglobal\RestApiClient\ApiKey(
                get_option('smsglobal_api_key'),
                get_option('smsglobal_api_secret')
            );

            self::$restClient = new Smsglobal\RestApiClient\RestApiClient($apiKey);
        }

        return self::$restClient;
    }

    public static function _($string, $namespace = 'smsglobal')
    {
        return __($string, $namespace);
    }

    public static function getRoles()
    {
        if (null === self::$roles) {
            global $wp_roles;

            self::$roles = array(
                'all' => self::_('All Users'),
            );

            foreach ($wp_roles->roles as $id => $role) {
                // Pluralize
                self::$roles[$id] = $role['name'] . 's';
            }
        }

        return self::$roles;
    }
}
