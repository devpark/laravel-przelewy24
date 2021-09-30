<?php

namespace Tests\Requests\Transfers24\Init;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Factories\ActionFactory;
use Devpark\Transfers24\Factories\RegisterResponseFactory;
use Devpark\Transfers24\Factories\RegisterTranslatorFactory;
use Devpark\Transfers24\Requests\Transfers24 as RequestTransfers24;
use Devpark\Transfers24\Responses\Register as RegisterResponse;
use Devpark\Transfers24\Services\Gateways\Transfers24;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Contracts\Container\Container;
use Mockery as m;
use Tests\UnitTestCase;

class Transfers24Test extends UnitTestCase
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
     * @var RequestTransfers24
     */
    private $request;

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
    private $handle_factory;

    /**
     * @var m\MockInterface
     */
    private $handler;

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
    private $register_transaction;

    /**
     * @var m\MockInterface
     */
    private $action_factory;

    protected function setUp()
    {
        parent::setUp();

        $this->response = m::mock(IResponse::class, RegisterResponse::class);
        $this->app->bind(Container::class, \Illuminate\Container\Container::class);
        $this->credentials = m::mock(Credentials::class);

        $this->translator_factory = m::mock(RegisterTranslatorFactory::class);
        $this->translator = m::mock(RegisterTranslator::class);

        $this->register_transaction = m::mock(Action::class);

        $this->gateway = m::mock(Transfers24::class);

        $this->response_factory = m::mock(RegisterResponseFactory::class);

        $this->action_factory = m::mock(ActionFactory::class);

        $this->request = $this->app->make(RequestTransfers24::class, [
            'response_factory' => $this->response_factory,
            'credentials_keeper' => $this->credentials,
            'translator_factory' => $this->translator_factory,
            'register_transaction' => $this->register_transaction,
            'action_factory' => $this->action_factory,
        ]);
    }

    /** @test */
    public function init()
    {
        //Given
        $this->provideBaseTransactionData();

        $this->translator_factory->shouldReceive('create')
            ->with($this->request, $this->credentials)
            ->once()
            ->andReturn($this->translator);

        $action = m::mock(Action::class);
        $this->action_factory->shouldReceive('create')
            ->with($this->response_factory, $this->translator)
            ->once()
            ->andReturn($action);

        $action->shouldReceive('execute')
            ->once()
            ->andReturn($this->response);

        //When
        $response = $this->request->init();

        //Then
        $this->assertSame($this->response, $response);
    }

    private function provideBaseTransactionData(): void
    {
        $this->request->setAmount(100)
            ->setEmail('user@email.com');
    }
}
