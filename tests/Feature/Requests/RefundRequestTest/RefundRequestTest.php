<?php
declare(strict_types=1);

namespace Tests\Feature\Requests\RefundRequestTest;

use Devpark\Transfers24\Contracts\Refund;
use Devpark\Transfers24\Models\RefundQuery;
use Devpark\Transfers24\Requests\RefundRequest;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\RefundResponse;
use Devpark\Transfers24\Services\Amount;
use Devpark\Transfers24\Services\Gateways\ClientFactory;
use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\UrlGenerator;
use Mockery as m;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use Ramsey\Uuid\UuidFactory;
use Tests\UnitTestCase;

class RefundRequestTest extends UnitTestCase
{
    /**
     * @var RefundRequest
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

        $this->request = $this->app->make(RefundRequest::class);

    }

    /**
     * @Feature Refund
     * @Scenario init Refund
     * @Case Refund was started
     * @test
     */
    public function refund_was_started_it_get_success_code()
    {
        //Given
        $refund_inquiry = $this->makeRefundQuery();
        $refund_query_raw = $refund_inquiry->toArray();

        //Then
        $this->thenRequestRefundSuccessful($refund_inquiry);

        //When
        $response = $this->request
            ->addRefundInquiry($refund_query_raw['orderId'], $refund_query_raw['sessionId'], $refund_query_raw['amount'], $refund_query_raw['description'])
            ->execute();

        //Then
        $this->assertInstanceOf(RefundResponse::class, $response);
        $this->assertSame(201, $response->getCode());
    }

    /**
     * @Feature Refund
     * @Scenario init Refund
     * @Case It gets Refunds Collection
     * @test
     */
    public function it_gets_refunds_collection()
    {
        //Given
        $refund_inquiry = $this->makeRefundQuery();
        $refund_query_raw = $refund_inquiry->toArray();


        //Then
        $this->thenRequestRefundSuccessful($refund_inquiry);

        //When
        $response = $this->request
            ->addRefundInquiry($refund_query_raw['orderId'], $refund_query_raw['sessionId'], $refund_query_raw['amount'], $refund_query_raw['description'])
            ->execute();

        //Then
        $this->assertSame($refund_query_raw['orderId'], $response->getRefunds()[0]['orderId']);
        $this->assertSame($refund_query_raw['sessionId'], $response->getRefunds()[0]['sessionId']);
        $this->assertSame($refund_query_raw['amount'], $response->getRefunds()[0]['amount']);
        $this->assertSame($refund_query_raw['description'], $response->getRefunds()[0]['description']);


    }

    /**
     * @Feature Refund
     * @Scenario init Refund
     * @Case It rejected because authorization failed
     * @test
     */
    public function execute_was_failed_and_return_invalid_response()
    {
        //Given
        $this->requestRefundFailed();

        //When
        $response = $this->request->execute();

        //Then
        $this->assertInstanceOf(InvalidResponse::class, $response);
        $this->assertSame(401, $response->getErrorCode());
    }

    protected function makeRefund(RefundQuery $refund_query):Refund
    {
        return new class($refund_query) implements Refund{
            public $orderId =  0;
            public $sessionId =  "string";
            public $amount =  0;
            public $description =  "string";
            public $status =  true;
            public $message =  "success";

            public function __construct(RefundQuery $refund_query)
            {
                $refund_query_raw = $refund_query->toArray();
                $this->orderId = $refund_query_raw['orderId'];
                $this->sessionId = $refund_query_raw['sessionId'];
                $this->amount = $refund_query_raw['amount'];
                $this->description = $refund_query_raw['description'];
            }
        };
    }

    private function requestRefundFailed(): void
    {
        $this->client->shouldReceive('request')
            ->once()
            ->andThrow(new \Exception('Incorrect authentication', 401));
    }

    /**
     * @return MockInterface
     */
    private function makeResponse($refund_query): MockInterface
    {
        $response = m::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(201);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode(['data' => [$this->makeRefund($refund_query)], 'error' => '']));
        return $response;
    }

    /**
     * @param MockInterface $response
     */
    private function thenRequestRefundSuccessful(RefundQuery $refund_query): void
    {
        $uuid = $this->generateUuid();
        $path = 'transaction/refund';
        $method = 'POST';
        $refund_query_raw = $refund_query->toArray();
        $request_options = [
            'auth' => [
                10,
                'report_key'
            ],
            'form_params' => [
                "requestId" => $uuid,
                "refunds" => [
                    [
                        'orderId' => $refund_query_raw['orderId'],
                        'sessionId' => $refund_query_raw['sessionId'],
                        'amount' => Amount::get($refund_query_raw['amount']),
                        'description' => $refund_query_raw['description'],
                    ]
                ],
                "refundsUuid" => $uuid,
                "urlStatus" => $this->app->make(UrlGenerator::class)->to("transfers24/refund-status"),
            ]
        ];
        $response = $this->makeResponse($refund_query);
        $this->client->shouldReceive('request')
            ->with($method, $path, $request_options)
            ->once()
            ->andReturn($response);
    }

    private function makeRefundQuery():RefundQuery
    {
        return new RefundQuery(1, 'order-id', 100, 'description');

    }

    protected function mockApi(): void
    {
        $this->client = m::mock(Client::class);
        $client_factory = m::mock(ClientFactory::class);
        $client_factory->shouldReceive('create')
            ->once()->andReturn($this->client);
        $this->app->instance(ClientFactory::class, $client_factory);

    }

    protected function setConfiguration(): void
    {
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
            "url_refund_status" => "transfers24/refund-status"
        ]]);
    }

    protected function skipLogs(): void
    {
        $this->app->bind(LoggerInterface::class, TestLogger::class);
    }

    protected function generateUuid(): string
    {
        $uuid_factory = m::mock(UuidFactory::class);
        $this->app->instance(UuidFactory::class, $uuid_factory);
        $uuid_factory->shouldReceive('uuid4')->andReturnSelf();

        $uuid = 'uuid-id';
        $uuid_factory->shouldReceive('toString')->andReturn($uuid);

        return $uuid;
    }

    protected function bindAppContainer(): void
    {
        $this->app->instance(Container::class, $this->app);
        $this->app->instance(\Illuminate\Container\Container::class, $this->app);
    }

}
