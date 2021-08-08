<?php

namespace Tests\Action;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Currency;
use Devpark\Transfers24\Exceptions\RequestExecutionException;
use Devpark\Transfers24\Factories\RegisterTranslatorFactory;
use Devpark\Transfers24\Forms\RegisterForm;
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

    protected function setUp()
    {
        parent::setUp();

        $this->response = m::mock(IResponse::class, RegisterResponse::class);
//        $transfers24 = m::mock(HandlersTransfers24::class);
        $app = m::mock(Container::class);
        $this->app->bind(Container::class, \Illuminate\Container\Container::class);
        $credentials_keeper = m::mock(Credentials::class);

        $this->translator = m::mock(RegisterTranslator::class);


        $this->handle_factory = m::mock(HandlerFactory::class);
        $this->handler = m::mock(RegisterHandler::class);

        $this->gateway = m::mock(Transfers24::class);

        $this->response_factory = m::mock(ResponseFactory::class);

        $this->action = $this->app->make(Action::class, [
            'translator' => $this->translator,
            'handler_factory' => $this->handler,
//            'app' => $this->app,
            'gateway' => $this->gateway,
            'response_factory' => $this->response_factory,
        ]);
    }


    /** @test */
    public function execute()
    {
        //Given

        $form = m::mock(RegisterForm::class);
        $this->translator->shouldReceive('translate')
//            ->with($user_data)
            ->once()
            ->andReturn($form);
//
//        $this->handle_factory->shouldReceive('create')
//            ->with()
//            ->once()
//            ->andReturn($this->handler);
//
//
        $this->handler->shouldReceive('fill')
            ->with($form)
            ->once();
//
//        $gateway_response = m::mock(GatewayResponse::class);
//
        $this->gateway->shouldReceive('callTransfers24')
            ->with($transfers24\r)
            ->once()
            ->andReturn($gateway_response);
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
        $response = $this->action->execute();

        //Then
        $this->assertSame($this->response, $response);
    }

    private function provideBaseTransactionData(): void
    {
        $this->action->setAmount(100)->setEmail('user@email.com')->setArticle('article');
    }
}
