<?php
declare(strict_types=1);

namespace Tests\Services\Gateways;

use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Services\Gateways\Transfers24 as GatewayTransfers24;
use Devpark\Transfers24\Services\Handlers\Transfers24;
use Illuminate\Foundation\Application;
use Tests\UnitTestCase;

class CheckCredentialsTest extends UnitTestCase
{
    /**
     * @var Transfers24
     */
    private $handler;

    /**
     * @var \Mockery\MockInterface|GatewayTransfers24
     */
    private $gateway_provider;

    /**
     * @var Response
     */
    private $http_response;

    protected function setUp()
    {
        parent::setUp();
        $this->gateway_provider = \Mockery::mock(GatewayTransfers24::class);
        $this->handler = $this->app->make(Transfers24::class, [
            'transfers24' => $this->gateway_provider
        ]);
        $this->http_response = new Response();

    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Connection failed
     * @test
     */
    public function testConnection_failed()
    {
        $this->setGatewayResponseCode('100');

        $passed = $this->handler->checkCredentials();
        $this->assertFalse($passed->isSuccess());
    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Connection passed
     * @test
     */
    public function testConnection_passed()
    {
        $this->setGatewayResponseCode('0');

        $passed = $this->handler->checkCredentials();
        $this->assertTrue($passed->isSuccess());
    }

    protected function setGatewayResponseCode(string $response_code): void
    {
        $this->http_response->addStatusCode(200);
        $response = implode('=', [
            Transfers24::ERROR_LABEL,
            $response_code
        ]);
        $this->http_response->addBody($response);

        $this->gateway_provider->shouldReceive('testConnection')
            ->once()
            ->andReturn($this->http_response);
    }

}
