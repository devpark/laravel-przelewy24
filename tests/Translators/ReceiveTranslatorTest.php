<?php

namespace Tests\Translators;

use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Forms\ReceiveForm;
use Devpark\Transfers24\Services\Crc;
use Devpark\Transfers24\Translators\ReceiveTranslator;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Arr;
use Mockery as m;
use Tests\UnitTestCase;

class ReceiveTranslatorTest extends UnitTestCase
{
    /**
     * @var m\Mock
     */
    private $credentials;

    /**
     * @var ReceiveTranslator
     */
    private $translator;

    /**
     * @var m\MockInterface
     */
    private $crc;

    /**
     * @var m\MockInterface
     */
    private $config;

    protected function setUp()
    {
        parent::setUp();

        $this->crc = m::mock(Crc::class);

        $this->config = m::mock(Config::class);

        $this->translator = $this->app->make(ReceiveTranslator::class, [
            'crc' => $this->crc,
            'config' => $this->config,
        ]);
        $this->credentials = m::mock(Credentials::class);
    }

    /**
     * @Feature Payments
     * @Scenario Receive Form
     * @Case translate receive form
     * @test
     */
    public function translate()
    {
        //Given
        $p24_sign = 'sign';
        $p24_session_id = 'sessionId';
        $p24_amount = 100;
        $p24_currency = 'pl';
        $p24_order_id = 1001001;

        //When
        $this->crc->shouldReceive('sum')
            ->once()
            ->andReturn('sign');

        $receive_data = [
            'merchantId' => 'merchant-id',
            'posId' => 'pos-id',
            'sessionId' => $p24_session_id,
            'amount' => $p24_amount,
            'originAmount' => $p24_amount,
            'currency' => $p24_currency,
            'orderId' => $p24_order_id,
            'methodId' => 'method-id',
            'statement' => 'statement',
            'sign' => 'sign',
        ];

        $this->translator->init($receive_data, $this->credentials);

        /**
         * @var ReceiveForm $form
         */
        $form = $this->translator->translate();

        $data = $form->toArray();
        $this->assertSame($p24_sign, Arr::get($data, $p24_sign));
        $this->assertNotEmpty(Arr::get($data, $p24_session_id));
        $this->assertSame($p24_amount, Arr::get($data, 'amount'));
        $this->assertSame($p24_currency, Arr::get($data, 'currency'));
        $this->assertSame($p24_order_id, Arr::get($data, 'orderId'));
        $this->assertSame($receive_data, $form->getReceiveParameters());
    }
}
