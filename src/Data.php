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
        verified TINYINT(1) DEFAULT 0 NOT NULL,
        UNIQUE KEY id (id)
    ) COLLATE='utf8_general_ci' ;";


    $verfication_table_name = $wpdb->prefix . "sms_verification";
    $sql .= "CREATE TABLE $verfication_table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        created_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        activated_time datetime DEFAULT '0000-00-00 00:00:00' NULL,
        code tinytext NOT NULL,
        mobile VARCHAR(20) NOT NULL,
        UNIQUE KEY id (id),
        UNIQUE KEY mobile (mobile)
    ) COLLATE='utf8_general_ci' ;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option( "smsglobal_db_version", $smsglobal_db_version );
}

function smsglobal_install_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . "sms_subscription";

    require_once( ABSPATH . 'wp-includes/pluggable.php' );
    $currentUserId = get_current_user_id();
    $authorMobile = get_user_meta($currentUserId, 'mobile', true);

    $checkExistArr = $wpdb->get_col($wpdb->prepare("SELECT count(1) AS count FROM `{$table_name}` WHERE `mobile` = '%s'", $authorMobile));

    if($checkExistArr[0] < 1) {
        $rows_affected = $wpdb->insert( $table_name,
            array(
                'time' => current_time('mysql'),
                'name' => 'Author Origin',
                'mobile' => $authorMobile,
				'verified' => 1
            )
        );
    }
}

function smsglobal_insert_subscription($name, $mobile, $url = null, $email = null) {
    global $wpdb;
    $table_name = $wpdb->prefix . "sms_subscription";

    $checkExistArr = $wpdb->get_col($wpdb->prepare("SELECT count(1) AS count FROM `{$table_name}` WHERE `mobile` = '%s'", $mobile));

    if($checkExistArr[0]< 1) {
        $rows_affected = $wpdb->insert( $table_name,
            array(
                'time' => current_time('mysql'),
                'name' => $name,
                'mobile' => $mobile,
                'url' => $url,
                'email' => $email,
            )
        );
    } else {
        return 1;
    }
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

    $table_name = $wpdb->prefix . "sms_verification";
    $wpdb->update(
        $table_name,
        array(
            'activated_time' => current_time('mysql')
        ),
        array( 'mobile' => $mobile ),
        null,
        null
    );
}

function smsglobal_is_subscription_verified($mobile) {
    global $wpdb;
    $table_name = $wpdb->prefix . "sms_subscription";
    $verified = $wpdb->query($wpdb->prepare("SELECT verified FROM `{$table_name}` WHERE `mobile` = '%s'", $mobile));
    if($verified == 1) {
        return true;
    }
    return false;
}

function smsglobal_get_subscription($name = null, $mobile = null, $mobileOnly = false) {
    global $wpdb;
    $table_name = $wpdb->prefix . "sms_subscription";

    $query = $mobileOnly ? "SELECT mobile FROM $table_name" : "SELECT * FROM $table_name";
    $query .= ($name != null) ? " WHERE (`name` LIKE '%$name%' OR `name` LIKE '%$name%' OR `name` LIKE '%$name%') " : "";
    if ($mobile != null) {
        $query .= $name != null ? " AND " : " WHERE ";
        $query .= " (`name` LIKE '%$name%' OR `name` LIKE '%$name%' OR `name` LIKE '%$name%') ";
    }
    $query .= " ORDER BY `time` DESC";
    return $mobileOnly ? $wpdb->get_col($query) : $wpdb->get_results($query);
}


function smsglobal_insert_verification($code, $mobile) {
    global $wpdb;
    $table_name = $wpdb->prefix . "sms_verification";

    $sql = "INSERT INTO $table_name (created_time, mobile, code) VALUES ('".current_time('mysql')."', '$mobile', '$code') ON DUPLICATE KEY UPDATE created_time = '". current_time('mysql')."', code = '$code'";
    $wpdb->query($sql);
}

function smsglobal_verify($code, $mobile) {
    global $wpdb;
    $table_name = $wpdb->prefix . "sms_verification";

    $query = "SELECT * FROM $table_name WHERE `code`= '$code'";
    if($mobile)
        $query .= " AND `mobile` = '$mobile'";
    $query .= " LIMIT 1";

    $rows_affected = $wpdb->get_results($query);
    return count($rows_affected) > 0;
}
