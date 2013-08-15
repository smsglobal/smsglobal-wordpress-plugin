<?php
$files = array(
    'Smsglobal.php',
    'SettingsPage.php',
);

// For PHP < 5.3, we can't use __DIR__
$dir = dirname(__FILE__);

foreach ($files as $file) {
    require sprintf('%s/%s', $dir, $file);
}

unset($files, $file, $dir);
