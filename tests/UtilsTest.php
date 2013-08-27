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
        $this->assertEquals('Test', Smsglobal_Utils::_('Test'));
    }

    public function testGetRoles()
    {
        $roles = Smsglobal_Utils::getRoles();
        $this->assertInternalType('array', $roles);
        $this->assertEquals(Smsglobal_Utils::_('All Users'), $roles['all']);
    }
}
