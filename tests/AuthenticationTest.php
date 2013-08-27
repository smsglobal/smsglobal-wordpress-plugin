<?php
class Smsglobal_AuthenticationTest extends WP_UnitTestCase
{
    public function testGenerateCode()
    {
        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_Authentication');

        for ($i = 0; $i < 5; ++$i) {
            $code = $plugin->generateCode();

            if ($code < 1000 || $code > 9999) {
                $this->fail('Code generated outside of range');
            }
        }

        $this->addToAssertionCount(1);
    }

    public function testHashCode()
    {
        $user = new WP_User();
        $user->data = new stdClass();
        $user->user_login = 'test';
        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_Authentication');
        $this->assertEquals('1303df0377b5c5c72aeb39f9334a94a7ad78d615', $plugin->hashCode(1234, $user));
    }

    public function testGetMessage()
    {
        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_Authentication');
        $actual = $plugin->getMessage(1234);
        $this->assertEquals('Your SMS code for Test Blog is 1234.', $actual);
    }
}
