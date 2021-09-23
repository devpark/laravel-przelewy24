<?php
declare(strict_types=1);

namespace Tests\Feature\Requests\TransactionRequest;

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

class TransactionRequestTest extends UnitTestCase
{
    use TransactionRequestTrait;
    /**
     * @var PaymentMethodsRequest
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

        $this->request = $this->app->make(TransactionRequest::class);

    }

    /**
     * @Feature Payments
     * @Scenario Getting Transaction
     * @Case It gets transaction by session-id
     * @test
     */
    public function it_gets_successful_status()
    {
        $response = $this->makeResponse();

        $this->requestGettingTransactionSuccessful($response, 'session-id');
        $response = $this->request->execute();

        $this->assertInstanceOf(TransactionResponse::class, $response);
        $this->assertSame(200, $response->getCode());
    }

    /**
     * @Feature Payments
     * @Scenario Getting Transaction
     * @Case It return empty data when not found transaction
     * @test
     */
    public function it_gets_empty_transaction()
    {

        $response = $this->makeResponse();

        $this->requestGettingTransactionSuccessful($response, 'en');
        $this->request->setLanguage('en');
        $response = $this->request->execute();

        $this->assertInstanceOf(PaymentMethods::class, $response);
        $this->assertSame(200, $response->getCode());
    }


    /**
     * @Feature Payments
     * @Scenario Getting Transaction
     * @Case It gets transaction by session-id
     * @test
     */
    public function it_gets_transaction_details()
    {

        $response = $this->makeResponse();

        $payment_method = $this->makeTransaction();

        $this->requestGettingTransactionSuccessful($response, 'en');
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
     * @Feature Payments
     * @Scenario Getting Transaction
     * @Case It return invalid data when authentication failed
     * @test
     */
    public function execute_was_failed_and_return_invalid_response()
    {

        $this->requestTestAccessFailed();
        $response = $this->request->execute();

        $this->assertInstanceOf(InvalidResponse::class, $response);
        $this->assertSame(401, $response->getErrorCode());
    }

}
