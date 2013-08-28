<?php
/**
 * @author Huy Dinh <huy.dinh@smsglobal.com>
 */

class Smsglobal_DataTest extends WP_UnitTestCase
{
    public function tearDown()
    {
        global $wpdb;
        $subTblName = $wpdb->prefix . "sms_subscription";
        $verTblName = $wpdb->prefix . "sms_verification";
        $wpdb->query("DROP TABLE IF EXISTS $subTblName;");
        $wpdb->query("DROP TABLE IF EXISTS $verTblName;");

        parent::tearDown();
    }

    public function setUp()
    {
        add_option('auth_origin', '041122334455');
        smsglobal_install();
        parent::setUp();
    }

    public function testInstall()
    {
        global $wpdb;

        $subTblName = $wpdb->prefix . "sms_subscription";
        $verTblName = $wpdb->prefix . "sms_verification";

        $wpdb->query("DROP TABLE IF EXISTS $subTblName;");
        $wpdb->query("DROP TABLE IF EXISTS $verTblName;");

        //Expect the 2 tables existed
        smsglobal_install();
        $this->assertEquals($wpdb->get_var("SHOW TABLES LIKE '$subTblName'"), $subTblName);
        $this->assertEquals($wpdb->get_var("SHOW TABLES LIKE '$verTblName'"), $verTblName);

    }

    public function testInstallData()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "sms_subscription";

        $user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        $this->assertEquals($user_count, 0);

        smsglobal_install_data();
        $user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        $this->assertTrue($user_count > 0);
    }

    public function testInsertSubscription()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "sms_subscription";

        $counts = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );

        smsglobal_insert_subscription('Tester 1', '040102030405');
        $new_count1 = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        $this->assertEquals($new_count1, $counts + 1);

        // Same mobile number is allowed
        smsglobal_insert_subscription('Tester 2', '040102030405');
        $new_count2 = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        $this->assertEquals($new_count2, $counts + 2);
    }

    public function testVerification()
    {
        global $wpdb;
        $subTblName = $wpdb->prefix . "sms_subscription";
        $verTblName = $wpdb->prefix . "sms_verification";

        smsglobal_insert_subscription('Tester 1', '040102030405');

        smsglobal_insert_verification('123456', '040102030405');
        $verCount = $wpdb->get_var("SELECT COUNT(*) FROM $verTblName");

        smsglobal_insert_verification('654321', '040102030405');
        $verCount2 = $wpdb->get_var("SELECT COUNT(*) FROM $verTblName");
        $this->assertEquals($verCount, $verCount2);

        $this->assertFalse(smsglobal_verify('123456', '040102030405'));
        $this->assertTrue(smsglobal_verify('654321', '040102030405'));
    }

}