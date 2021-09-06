<?php

namespace Tests\Requests\Transfers24\Init;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Currency;
use Devpark\Transfers24\Exceptions\RequestExecutionException;
use Devpark\Transfers24\Factories\RegisterTranslatorFactory;
use Devpark\Transfers24\Requests\Transfers24 as RequestTransfers24;
use Devpark\Transfers24\Services\Gateways\Transfers24;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Contracts\Container\Container;
use stdClass;
use Tests\UnitTestCase;
use Mockery as m;
use Illuminate\Foundation\Application;
use Devpark\Transfers24\Responses\Register as RegisterResponse;
use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlersTransfers24;
use Devpark\Transfers24\Exceptions\RequestException;
use Illuminate\Http\Request;
use Illuminate\Config\Repository as Config;

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

    protected function setUp()
    {
        parent::setUp();

        $this->response = m::mock(IResponse::class, RegisterResponse::class);
        $transfers24 = m::mock(HandlersTransfers24::class);
        $app = m::mock(Container::class);
        $this->app->bind(Container::class, \Illuminate\Container\Container::class);
        $credentials_keeper = m::mock(Credentials::class);

        $this->translator_factory = m::mock(RegisterTranslatorFactory::class);
        $this->translator = m::mock(RegisterTranslator::class);

        $this->register_transaction = m::mock(Action::class);

        $this->handle_factory = m::mock(HandlerFactory::class);
        $this->handler = m::mock(RegisterHandler::class);

        $this->gateway = m::mock(Transfers24::class);

        $this->response_factory = m::mock(ResponseFactory::class);

        $this->request = $this->app->make(RequestTransfers24::class, [
            'response' => $this->response,
            'transfers24' => $transfers24,
//            'app' => $this->app,
            'credentials_keeper' => $credentials_keeper,
            'translator_factory' => $this->translator_factory,
            'register_transaction' => $this->register_transaction
        ]);
    }


    /** @test */
    public function init()
    {
        //Given
        $this->provideBaseTransactionData();

        $this->translator_factory->shouldReceive('create')
//            ->with($user_data)
            ->once()
            ->andReturn($this->translator);
//
//        $this->handle_factory->shouldReceive('create')
//            ->with()
//            ->once()
//            ->andReturn($this->handler);
//
//
//        $this->handler->shouldReceive('fill')
//            ->with($this->translator)
//            ->once();
//
//        $gateway_response = m::mock(GatewayResponse::class);
//
//        $this->gateway->shouldReceive('send')
//            ->with($this->handler)
//            ->once()
//            ->andReturn($gateway_response);
//
//        $this->response_factory->shouldReceive('create')
//            ->with($gateway_response)
//            ->once()
//            ->andReturn($this->response);

        $this->register_transaction->shouldReceive('execute')
//            ->with($gateway_response)
            ->once()
            ->andReturn($this->response);

        //When
        $response = $this->request->init();

        //Then
        $this->assertSame($this->response, $response);
    }

    private function provideBaseTransactionData(): void
    {
        $this->request->setAmount(100)->setEmail('user@email.com')->setArticle('article');
    }
}