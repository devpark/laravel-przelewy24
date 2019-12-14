<?php
declare(strict_types=1);

namespace Tests\Services\Gateways;

use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Services\Gateways\Transfers24 as GatewayTransfers24;
use Devpark\Transfers24\Services\Handlers\Transfers24;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Mockery as m;
use Tests\UnitTestCase;

class ViaCredentialsTest extends UnitTestCase
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
     * @var m\MockInterface|Credentials
     */
    private $credentials;
    /**
     * @var Repository
     */
    private $config;

    protected function setUp()
    {
        parent::setUp();
        $this->config = $this->app->make(Repository::class);
        $this->gateway_provider = m::mock(GatewayTransfers24::class);
        $this->credentials =  m::mock(Credentials::class);
        $this->handler = $this->app->make(Transfers24::class, [
            'transfers24' => $this->gateway_provider,
            'config' => $this->config
        ]);
        $this->http_response = new Response();

    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Scope Credentials for Merchant
     * @test
     */
    public function viaCredentials_skip_merchant_credentials()
    {
        $this->skipCallingConfigureOnGateway();

        $this->skipGettingCredentials();

        $this->config->set('transfers24.credentials-scope', false);

        $this->handler->viaCredentials($this->credentials);
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

    /**
     * @return void
     */
    protected function skipCallingConfigureOnGateway():void
    {
        $this->gateway_provider->shouldNotReceive('configure');
    }

    protected function skipGettingCredentials(): void
    {
        $this->credentials->shouldNotReceive('getPosId');
        $this->credentials->shouldNotReceive('getMerchantId');
        $this->credentials->shouldNotReceive('getCrc');
        $this->credentials->shouldNotReceive('isTestMode');
    }

}
