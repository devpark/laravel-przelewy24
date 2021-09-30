<?php
declare(strict_types=1);

namespace Tests\Feature\Requests\PaymentNotificationRequest;

use Devpark\Transfers24\Contracts\Refund;
use Devpark\Transfers24\Models\RefundQuery;
use Devpark\Transfers24\Requests\PaymentNotificationRequest;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\NotificationResponse;
use Devpark\Transfers24\Responses\RefundResponse;
use Devpark\Transfers24\Services\Amount;
use Devpark\Transfers24\Services\Gateways\ClientFactory;
use GuzzleHttp\Client;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Mockery as m;
use Mockery\ExpectationInterface;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use Ramsey\Uuid\UuidFactory;
use Tests\UnitTestCase;

class PaymentNotificationRequestTest extends UnitTestCase
{
    use PaymentNotificationRequestTrait;
    /**
     * @var PaymentNotificationRequest
     */
    private $request;

    /**
     * @var MockInterface
     */
    private $client;
    /**
     * @var MockInterface
     */
    private $entry;

    protected function setUp()
    {
        parent::setUp();
        $this->entry = m::mock(Request::class);

        $this->request = $this->app->make(PaymentNotificationRequest::class, [
            'request' => $this->entry,
        ]);
    }

    /**
     * @Feature Payments
     * @Scenario notify Payment
     * @Case Payment was finished
     * @test
     */
    public function execute_notification_was_received()
    {
        //Given
        $notification = $this->makePaymentNotification();
        $received = $this->whenReceiveNotification($notification);

        //When
        $response = $this->request->execute();

        //Then
        $this->assertSame($notification->merchantId, $response->getNotification()->merchantId);
        $this->assertSame($notification->posId, $response->getNotification()->posId);
        $this->assertSame($notification->sessionId, $response->getNotification()->sessionId);
        $this->assertSame($notification->amount, $response->getNotification()->amount);
        $this->assertSame($notification->originAmount, $response->getNotification()->originAmount);
        $this->assertSame($notification->currency, $response->getNotification()->currency);
        $this->assertSame($notification->orderId, $response->getNotification()->orderId);
        $this->assertSame($notification->methodId, $response->getNotification()->methodId);
        $this->assertSame($notification->statement, $response->getNotification()->statement);
        $this->assertSame($notification->sign, $response->getNotification()->sign);

        $received->once();


    }

    private function whenReceiveNotification($notification): ExpectationInterface
    {
        $notification_raw = $notification->toArray();
        return $this->entry->shouldReceive('all')->andReturn($notification_raw);
    }
}
