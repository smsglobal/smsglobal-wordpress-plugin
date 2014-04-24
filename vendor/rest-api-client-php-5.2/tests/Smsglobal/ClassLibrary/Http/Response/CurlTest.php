<?php
class Smsglobal_RestApiClient_Http_CurlTest extends PHPUnit_Framework_TestCase
{
    const ADAPTER = 'Smsglobal\\RestApiClient\\Http\\Request\\Curl';
    public function testStatusCode()
    {
        $request = new Smsglobal_RestApiClient_Http_Request('http://httpbin.org/status/418', self::ADAPTER);
        $this->assertEquals(418, $request->get()->getStatusCode());
    }
}