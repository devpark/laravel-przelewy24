<?php
declare(strict_types=1);

namespace Tests\Feature\Requests\RefundRequestTest;

use Devpark\Transfers24\Contracts\PaymentMethod;
use Devpark\Transfers24\Contracts\PaymentMethodHours;
use Devpark\Transfers24\Contracts\Refund;
use Devpark\Transfers24\Models\RefundQuery;
use Devpark\Transfers24\Requests\CheckCredentialsRequest;
use Devpark\Transfers24\Requests\PaymentMethodsRequest;
use Devpark\Transfers24\Requests\RefundRequest;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\PaymentMethods;
use Devpark\Transfers24\Responses\RefundResponse;
use Devpark\Transfers24\Responses\Response;
use Devpark\Transfers24\Responses\TestConnection;
use Devpark\Transfers24\Services\Amount;
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

class RefundRequestTest extends UnitTestCase
{
    /**
     * @var RefundRequest
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
            "url_refund_status" => "transfers24/refund-status"
        ]]);

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
        $response = $this->makeResponse();
        $refund_inquiry = $this->makeRefundQuery();
        $refund_query_raw = $refund_inquiry->toArray();


        $this->requestRefundSuccessful($response, $refund_inquiry);
        $response = $this->request
            ->addRefundInquiry($refund_query_raw['orderId'], $refund_query_raw['sessionId'], $refund_query_raw['amount'], $refund_query_raw['description'])
            ->execute();

        $this->assertInstanceOf(RefundResponse::class, $response);
        $this->assertSame(201, $response->getCode());
    }

    /**
     * @Feature Refund
     * @Scenario init Refund
     * @Case Refund was started
     * @test
     */
    public function refund_was_started_it_get_()
    {

        $response = $this->makeResponse();
        $refund_query = $this->makeRefundQuery();
        $refund_query_raw = $refund_query->toArray();

        $this->requestRefundSuccessful($response, $refund_query);
        $response = $this->request
            ->addRefundInquiry($refund_query_raw['orderId'], $refund_query_raw['sessionId'], Amount::get($refund_query_raw['amount']), $refund_query_raw['description'])
            ->execute();

        $this->assertInstanceOf(RefundResponse::class, $response);
        $this->assertSame(201, $response->getCode());
    }

    /**
     * @Feature Payment Methods
     * @Scenario Getting Payment Methods
     * @Case It gets payment methods collection
     * @test
     */
    public function it_gets_payment_methods_collection()
    {

        $response = $this->makeResponse();

        $payment_method = $this->makeRefund();

        $this->requestRefundSuccessful($response, 'en');
        $this->request->setLanguage('en');
        $response = $this->request->execute();

        $this->assertInstanceOf(PaymentMethods::class, $response);
        $this->assertSame($payment_method->name, $response->getPaymentMethods()[0]['name']);
        $this->assertSame($payment_method->id, $response->getPaymentMethods()[0]['id']);
        $this->assertSame($payment_method->status, $response->getPaymentMethods()[0]['status']);
        $this->assertSame($payment_method->imgUrl, $response->getPaymentMethods()[0]['imgUrl']);
        $this->assertSame($payment_method->mobileImgUrl, $response->getPaymentMethods()[0]['mobileImgUrl']);
        $this->assertSame($payment_method->mobile, $response->getPaymentMethods()[0]['mobile']);
        $this->assertSame($payment_method->availabilityHours->mondayToFriday, $response->getPaymentMethods()[0]['availabilityHours']['mondayToFriday']);
        $this->assertSame($payment_method->availabilityHours->saturday, $response->getPaymentMethods()[0]['availabilityHours']['saturday']);
        $this->assertSame($payment_method->availabilityHours->sunday, $response->getPaymentMethods()[0]['availabilityHours']['sunday']);
    }

    /**
     * @Feature Payment Methods
     * @Scenario Getting Payment Methods
     * @Case It gets payment methods for set language
     * @test
     */
    public function execute_was_failed_and_return_invalid_response()
    {

        $this->requestRefundFailed();
        $response = $this->request->execute();

        $this->assertInstanceOf(InvalidResponse::class, $response);
        $this->assertSame(401, $response->getErrorCode());
    }

    protected function makeRefund():Refund
    {
        return new class implements Refund{
            public $orderId =  0;
            public $sessionId =  "string";
            public $amount =  0;
            public $description =  "string";
            public $status =  true;
            public $message =  "success";
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
    private function makeResponse(): MockInterface
    {
        $response = m::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(201);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode(['data' => [$this->makeRefund()], 'error' => '']));
        return $response;
    }

    /**
     * @param MockInterface $response
     */
    private function requestRefundSuccessful(MockInterface $response, RefundQuery $refund_query): void
    {
        $path = 'transaction/refund';
        $method = 'POST';
        $refund_query_raw = $refund_query->toArray();
        $request_options = [
            'auth' => [
                10,
                'report_key'
            ],
            'form_params' => [
                "requestId" => "request-id",
                "refunds" => [
                    [
                        'orderId' => $refund_query_raw['orderId'],
                        'sessionId' => $refund_query_raw['sessionId'],
                        'amount' => Amount::get($refund_query_raw['amount']),
                        'description' => $refund_query_raw['description'],
                    ]
                ],
                "refundsUuid" => "refund-uuid",
                "urlStatus" => "transfers24/refund-status",
            ]
        ];
        $this->client->shouldReceive('request')
            ->with($method, $path, $request_options)
            ->once()
            ->andReturn($response);
    }

    private function makeRefundQuery():RefundQuery
    {
        return new RefundQuery(1, 'order-id', 100, 'description');

    }

}
