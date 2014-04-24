<?php
class Smsglobal_RestApiClient_Http_RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException RuntimeException
     */
    public function testPostWithoutContentType()
    {
        $request = new Smsglobal_RestApiClient_Http_Request('http://httpbin.org/post');
        $request->post('{"data":true}');
    }
}
