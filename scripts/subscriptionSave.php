<?php
session_start();

$abspath = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

require_once($abspath .'/wp-config.php');
require_once($abspath .'/wp-load.php');
require_once($abspath .'/wp-includes/general-template.php');

$dir = dirname(__FILE__);
$pluginPath = plugin_dir_url(__FILE__);

require $dir . '/../src/require.php';

$name = $_POST['name'];
$mobile = $_POST['mobile'];
$email = $_POST['email'];
$url = $_POST['url'];
$output = array('error' => 0, 'msg' => '');

if( !empty($name) && !empty($mobile) )
{
    $errors = smsglobal_insert_subscription($name, $mobile, $url, $email);

    if (empty($errors)) {

        $rest = Smsglobal_Utils::getRestClient();
        $from = trim(get_option('smsglobal_post_alerts_origin'));

        ///Post alert origin is not found USE site name
        if(strlen($from) < 1) {
            $from = get_bloginfo('name');
            //strict site name to 11 characters
            if(strlen($from) > 11) {
                $from = substr($from, 0, 11);
            }
        }

        //blog name is not found
        if(strlen($from) < 1) {
            $from = get_option( 'smsglobal_auth_origin' );
        }

        //if no auth origin is set, use default
        if(strlen($from) < 1) {
            $from = 'pool:1';
        }

        $verificationCode = Smsglobal_Utils::getVerificationCode($mobile);
        smsglobal_insert_verification($verificationCode, $mobile);
        $sms = new Smsglobal_RestApiClient_Resource_Sms();
        $smsText = __('Subscription Activation Code: %s', SMSGLOBAL_TEXT_DOMAIN);
        $smsText = sprintf($smsText, $verificationCode);
        $sms->setOrigin($from)
            ->setMessage($smsText);
        try {
            $sms->setDestination($mobile)
                ->send($rest);
        } catch (Smsglobal_RestApiClient_Exception_InvalidDataException $ex) {
            $output['error'] = 1;
            $output['msg'] = __('Unable to send verification code to this mobile number', SMSGLOBAL_TEXT_DOMAIN);
            echo json_encode($output);
            exit();
        }
    } else {
        $output['error'] = 1;
        $output['msg'] = __('Mobile number already exists in subscription list.', SMSGLOBAL_TEXT_DOMAIN);
        echo json_encode($output);
        exit();
    }

    $output['msg'] = __('We have sent a verification code to your mobile.', SMSGLOBAL_TEXT_DOMAIN);
}
else
{
    $output['msg'] = __('Name or Mobile are invalid.', SMSGLOBAL_TEXT_DOMAIN);
}

echo json_encode($output);
