<?php

declare(strict_types=1);

namespace Tests\Feature\Requests\RegisterOfflineRequest;

use Devpark\Transfers24\Requests\RegisterOfflineRequest;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\RegisterOfflineResponse;
use Mockery\MockInterface;
use Tests\UnitTestCase;

class RegisterOfflineRequestTest extends UnitTestCase
{
    use RegisterOfflineRequestTrait;

    /**
     * @var RegisterOfflineRequest
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

        $this->request = $this->app->make(RegisterOfflineRequest::class);
    }

    /**
     * @Feature Payments
     * @Scenario register offline
     * @Case It gets refund info by order-id
     * @test
     */
    public function it_gets_successful_status()
    {
        $response = $this->makeResponse();

        $token = 'token';
        $this->requestGettingRegisterOfflineSuccessful($response, $token);
        $response = $this->request->setToken($token)->execute();

        $this->assertInstanceOf(RegisterOfflineResponse::class, $response);
        $this->assertSame(200, $response->getCode());
    }

    /**
     * @Feature Payments
     * @Scenario register offline
     * @Case It returns empty data when not found refund
     * @test
     */
    public function it_gets_empty_transaction()
    {
        $token = 'unknown-token';
        $this->requestGettingRegisterOfflineNotFound($token);
        $this->request->setToken($token);
        $response = $this->request->execute();
        $this->assertInstanceOf(InvalidResponse::class, $response);
//        $this->assertSame(404, $response->getErrorCode());
    }

    /**
     * @Feature Payments
     * @Scenario register offline
     * @Case It gets transaction by order-id
     * @test
     */
    public function it_gets_transaction_details()
    {
        $response = $this->makeResponse();

        $refund_info = $this->makeRegisterOffline();

        $token = 'order-id';
        $this->requestGettingRegisterOfflineSuccessful($response, $token);
        $this->request->setToken($token);
        $response = $this->request->execute();

        $this->assertInstanceOf(RegisterOfflineResponse::class, $response);

        $this->assertSame($refund_info->orderId, $response->getOffline()->orderId);
        $this->assertSame($refund_info->sessionId, $response->getOffline()->sessionId);
        $this->assertSame($refund_info->amount, $response->getOffline()->amount);
        $this->assertSame($refund_info->statement, $response->getOffline()->statement);
        $this->assertSame($refund_info->iban, $response->getOffline()->iban);
        $this->assertSame($refund_info->ibanOwner, $response->getOffline()->ibanOwner);
        $this->assertSame($refund_info->ibanOwnerAddress, $response->getOffline()->ibanOwnerAddress);
    }

    /**
     * @Feature Payments
     * @Scenario register offline
     * @Case It returns invalid data when authentication failed
     * @test
     */
    public function execute_was_failed_and_return_invalid_response()
    {
        $token = 'order-id';
        $this->requestRegisterOfflineFailed($token);
        $response = $this->request->setToken($token)->execute();

        $this->assertInstanceOf(InvalidResponse::class, $response);
        $this->assertSame(401, $response->getErrorCode());
    }
}
