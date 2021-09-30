<?php

declare(strict_types=1);

namespace Tests\Action;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Exceptions\EmptyCredentialsException;
use Devpark\Transfers24\Exceptions\NoEnvironmentChosenException;
use Devpark\Transfers24\Factories\RegisterResponseFactory;
use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\Register as RegisterResponse;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Config\Repository;
use Mockery as m;
use Psr\Log\LoggerInterface;
use Tests\UnitTestCase;

class ViaCredentialsTest extends UnitTestCase
{
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

    /**
     * @var m\MockInterface
     */
    private $response;

    /**
     * @var Action
     */
    private $action;

    /**
     * @var m\MockInterface
     */
    private $translator_factory;

    /**
     * @var m\MockInterface
     */
    private $translator;

    /**
     * @var m\MockInterface
     */
    private $gateway;

    /**
     * @var m\MockInterface
     */
    private $response_factory;

    protected function setUp()
    {
        parent::setUp();

        $this->response = m::mock(IResponse::class, RegisterResponse::class);

        $this->translator = m::mock(RegisterTranslator::class);

        $this->logger = m::mock(LoggerInterface::class);

        $this->gateway = m::mock(\Devpark\Transfers24\Services\Gateways\Transfers24::class);

        $this->credentials = m::mock(Credentials::class);

        $this->response_factory = m::mock(RegisterResponseFactory::class);

        $this->action = $this->app->make(Action::class, [
            'gateway' => $this->gateway,
            'logger' => $this->logger,
        ]);
        $this->action->init($this->response_factory, $this->translator);
    }

    /**
     * @Feature Connection with Provider
     * @Scenario Payment Processing
     * @Case Scope Credentials for Merchant
     * @test
     */
    public function execute_viaCredentials_lack_merchant_credentials()
    {
        $this->logger->shouldReceive('error')
            ->once();

        $this->translator->shouldReceive('configure')
            ->once()
            ->andThrow(EmptyCredentialsException::class);

        $response = $this->action->execute();

        $this->assertInstanceOf(InvalidResponse::class, $response);
    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Scope Credentials for Merchant
     * @test
     */
    public function viaCredentials_lack_environment_setting_in_merchant_credentials()
    {
        $this->translator->shouldReceive('getCredentials')
            ->once()
            ->andReturn($this->credentials);
        $this->logger->shouldReceive('error')
            ->once();

        $this->gateway->shouldReceive('configureGateway')
            ->once()
            ->andThrow(NoEnvironmentChosenException::class);

        $this->translator->shouldReceive('configure')
            ->once();
        $this->translator->shouldReceive('translate')
            ->once();

        $response = $this->action->execute();

        $this->assertInstanceOf(InvalidResponse::class, $response);
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
