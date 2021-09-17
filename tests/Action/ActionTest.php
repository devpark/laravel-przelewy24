<?php

namespace Tests\Action;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Currency;
use Devpark\Transfers24\Exceptions\RequestExecutionException;
use Devpark\Transfers24\Factories\HandlerFactory;
use Devpark\Transfers24\Factories\RegisterTranslatorFactory;
use Devpark\Transfers24\Factories\RegisterResponseFactory;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Requests\Transfers24 as RequestTransfers24;
use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Services\Gateways\Transfers24;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Contracts\Container\Container;
use Psr\Log\LoggerInterface;
use stdClass;
use Tests\UnitTestCase;
use Mockery as m;
use Illuminate\Foundation\Application;
use Devpark\Transfers24\Responses\Register as RegisterResponse;
use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlersTransfers24;
use Devpark\Transfers24\Exceptions\RequestException;
use Illuminate\Http\Request;
use Illuminate\Config\Repository as Config;

class ActionTest extends UnitTestCase
{
    /**
     * @var m\Mock
     */
    private $credentials;
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
    /**
     * @var m\MockInterface
     */
    private $logger;

    protected function setUp()
    {
        parent::setUp();

        $this->response = m::mock(IResponse::class, RegisterResponse::class);
        $this->credentials = m::mock(Credentials::class);
        $this->translator = m::mock(RegisterTranslator::class);
        $this->translator->shouldReceive('getCredentials')
            ->once()
            ->andReturn($this->credentials);

        $this->logger = m::mock(LoggerInterface::class);

        $this->gateway = m::mock(Transfers24::class);


        $this->response_factory = m::mock(RegisterResponseFactory::class);

        $this->action = $this->app->make(Action::class, [
            'gateway' => $this->gateway,
            'logger' => $this->logger,
        ]);
        $this->action->init($this->response_factory, $this->translator);
    }


    /** @test */
    public function execute()
    {
        //Given

        $form = m::mock(RegisterForm::class);
        $this->translator->shouldReceive('translate')
            ->once()
            ->andReturn($form);
        $this->translator->shouldReceive('configure')
            ->once();

        $this->gateway->shouldReceive('configureGateway')
            ->once()
            ->with($this->credentials);

        $gateway_response = m::mock(Response::class);

        $this->gateway->shouldReceive('callTransfers24')
            ->with($form)
            ->once()
            ->andReturn($gateway_response);

        $this->response_factory->shouldReceive('create')
            ->with($gateway_response)
            ->once()
            ->andReturn($this->response);

        //When
        $response = $this->action->execute();

        //Then
        $this->assertSame($this->response, $response);
    }

}
