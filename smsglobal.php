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
register_activation_hook( __FILE__, 'smsglobal_install' );
register_activation_hook( __FILE__, 'smsglobal_install_data' );

load_plugin_textdomain('smsglobal', false, basename($dir) . '/languages');

function modify_contact_methods($profile_fields) {

    // Add new fields
    $profile_fields['mobile'] = 'Mobile Phone Number';

    return $profile_fields;
}
add_filter('user_contactmethods', 'modify_contact_methods');

new Smsglobal_SettingsPage();
new Smsglobal_SmsPage();

// Clean up the global namespace
unset($dir);
