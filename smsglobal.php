<?php
/*
Plugin Name: SMSGlobal
Plugin URI: http://www.smsglobal.com/
Description: Send SMS with SMSGlobal
Version: 2.0.0
Author: SMSGlobal
Author URI: http://www.smsglobal.com/
Text Domain: smsglobal
License: MIT
*/
$dir = dirname(__FILE__);

require $dir . '/src/require.php';
require $dir . '/vendor/rest-api-client-php-5.2/Smsglobal/Autoloader.php';
require $dir . '/vendor/smsglobal-version-manager/SMSGlobal/VersionManager.php';

define('SMSGLOBAL_TEXT_DOMAIN', 'smsglobal');

Smsglobal_Autoloader::register();
$vm = SMSGlobal\VersionManager\VersionManager::getInstance();

// Set up
register_activation_hook(__FILE__, 'smsglobal_install');
register_activation_hook(__FILE__, 'smsglobal_install_data');

load_plugin_textdomain(SMSGLOBAL_TEXT_DOMAIN, false, basename($dir) . '/languages');

new Smsglobal_SettingsPage();
new Smsglobal_SmsPage();
new Smsglobal_ListPage();

// Incomplete
//new Smsglobal_GroupPage();

new Smsglobal_UserList();
new Smsglobal_PostAlert();
new Smsglobal_Authentication();

// Integration with other plugins
$vm->setPluginVersion('wp-e-commerce', Smsglobal_Utils::getPluginVersion('wp-e-commerce/wp-shopping-cart.php'));
if($vm->isAvailable('wp-e-commerce')) {
	new Smsglobal_Ecommerce();
}

$vm->setPluginVersion('shopp', Smsglobal_Utils::getPluginVersion('shopp/Shopp.php'));
if($vm->isAvailable('shopp')) {
	new Smsglobal_Shopp();
}

// Clean up the global namespace
unset($dir);
