<?php
class Smsglobal_ShoppTest extends WP_UnitTestCase
{
    public function testGetMessage()
    {
        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_Shopp');
        $purchase = (object) array(
            'firstname' => 'First name',
            'lastname' => 'Last name',
            'email' => 'email@address.com',
            'shipcity' => 'City',
            'shipstate' => 'State',
            'shipcountry' => 'USA',
            'total' => '100.00',
        );
        $actual = $plugin->getMessage($purchase);
        $expected = 'Customer: First name Last name
Email: email@address.com
Destination: City, State, USA
Total: $100.00';
        $this->assertEquals($expected, $actual);
    }
}
