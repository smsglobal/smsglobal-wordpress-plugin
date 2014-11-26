<?php
session_start();

$abspath = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

require_once($abspath .'/wp-config.php');
require_once($abspath .'/wp-load.php');

$dir = dirname(__FILE__);

$code = $_POST['code'];
$mobile = $_POST['mobile'];
$output = array('error' => 0, 'msg' => '');

if( !empty($code) && !empty($mobile) )
{
    if(smsglobal_verify($code, $mobile)) {
        $output['msg'] = __('Your subscription has been verified successfully.', SMSGLOBAL_TEXT_DOMAIN);
        smsglobal_mark_subscription_verified($mobile);
    } else {
        $output['error'] = 1;
        $output['msg'] = __('Verification code is incorrect.', SMSGLOBAL_TEXT_DOMAIN);
    }
}
else
{
    $output['error'] = 1;
    $output['msg'] = __('Please provide mobile number and verification code.', SMSGLOBAL_TEXT_DOMAIN);
}
