<?php
class Smsglobal_PostAlert
{
    public function __construct()
    {
        add_action('publish_post', array($this, 'sendPostAlert'));
    }

    public function sendPostAlert($id)
    {
        if (get_option('smsglobal_enable_post_alerts')
            && $_POST['post_status'] === 'publish'
            && $_POST['original_post_status'] !== 'publish') {
            // Get users with alerts enabled
            $users = $this->getDestinationNumbers();

            if (empty($users)) {
                // No users have alerts enabled
                return;
            }

            $post = get_post($id);

            $message = sprintf(
                'New post at %s: %s See it at %s',
                get_bloginfo('name'),
                get_the_title($post),
                get_permalink($post->ID)
            );

            $origin = get_option('smsglobal_post_alerts_origin');

            $apiKey = new Smsglobal\RestApiClient\ApiKey(
                get_option('smsglobal_api_key'),
                get_option('smsglobal_api_secret')
            );
            $rest = new Smsglobal\RestApiClient\RestApiClient($apiKey);

            // Send the SMS
            $sms = new Smsglobal\RestApiClient\Resource\Sms();
            $sms->setOrigin($origin)
                ->setMessage($message);

            foreach ($users as $msisdn) {
                try {
                    $sms->setDestination($msisdn)
                        ->send($rest);
                } catch (\Smsglobal\RestApiClient\Exception\InvalidDataException $ex) {

                    die;
                }
            }
        }
    }

    protected function getDestinationNumbers()
    {
        global $wpdb;

        $query = 'SELECT m1.meta_value FROM ' . $wpdb->usermeta . ' m1
            JOIN ' . $wpdb->usermeta . ' m2 ON (m1.user_id = m2.user_id AND
            m2.meta_key = "send_post_alerts" AND m2.meta_value = "1")
            WHERE m1.meta_key = "mobile"';

        return $wpdb->get_col($query, 0);
    }
}
