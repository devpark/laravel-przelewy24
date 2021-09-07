<?php

namespace Tests\Translators;

use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Services\Crc;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Support\Arr;
use Tests\UnitTestCase;
use Mockery as m;
use Illuminate\Config\Repository as Config;

class RegisterTranslatorTest extends UnitTestCase
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
    /**
     * @var m\MockInterface
     */
    private $request;

    protected function setUp()
    {
        parent::setUp();

        $this->crc = m::mock(Crc::class);

        $this->config = m::mock(Config::class);

        $this->translator = $this->app->make(RegisterTranslator::class, [
            'crc' => $this->crc,
            'config' => $this->config,
        ]);
        $this->request = m::mock(\Devpark\Transfers24\Requests\Transfers24::class);
        $this->credentials = m::mock(Credentials::class);

        $this->translator->init($this->request, $this->credentials);
    }

    /**
     * @Feature Payments
     * @Scenario Register Form
     * @Case translate register form
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
        $p24_description = 'description';
        $p24_email = 'email@p24.com';
        $p24_client = 'client';
        $p24_address = 'address';
        $p24_zip_code = 'zip_code';
        $p24_city = 'city';
        $p24_country = 'country';
        $p24_phone = 123456789;
        $p24_language = 'language';
        $p24_url_return = 'url_return';
        $p24_url_status = 'url_status';
        $p24_channel = 1;
        $p24_article_name = 'article-name';
        $p24_article_description = 'article-description';
        $p24_article_quantity = 1;
        $p24_article_price = 1;
        $p24_article_number = 10;
        $p24_shipping_cost = 10;

        //When
        $this->config->shouldReceive('get')
            ->once()
            ->with('transfers24.version')
            ->andReturn('p24_api_version');

        $this->crc->shouldReceive('sum')
            ->once()
            ->andReturn('p24_sign');

        $this->request->shouldReceive('getAmount')
            ->once()
            ->andReturn($p24_amount);

        $this->request->shouldReceive('getCurrency')
            ->once()
            ->andReturn($p24_currency);

        $this->request->shouldReceive('getDescription')
            ->once()
            ->andReturn($p24_description);

        $this->request->shouldReceive('getCustomerEmail')
            ->once()
            ->andReturn($p24_email);

        $this->request->shouldReceive('getClientName')
            ->once()
            ->andReturn($p24_client);

        $this->request->shouldReceive('getAddress')
            ->once()
            ->andReturn($p24_address);

        $this->request->shouldReceive('getZipCode')
            ->once()
            ->andReturn($p24_zip_code);

        $this->request->shouldReceive('getCity')
            ->once()
            ->andReturn($p24_city);

        $this->request->shouldReceive('getCountry')
            ->once()
            ->andReturn($p24_country);

        $this->request->shouldReceive('getClientPhone')
            ->once()
            ->andReturn($p24_phone);

        $this->request->shouldReceive('getLanguage')
            ->once()
            ->andReturn($p24_language);

        $this->request->shouldReceive('getUrlReturn')
            ->once()
            ->andReturn($p24_url_return);

        $this->request->shouldReceive('getUrlStatus')
            ->once()
            ->andReturn($p24_url_status);

        $this->request->shouldReceive('getChannel')
            ->once()
            ->andReturn($p24_channel);

        $this->request->shouldReceive('getArticleName')
            ->once()
            ->andReturn($p24_article_name);

        $this->request->shouldReceive('getArticleDescription')
            ->once()
            ->andReturn($p24_article_description);

        $this->request->shouldReceive('getArticleQuantity')
            ->once()
            ->andReturn($p24_article_quantity);

        $this->request->shouldReceive('getArticlePrice')
            ->once()
            ->andReturn($p24_article_price);

        $this->request->shouldReceive('getArticleNumber')
            ->once()
            ->andReturn($p24_article_number);

        $this->request->shouldReceive('getShippingCost')
            ->once()
            ->andReturn($p24_shipping_cost);

        $this->request->shouldReceive('getAdditionalArticles')
            ->once()
            ->andReturn([]);



        $form = $this->translator->translate();

        $data = $form->toArray();
        $this->assertSame($p24_api_version,Arr::get($data, $p24_api_version));
        $this->assertSame($p24_sign,Arr::get($data, $p24_sign));
        $this->assertNotEmpty(Arr::get($data, $p24_session_id));
        $this->assertSame($p24_amount,Arr::get($data, 'p24_amount'));
        $this->assertSame($p24_currency,Arr::get($data, 'p24_currency'));
        $this->assertSame($p24_description,Arr::get($data, 'p24_description'));
        $this->assertSame($p24_email,Arr::get($data, 'p24_email'));
        $this->assertSame($p24_client,Arr::get($data, 'p24_client'));
        $this->assertSame($p24_address,Arr::get($data, 'p24_address'));
        $this->assertSame($p24_zip_code,Arr::get($data, 'p24_zip'));
        $this->assertSame($p24_city,Arr::get($data, 'p24_city'));
        $this->assertSame($p24_country,Arr::get($data, 'p24_country'));
        $this->assertSame($p24_phone,Arr::get($data, 'p24_phone'));
        $this->assertSame($p24_language,Arr::get($data, 'p24_language'));
        $this->assertSame($p24_url_return,Arr::get($data, 'p24_url_return'));
        $this->assertSame($p24_url_status,Arr::get($data, 'p24_url_status'));
        $this->assertSame($p24_channel,Arr::get($data, 'p24_channel'));
        $this->assertSame($p24_article_name,Arr::get($data, 'p24_name_1'));
        $this->assertSame($p24_article_description,Arr::get($data, 'p24_description_1'));
        $this->assertSame($p24_article_quantity,Arr::get($data, 'p24_quantity_1'));
        $this->assertSame($p24_article_price,Arr::get($data, 'p24_price_1'));
        $this->assertSame($p24_article_number, Arr::get($data, 'p24_number_1'));
        $this->assertSame($p24_shipping_cost, Arr::get($data, 'p24_shipping'));
    }

}
