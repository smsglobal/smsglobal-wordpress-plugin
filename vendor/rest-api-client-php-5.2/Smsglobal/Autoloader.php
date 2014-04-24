<?php
class Smsglobal_Autoloader
{
    protected static $instance;

    protected $path;

    public static function register()
    {
        if (null === self::$instance) {
            self::$instance = new Smsglobal_Autoloader();
            spl_autoload_register(array(self::$instance, 'autoload'));
        }
    }

    public function autoload($class)
    {
        if ('Smsglobal_' !== substr($class, 0, 10)) {
            // We only autoload SMSGlobal classes
            return;
        }

        if (null === $this->path) {
            $this->path = dirname(dirname(__FILE__));
        }

        $class = str_replace('_', DIRECTORY_SEPARATOR, $class);
        $class = sprintf('%s/%s.php', $this->path, $class);

        if (file_exists($class)) {
            include $class;
        }
    }
}
