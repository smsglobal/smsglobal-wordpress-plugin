<?php
class Smsglobal_PostAlert
{
    /**
     * Constructor
     */
    public function __construct()
    {
        if (get_option('smsglobal_enable_post_alerts')) {
            add_action('publish_post', array($this, 'sendPostAlert'));
            add_action('add_meta_boxes', array($this, 'addMetaBox'));
        }
    }

    /**
     * Adds a meta box in the post page for customising the post alerts
     */
    public function addMetaBox()
    {
        $isPre27 = $this->isPre27();

        add_meta_box(
            'smsglobal-post-alerts',
            __('SMS Post Alerts', SMSGLOBAL_TEXT_DOMAIN),
            array($this, 'getMetaBox'),
            'post',
            // 'side' was added on 2.7
            $isPre27 ? 'advanced' : 'side',
            'high'
        );
    }

    /**
     * Prints (!) the HTML for the meta box
     */
    public function getMetaBox()
    {
        $toOptions = Smsglobal_Utils::getRoles();
        ?>
        <p><em><?php _e('Post Alerts', SMSGLOBAL_TEXT_DOMAIN) ?></em></p>
        <p><input checked="checked" name="smsglobal_post_alerts" type="checkbox" value="1"> <?php _e('Enabled', SMSGLOBAL_TEXT_DOMAIN) ?></p>
        <p><em><?php _e('Send To', SMSGLOBAL_TEXT_DOMAIN) ?></em></p>
        <p><select class="tags-input" name="smsglobal_post_alerts_to">
        <?php foreach ($toOptions as $value => $label): ?>
            <option value="<?php echo esc_attr($value) ?>"><?php echo esc_html($label) ?></option>
        <?php endforeach ?>
        </select><br></p>
        <?php
    }

    /**
     * Sends the SMS post alert for a given post ID
     *
     * @param int $id
     */
    public function sendPostAlert($id)
    {
        if (get_option('smsglobal_enable_post_alerts')
            && !empty($_POST['smsglobal_post_alerts'])
            && $_POST['post_status'] === 'publish'
            && $_POST['original_post_status'] !== 'publish') {
            // Get users with alerts enabled
            $role = isset($_POST['smsglobal_post_alerts_to']) ? $_POST['smsglobal_post_alerts_to'] : 'all';
            $users = $this->getDestinationNumbers($role);

            if (empty($users)) {
                // No users have alerts enabled
                return;
            }

            $post = get_post($id);

            $message = $this->getMessage($post);

            $origin = get_option('smsglobal_post_alerts_origin');

            $rest = Smsglobal_Utils::getRestClient();

            // Send the SMS
            $sms = new Smsglobal_RestApiClient_Resource_Sms();
            $sms->setOrigin($origin)
                ->setMessage($message);

            foreach ($users as $msisdn) {
                if(!$msisdn) continue;

                try {
                    $sms->setDestination($msisdn)
                        ->send($rest);
                } catch (Smsglobal_RestApiClient_Exception_InvalidDataException $ex) {
                    foreach ($ex->getErrors() as $field => $error) {
                        echo sprintf('%s: %s', $field, $error), PHP_EOL;
                    }
                }
            }
        }
    }

    /**
     * Gets destination numbers for a given role
     *
     * @param string $role
     * @return array
     */
    protected function getDestinationNumbers($role)
    {
        global $wpdb;

        if($role == 'sms') {
            return smsglobal_get_subscription(null, null, true);
        } else {
            $prefix = $wpdb->get_blog_prefix(get_current_blog_id());

            $query = 'SELECT m1.meta_value FROM ' . $wpdb->usermeta . ' m1
            JOIN ' . $wpdb->usermeta . ' m2 ON (m1.user_id = m2.user_id AND
            m2.meta_key = "smsglobal_send_post_alerts" AND m2.meta_value = "1")';

            if ('all' !== $role) {
                $query .= ' JOIN ' . $wpdb->usermeta . ' m3 ON (m1.user_id = m3.user_id AND
                m3.meta_key = "' . $prefix . 'capabilities" AND
                CAST(m3.meta_value AS CHAR) LIKE "%%\\"%s\\"%%")';

                $query = sprintf($query, $wpdb->_real_escape($role));


                $query .= ' WHERE m1.meta_key = "mobile"';

                return $wpdb->get_col($query, 0);
            } else {
                $query .= ' WHERE m1.meta_key = "mobile"';

                $all = $wpdb->get_col($query, 0);
                $sms_subscribers = smsglobal_get_subscription(null, null, true);
                return array_merge($all, $sms_subscribers);
            }

        }
    }

    /**
     * Determines whether the WordPress version is < 2.7
     *
     * @return bool
     */
    protected function isPre27()
    {
        global $wp_version;

        return -1 === version_compare($wp_version, '2.7')
            && -1 === version_compare($wp_version, '2.7.0');
    }

    /**
     * @param WP_Post $post
     * @return string
     */
    protected function getMessage(WP_Post $post)
    {
        /* translators: New post alert message format.*/
        $message = sprintf(
            __('New post at %s: %s. See it at %s', SMSGLOBAL_TEXT_DOMAIN),
            get_bloginfo('name'),
            get_the_title($post),
            get_permalink($post->ID)
        );

        return $message;
    }
}
