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

// Set up
register_activation_hook(__FILE__, 'smsglobal_install');
register_activation_hook(__FILE__, 'smsglobal_install_data');

load_plugin_textdomain('smsglobal', false, basename($dir) . '/languages');

new Smsglobal_SettingsPage();
new Smsglobal_SmsPage();
new Smsglobal_UserList();
new Smsglobal_PostAlert();

// Integration with other plugins
new Smsglobal_Shopp();
new Smsglobal_Ecommerce();

// Clean up the global namespace
unset($dir);
