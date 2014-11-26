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
            add_action('wpsc_edit_order_status', array($this, 'orderUpdateSms'), 10);
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
     * Sends an SMS when a order status is changed
     *
     * @param array $logInfo
     */
    public function orderUpdateSms($logInfo)
    {
        $logId = (int) $logInfo['purchlog_id'];
        $statusLabel = $this->getOrderStatusLabel( $logInfo['new_status'] );
        $message = $this->getOrderStatusMessage($logId, $statusLabel);
        $this->sendSms($message);
    }

    /**
     * Gets the Status label for order status
     *
     * @param int $statusNumber
     * @return string
     */
    public  function getOrderStatusLabel( $statusNumber ) {
        global $wpsc_purchlog_statuses;
        foreach ( $wpsc_purchlog_statuses as $status ) {
            if ( $statusNumber == $status[ 'order' ] ) {
                return $status[ 'label' ];
            }
        }
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
        /* translators: WP eCommerce order received alert message format.*/
        $message = __('Order #%s placed for %s', SMSGLOBAL_TEXT_DOMAIN);
        $message = sprintf($message, $logId, $price);

        return $message;
    }

    /**
     * Generates the order status message to send via SMS
     *
     * @param int $logId
     * @param string $price
     * @return string
     */
    protected function getOrderStatusMessage($logId, $statusLabel)
    {
        /* translators: WP eCommerce order status changed alert message format.*/
        $message = __('Order #%s status changed to %s', SMSGLOBAL_TEXT_DOMAIN);
        $message = sprintf($message, $logId, $statusLabel);

        return $message;
    }
}
