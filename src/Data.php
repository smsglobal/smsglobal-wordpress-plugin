<?PHP
global $smsglobal_db_version;
$smsglobal_db_version = "1.0";

function smsglobal_install() {
    global $wpdb;
    global $smsglobal_db_version;

    $table_name = $wpdb->prefix . "sms_subscription";

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        name tinytext NOT NULL,
        mobile VARCHAR(20) NOT NULL,
        email VARCHAR(100) DEFAULT '' NOT NULL,
        url VARCHAR(100) DEFAULT '' NOT NULL,
        UNIQUE KEY id (id)
    );";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );
    add_option( "smsglobal_db_version", $smsglobal_db_version );
}

function smsglobal_install_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . "sms_subscription";

    $rows_affected = $wpdb->insert( $table_name,
        array(
            'time' => current_time('mysql'),
            'name' => 'IAmWillingToReceiveSpamSMS',
            'mobile' => '0481267486',
        )
    );
}

function smsglobal_insert_subscription($name, $mobile, $url, $email) {
    global $wpdb;
    $table_name = $wpdb->prefix . "sms_subscription";

    $rows_affected = $wpdb->insert( $table_name,
        array(
            'time' => current_time('mysql'),
            'name' => $name,
            'mobile' => $mobile,
            'url' => $url,
            'email' => $email,
        )
    );
}