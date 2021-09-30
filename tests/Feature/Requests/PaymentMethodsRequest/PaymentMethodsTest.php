<?php

declare(strict_types=1);

namespace Tests\Feature\Requests\PaymentMethodsRequest;

use Devpark\Transfers24\Requests\PaymentMethodsRequest;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\PaymentMethodsResponse;
use Mockery\MockInterface;
use Tests\UnitTestCase;

class PaymentMethodsTest extends UnitTestCase
{
    use PaymentMethodsRequestTrait;

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

        $this->mockApi();

        $this->setConfiguration();

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

        $this->assertInstanceOf(PaymentMethodsResponse::class, $response);
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

        $this->assertInstanceOf(PaymentMethodsResponse::class, $response);
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

        $this->assertInstanceOf(PaymentMethodsResponse::class, $response);
        $this->assertSame($payment_method->name, $response->getPaymentMethods()[0]->name);
        $this->assertSame($payment_method->id, $response->getPaymentMethods()[0]->id);
        $this->assertSame($payment_method->status, $response->getPaymentMethods()[0]->status);
        $this->assertSame($payment_method->imgUrl, $response->getPaymentMethods()[0]->imgUrl);
        $this->assertSame($payment_method->mobileImgUrl, $response->getPaymentMethods()[0]->mobileImgUrl);
        $this->assertSame($payment_method->mobile, $response->getPaymentMethods()[0]->mobile);
        $this->assertSame($payment_method->availabilityHours->mondayToFriday, $response->getPaymentMethods()[0]->availabilityHours->mondayToFriday);
        $this->assertSame($payment_method->availabilityHours->saturday, $response->getPaymentMethods()[0]->availabilityHours->saturday);
        $this->assertSame($payment_method->availabilityHours->sunday, $response->getPaymentMethods()[0]->availabilityHours->sunday);
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
}
