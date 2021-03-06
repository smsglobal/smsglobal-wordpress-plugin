<?php
class Smsglobal_Ecommerce
{
    /**
     * Constructor
     */
    public function __construct()
    {
        if (get_option('smsglobal_ecommerce_enabled')) {
            add_action('wpsc_submit_checkout', array($this, 'newOrderSms'), 10);
        }
    }

    /**
     * Sends an SMS when a new order is placed
     *
     * @param array $logInfo
     */
    public function newOrderSms($logInfo)
    {
        $logId = (int) $logInfo['purchase_log_id'];
        $price = $this->getTotalPrice($logId);
        $message = $this->getMessage($logId, $price);

        $this->sendSms($message);
    }

    /**
     * Gets the total price of an order log with the currency symbol
     *
     * @param int $logId
     * @return string
     */
    protected function getTotalPrice($logId)
    {
        global $wpdb;

        $query = 'SELECT totalprice FROM ' . $wpdb->prefix . 'wpsc_purchase_logs WHERE id = %d';
        $totalPrice = $wpdb->get_col($wpdb->prepare($query, $logId));
        $totalPrice = $totalPrice[0];

        return wpsc_currency_display(
            $totalPrice,
            array(
                'display_as_html' => false,
                'display_currency_symbol' => true,
            )
        );
    }

    /**
     * Sends the SMS using the specified message
     *
     * @param string $message
     */
    protected function sendSms($message)
    {
        // Send the message
        $rest = Smsglobal_Utils::getRestClient();
        $sms = new Smsglobal_RestApiClient_Resource_Sms();

        $origin = get_option('smsglobal_ecommerce_origin');
        $destination = get_option('smsglobal_ecommerce_destination');

        $sms->setOrigin($origin)
            ->setMessage($message)
            ->setDestination($destination);

        try {
            $sms->send($rest);
        } catch (Smsglobal_RestApiClient_Exception_InvalidDataException $ex) {
            foreach ($ex->getErrors() as $field => $error) {
                echo sprintf('%s: %s', $field, $error), PHP_EOL;
            }
        }
    }

    /**
     * Generates the message to send via SMS
     *
     * @param int $logId
     * @param string $price
     * @return string
     */
    protected function getMessage($logId, $price)
    {
        $message = Smsglobal_Utils::_('Order #%s placed for %s');
        $message = sprintf($message, $logId, $price);

        return $message;
    }
}
