<?php
/*
Plugin Name: SMSGlobal
Plugin URI: http://www.smsglobal.com/
Description: Send SMS with SMSGlobal
Version: 2.0.0
Author: SMSGlobal
Author URI: http://www.smsglobal.com/
License: MIT
*/
$dir = dirname(__FILE__);

require $dir . '/src/require.php';
require $dir . '/vendor/autoload.php';

$settingsPage = new Smsglobal_SettingsPage();
load_plugin_textdomain('smsglobal', false, basename($dir) . '/languages');

// Clean up the global namespace
unset($dir, $settingsPage);
