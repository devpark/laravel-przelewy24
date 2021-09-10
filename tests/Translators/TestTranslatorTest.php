<?php

namespace Tests\Translators;

use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Services\Crc;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Devpark\Transfers24\Translators\TestTranslator;
use Illuminate\Support\Arr;
use Tests\UnitTestCase;
use Mockery as m;
use Illuminate\Config\Repository as Config;

class TestTranslatorTest extends UnitTestCase
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

        $this->translator = $this->app->make(TestTranslator::class, [
            'crc' => $this->crc,
            'config' => $this->config,
        ]);
        $this->credentials = m::mock(Credentials::class);
        $this->translator->init($this->credentials);
    }

    /**
     * @Feature Payments
     * @Scenario Check connection
     * @Case translate test form
     * @test
     */
    public function translate()
    {
        //Given
        $p24_api_version = 'p24_api_version';
        $p24_sign = 'p24_sign';
        $p24_merchant_id = 'merchant-id';
        $p24_pos_id = 'pos-id';

        //When
        $this->config->shouldReceive('get')
            ->once()
            ->with('transfers24.version')
            ->andReturn('p24_api_version');

        $this->crc->shouldReceive('sum')
            ->once()
            ->andReturn('p24_sign');

        $this->config->shouldReceive('get')
            ->times(4)
            ->andReturn(false, $p24_pos_id, $p24_merchant_id, null);

        $this->translator->configure();
        $form = $this->translator->translate();

        $data = $form->toArray();
        $this->assertSame($p24_api_version,Arr::get($data, $p24_api_version));
        $this->assertSame($p24_sign,Arr::get($data, $p24_sign));
        $this->assertSame($p24_merchant_id,Arr::get($data, 'p24_merchant_id'));
        $this->assertSame($p24_pos_id, Arr::get($data, 'p24_pos_id'));
    }

}