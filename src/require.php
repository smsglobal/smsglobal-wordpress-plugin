<?php
$smsglobal_files = array(
    'Authentication.php',
    'Data.php',
    'Ecommerce.php',
    'PostAlert.php',
    'SettingsPage.php',
    'Shopp.php',
    'Utils.php',
    'ListPage.php',
    'GroupPage.php',
    'SmsPage.php',
    'Widget.php',
    'UserList.php',
    'Settings/ApiKeys.php',
    'Settings/Authentication.php',
    'Settings/PostAlert.php',
    'Settings/WPecommerce.php',
    'Settings/Shopp.php'
);

// For PHP < 5.3, we can't use __DIR__
$smsglobal_dir = dirname(__FILE__);

foreach ($smsglobal_files as $smsglobal_file) {
    require_once sprintf('%s/%s', $smsglobal_dir, $smsglobal_file);
}

// Clean up the global namespace
unset($smsglobal_files, $smsglobal_file, $smsglobal_dir);
