<?php
session_start();

$abspath = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

require_once($abspath .'/wp-config.php');
require_once($abspath .'/wp-load.php');

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