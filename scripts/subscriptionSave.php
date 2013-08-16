<?php
session_start();

$abspath = dirname(__FILE__);
$abspath_1 = str_replace('wp-content/plugins/smsglobal/scripts', '', $abspath);
$abspath_1 = str_replace('wp-content\plugins\smsglobal\scripts', '', $abspath_1);

require_once($abspath_1 .'wp-config.php');
$dir = dirname(__FILE__);

//require $dir . '/../src/require.php';
//require $dir . '/../vendor/autoload.php';

$name = $_POST['name'];
$mobile = $_POST['mobile'];
$email = $_POST['email'];
$url = $_POST['url'];

if( !empty($name) && !empty($mobile) )
{
    smsglobal_insert_subscription($name, $mobile, $url, $email);
//    We should enable this in the future to allow the plugin to send out email to the subscriber
//    if( !empty($email))
//    {
//        $sender_email = mysql_real_escape_string(trim($email));
//        $message = "You have subscribed successfully";
//
//        $message = preg_replace('|&[^a][^m][^p].{0,3};|', '', $message);
//        $message = preg_replace('|&amp;|', '&', $message);
//        $mailtext = wordwrap(strip_tags($message), 80, "\n");
//
//        $headers = "MIME-Version: 1.0" . "\r\n";
//        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
//        $headers .= "From: \"$sender_name\" <$sender_email>\n";
//        $headers .= "Return-Path: <" . mysql_real_escape_string(trim($email)) . ">\n";
//        $headers .= "Reply-To: \"" . mysql_real_escape_string(trim($name)) . "\" <" . mysql_real_escape_string(trim($email)) . ">\n";
//        //$headers .= "To: \"" . $On_MyEmail . "\" <" . $On_MyEmail . ">\n";
//        $mailtext = str_replace("\r\n", "<br />", $mailtext);
//        //@wp_mail($sender_email, $subject, $mailtext, $headers);
//        @wp_mail($On_MyEmail, $subject, $mailtext, $headers);
//    }
//
    echo "You have subscribed successfully.";
}
else
{
    echo 'Name and Mobile are invalid.';
}

?>