<?php
class Smsglobal_Test_EcommerceTest extends WP_UnitTestCase
{
    public function testGetTotalPrice()
    {
        if (!function_exists('wpsc_currency_display')) {
            $this->markTestSkipped('e-Commerce plugin is not installed');
        }

        global $wpdb;

        $query = 'INSERT INTO ' . $wpdb->prefix . 'wpsc_purchase_logs (id, totalprice) VALUES (1, 100)';
        $wpdb->query($query);

        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_Ecommerce');
        $this->assertEquals('$100.00', $plugin->getTotalPrice(1));
    }

    public function testGetMessage()
    {
        /** @var Smsglobal_EcommerceMock $plugin */
        $plugin = Smsglobal_Utils::createMockClass('Smsglobal_Ecommerce');
        $actual = $plugin->getMessage(1, '$100.00');

        $this->assertEquals('Order #1 placed for $100.00', $actual);
    }
}
