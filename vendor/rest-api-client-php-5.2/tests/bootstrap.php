<?php
require dirname(__FILE__) . '/../Smsglobal/Autoloader.php';

Smsglobal_Autoloader::register();

// Below code is for PHP 5.2 compatibility
class _ReflectionClass extends ReflectionClass
{
    public function getName()
    {
        return str_replace('__', '\\', parent::getName());
    }

    public function getNamespaceName()
    {
        $name = $this->getName();
        if (false !== ($pos = strrpos($name, '\\'))) {
            return substr($name, 0, $pos);
        } else {
            return null;
        }
    }
}
