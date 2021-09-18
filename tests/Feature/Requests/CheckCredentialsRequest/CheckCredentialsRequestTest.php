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
    /**
     * @var CheckCredentialsRequest
     */
    private $request;

    /**
     * @var MockInterface|TestConnection
     */
    private $response;
    /**
     * @var MockInterface|InvalidResponse
     */
    private $invalid_response;
    /**use Illuminate\Contracts\Config\Repository;

     * @var MockInterface
     */
    private $test_response_factory;
    /**
     * @var MockInterface
     */
    private $translator;
    /**
     * @var MockInterface
     */
    private $client;
    /**
     * @var Repository
     */
    private $config;

    protected function setUp()
    {
        parent::setUp();

        $this->client = m::mock(Client::class);


        $this->client_factory = m::mock(ClientFactory::class);
        $this->client_factory->shouldReceive('create')
            ->once()->andReturn($this->client);
        $this->app->instance(ClientFactory::class, $this->client_factory);
        $this->app->instance(Container::class, $this->app);
        $this->app->instance(\Illuminate\Container\Container::class, $this->app);
        $this->app->bind(LoggerInterface::class, TestLogger::class);
        $this->config = $this->app->make(Repository::class);
        $this->app->instance(Repository::class, $this->config);
        $this->config->set(['transfers24' => [
            'merchant_id' => 10,
            'pos_id' => 10,
            'crc' => 'crc',
            'report_key' => 'report_key',
            'test_server' => true,
            'url_return' => '',
            'url_status' => '',
            'credentials-scope' => false,
        ]]);

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
        $response = m::mock(ResponseInterface::class);

        $this->client->shouldReceive('request')
            ->with('GET', 'testAccess',
                [
                    'form_params' => [],
                    'auth' => [
                        10,
                        'report_key'
                    ],
                ])
            ->once()
            ->andReturn($response);
        $response->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('');
        $response = $this->request->execute();

        $this->assertInstanceOf(TestConnection::class, $response);
        $this->assertSame(200, $response->getCode());
    }

    /**
     * @Feature Connection with Provider
     * @Scenario Testing Connection
     * @Case Connection passed
     * @test
     */
    public function execute_was_failed_and_return_invalid_connection()
    {
        $this->client->shouldReceive('request')
        ->with('GET', 'testAccess',
            [
                'form_params' => [],
                'auth' => [
                    10,
                    'report_key'
                ],
            ])
        ->once()
        ->andThrow(new \Exception('messasge', 401));
        $response = $this->request->execute();

        $this->assertInstanceOf(InvalidResponse::class, $response);
        $this->assertSame(401, $response->getErrorCode());
    }

}
