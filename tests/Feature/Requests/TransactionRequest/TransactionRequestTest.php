<?php
declare(strict_types=1);

namespace Tests\Feature\Requests\TransactionRequest;

use Devpark\Transfers24\Contracts\PaymentMethod;
use Devpark\Transfers24\Contracts\PaymentMethodHours;
use Devpark\Transfers24\Requests\CheckCredentialsRequest;
use Devpark\Transfers24\Requests\PaymentMethodsRequest;
use Devpark\Transfers24\Requests\TransactionRequest;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\PaymentMethods;
use Devpark\Transfers24\Responses\Response;
use Devpark\Transfers24\Responses\TestConnection;
use Devpark\Transfers24\Responses\TransactionResponse;
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
     * @var TransactionRequest
     */
    private $request;

    /**
     * @var MockInterface
     */
    private $client;

    protected function setUp()
    {
        parent::setUp();

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
        $response = $this->request->setSessionId('session-id')->execute();

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

//        $response = $this->makeResponse();

        $session_id = 'known-session-id';
        $this->requestGettingTransactionNotFound($session_id);
        $this->request->setSessionId($session_id);
        $response = $this->request->execute();
        $this->assertInstanceOf(InvalidResponse::class, $response);
//        $this->assertSame(404, $response->getErrorCode());
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

        $transaction = $this->makeTransaction();

        $this->requestGettingTransactionSuccessful($response, 'session-id');
        $this->request->setSessionId('session-id');
        $response = $this->request->execute();

        $this->assertInstanceOf(TransactionResponse::class, $response);

        $this->assertSame($transaction->orderId, $response->getTransaction()['orderId']);
        $this->assertSame($transaction->sessionId, $response->getTransaction()['sessionId']);
        $this->assertSame($transaction->status, $response->getTransaction()['status']);
        $this->assertSame($transaction->amount, $response->getTransaction()['amount']);
        $this->assertSame($transaction->currency, $response->getTransaction()['currency']);
        $this->assertSame($transaction->date, $response->getTransaction()['date']);
        $this->assertSame($transaction->dateOfTransaction, $response->getTransaction()['dateOfTransaction']);
        $this->assertSame($transaction->clientEmail, $response->getTransaction()['clientEmail']);
        $this->assertSame($transaction->accountMD5, $response->getTransaction()['accountMD5']);
        $this->assertSame($transaction->paymentMethod, $response->getTransaction()['paymentMethod']);
        $this->assertSame($transaction->description, $response->getTransaction()['description']);
        $this->assertSame($transaction->clientName, $response->getTransaction()['clientName']);
        $this->assertSame($transaction->clientAddress, $response->getTransaction()['clientAddress']);
        $this->assertSame($transaction->clientCity, $response->getTransaction()['clientCity']);
        $this->assertSame($transaction->clientPostcode, $response->getTransaction()['clientPostcode']);
        $this->assertSame($transaction->batchId, $response->getTransaction()['batchId']);
        $this->assertSame($transaction->fee, $response->getTransaction()['fee']);

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
        $response = $this->request->setSessionId('session-id')->execute();

        $this->assertInstanceOf(InvalidResponse::class, $response);
        $this->assertSame(401, $response->getErrorCode());
    }

}
