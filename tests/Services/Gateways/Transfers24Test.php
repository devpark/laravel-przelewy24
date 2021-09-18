<?php

namespace Tests\Services\Gateways;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Factories\HttpResponseFactory;
use Devpark\Transfers24\Services\Gateways\ClientFactory;
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
    /**
     * @var ClientFactory
     */
    private $client_factory;

    protected function setUp()
    {
        parent::setUp();
        $this->config = m::mock(Config::class);
        $this->response = m::mock(HttpResponseFactory::class);
        $this->container = m::mock(Container::class);
        $this->client_factory = m::mock(ClientFactory::class);
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
     * @Case Connection to sandbox
     * @test
     */
    public function test_get_test_host_with_path()
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

    /** @test */
    public function trnRequest_return_response()
    {
        $this->makeGateway(true);

        $token = '00000000';
        $response = $this->gateway->trnRequest($token, false);
        $url_payment = $this->gateway->getHost() . 'trnRequest/' . $token;
        $this->assertSame($response, $url_payment);
    }

    protected function makeGateway($test_mode): void
    {
        $this->config->shouldReceive('get')->andReturn($test_mode);
        $this->client_factory->shouldReceive('create')->once()->andReturn($this->client);
        $this->gateway = new GatewayTransfers24($this->config, $this->response, $this->client_factory);
    }
}
