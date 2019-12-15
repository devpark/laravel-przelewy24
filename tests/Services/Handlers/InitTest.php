<?php
declare(strict_types=1);

namespace Tests\Services\Gateways;

use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Responses\Register;
use Devpark\Transfers24\Responses\TestConnection;
use Devpark\Transfers24\Services\Gateways\Transfers24 as GatewayTransfers24;
use Devpark\Transfers24\Services\Handlers\Transfers24;
use Illuminate\Foundation\Application;
use Mockery as m;
use Psr\Log\LoggerInterface;
use Tests\UnitTestCase;

class InitTest extends UnitTestCase
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
    /**
     * @var m\MockInterface
     */
    private $logger;

    protected function setUp()
    {
        parent::setUp();
        $this->logger = m::mock(LoggerInterface::class);
        $this->gateway_provider = m::mock(GatewayTransfers24::class);
        $this->handler = $this->app->make(Transfers24::class, [
            'transfers24' => $this->gateway_provider,
            'logger' => $this->logger
        ]);
        $this->http_response = new Response();

    }

    /**
     * @Feature Payment Processing
     * @Scenario Register Payment
     * @Case Connection failed
     * @test
     */
    public function init_failed()
    {
        $this->setGatewayResponseCode('100');

        $passed = $this->handler->init([]);

        $this->assertInstanceOf(Register::class, $passed);

        $this->assertFalse($passed->isSuccess());
    }

    /**
     * @Feature Payment Processing
     * @Scenario Register Payment
     * @Case Connection passed
     * @test
     */
    public function init_passed()
    {
        $this->setGatewayResponseCode('0');

        $passed = $this->handler->init([]);

        $this->assertInstanceOf(Register::class, $passed);

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

        $this->gateway_provider->shouldReceive('trnRegister')
            ->once()
            ->with([])
            ->andReturn($this->http_response);
    }

}
