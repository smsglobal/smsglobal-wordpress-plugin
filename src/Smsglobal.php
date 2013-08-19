<?php
class Smsglobal
{
    protected static $roles;

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
