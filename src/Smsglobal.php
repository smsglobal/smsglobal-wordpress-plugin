<?php
class Smsglobal
{
    public static function _($string)
    {
        return __($string, 'smsglobal');
    }
}
