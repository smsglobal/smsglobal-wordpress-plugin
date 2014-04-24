<?php
class Smsglobal_RestApiClient_ApiKeyTest extends PHPUnit_Framework_TestCase
{
    public function testHashAlgoSupported()
    {
        $this->assertContains(Smsglobal_RestApiClient_ApiKey::HASH_ALGO, hash_algos());
    }
    public function testConstructor()
    {
        $key = 'test';
        $secret = 'abcd';
        $apiKey = new Smsglobal_RestApiClient_ApiKey($key, $secret);
        $this->assertAttributeEquals($key, 'key', $apiKey);
        $this->assertAttributeEquals($secret, 'secret', $apiKey);
    }
    public function testGetKey()
    {
        $expected = 'test';
        $apiKey = new Smsglobal_RestApiClient_ApiKey($expected, 'abcd');
        $this->assertEquals($expected, $apiKey->getKey());
    }
    /**
     * @covers Smsglobal\RestApiClient\ApiKey::getAuthorizationHeader
     */
    public function testGetAuthorizationHeader()
    {
        $apiKey = new Smsglobal_RestApiClient_ApiKey('test', 'abcd');
        $header = $apiKey->getAuthorizationHeader('GET', '/v1/sms/', 'api.smsglobal.com', 443);
        $regExp = '/^MAC id="test", ts="\\d+", nonce=".*", mac=".*"$/';
        $this->assertRegExp($regExp, $header);
    }
}
