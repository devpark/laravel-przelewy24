<?php
declare(strict_types=1);

namespace Tests\Services\Gateways;

use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\TestConnection;
use Devpark\Transfers24\Services\Gateways\Transfers24 as GatewayTransfers24;
use Devpark\Transfers24\Services\Handlers\Transfers24;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Mockery as m;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
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
    /**
     * @var LoggerInterface
     */
    private $logger;

    protected function setUp()
    {
        parent::setUp();
        $this->config = $this->app->make(Repository::class);
        $this->logger = $this->app->make(\Symfony\Component\HttpKernel\Log\Logger::class);
        $this->gateway_provider = m::mock(GatewayTransfers24::class);
        $this->credentials =  m::mock(Credentials::class);

        $this->handler = $this->app->make(Transfers24::class, [
            'transfers24' => $this->gateway_provider,
            'config' => $this->config,
            'logger' => $this->logger
        ]);
        $this->http_response = new Response();

    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Skip Credentials for Merchant
     * @test
     */
    public function viaCredentials_skip_merchant_credentials()
    {
        $this->skipCallingConfigureOnGateway();

        $this->skipGettingCredentials();

        $this->disableCredentialsScope();

        $this->setGatewayResponseCode('100');

        $response = $this->handler
            ->viaCredentials($this->credentials)
            ->checkCredentials();

        $this->assertInstanceOf(TestConnection::class, $response);

    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Scope Credentials for Merchant
     * @test
     */
    public function viaCredentials_merchant_credentials_incomplete()
    {
        $this->skipCallingConfigureOnGateway();

        $this->gettingIncompleteCredentials();

        $this->enableCredentialsScope();

        $this->skipCallingGatewayTestConnection();

        $response = $this->handler
            ->viaCredentials($this->credentials)
            ->checkCredentials();

        $this->assertInstanceOf(InvalidResponse::class, $response);
    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Scope Credentials for Merchant
     * @test
     */
    public function viaCredentials_lack_merchant_credentials()
    {
        $this->skipCallingConfigureOnGateway();

        $this->skipGettingCredentials();

        $this->enableCredentialsScope();

        $this->skipCallingGatewayTestConnection();

        $response = $this->handler
            ->checkCredentials();

        $this->assertInstanceOf(InvalidResponse::class, $response);
    }

    /**
     * @Feature Connection with Provider
     * @Scenario Register Connection
     * @Case Scope Credentials for Merchant
     * @test
     */
    public function register_viaCredentials_lack_merchant_credentials()
    {
        $this->gateway_provider->shouldNotReceive('trnRegister');


        $this->skipGettingCredentials();

        $this->enableCredentialsScope();

        $this->skipCallingGatewayTestConnection();

        $response = $this->handler
            ->checkCredentials();

        $this->assertInstanceOf(InvalidResponse::class, $response);
    }

    /**
     * @Feature Connection with Provider
     * @Scenario Payment Processing
     * @Case Scope Credentials for Merchant
     * @test
     */
    public function receive_viaCredentials_lack_merchant_credentials()
    {
        $this->gateway_provider->shouldNotReceive('checkSum', 'trnRegister');


        $this->skipGettingCredentials();

        $this->enableCredentialsScope();

        $this->skipCallingGatewayTestConnection();

        $response = $this->handler
            ->receive([]);

        $this->assertInstanceOf(InvalidResponse::class, $response);
    }


    /**
     * @Feature Connection with Provider
     * @Scenario Payment Processing
     * @Case Scope Credentials for Merchant
     * @test
     */
    public function execute_viaCredentials_lack_merchant_credentials()
    {
        $this->gateway_provider->shouldNotReceive('trnRequest');


        $this->skipGettingCredentials();

        $this->enableCredentialsScope();

        $this->skipCallingGatewayTestConnection();

        $response = $this->handler
            ->execute('token');

        $this->assertSame('Empty credentials.', $response);
    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Scope Credentials for Merchant
     * @test
     */
    public function viaCredentials_lack_environment_setting_in_merchant_credentials()
    {
        $this->skipCallingConfigureOnGateway();

        $this->gettingCredentialsWithoutEnvironmentSetting();

        $this->enableCredentialsScope();

        $this->skipCallingGatewayTestConnection();

        $response = $this->handler
            ->viaCredentials($this->credentials)
            ->checkCredentials();

        $this->assertInstanceOf(InvalidResponse::class, $response);
    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Scope Credentials for Merchant
     * @test
     */
    public function register_viaCredentials_lack_environment_setting_in_merchant_credentials()
    {
        $this->gateway_provider->shouldNotReceive('trnRegister');

        $this->gettingCredentialsWithoutEnvironmentSetting();

        $this->enableCredentialsScope();

        $this->skipCallingGatewayTestConnection();

        $response = $this->handler
            ->viaCredentials($this->credentials)
            ->init([]);

        $this->assertInstanceOf(InvalidResponse::class, $response);
    }


    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Scope Credentials for Merchant
     * @test
     */
    public function receive_viaCredentials_lack_environment_setting_in_merchant_credentials()
    {
        $this->gateway_provider->shouldNotReceive('checkSum', 'trnRegister');

        $this->gettingCredentialsWithoutEnvironmentSetting();

        $this->enableCredentialsScope();

        $this->skipCallingGatewayTestConnection();

        $response = $this->handler
            ->viaCredentials($this->credentials)
            ->receive([]);

        $this->assertInstanceOf(InvalidResponse::class, $response);
    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Scope Credentials for Merchant
     * @test
     */
    public function execute_viaCredentials_lack_environment_setting_in_merchant_credentials()
    {
        $this->gateway_provider->shouldNotReceive('trnRequest');

        $this->gettingCredentialsWithoutEnvironmentSetting();

        $this->enableCredentialsScope();

        $this->skipCallingGatewayTestConnection();

        $response = $this->handler
            ->viaCredentials($this->credentials)
            ->execute('token');

    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Scope Credentials for Merchant
     * @test
     */
    public function viaCredentials_use_merchant_credentials()
    {
        $this->callingConfigureOnGateway();

        $this->gettingCredentials();

        $this->enableCredentialsScope();

        $this->setGatewayResponseCode('200');

        $response = $this->handler
            ->viaCredentials($this->credentials)
            ->checkCredentials();

        $this->assertInstanceOf(TestConnection::class, $response);
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

    protected function skipCallingGatewayTestConnection(): void
    {

        $this->gateway_provider->shouldNotReceive('testConnection');
    }

    /**
     * @return void
     */
    protected function skipCallingConfigureOnGateway():void
    {
        $this->gateway_provider->shouldNotReceive('configure');
    }


    /**
     * @return void
     */
    protected function callingConfigureOnGateway():void
    {
        $this->gateway_provider->shouldReceive('configure')
        ->once();
    }

    protected function skipGettingCredentials(): void
    {
        $this->credentials->shouldNotReceive('getPosId');
        $this->credentials->shouldNotReceive('getMerchantId');
        $this->credentials->shouldNotReceive('getCrc');
        $this->credentials->shouldNotReceive('isTestMode');
    }


    protected function gettingCredentials(): void
    {
        $this->credentials->shouldReceive('getPosId')->once()->andReturn(1);
        $this->credentials->shouldReceive('getMerchantId')->once()->andReturn(1);
        $this->credentials->shouldReceive('getCrc')->once()->andReturn('crc');
        $this->credentials->shouldReceive('isTestMode')->once()->andReturn(false);
    }


    protected function gettingCredentialsWithoutEnvironmentSetting(): void
    {
        $this->credentials->shouldReceive('getPosId')->once()->andReturn(1);
        $this->credentials->shouldReceive('getMerchantId')->once()->andReturn(1);
        $this->credentials->shouldReceive('getCrc')->once()->andReturn('crc');
        $this->credentials->shouldReceive('isTestMode')->once()->andReturn(null);
    }

    protected function gettingIncompleteCredentials(): void
    {
        $this->credentials->shouldReceive('getPosId')->once()->andReturn(null);
        $this->credentials->shouldNotReceive('getMerchantId');
        $this->credentials->shouldNotReceive('getCrc');
        $this->credentials->shouldNotReceive('isTestMode');
    }

    protected function enableCredentialsScope(): void
    {
        $this->config->set('transfers24.credentials-scope', true);
    }

    protected function disableCredentialsScope(): void
    {
        $this->config->set('transfers24.credentials-scope', false);
    }

}
