<?php

namespace Tests\Requests\Http;

use Devpark\Transfers24\Currency;
use Devpark\Transfers24\Exceptions\RequestExecutionException;
use Devpark\Transfers24\Requests\Transfers24 as RequestTransfers24;
use stdClass;
use Tests\UnitTestCase;
use Mockery as m;
use Illuminate\Foundation\Application;
use Devpark\Transfers24\Responses\Register as RegisterResponse;
use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlersTransfers24;
use Devpark\Transfers24\Exceptions\RequestException;
use Illuminate\Http\Request;
use Illuminate\Config\Repository as Config;

class Transfers24Test extends UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->request_test = m::mock(RequestTransfers24::class)->makePartial();
    }

    /** @test */
    public function validation_setDescription()
    {
        $test_array = [];
        $this->request_test->setDescription($test_array);
        $set_fields = $this->request_test->getField('description');
        $this->assertTrue(is_string($set_fields));

        $test_array = 'dsfdf';
        $this->request_test->setDescription($test_array);
        $set_fields = $this->request_test->getField('description');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function validate_setEmail()
    {
        $email = 'test';
        $this->request_test->setEmail($email);
        $set_email = $this->request_test->getField('customer_email');
        $this->assertNull($set_email);

        $email = 'test@test.pl';
        $this->request_test->setEmail($email);
        $set_email = $this->request_test->getField('customer_email');
        $this->assertEquals($set_email, $email);
    }

    /** @test */
    public function validate_setAmount()
    {
        $amount = 12.5;
        $currency = 'eur';
        $except_currency = 'EUR';
        $this->request_test->setAmount($amount, $currency);
        $set_amount = $this->request_test->getField('amount');
        $set_currency = $this->request_test->getField('currency');
        $this->assertEquals($set_amount, 1250);
        $this->assertEquals($set_currency, $except_currency);

        $amount = '12,5';
        $this->request_test->setAmount($amount);
        $set_amount = $this->request_test->getField('amount');
        $set_currency = $this->request_test->getField('currency');
        $this->assertEquals($set_amount, 1250);
        $this->assertEquals($set_currency, Currency::PLN);

        $amount = '12';
        $this->request_test->setAmount($amount);
        $set_amount = $this->request_test->getField('amount');
        $this->assertEquals($set_amount, 1200);

        $amount = 'text';
        $this->request_test->setAmount($amount);
        $set_amount = $this->request_test->getField('amount');
        $this->assertEquals($set_amount, 0);
    }

    /** @test */
    public function validate_setCountry()
    {
        $country = 'Portugal';
        $this->request_test->setCountry($country);
        $set_country = $this->request_test->getField('country');
        $this->assertEquals($set_country, 'PT');
    }

    /** @test */
    public function validate_setLanguage()
    {
        $language = 'german';
        $this->request_test->setLanguage($language);
        $set_language = $this->request_test->getField('language');
        $this->assertEquals($set_language, 'de');

        $language = 'de';
        $this->request_test->setLanguage($language);
        $set_language = $this->request_test->getField('language');
        $this->assertEquals($set_language, 'de');
    }

    /** @test */
    public function validate_set_url_return()
    {
        $url = 'www.url.not.valid';
        $this->request_test->setUrlReturn($url);
        $set_url = $this->request_test->getField('url_return');
        $this->assertNull($set_url);

        $url = 'http://localhost/callback';
        $this->request_test->setUrlReturn($url);
        $set_url = $this->request_test->getField('url_return');
        $this->assertEquals($set_url, $url);
    }

    /** @test */
    public function validate_set_url_status()
    {
        $url = 'www.url.not.valid';
        $this->request_test->setUrlStatus($url);
        $set_url = $this->request_test->getField('url_status');
        $this->assertNull($set_url);

        $url = 'http://localhost/status';
        $this->request_test->setUrlStatus($url);
        $set_url = $this->request_test->getField('url_status');
        $this->assertEquals($set_url, $url);
    }

    /** @test */
    public function filterString_validate()
    {
        $name_array = [];
        $filter = $this->request_test->filterString($name_array);
        $this->assertFalse($filter);

        $name = 'string';
        $filter = $this->request_test->filterString($name);
        $this->assertTrue($filter);
    }

    /** @test */
    public function filterNumber_validate()
    {
        $name_array = [];
        $filter = $this->request_test->filterNumber($name_array);
        $this->assertFalse($filter);

        $name = 'string';
        $filter = $this->request_test->filterNumber($name);
        $this->assertfalse($filter);

        $name = 100;
        $filter = $this->request_test->filterNumber($name);
        $this->assertTrue($filter);
    }

    /** @test */
    public function validation_set_article_name()
    {
        $name_array = [];
        $price_array = [];
        $this->request_test->setArticle($name_array, $price_array);
        $set_fields = $this->request_test->getField('article_name');
        $this->assertNull($set_fields);
        $set_amount = $this->request_test->getField('article_price');
        $this->assertNull($set_amount);

        $test_name = 'testowa nazwa';
        $test_price = '100';
        $test_quantity = 'no number';
        $this->request_test->setArticle($test_name, $test_price, $test_quantity);
        $set_fields = $this->request_test->getField('article_name');
        $set_amount = $this->request_test->getField('article_price');
        $set_quantity = $this->request_test->getField('article_quantity');
        $this->assertEquals($test_name, $set_fields);
        $this->assertEquals($set_amount, 10000);
        $this->assertEquals($set_quantity, RequestTransfers24::DEFAULT_ARTICLE_QUANTITY);

        $test_name = 'testowa nazwa';
        $amount = '12,5';
        $test_quantity = 21.4;
        $this->request_test->setArticle($test_name, $amount, $test_quantity);
        $set_fields = $this->request_test->getField('article_name');
        $set_amount = $this->request_test->getField('article_price');
        $set_quantity = $this->request_test->getField('article_quantity');
        $this->assertEquals($test_name, $set_fields);
        $this->assertEquals($set_amount, 1250);
        $this->assertEquals($set_quantity, (int) $test_quantity);
    }

    /** @test */
    public function setTransferLabel_validation()
    {
        $test_array = [];
        $this->request_test->setTransferLabel($test_array);
        $set_fields = $this->request_test->getField('transfer_label');
        $this->assertNull($set_fields);

        $label = 'transfer label';
        $this->request_test->setTransferLabel($label);
        $set_fields = $this->request_test->getField('transfer_label');
        $this->assertEquals($label, $set_fields);
    }

    /** @test */
    public function validation_set_article_description()
    {
        $test_array = [];
        $this->request_test->setArticleDescription($test_array);
        $set_fields = $this->request_test->getField('article_description');
        $this->assertNull($set_fields);

        $test_array = 'dsfdf';
        $this->request_test->setArticleDescription($test_array);
        $set_fields = $this->request_test->getField('article_description');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function validation_set_client_name()
    {
        $test_array = [];
        $this->request_test->setClientName($test_array);
        $set_fields = $this->request_test->getField('client_name');
        $this->assertNull($set_fields);

        $test_array = 'dsfdf';
        $this->request_test->setClientName($test_array);
        $set_fields = $this->request_test->getField('client_name');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function validation_set_client_phone()
    {
        $test_array = [];
        $this->request_test->setClientPhone($test_array);
        $set_fields = $this->request_test->getField('client_phone');
        $this->assertNull($set_fields);

        $test_array = 'dsfdf';
        $this->request_test->setClientPhone($test_array);
        $set_fields = $this->request_test->getField('client_phone');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function validation_set_address()
    {
        $test_array = [];
        $this->request_test->setAddress($test_array);
        $set_fields = $this->request_test->getField('address');
        $this->assertNull($set_fields);

        $test_array = 'dsfdf';
        $this->request_test->setAddress($test_array);
        $set_fields = $this->request_test->getField('address');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function validation_set_zip_code()
    {
        $test_array = [];
        $this->request_test->setZipCode($test_array);
        $set_fields = $this->request_test->getField('zip_code');
        $this->assertNull($set_fields);

        $test_array = 'dsfdf';
        $this->request_test->setZipCode($test_array);
        $set_fields = $this->request_test->getField('zip_code');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function validation_set_city()
    {
        $test_array = [];
        $this->request_test->setCity($test_array);
        $set_fields = $this->request_test->getField('city');
        $this->assertNull($set_fields);

        $test_array = 'dsfdf';
        $this->request_test->setCity($test_array);
        $set_fields = $this->request_test->getField('city');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function validation_set_article_number()
    {
        $test_array = [];
        $this->request_test->setArticleNumber($test_array);
        $set_fields = $this->request_test->getField('article_number');
        $this->assertNull($set_fields);

        $test_array = 'AXC123';
        $this->request_test->setArticleNumber($test_array);
        $set_fields = $this->request_test->getField('article_number');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function shippingCost_validate()
    {
        $test_array = [];
        $this->request_test->setShipping($test_array);
        $set_fields = $this->request_test->getField('shipping_cost');
        $this->assertNull($set_fields);

        $test_array = 21.4;
        $this->request_test->setShipping($test_array);
        $set_fields = $this->request_test->getField('shipping_cost');
        $this->assertEquals((int) $test_array, $set_fields);
    }

    /** @test */
    public function validation_init()
    {
        $this->request_concrete = $this->createConcreteRequest();

        $this->handler->shouldReceive('init')->andReturn(1);

        $response = $this->request_concrete->setEmail('test@test.pl')->setAmount(100)
            ->setArticle('Article 1')->init();

        $this->assertEquals($response, 1);

        try {
            $response = $this->request_test->init();
        } catch (\Exception $e) {
            $this->assertInstanceOf(RequestException::class, $e);
            $this->assertEquals($e->getMessage(), 'Empty email or amount');
        }
    }

    /** @test */
    public function validate_set_Default_urls()
    {
        $this->request_concrete = $this->createConcreteRequest();

        $this->request_concrete->setDefaultUrls();

        $url_status = $this->request_concrete->getField('url_status');
        $url_return = $this->request_concrete->getField('url_return');

        $this->assertEquals($url_status, 'http://:');
        $this->assertEquals($url_return, 'http://:');
    }

    /** @test */
    public function it_sets_valid_urls_for_relative_urls()
    {
        $this->app = m::mock(Application::class)->makePartial();
        $config = m::mock(stdClass::class);
        $config->shouldReceive('get')->with('transfers24.url_return')->andReturn('abc');
        $config->shouldReceive('get')->with('transfers24.url_status')->andReturn('def');
        $this->app->shouldReceive('make')->once()->with(Config::class)->andReturn($config);
        $url = m::mock(stdClass::class);
        $this->app->shouldReceive('make')->once()->with(\Illuminate\Routing\UrlGenerator::class)
            ->andReturn($url);
        $url->shouldReceive('to')->atLeast()->once()->with('abc')
            ->andReturn('http://sample.domain/abc');
        $url->shouldReceive('to')->atLeast()->once()->with('def')
            ->andReturn('http://sample.domain/def');
        $this->request_concrete = $this->createConcreteRequest();

        $this->request_concrete->setDefaultUrls();

        $url_status = $this->request_concrete->getField('url_status');
        $url_return = $this->request_concrete->getField('url_return');

        $this->assertEquals('http://sample.domain/abc', $url_return);
        $this->assertEquals('http://sample.domain/def', $url_status);
    }

    /** @test */
    public function it_sets_valid_urls_for_absolute_urls()
    {
        $this->app = m::mock(Application::class)->makePartial();
        $config = m::mock(stdClass::class);
        $config->shouldReceive('get')->with('transfers24.url_return')->andReturn('http://abc.example');
        $config->shouldReceive('get')->with('transfers24.url_status')->andReturn('https://def.example');
        $this->app->shouldReceive('make')->once()->with(Config::class)->andReturn($config);
        $url = m::mock(stdClass::class);
        $this->app->shouldReceive('make')->once()->with(\Illuminate\Routing\UrlGenerator::class)
            ->andReturn($url);
        $url->shouldNotReceive('to');
        $this->request_concrete = $this->createConcreteRequest();

        $this->request_concrete->setDefaultUrls();

        $url_status = $this->request_concrete->getField('url_status');
        $url_return = $this->request_concrete->getField('url_return');

        $this->assertEquals('http://abc.example', $url_return);
        $this->assertEquals('https://def.example', $url_status);
    }

    /** @test */
    public function test_execution_payment()
    {
        $this->request_concrete = $this->createConcreteRequest();
        $token = [];

        try {
            $response = $this->request_concrete->execute($token);
        } catch (\Exception $e) {
            $this->assertInstanceOf(RequestExecutionException::class, $e);
            $this->assertEquals($e->getMessage(), 'Empty or not valid Token');
        }

        $this->handler->shouldReceive('execute')->andReturn('http://redirect');
        $response = $this->request_concrete->execute('123456789');
        $this->assertEquals($response, 'http://redirect');
    }

    /** @test */
    public function test_receive_transfers24_request()
    {
        $this->request_concrete = $this->createConcreteRequest();
        $this->handler->shouldReceive('receive')->andReturn(1);
        $request = new Request();
        $response = $this->request_concrete->receive($request);
        $this->assertEquals($response, 1);
    }

    /** @test */
    public function test_set_fields_for_register_payment()
    {
        $this->request_concrete = $this->createConcreteRequest();
        $this->handler->shouldReceive('init')->andReturn(1);
        $this->request_concrete->setEmail('test@test.pl')
            ->setAmount(100)
            ->setDescription('Example description')
            ->setCountry('PL')
            ->setUrlReturn('url_return')
            ->setUrlStatus('url_status')
            ->setCountry('PL')
            ->setArticle('ProductName')
            ->setArticleDescription('ProductDescription')
            ->setClientName('last name')
            ->setClientPhone('77777777')
            ->setAddress('Poznanska 20')
            ->setZipCode('62-021')
            ->setCity('Poznan')
            ->setLanguage('pl')
            ->setChannel(1)
            ->setArticleNumber('ACX123')
            ->setShipping(200)
            ->init();

        $payment_form = [
            'p24_session_id' => $this->request_concrete->getField('transaction_id'),
            'p24_amount' => $this->request_concrete->getField('amount'),
            'p24_currency' => $this->request_concrete->getField('currency'),
            'p24_description' => $this->request_concrete->getField('description'),
            'p24_email' => $this->request_concrete->getField('customer_email'),
            'p24_client' => $this->request_concrete->getField('client_name'),
            'p24_address' => $this->request_concrete->getField('address'),
            'p24_zip' => $this->request_concrete->getField('zip_code'),
            'p24_city' => $this->request_concrete->getField('city'),
            'p24_country' => $this->request_concrete->getField('country'),
            'p24_phone' => $this->request_concrete->getField('client_phone'),
            'p24_language' => $this->request_concrete->getField('language'),
            'p24_url_return' => $this->request_concrete->getField('url_return'),
            'p24_url_status' => $this->request_concrete->getField('url_status'),
            'p24_channel' => $this->request_concrete->getField('channel'),
            'p24_name_1' => $this->request_concrete->getField('article_name'),
            'p24_description_1' => $this->request_concrete->getField('article_description'),
            'p24_quantity_1' => $this->request_concrete->getField('article_quantity'),
            'p24_price_1' => $this->request_concrete->getField('article_price'),
            'p24_number_1' => $this->request_concrete->getField('article_number'),
            'p24_price_1' => $this->request_concrete->getField('article_price'),
            'p24_shipping' => $this->request_concrete->getField('shipping_cost'),
        ];

        $set_fields = $this->request_concrete->setFields();

        $this->assertEquals($set_fields, $payment_form);
    }

    public function createConcreteRequest()
    {
        $this->handler = m::mock(HandlersTransfers24::class)->makePartial();
        $this->response = m::mock(RegisterResponse::class)->makePartial();
        if (! isset($this->app)) {
            $this->app = m::mock(Application::class)->makePartial();
        }

        $request_concrete = new RequestTransfers24($this->handler, $this->response, $this->app);

        return $request_concrete;
    }

    /** @test */
    public function setNextArticle_validate()
    {
        $next_article = [
            'name' => 'test payment',
            'price' => '111,11',
            'quantity' => 100,
            'number' => 'ACG1122',
        ];

        $expected_article = [
            'name' => 'test payment',
            'price' => 11111,
            'quantity' => 100,
            'number' => 'ACG1122',
            'description' => '',
        ];

        $this->request_test->setNextArticle($next_article['name'], $next_article['price'],
            $next_article['quantity'], $next_article['number']);

        $additional_articles = $this->request_test->getField('additional_articles');
        $first_article = array_pop($additional_articles);
        $this->assertEquals($expected_article, $first_article);
        $this->assertEmpty($additional_articles);
    }
}
