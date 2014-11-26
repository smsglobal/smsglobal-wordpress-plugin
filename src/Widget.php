<?php
class Smsglobal_Subscription_Widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct(
            'smsglobal_subscription_widget',
            __('SMSGlobal Widget', SMSGLOBAL_TEXT_DOMAIN),
            array(
                'description' => __('Display subscription input box so user can enter their mobile number to receive notification SMS.', SMSGLOBAL_TEXT_DOMAIN)
            )
        );

        if (is_active_widget(false, false, $this->id_base)) {
            add_action('wp_head', array($this, 'css'));
        }
    }

    function css() {
        echo "";
    }

    function form($instance)
    {
        if ($instance) {
            $form_title = esc_attr($instance['form_title']);
            $name_label = esc_attr($instance['name_label']);
            $mobile_label = esc_attr($instance['mobile_label']);
            $code_label = esc_attr($instance['code_label']);
            $submit_text = esc_attr($instance['submit_text']);
            $verify_text = esc_attr($instance['verify_text']);
        } else {
            $form_title = __('SMS Subscription', SMSGLOBAL_TEXT_DOMAIN);
            $name_label = __('Name', SMSGLOBAL_TEXT_DOMAIN);
            $mobile_label = __('Mobile', SMSGLOBAL_TEXT_DOMAIN);
            $code_label = __('Code', SMSGLOBAL_TEXT_DOMAIN);
            $submit_text = __('Subscribe', SMSGLOBAL_TEXT_DOMAIN);
            $verify_text = __('Verify', SMSGLOBAL_TEXT_DOMAIN);
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('form_title') ?>"><?php _e('Subscription Form Title:', SMSGLOBAL_TEXT_DOMAIN) ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('form_title') ?>" name="<?php echo $this->get_field_name('form_title') ?>" type="text" value="<?php echo $form_title ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('name_label') ?>"><?php _e('Name Label:', SMSGLOBAL_TEXT_DOMAIN) ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('name_label') ?>" name="<?php echo $this->get_field_name('name_label') ?>" type="text" value="<?php echo $name_label ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('mobile_label') ?>"><?php _e('Mobile Label:', SMSGLOBAL_TEXT_DOMAIN) ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('mobile_label') ?>" name="<?php echo $this->get_field_name('mobile_label') ?>" type="text" value="<?php echo $mobile_label ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('code_label') ?>"><?php _e('Verifying Code Label:', SMSGLOBAL_TEXT_DOMAIN) ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('code_label') ?>" name="<?php echo $this->get_field_name('code_label') ?>" type="text" value="<?php echo $code_label ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('submit_text') ?>"><?php _e('Submit Text:', SMSGLOBAL_TEXT_DOMAIN) ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('submit_text') ?>" name="<?php echo $this->get_field_name('submit_text') ?>" type="text" value="<?php echo $submit_text ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('verify_text') ?>"><?php _e('Verify Button Text:', SMSGLOBAL_TEXT_DOMAIN) ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('verify_text') ?>" name="<?php echo $this->get_field_name('verify_text') ?>" type="text" value="<?php echo $verify_text ?>" />
        </p>
        <?php
	}

    function update($new_instance, $old_instance)
    {
        $instance['form_title'] = strip_tags($new_instance['form_title']);
        $instance['name_label'] = strip_tags($new_instance['name_label']);
        $instance['mobile_label'] = strip_tags($new_instance['mobile_label']);
        $instance['code_label'] = strip_tags($new_instance['code_label']);
        $instance['submit_text'] = strip_tags($new_instance['submit_text']);
        $instance['verify_text'] = strip_tags($new_instance['verify_text']);

        return $instance;
    }

    function widget($args, $instance)
    {
        echo $args['before_widget'];
        if (!empty($instance['form_title'])) {
            echo $args['before_title'];
            echo esc_html($instance['form_title']);
            echo $args['after_title'];
        }
        $action_url = plugin_dir_url(__FILE__).'../scripts';
        ?>

    <div id="smsglobal_alertmessage"></div>
    <div id="subscription_wrapper">
        <form class="" id="subscription_form" method="POST" action="<?php echo $action_url; ?>/subscriptionSave.php">
        <div>
            <label for="name"><?php if ( ! empty( $instance['name_label'] ) ) echo $instance['name_label']; ?></label>
            <input class="widefat" id="name" name="name" type="text" value="" />
        </div>
        <div>
            <label for="mobile"><?php if ( ! empty( $instance['mobile_label'] ) ) echo $instance['mobile_label']; ?></label>
            <input class="widefat" id="mobile" name="mobile" type="text" value="" />
        </div>
        <div>
            <br><input id="subscription_submit" name="subscription_submit" type="submit" value="<?php echo $instance['submit_text'];?>"/>
        </div>
        </form>
        <br/>
        <form class="" id="subscription_verification_form" method="POST" action="<?php echo $action_url; ?>/subscriptionVerification.php" style="display: none;">
            <div>
                <label for="name"><?php if ( ! empty( $instance['mobile_label'] ) ) echo $instance['mobile_label']; ?></label>
                <input class="widefat" id="mobile_verify" name="mobile" type="text" value="" />
            </div>
            <div>
                <label for="code"><?php if ( ! empty( $instance['code_label'] ) ) echo $instance['code_label']; ?></label>
                <input class="widefat" id="code" name="code" type="text" value="" />
            </div>
            <div>
                <br><input id="subscription_verification_submit" name="subscription_verification_submit" type="submit" value="<?php echo $instance['verify_text'];?>"/>
            </div>
        </form>
    </div>
<?php
		echo $args['after_widget'];
	}
}

