<?php
declare(strict_types=1);

namespace Tests\Feature\Requests\PaymentMethodsRequest;

use Devpark\Transfers24\Contracts\PaymentMethod;
use Devpark\Transfers24\Contracts\PaymentMethodHours;
use Devpark\Transfers24\Requests\CheckCredentialsRequest;
use Devpark\Transfers24\Requests\PaymentMethodsRequest;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\PaymentMethods;
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

class PaymentMethodsTest extends UnitTestCase
{
    /**
     * @var PaymentMethodsRequest
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

        $this->request = $this->app->make(PaymentMethodsRequest::class);

    }

    /**
     * @Feature Payment Methods
     * @Scenario Getting Payment Methods
     * @Case It gets payment methods for default language
     * @test
     */
    public function it_gets_payment_methods_for_default_language()
    {
        $response = $this->makeResponse();

        $this->requestTestAccessSuccessful($response, 'pl');
        $response = $this->request->execute();

        $this->assertInstanceOf(PaymentMethods::class, $response);
        $this->assertSame(200, $response->getCode());
    }

    /**
     * @Feature Payment Methods
     * @Scenario Getting Payment Methods
     * @Case It gets payment methods for set language
     * @test
     */
    public function it_gets_payment_methods_for_set_language()
    {

        $response = $this->makeResponse();

        $this->requestTestAccessSuccessful($response, 'en');
        $this->request->setLanguage('en');
        $response = $this->request->execute();

        $this->assertInstanceOf(PaymentMethods::class, $response);
        $this->assertSame(200, $response->getCode());
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

        $payment_method = $this->makePaymentMethod();

        $this->requestTestAccessSuccessful($response, 'en');
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

        $this->requestTestAccessFailed();
        $response = $this->request->execute();

        $this->assertInstanceOf(InvalidResponse::class, $response);
        $this->assertSame(401, $response->getErrorCode());
    }

    protected function makePaymentMethod():PaymentMethod
    {
        $payment_method_hours = new class implements PaymentMethodHours{
            public $mondayToFriday = "00-24";
            public $saturday = "unavailable";
            public $sunday = "00-24";
        };

        return new class($payment_method_hours) implements PaymentMethod{
            public $name = 'name';
            public $id = 1;
            public $status = true;
            public $imgUrl = 'img-url';
            public $mobileImgUrl = 'mobile-img-url';
            public $mobile = true;
            public $availabilityHours;
            public function __construct(PaymentMethodHours $availabilityHours)
            {
                $this->availabilityHours = $availabilityHours;
            }
        };
    }

    private function requestTestAccessFailed(): void
    {
        $path = 'payment/methods/pl';

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

    /**
     * @return MockInterface
     */
    private function makeResponse(): MockInterface
    {
        $response = m::mock(ResponseInterface::class);
        $response->shouldReceive('getStatusCode')
            ->once()
            ->andReturn(200);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode(['data' => [$this->makePaymentMethod()], 'error' => '']));
        return $response;
    }

    /**
     * @param MockInterface $response
     */
    private function requestTestAccessSuccessful(MockInterface $response, $lang): void
    {
        $path = 'payment/methods/'. $lang;
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

}
