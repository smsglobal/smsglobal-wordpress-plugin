<?php
class Smsglobal_UserListTest extends WP_UnitTestCase
{
    public function tearDown()
    {
        delete_option('smsglobal_enable_post_alerts');
        delete_user_meta(1, 'mobile');
        delete_user_meta(1, 'smsglobal_send_post_alerts');

        parent::tearDown();
    }

    public function testCheckSetting()
    {
        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_UserList');
        $this->assertFalse($plugin->arePostAlertsEnabled());

        update_option('smsglobal_enable_post_alerts', true);
        // Need to re-instantiate because the setting gets cached in the class instance
        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_UserList');
        $this->assertTrue($plugin->arePostAlertsEnabled());
    }

    public function testAddCustomColumns()
    {
        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_UserList');

        $fields = $plugin->addCustomColumns(array());
        $this->assertArrayHasKey('mobile', $fields);
        $this->assertArrayNotHasKey('smsglobal_send_post_alerts', $fields);

        update_option('smsglobal_enable_post_alerts', true);
        // Need to re-instantiate because the setting gets cached in the class instance
        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_UserList');
        $fields = $plugin->addCustomColumns(array());
        $this->assertArrayHasKey('smsglobal_send_post_alerts', $fields);
    }

    public function testGetCustomFieldMobile()
    {
        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_UserList');

        // User does not have mobile number yet
        $this->assertEquals('', $plugin->getCustomField('', 'mobile', 1));

        $number = '61447100250';
        update_user_meta(1, 'mobile', $number);

        $this->assertEquals($number, $plugin->getCustomField('', 'mobile', 1));
    }

    public function testGetCustomFieldPostAlerts()
    {
        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_UserList');

        // User does not have mobile number yet
        $this->assertEquals('No', $plugin->getCustomField('', 'smsglobal_send_post_alerts', 1));

        update_user_meta(1, 'smsglobal_send_post_alerts', true);

        $this->assertEquals('Yes', $plugin->getCustomField('', 'smsglobal_send_post_alerts', 1));
    }
}
