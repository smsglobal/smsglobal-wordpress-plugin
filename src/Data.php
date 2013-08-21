<?PHP
global $smsglobal_db_version;
$smsglobal_db_version = "2.1";

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
        verified TINYINT(1) DEFAULT 0 NOT NULL
        UNIQUE KEY id (id)
    );";


    $verfication_table_name = $wpdb->prefix . "sms_verification";
    $sql .= "CREATE TABLE $verfication_table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        created_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        activated_time datetime DEFAULT '0000-00-00 00:00:00' NULL,
        code tinytext NOT NULL,
        mobile VARCHAR(20) NOT NULL,
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

function smsglobal_mark_subscription_verified($mobile) {
    global $wpdb;
    $table_name = $wpdb->prefix . "sms_subscription";

    $wpdb->update(
        $table_name,
        array(
            'verified' => 1,
        ),
        array( 'mobile' => $mobile ),
        null,
        null
    );
}


function smsglobal_get_subscription($name = null, $mobile = null) {
    global $wpdb;
    $table_name = $wpdb->prefix . "sms_subscription";

    $query = "SELECT * FROM $table_name";
    $query .= ($name != null) ? " WHERE (`name` LIKE '%$name%' OR `name` LIKE '%$name%' OR `name` LIKE '%$name%') " : "";
    if ($mobile != null) {
        $query .= $name != null ? " AND " : " WHERE ";
        $query .= " (`name` LIKE '%$name%' OR `name` LIKE '%$name%' OR `name` LIKE '%$name%') ";
    }
    $query .= " ORDER BY `time` DESC";
    return $wpdb->get_results($query);
}

function smsglobal_insert_verification($code, $mobile) {
    global $wpdb;
    $table_name = $wpdb->prefix . "sms_verification";

    $rows_affected = $wpdb->insert( $table_name,
        array(
            'created_time' => current_time('mysql'),
            'mobile' => $mobile,
            'code' => $code
        )
    );
}

function smsglobal_verify($code, $mobile) {
    global $wpdb;
    $table_name = $wpdb->prefix . "sms_verification";

    $rows_affected = $wpdb->get_results(
        "SELECT * FROM $table_name WHERE `mobile` = '$mobile' AND `code`= '$code' AND activated != 1 LIMIT 1"
    );
    return count($rows_affected) > 0;
}
