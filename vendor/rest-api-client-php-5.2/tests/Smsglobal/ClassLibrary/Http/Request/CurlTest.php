<?php
class Smsglobal_RestApiClient_Http_Request_CurlTest extends PHPUnit_Framework_TestCase
{
    const ADAPTER = 'Smsglobal\\RestApiClient\\Http\\Request\\Curl';
    /**
     * @covers Smsglobal\RestApiClient\Http\Request::__construct
     */
    public function testConstructorSetsUrl()
    {
        $url = 'http://test.com/';
        $request = new Smsglobal_RestApiClient_Http_Request($url, self::ADAPTER);
        $this->assertAttributeEquals($url, 'url', $request);
    }
    /**
     * @covers Smsglobal\RestApiClient\Http\Request::setUrl
     * @covers Smsglobal\RestApiClient\Http\Request::getUrl
     */
    public function testSetUrl()
    {
        $request = new Smsglobal_RestApiClient_Http_Request('http://test.com/', self::ADAPTER);
        $url = 'http://test2.com';
        $request->setUrl($url);
        $this->assertEquals($url, $request->getUrl());
    }
    public function testSetMethod()
    {
        $request = new Smsglobal_RestApiClient_Http_Request('http://test.com/', self::ADAPTER);
        $method = 'POST';
        $request->setMethod($method);
        $this->assertEquals($method, $request->getMethod());
    }
    public function testGet()
    {
        $request = new Smsglobal_RestApiClient_Http_Request('http://httpbin.org/get', self::ADAPTER);
        $this->assertNotEquals('', $request->get()->getContent());
    }
    public function testPost()
    {
        $request = new Smsglobal_RestApiClient_Http_Request('http://httpbin.org/post', self::ADAPTER);
        $request->headers->set('Content-Type', 'application/json');
        $content = $request->post('test')->getContent();
        $content = json_decode($content);
        $this->assertEquals('test', $content->data);
    }
    public function testDelete()
    {
        $request = new Smsglobal_RestApiClient_Http_Request('http://httpbin.org/delete', self::ADAPTER);
        $this->assertNotEquals('', $request->delete()->getContent());
    }
    public function testPatch()
    {
        $request = new Smsglobal_RestApiClient_Http_Request('http://httpbin.org/patch', self::ADAPTER);
        $request->headers->set('Content-Type', 'application/json');
        $content = $request->patch('test')->getContent();
        $content = json_decode($content);
        $this->assertEquals('test', $content->data);
    }
    public function testPut()
    {
        $request = new Smsglobal_RestApiClient_Http_Request('http://httpbin.org/put', self::ADAPTER);
        $request->headers->set('Content-Type', 'application/json');
        $content = $request->put('test')->getContent();
        $content = json_decode($content);
        $this->assertEquals('test', $content->data);
    }
    public function testOptions()
    {
        $request = new Smsglobal_RestApiClient_Http_Request('http://httpbin.org/', self::ADAPTER);
        $this->assertNotEquals('', $request->options()->getHeaders()->get('allow'));
    }
    public function testHead()
    {
        $request = new Smsglobal_RestApiClient_Http_Request('http://httpbin.org/', self::ADAPTER);
        $this->assertNotEmpty($request->head()->getHeaders()->all());
    }
}