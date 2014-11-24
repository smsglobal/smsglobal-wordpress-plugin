<?php
class Smsglobal_Utils
{
    protected static $roles;

    protected static $restClient;

    protected static $templatePath;

    protected static $mockClasses = array();

    public static function getRestClient()
    {
        if (null === self::$restClient) {
            $apiKey = new Smsglobal_RestApiClient_ApiKey(
                get_option('smsglobal_api_key'),
                get_option('smsglobal_api_secret')
            );

            self::$restClient = new Smsglobal_RestApiClient_RestApiClient($apiKey);
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
                'all' => __('All Users', SMSGLOBAL_TEXT_DOMAIN),
            );

            foreach ($wp_roles->roles as $id => $role) {
                // Pluralize
                self::$roles[$id] = $role['name'] . 's';
            }
        }

        self::$roles['sms'] = 'SMS Subscribers';
        return self::$roles;
    }

    public static function renderTemplate($name, array $vars = array())
    {
        if (null === self::$templatePath) {
            self::$templatePath = realpath(dirname(__FILE__) . '/../templates');
        }

        extract($vars);

        ob_start();
        require sprintf('%s/%s.phtml', self::$templatePath, $name);
        $template = ob_get_contents();
        ob_end_clean();

        return $template;
    }

    /**
     * Creates a class with public access to every method
     *
     * @param string $class
     * @return mixed
     * @throws Exception
     */
    public static function createMockClass($class)
    {
        if (null === self::$mockClasses) {
            self::$mockClasses = array();
        }

        if (!isset(self::$mockClasses[$class])) {
            $mockClass = $class . 'Mock';

            if (class_exists($mockClass)) {
                throw new Exception('Mock class already exists: ' . $mockClass);
            }

            $code = sprintf(
                'class %s extends %s
{
    public function __call($name, $args)
    {
        return call_user_func_array(array(parent, $name), $args);
    }
}
',
                $mockClass,
                $class
            );

            eval($code);

            self::$mockClasses[$class] = $mockClass;
        }

        return new self::$mockClasses[$class];
    }

    public static function getVerificationCode($mobile) {
        $md5 = md5($mobile);
        $base64 = base64_encode($md5);
        return substr($base64, 2, 4);

    }

    public static function getPluginVersion($pluginFolderFile) {
        if ( ! function_exists( 'get_plugin_data' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        $pluginData = get_plugin_data( WP_PLUGIN_DIR . '/' . $pluginFolderFile );
        return $pluginData['Version'];
    }
}
