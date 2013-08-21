<?php
class Smsglobal_PostAlert
{
    public function __construct()
    {
        if (get_option('smsglobal_enable_post_alerts')) {
            add_action('publish_post', array($this, 'sendPostAlert'));
            add_action('add_meta_boxes', array($this, 'addMetaBox'));
        }
    }

    public function addMetaBox()
    {
        global $wp_version;

        $isPre27 = -1 === version_compare($wp_version, '2.7')
            || -1 === version_compare($wp_version, '2.7.0');

        add_meta_box(
            'smsglobal-post-alerts',
            Smsglobal::_('SMS Post Alerts'),
            array($this, 'getMetaBox'),
            'post',
            // side was added on 2.7
            $isPre27 ? 'advanced' : 'side',
            'high'
        );
    }

    public function getMetaBox()
    {
        $toOptions = Smsglobal::getRoles();
        ?>
        <p><em>Post alerts</em></p>
        <p><input checked="checked" name="smsglobal_post_alerts" type="checkbox" value="1"> Enabled</p>
        <p><em>Send to</em></p>
        <p><select class="tags-input" name="smsglobal_post_alerts_to">
        <?php foreach ($toOptions as $value => $label): ?>
            <option value="<?php echo esc_attr($value) ?>"><?php echo esc_html($label) ?></option>
        <?php endforeach ?>
        </select><br></p>
        <?php
    }

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

            $message = sprintf(
                Smsglobal::_('New post at %s: %s See it at %s'),
                get_bloginfo('name'),
                get_the_title($post),
                get_permalink($post->ID)
            );

            $origin = get_option('smsglobal_post_alerts_origin');

            $rest = Smsglobal::getRestClient();

            // Send the SMS
            $sms = new Smsglobal_RestApiClient_Resource_Sms();
            $sms->setOrigin($origin)
                ->setMessage($message);

            foreach ($users as $msisdn) {
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

    protected function getDestinationNumbers($role)
    {
        global $wpdb;


        $prefix = $wpdb->get_blog_prefix(get_current_blog_id());

        $query = 'SELECT m1.meta_value FROM ' . $wpdb->usermeta . ' m1
            JOIN ' . $wpdb->usermeta . ' m2 ON (m1.user_id = m2.user_id AND
            m2.meta_key = "smsglobal_send_post_alerts" AND m2.meta_value = "1")';

        if ('all' !== $role) {
            $query .= ' JOIN ' . $wpdb->usermeta . ' m3 ON (m1.user_id = m3.user_id AND
                m3.meta_key = "' . $prefix . 'capabilities" AND
                CAST(m3.meta_value AS CHAR) LIKE "%%\\"%s\\"%%")';

            $query = sprintf($query, $wpdb->_real_escape($role));
        }

        $query .= ' WHERE m1.meta_key = "mobile"';

        return $wpdb->get_col($query, 0);
    }
}
