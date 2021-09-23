<?php
declare(strict_types=1);

namespace Tests\Feature\Requests\CheckCredentialsRequest;

use Devpark\Transfers24\Requests\CheckCredentialsRequest;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\Response;
use Devpark\Transfers24\Responses\TestConnection;
use Devpark\Transfers24\Services\Gateways\ClientFactory;
use Devpark\Transfers24\Translators\TestTranslator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Log\Logger;
use Illuminate\Log\LogManager;
use Mockery as m;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use Tests\UnitTestCase;

class CheckCredentialsRequestTest extends UnitTestCase
{
    use CheckCredentialsRequestTrait;
    /**
     * @var CheckCredentialsRequest
     */
    private $request;

    /**
     * @var MockInterface
     */
    private $client;

    protected function setUp()
    {
        parent::setUp();

        $this->skipLogs();
        $this->bindAppContainer();
        $this->mockApi();

        $this->setConfiguration();

        $this->request = $this->app->make(CheckCredentialsRequest::class);

    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Connection passed
     * @test
     */
    public function execute_was_call_transfers_provider_test_connection()
    {

        //When
        $response = $this->makeResponse();
        $this->requestTestAccessSuccessful($response);
        $response = $this->request->execute();

        //Then
        $this->assertInstanceOf(TestConnection::class, $response);
        $this->assertSame(200, $response->getCode());
    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Connection failed
     * @test
     */
    public function execute_was_failed_and_return_invalid_connection()
    {
        //When
        $this->requestTestAccessFailed();
        $response = $this->request->execute();

        //Then
        $this->assertInstanceOf(InvalidResponse::class, $response);
        $this->assertSame(401, $response->getErrorCode());
    }

}
