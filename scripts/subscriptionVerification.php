<?php
session_start();

$abspath = dirname(__FILE__);
$abspath_1 = str_replace('wp-content/plugins/smsglobal/scripts', '', $abspath);
$abspath_1 = str_replace('wp-content\plugins\smsglobal\scripts', '', $abspath_1);

require_once($abspath_1 .'wp-config.php');
$dir = dirname(__FILE__);

$code = $_POST['code'];
$mobile = $_POST['mobile'];

if( !empty($code) && !empty($mobile) )
{
    if(smsglobal_verify($code, $mobile)) {
        echo "Your subscription has been verified sucessfully.";
        smsglobal_mark_subscription_verified($mobile);
    } else {
        echo "Verification code is incorrect.";
    }
}
else
{
    echo 'Please provide mobile number and verification code.';
}

?>