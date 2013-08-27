<?php
class Smsglobal_SmsPageTest extends WP_UnitTestCase
{
    public function testCheckConfiguration()
    {
        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_SmsPage');
        $this->assertFalse($plugin->checkConfiguration());

        update_option('smsglobal_api_key', 'test');

        $this->assertFalse($plugin->checkConfiguration());

        update_option('smsglobal_api_secret', 'test');

        $this->assertTrue($plugin->checkConfiguration());
    }

    public function testGetToOptions()
    {
        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_SmsPage');
        $actual = $plugin->getToOptions();

        $this->assertInternalType('array', $actual);
        $this->assertArrayHasKey('number', $actual);
        $this->assertArrayHasKey('all', $actual);
    }

    public function testToValueToNumbers()
    {
        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_SmsPage');
        $this->assertInternalType('array', $plugin->toValueToNumbers('all'));
        $this->assertInternalType('array', $plugin->toValueToNumbers('61447100250'));
        $this->assertInternalType('array', $plugin->toValueToNumbers('administrator'));
    }
}
