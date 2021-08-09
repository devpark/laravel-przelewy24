<?php

namespace Tests\Services\Gateways;

use Devpark\Transfers24\Factories\HttpResponseFactory;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Services\Crc;
use Devpark\Transfers24\Services\Gateways\Transfers24 as GatewayTransfers24;
use Devpark\Transfers24\Services\Handlers\Transfers24;
use Illuminate\Contracts\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Tests\UnitTestCase;
use Mockery as m;
use Devpark\Transfers24\Responses\Http\Response;
use Illuminate\Config\Repository as Config;
use GuzzleHttp\Client;

class CallTransfers24Test extends UnitTestCase
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
    private $http_response_factory;
    /**
     * @var m\MockInterface
     */
    private $api;
    private $crc;

    protected function setUp()
    {
        parent::setUp();
        $app = m::mock(Container::class);
        $this->config = m::mock(Config::class);

        $this->api = m::mock(Client::class);
        $app->shouldReceive('make')
            ->once()
            ->andReturn($this->api);

        $pos_id = 'pos_id';
        $merchant_id = 'merchant_id';
        $crc = 'crc';
        $sandbox = false;
        $version = 'version';

        $this->config->shouldReceive('get')
            ->times(5)
            ->andReturn($pos_id,$merchant_id,$crc,$sandbox, $version);

        $this->http_response_factory = m::mock(HttpResponseFactory::class);

        $this->crc = m::mock(Crc::class);
        $this->crc->shouldReceive('sum')
            ->once()
            ->andReturn('crc');

        $this->crc->shouldReceive('setSalt')
            ->once();

        $this->gateway = $this->app->make(GatewayTransfers24::class, [
            'config' => $this->config,
            'http_response_factory' => $this->http_response_factory,
            'app' => $app,
            'crc' => $this->crc
        ]);

    }

    /** @test */
    public function callTransfers24()
    {
        $handler = m::mock(Transfers24::class);

        $uri = 'uri';
        $handler->shouldReceive('getUri')
            ->once()
            ->andReturn($uri);

        $method = 'method';
        $handler->shouldReceive('getMethod')
            ->once()
            ->andReturn($method);

        $form = m::mock(RegisterForm::class);
        $handler->shouldReceive('getForm')
            ->once()
            ->andReturn($form);

        $form_params = [];
        $form->shouldReceive('toArray')
            ->once()
            ->andReturn($form_params);

        $psr = m::mock(ResponseInterface::class);
        $this->api->shouldReceive('request')
            ->once()
            ->with($method, $uri, m::any())
            ->andReturn($psr);


        $http_response = m::mock(Response::class);

        $this->http_response_factory->shouldReceive('create')
            ->once()
            ->with(m::any(), $psr)
            ->andReturn($http_response);

        //When
        $response = $this->gateway->callTransfers24($handler);

        //Then
        $this->assertSame($response, $http_response);
    }
}
