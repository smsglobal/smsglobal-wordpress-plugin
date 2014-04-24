<?php
class Smsglobal_Authentication
{
    /**
     * The number of digits in the verification code. The code will always be
     * 1xxx - 9yyy where x = 0 and y = 9, repeated so the code length is as set
     * @var int
     */
    protected $codeLength = 4;

    /**
     * Constructor
     */
    public function __construct()
    {
        if (!get_option('smsglobal_enable_auth')) {
            return;
        }

        // Config override
        if (defined('SMSGLOBAL_AUTH')) {
            if (SMSGLOBAL_AUTH === false) {
                return;
            }
        }

        add_action('clear_auth_cookie', array($this, 'clearCode'));
        add_action('admin_init', array($this, 'handleAuth'));
    }

    /**
     * Sends the login code to the user if they have a mobile number set
     *
     * @param WP_User $user
     */
    public function sendCode(WP_User $user)
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
        $message = $this->getMessage($code);

        $code = $this->hashCode($code, $user);
        update_user_meta($user->ID, 'smsglobal_auth_code', $code);

        $this->sendSms($message, $mobile);
    }

    /**
     * Handles the login request, showing a prompt for the SMS code if needed
     */
    public function handleAuth()
    {
        $user = wp_get_current_user();
        $actualCode = get_user_meta($user->ID, 'smsglobal_auth_code', true);

        if (isset($_POST['code'])) {
            // Code entered. Compare it to the saved one
            $code = $this->hashCode($_POST['code'], $user);

            if ($actualCode === $code) {
                // Login successful! Clear the code and continue as normal
                $this->clearCode();

                return;
            }
        }

        if ($actualCode) {
            // The user has a code but hasn't filled out the form yet. Show it
            echo Smsglobal_Utils::renderTemplate('sms-code-form');
            die;
        }
    }

    /**
     * Generates the SMS code
     *
     * @return int
     */
    protected function generateCode()
    {
        // 1000
        $start = pow(10, $this->codeLength - 1);
        // 9999
        $end = $start * 10 - 1;

        return mt_rand($start, $end);
    }

    /**
     * Hashes the SMS code for storage using the username as a salt
     *
     * @param int     $code
     * @param WP_User $user
     * @return string
     */
    protected function hashCode($code, WP_User $user)
    {
        return sha1($code . $user->user_login);
    }

    /**
     * Clears the code data for the current user, so it no longer asks for it
     */
    public function clearCode()
    {
        $user = wp_get_current_user();
        delete_user_meta($user->ID, 'smsglobal_auth_code');
    }

    /**
     * @param $code
     * @return string
     */
    protected function getMessage($code)
    {
        $message = sprintf(
            Smsglobal_Utils::_('Your SMS code for %s is %s.'),
            get_bloginfo('name'),
            $code
        );

        return $message;
    }

    /**
     * @param $message
     * @param $mobile
     */
    protected function sendSms($message, $mobile)
    {
        // Send the message
        $rest = Smsglobal_Utils::getRestClient();
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
}
