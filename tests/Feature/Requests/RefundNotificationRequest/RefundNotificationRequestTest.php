<?php
declare(strict_types=1);

namespace Tests\Feature\Requests\RefundNotificationRequest;

use Devpark\Transfers24\Contracts\Refund;
use Devpark\Transfers24\Models\RefundQuery;
use Devpark\Transfers24\Requests\RefundNotificationRequest;
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

class RefundNotificationRequestTest extends UnitTestCase
{
    use RefundNotificationRequestTrait;
    /**
     * @var RefundNotificationRequest
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

        $this->request = $this->app->make(RefundNotificationRequest::class);

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

}
