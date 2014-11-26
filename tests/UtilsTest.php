<?php
class Smsglobal_UtilsTest extends WP_UnitTestCase
{
    public function testGetRestClient()
    {
        $rest = Smsglobal_Utils::getRestClient();

        $this->assertInstanceOf('Smsglobal_RestApiClient_RestApiClient', $rest);

        // Assert it gets the same instance each time
        $this->assertSame($rest, Smsglobal_Utils::getRestClient());
    }

    public function testTranslate()
    {
        $this->assertEquals('Test', __('Test', SMSGLOBAL_TEXT_DOMAIN));
    }

    public function testGetRoles()
    {
        $roles = Smsglobal_Utils::getRoles();
        $this->assertInternalType('array', $roles);
        $this->assertEquals(__('All Users', SMSGLOBAL_TEXT_DOMAIN), $roles['all']);
    }
}
