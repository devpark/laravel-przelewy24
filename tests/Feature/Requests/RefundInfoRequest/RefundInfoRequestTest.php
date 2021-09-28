<?php
declare(strict_types=1);

namespace Tests\Feature\Requests\RefundInfoRequest;

use Devpark\Transfers24\Contracts\PaymentMethod;
use Devpark\Transfers24\Contracts\PaymentMethodHours;
use Devpark\Transfers24\Requests\CheckCredentialsRequest;
use Devpark\Transfers24\Requests\PaymentMethodsRequest;
use Devpark\Transfers24\Requests\RefundInfoRequest;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\PaymentMethods;
use Devpark\Transfers24\Responses\Response;
use Devpark\Transfers24\Responses\TestConnection;
use Devpark\Transfers24\Responses\RefundInfoResponse;
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

class RefundInfoRequestTest extends UnitTestCase
{
    use RefundInfoRequestTrait;
    /**
     * @var RefundInfoRequest
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

        $this->request = $this->app->make(RefundInfoRequest::class);

    }

    /**
     * @Feature Refunds
     * @Scenario Getting RefundInfo
     * @Case It gets refund info by order-id
     * @test
     */
    public function it_gets_successful_status()
    {
        $response = $this->makeResponse();

        $order_id = 'order-id';
        $this->requestGettingRefundInfoSuccessful($response, $order_id);
        $response = $this->request->setOrderId($order_id)->execute();

        $this->assertInstanceOf(RefundInfoResponse::class, $response);
        $this->assertSame(200, $response->getCode());
    }

    /**
     * @Feature Refunds
     * @Scenario Getting RefundInfo
     * @Case It returns empty data when not found refund
     * @test
     */
    public function it_gets_empty_transaction()
    {

        $order_id = 'known-order-id';
        $this->requestGettingRefundInfoNotFound($order_id);
        $this->request->setOrderId($order_id);
        $response = $this->request->execute();
        $this->assertInstanceOf(InvalidResponse::class, $response);
//        $this->assertSame(404, $response->getErrorCode());
    }


    /**
     * @Feature Refunds
     * @Scenario Getting RefundInfo
     * @Case It gets transaction by order-id
     * @test
     */
    public function it_gets_transaction_details()
    {

        $response = $this->makeResponse();

        $refund_info = $this->makeRefundInfoData();

        $order_id = 'order-id';
        $this->requestGettingRefundInfoSuccessful($response, $order_id);
        $this->request->setOrderId($order_id);
        $response = $this->request->execute();

        $this->assertInstanceOf(RefundInfoResponse::class, $response);

        $this->assertSame($refund_info->orderId, $response->getRefundInfo()['orderId']);
        $this->assertSame($refund_info->sessionId, $response->getRefundInfo()['sessionId']);
        $this->assertSame($refund_info->amount, $response->getRefundInfo()['amount']);
        $this->assertSame($refund_info->currency, $response->getRefundInfo()['currency']);
        $this->assertSame($refund_info->refunds[0]->batchId, $response->getRefundInfo()['refunds'][0]['batchId']);
        $this->assertSame($refund_info->refunds[0]->requestId, $response->getRefundInfo()['refunds'][0]['requestId']);
        $this->assertSame($refund_info->refunds[0]->date, $response->getRefundInfo()['refunds'][0]['date']);
        $this->assertSame($refund_info->refunds[0]->login, $response->getRefundInfo()['refunds'][0]['login']);
        $this->assertSame($refund_info->refunds[0]->description, $response->getRefundInfo()['refunds'][0]['description']);
        $this->assertSame($refund_info->refunds[0]->status, $response->getRefundInfo()['refunds'][0]['status']);
        $this->assertSame($refund_info->refunds[0]->amount, $response->getRefundInfo()['refunds'][0]['amount']);

    }

    /**
     * @Feature Refunds
     * @Scenario Getting RefundInfo
     * @Case It returns invalid data when authentication failed
     * @test
     */
    public function execute_was_failed_and_return_invalid_response()
    {

        $this->requestRefundInfoFailed();
        $response = $this->request->setOrderId('order-id')->execute();

        $this->assertInstanceOf(InvalidResponse::class, $response);
        $this->assertSame(401, $response->getErrorCode());
    }

}
