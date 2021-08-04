<?php
declare(strict_types=1);

namespace Tests\Requests\CheckCredentialsRequest;

use Devpark\Transfers24\Exceptions\EmptyCredentialsException;
use Devpark\Transfers24\Exceptions\NoEnvironmentChosenException;
use Devpark\Transfers24\Requests\CheckCredentialsRequest;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\Response;
use Devpark\Transfers24\Responses\TestConnection;
use Devpark\Transfers24\Services\Handlers\Transfers24;
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

    protected function setUp()
    {
        parent::setUp();
        $this->handler = \Mockery::mock(Transfers24::class);
        $this->response = \Mockery::mock(TestConnection::class);
        $this->invalid_response = \Mockery::mock(InvalidResponse::class);

        $this->request = $this->app->make(CheckCredentialsRequest::class, [
            'handler' => $this->handler,
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
        $this->handler->shouldReceive('checkCredentials')
            ->once()
            ->andReturn($response);
        $this->handler->shouldReceive('viaCredentials')
            ->once()
            ->andReturnSelf();
    }

}
