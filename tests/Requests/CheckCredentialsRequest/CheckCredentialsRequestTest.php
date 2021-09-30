<?php

declare(strict_types=1);

namespace Tests\Requests\CheckCredentialsRequest;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Factories\ActionFactory;
use Devpark\Transfers24\Factories\TestResponseFactory;
use Devpark\Transfers24\Factories\TestTranslatorFactory;
use Devpark\Transfers24\Requests\CheckCredentialsRequest;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\TestConnection;
use Devpark\Transfers24\Services\Handlers\Transfers24;
use Devpark\Transfers24\Translators\TestTranslator;
use Mockery as m;
use Tests\UnitTestCase;

class CheckCredentialsRequestTest extends UnitTestCase
{
    /**
     * @var CheckCredentialsRequest
     */
    private $request;

    /**
     * @var \Mockery\MockInterface|Transfers24
     */
    private $handler;

    /**
     * @var \Mockery\MockInterface|TestConnection
     */
    private $response;

    /**
     * @var \Mockery\MockInterface|InvalidResponse
     */
    private $invalid_response;

    /**
     * @var m\MockInterface
     */
    private $test_translator_factory;

    /**
     * @var m\MockInterface
     */
    private $credentials_keeper;

    /**
     * @var m\MockInterface
     */
    private $action_factory;

    /**
     * @var m\MockInterface
     */
    private $test_response_factory;

    /**
     * @var m\MockInterface
     */
    private $translator;

    protected function setUp()
    {
        parent::setUp();
        $this->translator = m::mock(TestTranslator::class);
        $this->response = m::mock(TestConnection::class);
        $this->invalid_response = m::mock(InvalidResponse::class);

        $this->test_translator_factory = m::mock(TestTranslatorFactory::class);
        $this->credentials_keeper = m::mock(Credentials::class);
        $this->action_factory = m::mock(ActionFactory::class);
        $this->test_response_factory = m::mock(TestResponseFactory::class);

        $this->request = $this->app->make(CheckCredentialsRequest::class, [
            'credentials_keeper' => $this->credentials_keeper,
            'test_translator_factory' => $this->test_translator_factory,
            'action_factory' => $this->action_factory,
            'test_response_factory' => $this->test_response_factory,
        ]);
    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Connection passed
     * @test
     */
    public function execute_was_call_transfers_provider_test_connection()
    {
        $this->mockHandlerMethods($this->response);

        $response = $this->request->execute();

        $this->assertInstanceOf(TestConnection::class, $response);
    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Connection failed
     * @test
     */
    public function execute_throw_invalid_response()
    {
        $this->mockHandlerMethods($this->invalid_response);

        $response = $this->request->execute();

        $this->assertInstanceOf(InvalidResponse::class, $response);
    }

    protected function mockHandlerMethods($response): void
    {
        $this->test_translator_factory->shouldReceive('create')
            ->once()
            ->andReturn($this->translator);

        $action = m::mock(Action::class);

        $action->shouldReceive('execute')
            ->once()
            ->andReturn($response);

        $this->action_factory->shouldReceive('create')
            ->once()
            ->andReturn($action);
    }
}
