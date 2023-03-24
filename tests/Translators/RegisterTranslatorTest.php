<?php

namespace Tests\Translators;

use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Services\Crc;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Arr;
use Mockery as m;
use Tests\UnitTestCase;

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
        $p24_phone = '123456789';
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

        $p24_merchant_id = 'p24-merchant-id';
        $p24_pos_id = 'p24-pos-id';

        $p24_method = 'method';
        $p24_time_limit = 'time_limit';
        $p24_wait_for_result = 'wait-for-result';
        $p24_regulation_accept = 'regulation-accept';
        $p24_transfer_label = 'transfet-label';
        $p24_mobile_lib = 'mobile-lib';
        $p24_sdk_version = 'sdk-version';
        $p24_encoding = 'encoding';
        $p24_method_ref_id = 'method-ref-id';
        $p24_seller_id = 'seller-id';
        $p24_seller_category = 'seller-category';
        $p24_shipping_type = 'shipping-type';
        $p24_shipping_address = 'shipping-address';
        $p24_shipping_zip = 'shipping-zip';
        $p24_shipping_city = 'shipping-city';
        $p24_shipping_country = 'shipping-country';

        //When

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

        $this->request->shouldReceive('getMethod')
            ->once()
            ->andReturn($p24_method);

        $this->request->shouldReceive('getMethodRefId')
            ->once()
            ->andReturn($p24_method_ref_id);

        $this->request->shouldReceive('getTransferLabel')
            ->once()
            ->andReturn($p24_transfer_label);

        $this->request->shouldReceive('getShippingCost')
            ->once()
            ->andReturn($p24_shipping_cost);

        $this->request->shouldReceive('getCart')
            ->once()
            ->andReturn([
                [
                    'sellerId' => $p24_seller_id,
                    'sellerCategory' => $p24_seller_category,
                    'name' => $p24_article_name,
                    'description' => $p24_article_description,
                    'quantity' => $p24_article_quantity,
                    'price' => $p24_article_price,
                    'number' => $p24_article_number,
                ],
            ]);

        $this->request->shouldReceive('hasShippingDetails')
            ->once()
            ->andReturn(true);
        $this->request->shouldReceive('getShippingDetails')
            ->once()
            ->andReturn([
                'type' => $p24_shipping_type,
                'address' => $p24_shipping_address,
                'zip' => $p24_shipping_zip,
                'city' => $p24_shipping_city,
                'country' => $p24_shipping_country,

            ]);

        $this->config->shouldReceive('get')->times(8)->andReturn(false, $p24_pos_id, $p24_merchant_id, null, $p24_time_limit, $p24_wait_for_result, $p24_regulation_accept, $p24_encoding);
//        $this->config->shouldReceive('get')
//            ->once()
//            ->with('transfers24.version')
//            ->andReturn('p24_api_version');

        $form = $this->translator->configure()->translate();

        $data = $form->toArray();
        $this->assertSame($p24_merchant_id, Arr::get($data, 'merchantId'));
        $this->assertSame($p24_pos_id, Arr::get($data, 'posId'));

        $this->assertSame($p24_time_limit, Arr::get($data, 'timeLimit'));
        $this->assertSame($p24_wait_for_result, Arr::get($data, 'waitForResult'));
        $this->assertSame($p24_regulation_accept, Arr::get($data, 'regulationAccept'));
        $this->assertSame($p24_encoding, Arr::get($data, 'encoding'));

        $this->assertNotEmpty(Arr::get($data, 'sessionId'));
        $this->assertSame($p24_amount, Arr::get($data, 'amount'));
        $this->assertSame($p24_currency, Arr::get($data, 'currency'));
        $this->assertSame($p24_description, Arr::get($data, 'description'));
        $this->assertSame($p24_email, Arr::get($data, 'email'));
        $this->assertSame($p24_client, Arr::get($data, 'client'));
        $this->assertSame($p24_address, Arr::get($data, 'address'));
        $this->assertSame($p24_zip_code, Arr::get($data, 'zip'));
        $this->assertSame($p24_city, Arr::get($data, 'city'));
        $this->assertSame($p24_country, Arr::get($data, 'country'));
        $this->assertSame($p24_phone, Arr::get($data, 'phone'));
        $this->assertSame($p24_language, Arr::get($data, 'language'));
        $this->assertSame($p24_method, Arr::get($data, 'method'));
        $this->assertSame($p24_url_return, Arr::get($data, 'urlReturn'));
        $this->assertSame($p24_url_status, Arr::get($data, 'urlStatus'));

        $this->assertSame($p24_channel, Arr::get($data, 'channel'));
        $this->assertSame($p24_shipping_cost, Arr::get($data, 'shipping'));
        $this->assertSame($p24_transfer_label, Arr::get($data, 'transferLabel'));
        $this->assertSame($p24_sign, Arr::get($data, 'sign'));
        $this->assertSame($p24_method_ref_id, Arr::get($data, 'methodRefId'));

        $this->assertSame($p24_seller_id, Arr::get($data, 'cart.0.sellerId'));
        $this->assertSame($p24_seller_category, Arr::get($data, 'cart.0.sellerCategory'));
        $this->assertSame($p24_article_name, Arr::get($data, 'cart.0.name'));
        $this->assertSame($p24_article_description, Arr::get($data, 'cart.0.description'));
        $this->assertSame($p24_article_quantity, Arr::get($data, 'cart.0.quantity'));
        $this->assertSame($p24_article_price, Arr::get($data, 'cart.0.price'));
        $this->assertSame($p24_article_number, Arr::get($data, 'cart.0.number'));

        $this->assertSame($p24_shipping_type, Arr::get($data, 'additional.shipping.type'));
        $this->assertSame($p24_shipping_address, Arr::get($data, 'additional.shipping.address'));
        $this->assertSame($p24_shipping_zip, Arr::get($data, 'additional.shipping.zip'));
        $this->assertSame($p24_shipping_city, Arr::get($data, 'additional.shipping.city'));
        $this->assertSame($p24_shipping_country, Arr::get($data, 'additional.shipping.country'));
    }
}
