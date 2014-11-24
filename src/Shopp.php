<?php
class Smsglobal_Shopp
{
    /**
     * Constructor
     */
    public function __construct()
    {
        if (get_option('smsglobal_shopp_enabled')) {
            add_action('shopp_order_success', array($this, 'sendSms'));
        }
    }

    /**
     * Sends an SMS for the given purchase
     */
    public function sendSms($purchase)
    {
        $origin = get_option('smsglobal_shopp_origin');
        $destination = get_option('smsglobal_shopp_destination');

        $message = $this->getMessage($purchase);

        $rest = Smsglobal_Utils::getRestClient();

        $sms = new Smsglobal_RestApiClient_Resource_Sms();
        $sms->setOrigin($origin)
            ->setDestination($destination)
            ->setMessage($message)
            ->send($rest);
    }

    /**
     * Gets the SMS message template for a given purchase
     *
     * @param $purchase
     * @return string
     */
    protected function getMessage($purchase)
    {
        /* translators: Shopp plugin new order received alert message format.*/
        $message = sprintf(
            __("Customer: %s %s\nEmail: %s\nDestination: %s, %s, %s\nTotal: $%s", SMSGLOBAL_TEXT_DOMAIN),
            $purchase->firstname,
            $purchase->lastname,
            $purchase->email,
            $purchase->shipcity,
            $purchase->shipstate,
            $purchase->shipcountry,
            $purchase->total
        );

        return $message;
    }
}
