<?php
session_start();

$abspath = dirname(__FILE__);
$abspath_1 = str_replace('wp-content/plugins/smsglobal/scripts', '', $abspath);
$abspath_1 = str_replace('wp-content\plugins\smsglobal\scripts', '', $abspath_1);

require_once($abspath_1 .'wp-config.php');
require_once($abspath_1 .'wp-includes/general-template.php');

$dir = dirname(__FILE__);

require $dir . '/../src/require.php';
//require $dir . '/../vendor/autoload.php';

$name = $_POST['name'];
$mobile = $_POST['mobile'];
$email = $_POST['email'];
$url = $_POST['url'];


if( !empty($name) && !empty($mobile) )
{
    smsglobal_insert_subscription($name, $mobile, $url, $email);

    if (empty($errors)) {

        $rest = Smsglobal::getRestClient();
        $from = get_bloginfo('name');
        if($from == '')
            $from = 'pool:1';
        $from = 'pool:1';
        $verificationCode = Smsglobal::getVerificationCode($mobile);
        smsglobal_insert_verification($verificationCode, $mobile);
        $sms = new Smsglobal_RestApiClient_Resource_Sms();
        $sms->setOrigin($from)
            ->setMessage('Subscription Activation Code: '.$verificationCode);
        try {
            $sms->setDestination($mobile)
                ->send($rest);
        } catch (Smsglobal_RestApiClient_Exception_InvalidDataException $ex) {
            echo "Unable to send verification code to this mobile number";
            exit();
        }
    }

    echo "We have sent you a verification code to your mobile.";
}
else
{
    echo 'Name or Mobile are invalid.';
}

?>