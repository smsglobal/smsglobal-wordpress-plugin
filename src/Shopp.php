<?php
class Smsglobal_Shopp
{
    public function __construct()
    {
        add_action('shopp_order_success', array($this, 'sendSms'));
    }

    public function sendSms($Purchase)
    {
        if (!get_option('smsglobal_shopp_enabled')) {
            return;
        }

        $origin = get_option('smsglobal_shopp_origin');
        $destination = get_option('smsglobal_shopp_destination');

        $message = "Customer - $Purchase->firstname $Purchase->lastname \r\n Email - $Purchase->email \r\n Destination - $Purchase->shipcity, $Purchase->shipstate, $Purchase->shipcountry \r\n Total - $$Purchase->total";

        $rest = Smsglobal::getRestClient();

        $sms = new Smsglobal_RestApiClient_Resource_Sms();
        $sms->setOrigin($origin)
            ->setDestination($destination)
            ->setMessage($message)
            ->send($rest);
    }
}