function smsglobal_add_javascript_files()
{
    if (!is_admin())
    {
        $assets_url = plugin_dir_url(__FILE__).'../assets';
        wp_enqueue_style( 'smsglobal', $assets_url."/smsglobal.css");
        wp_enqueue_script( 'smsglobal', $assets_url."/smsglobal.js",  array('jquery'));

        wp_localize_script( 'smsglobal', 'smsglobalL10n', array(
            'fullname' => __('Please enter your full name.', SMSGLOBAL_TEXT_DOMAIN),
            'mobilenumber' => __('Please enter your mobile number.', SMSGLOBAL_TEXT_DOMAIN),
            'verificationcode' => __('Please enter the verification code.', SMSGLOBAL_TEXT_DOMAIN),
            'verificationmobile' => __('Please enter your mobile for verification purpose.', SMSGLOBAL_TEXT_DOMAIN),
            'sending' => __('Sending...', SMSGLOBAL_TEXT_DOMAIN),
            'requestproblem' => __('There was a problem with the request.', SMSGLOBAL_TEXT_DOMAIN),
        ) );

    }
}

add_action('wp_enqueue_scripts', 'smsglobal_add_javascript_files');

function smsglobal_register_widgets()
{
    register_widget('Smsglobal_Subscription_Widget');
}

function show_subscription_widget()
{
    $args = array(
        'before_widget' => '<div class="box widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<div style="font-size:14px;font-weight:700;line-height:1.7;margin:0 0 24px;text-transform:uppercase">',
        'after_title'   => '</div>',
    );

    $instance['form_title'] = __('SMS Subscription', SMSGLOBAL_TEXT_DOMAIN);
    $instance['name_label'] = __('Name', SMSGLOBAL_TEXT_DOMAIN);
    $instance['mobile_label'] = __('Mobile', SMSGLOBAL_TEXT_DOMAIN);
    $instance['code_label'] = __('Code', SMSGLOBAL_TEXT_DOMAIN);
    $instance['submit_text'] = __('Subscribe', SMSGLOBAL_TEXT_DOMAIN);
    $instance['verify_text'] = __('Verify', SMSGLOBAL_TEXT_DOMAIN);

    ob_start();
    the_widget('Smsglobal_Subscription_Widget', $instance, $args );
    $output = ob_get_clean();

    return $output;
}

add_action('widgets_init', 'smsglobal_register_widgets');
add_shortcode('smsglobal', 'show_subscription_widget');
