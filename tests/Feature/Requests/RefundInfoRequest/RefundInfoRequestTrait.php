<?php
declare(strict_types=1);

namespace Tests\Feature\Requests\RefundInfoRequest;

use Devpark\Transfers24\Contracts\PaymentMethod;
use Devpark\Transfers24\Contracts\PaymentMethodHours;
use Devpark\Transfers24\Contracts\Refund;
use Devpark\Transfers24\Contracts\RefundInfo;
use Devpark\Transfers24\Contracts\RefundInfoData;
use Devpark\Transfers24\Currency;
use Devpark\Transfers24\Models\RefundQuery;
use Devpark\Transfers24\Models\RefundStatus;
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
use Symfony\Component\Translation\Exception\NotFoundResourceException;

trait RefundInfoRequestTrait
{

    protected function setConfiguration(): void
    {
        $this->config = $this->app->make(Repository::class);
        $this->app->instance(Repository::class, $this->config);
        $this->config->set([
        'transfers24' => [
            'merchant_id' => 10,
            'pos_id' => 10,
            'crc' => 'crc',
            'report_key' => 'report_key',
            'test_server' => true,
            'url_return' => '',
            'url_status' => '',
            'credentials-scope' => false,
            "url_refund_status" => "transfers24/refund-status"
        ]
    ]);
    }

    protected function mockApi(): void
    {
        $this->client = m::mock(Client::class);
        $client_factory = m::mock(ClientFactory::class);
        $client_factory->shouldReceive('create')
            ->once()->andReturn($this->client);
        $this->app->instance(ClientFactory::class, $client_factory);

    }

    /**
     * @param MockInterface $response
     */
    protected function requestGettingRefundInfoSuccessful(MockInterface $response, $order_id): void
    {
        $path = 'refund/by/orderId/' . $order_id;
        $method = 'GET';
        $request_options = [
            'auth' => [
                10,
                'report_key'
            ],
        ];
        $this->client->shouldReceive('request')
            ->with($method, $path, $request_options)
            ->once()
            ->andReturn($response);
    }

    /**
     * @param MockInterface $response
     */
    protected function requestGettingRefundInfoNotFound($order_id): void
    {
        $path = 'refund/by/orderId/' . $order_id;
        $method = 'GET';
        $request_options = [
            'auth' => [
                10,
                'report_key'
            ],
        ];
        $this->client->shouldReceive('request')
            ->with($method, $path, $request_options)
            ->once()
            ->andThrow(NotFoundResourceException::class);
    }

    /**
     * @return MockInterface
     */
    protected function makeResponse(): MockInterface
    {
        $response = m::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode(['data' => $this->makeRefundInfoData(), 'error' => '']));

        return $response;
    }

    protected function requestRefundInfoFailed(): void
    {
        $path = 'refund/by/orderId/order-id';

        $this->client->shouldReceive('request')
            ->with('GET', $path,
                [
                    'auth' => [
                        10,
                        'report_key'
                    ],
                ])
            ->once()
            ->andThrow(new \Exception('Incorrect authentication', 401));
    }

    protected function makeRefundInfo(): RefundInfo
    {
        return new class implements RefundInfo {

            public $batchId = 0;
            public $requestId = 'request-id';
            public $date = '2020-01-01';
            public $login = '2020-01-01';
            public $description = 'description';
            public $status = RefundStatus::DONE;
            public $amount = 100;
        };
    }

    protected function makeRefundInfoData(): RefundInfoData
    {
        $refund_info = $this->makeRefundInfo();

        return new class($refund_info) implements RefundInfoData {

            public $orderId = 1;
            public $sessionId = 'session-id';
            public $amount = 100;
            public $currency = Currency::PLN;
            public $refunds = [];

            public function __construct(RefundInfo $refund_info)
            {
                $this->refunds[] = $refund_info;
            }
        };
    }
}
