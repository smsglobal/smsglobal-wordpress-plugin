<?php
class Smsglobal_SmsPage
{
    protected $toOptions;

    public function __construct()
    {
        if (is_admin()) {
            add_action('admin_menu', array($this, 'addMenu'));
            add_action('admin_enqueue_scripts', array($this, 'addScript'));
        }
    }

    public function addMenu()
    {
        $title = __('SMS', SMSGLOBAL_TEXT_DOMAIN);
        add_menu_page($title, $title, 'manage_options', 'smsglobal', array($this, 'getPage'));
    }

    public function addScript($page)
    {
        if ($page === 'toplevel_page_smsglobal') {
            $url = plugins_url(
                'assets/admin.js',
                dirname(__FILE__)
            );
            wp_enqueue_script('smsglobal-admin', $url, array('jquery'));
        }
    }

    public function getPage()
    {
        $toOptions = $this->getToOptions();

        $errors = null;

        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $errors = $this->processForm($_POST);
        }

        $from = isset($_POST['from']) ? $_POST['from'] : get_option('smsglobal_post_alerts_origin');
        $to = isset($_POST['to']) ? $_POST['to'] : 'all';
        $number = isset($_POST['number']) ? $_POST['number'] : '';
        $message = isset($_POST['message']) ? $_POST['message'] : '';

        $isConfigured = $this->checkConfiguration();
        ?>
        <div class="wrap">
            <?php screen_icon() ?>
            <h2><?php _e('Send an SMS', SMSGLOBAL_TEXT_DOMAIN) ?></h2>
            <?php
            if (!$isConfigured):
                ?>
                <div class="updated" id="message">
                    <p><?php _e('Please configure your SMSGlobal settings first.', SMSGLOBAL_TEXT_DOMAIN) ?></p>

                    <p><a href="options-general.php?page=smsglobal-settings"><?php _e('Configure now', SMSGLOBAL_TEXT_DOMAIN) ?></a></p>
                </div>
                <?php
                return;
            endif;

            if (null !== $errors):
                ?>
                <div class="updated" id="message">
                <?php if (empty($errors)): ?>
                    <p>SMS sent!</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($errors as $field => $error): ?>
                            <li><?php echo esc_html(ucfirst($field)) ?>: <?php echo esc_html($error) ?></li>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>
                </div>
            <?php endif ?>
            <form action="admin.php?page=smsglobal" method="post">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label for="id_from"><?php _e('From', SMSGLOBAL_TEXT_DOMAIN) ?></label></th>
                        <td><input class="regular-text" id="id_from" name="from" type="text" value="<?php echo esc_attr($from) ?>"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="id_to"><?php _e('To', SMSGLOBAL_TEXT_DOMAIN) ?></label></th>
                        <td><select name="to" id="id_to">
                            <?php foreach ($toOptions as $value => $label): ?>
                                <option<?php if ($value === $to): ?> selected="selected"<?php endif ?> value="<?php echo esc_attr($value) ?>"><?php echo esc_html(__($label, SMSGLOBAL_TEXT_DOMAIN)) ?></option>
                            <?php endforeach ?>
                        </select>
                        <input class="regular-text" id="id_number" name="number" type="tel" value="<?php echo esc_attr($number) ?>"></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="id_message"><?php _e('Message', SMSGLOBAL_TEXT_DOMAIN) ?></label></th>
                        <td><textarea cols="50" id="id_message" rows="10" name="message" class="large-text"><?php echo esc_html($message) ?></textarea></td>
                    </tr>
                </table>

                <p class="submit">
                    <button class="button button-primary" type="submit"><?php _e('Send SMS', SMSGLOBAL_TEXT_DOMAIN) ?></button>
                </p>
            </form>
        </div>
        <?php
    }

    protected function checkConfiguration()
    {
        $key = get_option('smsglobal_api_key');
        $secret = get_option('smsglobal_api_secret');

        return false !== $key && false !== $secret;
    }

    protected function getToOptions()
    {
        if (null === $this->toOptions) {
            $this->toOptions = Smsglobal_Utils::getRoles();
            $this->toOptions['number'] = __('Number', SMSGLOBAL_TEXT_DOMAIN);
        }

        return $this->toOptions;
    }

    protected function processForm(array $data)
    {
        $errors = array();

        // From
        if (isset($data['from'])) {
            $from = (string) $data['from'];
            if ('' === $from) {
                $errors['from'] = __('This field is required', SMSGLOBAL_TEXT_DOMAIN);
            }
        } else {
            $errors['from'] = __('This field is required', SMSGLOBAL_TEXT_DOMAIN);
        }

        // To
        if (isset($data['to'])) {
            $to = (string) $data['to'];

            $toOptions = $this->getToOptions();
            if (!isset($toOptions[$to])) {
                $errors['to'] = __('Please select an option', SMSGLOBAL_TEXT_DOMAIN);
            } elseif ('number' === $to) {
                $to = (string) $data['number'];

                if (!ctype_digit($to)) {
                    $errors['to'] = __('Please enter a number', SMSGLOBAL_TEXT_DOMAIN);
                }
            }
        } else {
            $errors['to'] = __('This field is required', SMSGLOBAL_TEXT_DOMAIN);
        }

        // Message
        if (isset($data['message'])) {
            $message = (string) $data['message'];
        } else {
            $errors['message'] = __('This field is required', SMSGLOBAL_TEXT_DOMAIN);
        }

        if (empty($errors)) {
            // Do the send
            $to = $this->toValueToNumbers($to);

            $rest = Smsglobal_Utils::getRestClient();

            $sms = new Smsglobal_RestApiClient_Resource_Sms();
            $sms->setOrigin($from)
                ->setMessage($message);

            foreach ($to as $destination) {
                if(!$destination) continue;

                try {
                    $sms->setDestination($destination)
                        ->send($rest);
                } catch (Smsglobal_RestApiClient_Exception_InvalidDataException $ex) {
                    return $ex->getErrors();
                }
            }
        }

        return $errors;
    }

    protected function toValueToNumbers($to)
    {
        if ('all' === $to) {
            // Send to every user
            global $wpdb;

            // Couldn't find a way to do this with Wordpress's own features...
            $query = 'SELECT meta_value FROM ' . $wpdb->usermeta . '
            WHERE meta_key = "mobile"';

            $sms_subscribers = smsglobal_get_subscription(null, null, true);
            $all = $wpdb->get_col($query, 0);

            return array_merge($sms_subscribers, $all);
        } elseif ('sms' === $to) {
            return smsglobal_get_subscription(null, null, true);
        } elseif (ctype_digit($to)) {
            // It's a number
            return array($to);
        } else {
            // It's a role
            global $wpdb;

            $prefix = $wpdb->get_blog_prefix(get_current_blog_id());

            $query = 'SELECT m1.meta_value FROM ' . $wpdb->usermeta . ' m1
            JOIN ' . $wpdb->usermeta . ' m2 ON (m1.user_id = m2.user_id AND
            m2.meta_key = "' . $prefix . 'capabilities" AND
            CAST(m2.meta_value AS CHAR) LIKE "%%%s%%")
            WHERE m1.meta_key = "mobile"';
            $query = $wpdb->prepare($query, $to);

            return $wpdb->get_col($query, 0);
        }
    }
}
