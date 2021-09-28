<?php
declare(strict_types=1);

namespace Tests\Feature\Requests\RegisterOfflineRequest;

use Devpark\Transfers24\Contracts\PaymentMethod;
use Devpark\Transfers24\Contracts\PaymentMethodHours;
use Devpark\Transfers24\Contracts\Refund;
use Devpark\Transfers24\Contracts\RegisterOffline;
use Devpark\Transfers24\Contracts\RegisterOfflineData;
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

trait RegisterOfflineRequestTrait
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
    protected function requestGettingRegisterOfflineSuccessful(MockInterface $response, $token): void
    {
        $path = 'transaction/registerOffline';
        $method = 'POST';
        $request_options = [
            'auth' => [
                10,
                'report_key'
            ],
            'form_params' => [
                "token" => $token,
            ]
        ];
        $this->client->shouldReceive('request')
            ->with($method, $path, $request_options)
            ->once()
            ->andReturn($response);
    }

    /**
     * @param MockInterface $response
     */
    protected function requestGettingRegisterOfflineNotFound($token): void
    {
        $path = 'transaction/registerOffline';
        $method = 'POST';
        $request_options = [
            'auth' => [
                10,
                'report_key'
            ],
            'form_params' => [
                "token" => $token,
            ]
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
            ->andReturn(json_encode(['data' => $this->makeRegisterOffline(), 'error' => '']));

        return $response;
    }

    protected function requestRegisterOfflineFailed($token): void
    {

        $path = 'transaction/registerOffline';

        $this->client->shouldReceive('request')
            ->with('POST', $path,
                [
                    'auth' => [
                        10,
                        'report_key'
                    ],
                    'form_params' => [
                        "token" => $token,
                    ]
                ])
            ->once()
            ->andThrow(new \Exception('Incorrect authentication', 401));
    }

    protected function makeRegisterOffline(): RegisterOffline
    {
        return new class implements RegisterOffline {

            public $orderId = 0;
            public $sessionId = 'sessionId';
            public $amount = 0;
            public $statement = 'statement';
            public $iban = 'iban';
            public $ibanOwner = 'ibanOwner';
            public $ibanOwnerAddress = 'ibanOwnerAddress';
        };
    }
}
