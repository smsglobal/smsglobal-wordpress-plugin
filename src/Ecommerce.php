<?php
class Smsglobal_Ecommerce
{
    public function __construct()
    {
        if (get_option('smsglobal_ecommerce_enabled')) {
            add_action('wpsc_submit_checkout', array($this, 'newOrderSms'), 10);
        }
    }

    public function newOrderSms($logId)
    {
        global $wpdb;

        $query = 'SELECT * FROM {$wpdb->prefix}wpsc_purchase_logs WHERE id = %d';
        $purchaseData = array('purchlog_id' => $logId);
        $purchaseData['purchlog_data'] = $wpdb->get_row($wpdb->prepare($query, $logId), ARRAY_A);

        $origin = get_option('smsglobal_ecommerce_origin');

        $price = wpsc_currency_display(
            $purchaseData['purchlog_data']['totalprice'],
            array(
                'display_as_html' => false,
                'display_currency_symbol' => true,
            )
        );
        $message = 'Order #%s placed for %s';
        $message = sprintf($message, $logId['purchase_log_id'], $price);

        // Send the message
        $destination = get_option('smsglobal_ecommerce_destination');

        $rest = Smsglobal::getRestClient();

        $sms = new Smsglobal\RestApiClient\Resource\Sms();

        $sms->setOrigin($origin)
            ->setMessage($message)
            ->setDestination($destination);

        try {
            $sms->send($rest);
        } catch (\Smsglobal\RestApiClient\Exception\InvalidDataException $ex) {
            foreach ($ex->getErrors() as $field => $error) {
                echo sprintf('%s: %s', $field, $error), PHP_EOL;
            }
        }
    }
}
