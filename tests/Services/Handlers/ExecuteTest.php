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

class ExecuteTest extends UnitTestCase
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
     * @Scenario Payment Processing
     * @Case Payment Execute
     * @test
     */
    public function init_failed()
    {
        $link = 'http://payment';
        $this->mockGettingPaymentLink($link);

        $passed = $this->handler->execute('token', false);

        $this->assertSame($link, $passed);

    }

    protected function mockGettingPaymentLink(string $link): void
    {
        $this->gateway_provider->shouldReceive('trnRequest')
            ->once()
            ->with('token', false)
            ->andReturn($link);
    }

}
