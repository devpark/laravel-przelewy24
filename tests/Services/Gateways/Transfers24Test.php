<?php

namespace Tests\Services\Gateways;

use Devpark\Transfers24\Services\Gateways\Transfers24 as GatewayTransfers24;
use Tests\UnitTestCase;
use Mockery as m;
use Devpark\Transfers24\Responses\Http\Response;
use Illuminate\Config\Repository as Config;
use GuzzleHttp\Client;

class Transfers24Test extends UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->config = m::mock(Config::class)->makePartial();
        $this->response = m::mock(Response::class)->makePartial();
        $this->gateway = new GatewayTransfers24($this->config, $this->response);
    }

    /** @test  */
    public function test_get_test_host()
    {
        $sandbox_host = 'https://sandbox.przelewy24.pl/';

        $this->config->shouldReceive('get')->andReturn(true);

        $this->gateway = new GatewayTransfers24($this->config, $this->response);

        $this->assertSame($this->gateway->getHost(), $sandbox_host);
    }

    /** @test  */
    public function test_get_production_host()
    {
        $transfers24_host = 'https://secure.przelewy24.pl/';

        $this->config->shouldReceive('get')->andReturn(false);

        $this->gateway = new GatewayTransfers24($this->config, $this->response);

        $this->assertSame($this->gateway->getHost(), $transfers24_host);
    }

    /** @test  */
    public function test_add_value_to_post_data()
    {
        $name = 'label';
        $value = 1;

        $this->gateway->addValue($name, $value);

        $response = $this->gateway->testConnection();
        $response_data = $response->getFormParams();

        $this->assertSame($response_data[$name], $value);
        $this->assertTrue(array_key_exists($name, $response_data));
    }

    /** @test  */
    public function test_calculate_CRC_sum()
    {
        $this->get_environment_for_CRC_check();
        $this->salt = 'zxy';
        $this->handler_property->setValue($this->gateway, $this->salt);

        $crc_array = $this->values + ['salt' => $this->salt];
        $crc = md5(implode('|', $crc_array));

        $test_crc = $this->gateway->calculateCrcSum($this->labels, $this->values);

        $this->assertSame($test_crc, $crc);
    }
    /** @test  */
    public function test_calculate_CRC_sum_without_salt()
    {
        $this->get_environment_for_CRC_check();
        $this->salt = '';
        $crc_array = $this->values + ['salt' => $this->salt];
        $crc = md5(implode('|', $crc_array));
        $this->handler_property->setValue($this->gateway, $this->salt);

        $test_crc = $this->gateway->calculateCrcSum($this->labels, $this->values);

        $this->assertSame($test_crc, $crc);
    }

    /** @test  */
    public function test_return_null_after_calculate_CRC_with_empty_value()
    {
        $this->get_environment_for_CRC_check();

        $test_crc = $this->gateway->calculateCrcSum($this->labels, []);

        $this->assertNull($test_crc);
    }

    protected function get_environment_for_CRC_check()
    {
        $this->labels = [
            'a',
            'b',
        ];
        $this->values = [
            'a' => '123456789',
            'b' => 'abcd',
        ];

        $this->gateway = m::mock(GatewayTransfers24::class)->makePartial();

        $this->reflection = new \ReflectionClass(GatewayTransfers24::class);
        $this->handler_method = $this->reflection->getMethod('calculateCrcSum');
        $this->handler_method->setAccessible(true);
        $this->handler_property = $this->reflection->getProperty('salt');
        $this->handler_property->setAccessible(true);
    }

    /** @test */
    public function test_calculate_sing_field()
    {
        $this->get_environment_for_CRC_check();
        $this->handler_method = $this->reflection->getMethod('calculateSign');
        $this->handler_method->setAccessible(true);
        $this->handler_property = $this->reflection->getProperty('postData');
        $this->handler_property->setAccessible(true);

        $this->salt = '';
        $sign_field_array = $this->values + ['salt' => $this->salt];
        $sign_field = md5(implode('|', $sign_field_array));

        $this->gateway->addValue('a', '123456789');
        $this->gateway->addValue('b', 'abcd');
        $this->gateway->calculateSign(['a', 'b']);

        $this->assertSame($this->handler_property->getValue($this->gateway)['p24_sign'], $sign_field);
    }

    /** @test */
    public function test_is_false_check_sum()
    {
        $post_data = [
            'p24_session_id' => '1234',
            'p24_order_id' => '5678',
            'p24_amount' => 'abcd',
            'p24_currency' => 'efgh',
            'p24_sign'  => '1234567689',
        ];
        $crc_test = $this->gateway->checkSum($post_data);
        $this->assertFalse($crc_test);
    }

    /** @test */
    public function test_is_true_check_sum()
    {
        $post_data = [
            'p24_session_id' => '1234',
            'p24_order_id' => '5678',
            'p24_amount' => 'abcd',
            'p24_currency' => 'efgh',
        ];
        $crc_array = $post_data + ['salt' => ''];
        $crc = md5(implode('|', $crc_array));

        $post_data['p24_sign'] = $crc;

        $crc_test = $this->gateway->checkSum($post_data);
        $this->assertTrue($crc_test);
    }

    /** @test */
    public function test_return_response_from_calling_to_transfers24()
    {
        $this->gateway = $this->get_mock_gateway();

        $this->reflection = new \ReflectionClass(GatewayTransfers24::class);
        $this->gateway_property = $this->reflection->getProperty('response');
        $this->gateway_property->setAccessible(true);
        $this->gateway_property->setValue($this->gateway, $this->response);

        $this->client = new Client(['base_uri' => 'https://sandbox.przelewy24.pl/']);
        $this->gateway_property = $this->reflection->getProperty('client');
        $this->gateway_property->setAccessible(true);
        $this->gateway_property->setValue($this->gateway, $this->client);
        $response = $this->gateway->callTransfers24('testConnection');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($response->getStatusCode(), 200);
    }

    /** @test */
    public function trnRegister_return_response()
    {
        $this->get_mock_callTransfers24();
        $response = $this->gateway->trnRegister([]);
        $this->assertTrue($response);
    }

    /** @test */
    public function trnVerify_return_response()
    {
        $this->get_mock_callTransfers24();
        $response = $this->gateway->trnVerify([]);
        $this->assertTrue($response);
    }

    /** @test */
    public function testConnection_return_response()
    {
        $this->get_mock_callTransfers24();
        $response = $this->gateway->testConnection([]);
        $this->assertTrue($response);
    }

    /** @test */
    public function trnRequest_return_response()
    {
        $token = '00000000';
        $response = $this->gateway->trnRequest($token, false);
        $url_payment = $this->gateway->getHost() . 'trnRequest/' . $token;
        $this->assertSame($response, $url_payment);
    }

    protected function get_mock_gateway()
    {
        return m::mock(GatewayTransfers24::class)->makePartial()->shouldAllowMockingProtectedMethods();
    }

    protected function get_mock_callTransfers24()
    {
        $this->gateway = $this->get_mock_gateway();
        $this->gateway->shouldReceive('callTransfers24')->once()->andReturn(true);
    }
}
