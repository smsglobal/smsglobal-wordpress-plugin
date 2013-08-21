<?php
session_start();

$abspath = dirname(__FILE__);
$abspath_1 = str_replace('wp-content/plugins/smsglobal/scripts', '', $abspath);
$abspath_1 = str_replace('wp-content\plugins\smsglobal\scripts', '', $abspath_1);

require_once($abspath_1 .'wp-config.php');
$dir = dirname(__FILE__);

$code = $_REQUEST['code'];

if( !empty($code) && !empty($mobile) )
{
    $verificationCode = Smsglobal::getVerificationCode($mobile);
    if(smsglobal_verify($verificationCode, $mobile)) {
        smsglobal_mark_subscription_verified($mobile);
    } else {
        echo "Verification code is incorrect.";
    }
    echo "You have successfully subscribe to our SMS verification.";
}
else
{
    echo 'Please provide mobile number and verification code.';
}

?>