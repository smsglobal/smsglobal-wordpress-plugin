<?php
class Smsglobal_Shopp
{
    public function __construct()
    {
        add_action('shopp_order_success', array($this, 'sendSms'));
    }

    public function sendSms($purchase)
    {
        if (!get_option('smsglobal_shopp_enabled')) {
            return;
        }

        $origin = get_option('smsglobal_shopp_origin');
        $destination = get_option('smsglobal_shopp_destination');

        $message = sprintf(
            Smsglobal::_("Customer: %s %s\nEmail: %s\nDestination: %s, %s, %s\nTotal: $%s"),
            $purchase->firstname,
            $purchase->lastname,
            $purchase->email,
            $purchase->shipcity,
            $purchase->shipstate,
            $purchase->shipcountry,
            $purchase->total
        );

        $rest = Smsglobal::getRestClient();

        $sms = new Smsglobal_RestApiClient_Resource_Sms();
        $sms->setOrigin($origin)
            ->setDestination($destination)
            ->setMessage($message)
            ->send($rest);
    }
}
