<?php

namespace Tests\Translators;

use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Services\Crc;
use Devpark\Transfers24\Translators\ReceiveTranslator;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Support\Arr;
use Tests\UnitTestCase;
use Mockery as m;
use Illuminate\Config\Repository as Config;

class ReceiveTranslatorTest extends UnitTestCase
{
    /**
     * @var m\Mock
     */
    private $credentials;
    /**
     * @var RegisterTranslator
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
        $p24_api_version = 'p24_api_version';
        $p24_sign = 'p24_sign';
        $p24_session_id = 'p24_session_id';
        $p24_amount = 100;
        $p24_currency = 'pl';
        $p24_order_id = 'order-id';

        //When
        $this->config->shouldReceive('get')
            ->once()
            ->with('transfers24.version')
            ->andReturn('p24_api_version');

        $this->crc->shouldReceive('sum')
            ->once()
            ->andReturn('p24_sign');

        $receive_data = [
            'p24_amount' => $p24_amount,
            'p24_currency' => $p24_currency,
            'p24_session_id' => $p24_session_id,
            'p24_order_id' => $p24_order_id,
        ];

        $this->translator->init($receive_data, $this->credentials);

        $form = $this->translator->translate();

        $data = $form->toArray();
        $this->assertSame($p24_api_version,Arr::get($data, $p24_api_version));
        $this->assertSame($p24_sign,Arr::get($data, $p24_sign));
        $this->assertNotEmpty(Arr::get($data, $p24_session_id));
        $this->assertSame($p24_amount,Arr::get($data, 'p24_amount'));
        $this->assertSame($p24_currency,Arr::get($data, 'p24_currency'));
        $this->assertSame($p24_order_id, Arr::get($data, 'p24_order_id'));
    }

}
