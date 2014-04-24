<?php
class Smsglobal_RestApiClient_VersionTest extends PHPUnit_Framework_TestCase
{
    public function testVersion()
    {
        $this->assertEquals(1, Smsglobal_RestApiClient_Version::REST_API_VERSION);
    }
}