<?php

namespace Tests\Services\Gateways;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Factories\HttpResponseFactory;
use Devpark\Transfers24\Services\Gateways\Transfers24 as GatewayTransfers24;
use Illuminate\Contracts\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Tests\UnitTestCase;
use Mockery as m;
use Devpark\Transfers24\Responses\Http\Response;
use Illuminate\Config\Repository as Config;
use GuzzleHttp\Client;

class Transfers24Test extends UnitTestCase
{
    /**
     * @var GatewayTransfers24
     */
    private $gateway;
    /**
     * @var m\Mock|Config
     */
    private $config;
    /**
     * @var m\Mock|Response
     */
    private $response;
    /**
     * @var m\MockInterface
     */
    private $container;
    /**
     * @var m\MockInterface
     */
    private $client;

    protected function setUp()
    {
        parent::setUp();
        $this->config = m::mock(Config::class);
        $this->response = m::mock(HttpResponseFactory::class);
        $this->container = m::mock(Container::class);
        $this->client = m::mock(Client::class);}

    /**
     * @Feature Payments
     * @Scenario Execute Payment
     * @Case Connection to sandbox
     * @test
     */
    public function test_get_test_host()
    {
        $sandbox_host = 'https://sandbox.przelewy24.pl/';
        $this->makeGateway(true);

        $this->assertSame($this->gateway->getHost(), $sandbox_host);
    }


    /**
     * @Feature Payments
     * @Scenario Execute Payment
     * @Case Connection to live
     * @test
     */
    public function test_get_production_host()
    {
        $transfers24_host = 'https://secure.przelewy24.pl/';

        $this->makeGateway(false);

        $this->assertSame($this->gateway->getHost(), $transfers24_host);
    }



    /**
     * @Feature Payments
     * @Scenario Execute Payment
     * @Case Send Payment
     * @test
     */
    public function send_payment()
    {

        //When
        $form = m::mock(Form::class);
        $this->makeGateway(true);

        $form->shouldReceive('getUri','getMethod')
            ->once()
            ->andReturn('form-data');
        $form->shouldReceive('toArray')
            ->once()
            ->andReturn([]);

        $client_response = m::mock(ResponseInterface::class);
        $this->client->shouldReceive('request')
            ->once()
            ->andReturn($client_response);

        $expected_response = m::mock(Response::class);
        $this->response->shouldReceive('create')
            ->once()
            ->with($form, $client_response)
            ->andReturn($expected_response);

        //Then
        $response = $this->gateway->callTransfers24($form);

        //Then
        $this->assertSame($expected_response, $response);
    }






    /** @test  */
    public function test_add_value_to_post_data()
    {
        $this->makeGateway(true);

        $name = 'label';
        $value = 1;

        $this->gateway->addValue($name, $value);

        $response = $this->gateway->testConnection();
        $response_data = $response->getForm();

        $this->assertSame($response_data[$name], $value);
        $this->assertTrue(array_key_exists($name, $response_data));
    }



    /** @test */
    public function test_is_false_check_sum()
    {
        $this->makeGateway(true);

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
        $this->makeGateway(false);

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
    public function testConnection_return_response()
    {
        $this->get_mock_callTransfers24();
        $response = $this->gateway->testConnection([]);
        $this->assertTrue($response);
    }

    /** @test */
    public function trnRequest_return_response()
    {
        $this->makeGateway(true);

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

    protected function makeGateway($test_mode): void
    {
        $this->config->shouldReceive('get')->andReturn($test_mode);
        $this->container->shouldReceive('make')->once()->andReturn($this->client);
        $this->gateway = new GatewayTransfers24($this->config, $this->response, $this->container);
    }
}
