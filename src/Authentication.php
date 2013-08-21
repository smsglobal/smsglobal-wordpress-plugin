<?php
class Smsglobal_Authentication
{
    public function __construct()
    {
        if (!get_option('smsglobal_enable_auth')) {
            return;
        }

        // Config override
        if (defined('SMSGLOBAL_AUTH')) {
            if (constant('SMSGLOBAL_AUTH') === false) {
                return;
            }
        }

        add_action('clear_auth_cookie', array($this, 'clearCode'));
        add_action('wp_login', array($this, 'sendCode'), 10, 2);
        add_action('admin_init', array($this, 'handleAuth'));
    }

    /**
     * Sends the login code to the user if they have a mobile number set
     *
     * @param         $wp_login
     * @param WP_User $user
     */
    public function sendCode($wp_login, WP_User $user)
    {
        $mobile = get_user_meta($user->ID, 'mobile', true);

        if (!$mobile) {
            return;
        }

        $code = get_user_meta($user->ID, 'smsglobal_auth_code', true);

        if ($code) {
            // Already have a code
            return;
        }

        // Send them the code
        $code = $this->generateCode();
        $message = sprintf(
            Smsglobal::_('Your SMS code for %s is %s.'),
            get_bloginfo('name'),
            $code
        );

        $code = $this->hashCode($code, $user);
        update_user_meta($user->ID, 'smsglobal_auth_code', $code);
        update_user_meta($user->ID, 'smsglobal_auth_time', time());

        // Send the message
        $rest = Smsglobal::getRestClient();
        $sms = new Smsglobal_RestApiClient_Resource_Sms();
        $sms->setOrigin(get_option('smsglobal_auth_origin'))
            ->setMessage($message)
            ->setDestination($mobile);

        try {
            $sms->send($rest);
        } catch (Smsglobal_RestApiClient_Exception_InvalidDataException $ex) {
            foreach ($ex->getErrors() as $field => $error) {
                echo sprintf('%s: %s', $field, $error), PHP_EOL;
            }
        }
    }

    public function handleAuth()
    {
        $user = wp_get_current_user();

        // Have we entered a code?
        if (isset($_POST['code'])) {
            $actualCode = get_user_meta($user->ID, 'smsglobal_auth_code', true);
            $code = $this->hashCode($_POST['code'], $user);

            if ($actualCode === $code) {
                $this->clearCode();

                return;
            } else {
                $this->render_template(
                    'code-form',
                    array('error_message' => "Your code was entered incorrectly. Please try again.")
                );
                die();
            }
        }

        $code = get_user_meta($user->ID, 'smsglobal_auth_code', true);
        if ($code) {
            echo Smsglobal::renderTemplate('sms-code-form');
            die;
        }
    }

    protected function generateCode()
    {
        return mt_rand(1000, 9999);
    }

    protected function hashCode($code, WP_User $user)
    {
        return sha1($code . $user->user_login);
    }

    public function clearCode()
    {
        $user = wp_get_current_user();
        delete_user_meta($user->ID, 'smsglobal_auth_time');
        delete_user_meta($user->ID, 'smsglobal_auth_code');
    }
}
