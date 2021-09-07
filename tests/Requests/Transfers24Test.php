<?php

namespace Tests\Requests;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Actions\Runner;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Currency;
use Devpark\Transfers24\Exceptions\RequestExecutionException;
use Devpark\Transfers24\Factories\ActionFactory;
use Devpark\Transfers24\Factories\ReceiveTranslatorFactory;
use Devpark\Transfers24\Factories\RegisterTranslatorFactory;
use Devpark\Transfers24\Factories\ResponseFactory;
use Devpark\Transfers24\Factories\RunnerFactory;
use Devpark\Transfers24\Requests\Transfers24 as RequestTransfers24;
use Devpark\Transfers24\Translators\ReceiveTranslator;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\UrlGenerator as Url;
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
    /**
     * @var m\Mock
     */
    private $credentials;

    /**
     * @var RequestTransfers24
     */
    private $request;
    /**
     * @var m\MockInterface
     */
    private $action_factory;
    /**
     * @var m\MockInterface
     */
    private $translator_factory;
    /**
     * @var m\MockInterface
     */
    private $response_factory;
    /**
     * @var m\MockInterface
     */
    private $runner_factory;
    /**
     * @var m\MockInterface
     */
    private $receive_translator_factory;

    protected function setUp()
    {
        parent::setUp();
        $this->app->bind(Container::class, \Illuminate\Container\Container::class);
        $this->request = $this->createConcreteRequest();
    }

    /** @test */
    public function validation_setDescription()
    {
        $test_array = [];
        $this->request->setDescription($test_array);
        $set_fields = $this->request->getField('description');
        $this->assertTrue(is_string($set_fields));

        $test_array = 'dsfdf';
        $this->request->setDescription($test_array);
        $set_fields = $this->request->getField('description');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function validate_setEmail()
    {
        $email = 'test';
        $this->request->setEmail($email);
        $set_email = $this->request->getField('customer_email');
        $this->assertNull($set_email);

        $email = 'test@test.pl';
        $this->request->setEmail($email);
        $set_email = $this->request->getField('customer_email');
        $this->assertEquals($set_email, $email);
    }

    /** @test */
    public function validate_setAmount()
    {
        $amount = 12.5;
        $currency = 'eur';
        $except_currency = 'EUR';
        $this->request->setAmount($amount, $currency);
        $set_amount = $this->request->getField('amount');
        $set_currency = $this->request->getField('currency');
        $this->assertEquals($set_amount, 1250);
        $this->assertEquals($set_currency, $except_currency);

        $amount = '12,5';
        $this->request->setAmount($amount);
        $set_amount = $this->request->getField('amount');
        $set_currency = $this->request->getField('currency');
        $this->assertEquals($set_amount, 1250);
        $this->assertEquals($set_currency, Currency::PLN);

        $amount = '12';
        $this->request->setAmount($amount);
        $set_amount = $this->request->getField('amount');
        $this->assertEquals($set_amount, 1200);

        $amount = 'text';
        $this->request->setAmount($amount);
        $set_amount = $this->request->getField('amount');
        $this->assertEquals($set_amount, 0);
    }

    /** @test */
    public function validate_setCountry()
    {
        $country = 'Portugal';
        $this->request->setCountry($country);
        $set_country = $this->request->getField('country');
        $this->assertEquals($set_country, 'PT');
    }

    /** @test */
    public function validate_setLanguage()
    {
        $language = 'german';
        $this->request->setLanguage($language);
        $set_language = $this->request->getField('language');
        $this->assertEquals($set_language, 'de');

        $language = 'de';
        $this->request->setLanguage($language);
        $set_language = $this->request->getField('language');
        $this->assertEquals($set_language, 'de');
    }

    /** @test */
    public function validate_set_url_return()
    {
        $url = 'www.url.not.valid';
        $this->request->setUrlReturn($url);

        $url = 'http://localhost/callback';
        $this->request->setUrlReturn($url);
        $set_url = $this->request->getField('url_return');
        $this->assertEquals($url, $set_url);
    }

    /** @test */
    public function validate_set_url_status()
    {
        $url = 'www.url.not.valid';
        $this->request->setUrlStatus($url);

        $url = 'http://localhost/status';
        $this->request->setUrlStatus($url);
        $set_url = $this->request->getField('url_status');
        $this->assertEquals($url, $set_url);
    }

    /** @test */
    public function filterString_validate()
    {
        $name_array = [];
        $filter = $this->request->filterString($name_array);
        $this->assertFalse($filter);

        $name = 'string';
        $filter = $this->request->filterString($name);
        $this->assertTrue($filter);
    }

    /** @test */
    public function filterNumber_validate()
    {
        $name_array = [];
        $filter = $this->request->filterNumber($name_array);
        $this->assertFalse($filter);

        $name = 'string';
        $filter = $this->request->filterNumber($name);
        $this->assertfalse($filter);

        $name = 100;
        $filter = $this->request->filterNumber($name);
        $this->assertTrue($filter);
    }

    /** @test */
    public function validation_set_article_name()
    {
        $name_array = [];
        $price_array = [];
        $this->request->setArticle($name_array, $price_array);
        $set_fields = $this->request->getField('article_name');
        $this->assertNull($set_fields);
        $set_amount = $this->request->getField('article_price');
        $this->assertNull($set_amount);

        $test_name = 'testowa nazwa';
        $test_price = '100';
        $test_quantity = 'no number';
        $this->request->setArticle($test_name, $test_price, $test_quantity);
        $set_fields = $this->request->getField('article_name');
        $set_amount = $this->request->getField('article_price');
        $set_quantity = $this->request->getField('article_quantity');
        $this->assertEquals($test_name, $set_fields);
        $this->assertEquals($set_amount, 10000);
        $this->assertEquals($set_quantity, RequestTransfers24::DEFAULT_ARTICLE_QUANTITY);

        $test_name = 'testowa nazwa';
        $amount = '12,5';
        $test_quantity = 21.4;
        $this->request->setArticle($test_name, $amount, $test_quantity);
        $set_fields = $this->request->getField('article_name');
        $set_amount = $this->request->getField('article_price');
        $set_quantity = $this->request->getField('article_quantity');
        $this->assertEquals($test_name, $set_fields);
        $this->assertEquals($set_amount, 1250);
        $this->assertEquals($set_quantity, (int) $test_quantity);
    }

    /** @test */
    public function setTransferLabel_validation()
    {
        $test_array = [];
        $this->request->setTransferLabel($test_array);
        $set_fields = $this->request->getField('transfer_label');
        $this->assertNull($set_fields);

        $label = 'transfer label';
        $this->request->setTransferLabel($label);
        $set_fields = $this->request->getField('transfer_label');
        $this->assertEquals($label, $set_fields);
    }

    /** @test */
    public function validation_set_article_description()
    {
        $test_array = [];
        $this->request->setArticleDescription($test_array);
        $set_fields = $this->request->getField('article_description');
        $this->assertNull($set_fields);

        $test_array = 'dsfdf';
        $this->request->setArticleDescription($test_array);
        $set_fields = $this->request->getField('article_description');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function validation_set_client_name()
    {
        $test_array = [];
        $this->request->setClientName($test_array);
        $set_fields = $this->request->getField('client_name');
        $this->assertNull($set_fields);

        $test_array = 'dsfdf';
        $this->request->setClientName($test_array);
        $set_fields = $this->request->getField('client_name');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function validation_set_client_phone()
    {
        $test_array = [];
        $this->request->setClientPhone($test_array);
        $set_fields = $this->request->getField('client_phone');
        $this->assertNull($set_fields);

        $test_array = 'dsfdf';
        $this->request->setClientPhone($test_array);
        $set_fields = $this->request->getField('client_phone');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function validation_set_address()
    {
        $test_array = [];
        $this->request->setAddress($test_array);
        $set_fields = $this->request->getField('address');
        $this->assertNull($set_fields);

        $test_array = 'dsfdf';
        $this->request->setAddress($test_array);
        $set_fields = $this->request->getField('address');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function validation_set_zip_code()
    {
        $test_array = [];
        $this->request->setZipCode($test_array);
        $set_fields = $this->request->getField('zip_code');
        $this->assertNull($set_fields);

        $test_array = 'dsfdf';
        $this->request->setZipCode($test_array);
        $set_fields = $this->request->getField('zip_code');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function validation_set_city()
    {
        $test_array = [];
        $this->request->setCity($test_array);
        $set_fields = $this->request->getField('city');
        $this->assertNull($set_fields);

        $test_array = 'dsfdf';
        $this->request->setCity($test_array);
        $set_fields = $this->request->getField('city');
        $this->assertEquals($test_array, $set_fields);
    }

    /** @test */
    public function validation_set_article_number()
    {
        $test_array = [];
        $this->request->setArticleNumber($test_array);
        $set_fields = $this->request->getField('article_number');
        $this->assertNull($set_fields);

        $test_array = 'AXC123';
        $this->request->setArticleNumber($test_array);
        $set_fields = $this->request->getField('article_number');
        $this->assertEquals($test_array, $set_fields);
    }

    /**
     * @Feature Payments
     * @Scenario Register Payment
     * @Case Set shipping cost
     * @test
     */
    public function shippingCost_validate()
    {
        $test_array = [];
        $this->request->setShipping($test_array);
        $set_fields = $this->request->getField('shipping_cost');
        $this->assertNull($set_fields);

        $test_array = 21.4;
        $this->request->setShipping($test_array);
        $set_fields = $this->request->getField('shipping_cost');
        $this->assertEquals((int) $test_array, $set_fields);
    }

    /**
     * @Feature Payments
     * @Scenario Register Payment
     * @Case Register payment
     * @test
     */
    public function validation_init()
    {
        $translator = m::mock(RegisterTranslator::class);
        $action = m::mock(Action::class);
        $expected_response = m::mock(IResponse::class);


        $this->translator_factory->shouldReceive('create')->once()
            ->with($this->request, m::any())
            ->andReturn($translator);
        $this->action_factory->shouldReceive('create')
            ->once()
            ->with($this->response_factory, $translator)
            ->andReturn($action);

        $action->shouldReceive('execute')
            ->once()
            ->andReturn($expected_response);

        $response = $this->request->setEmail('test@test.pl')->setAmount(100)
            ->setArticle('Article 1')->init();

        $this->assertEquals($expected_response, $response);

    }


    /**
     * @Feature Payments
     * @Scenario Register Payment
     * @Case Validation exception
     * @test
     */
    public function validation_exception()
    {
        try {
            $this->request->init();
        } catch (\Exception $e) {
            $this->assertInstanceOf(RequestException::class, $e);
            $this->assertEquals($e->getMessage(), 'Empty email or amount');
        }
    }

    /**
     * @Feature Payments
     * @Scenario Register Payment
     * @Case Set default Urls
     * @test
     */
    public function validate_set_Default_urls()
    {

        $this->request->setDefaultUrls();

        $url_status = $this->request->getField('url_status');
        $url_return = $this->request->getField('url_return');

        $this->assertEquals($url_status, 'http://:');
        $this->assertEquals($url_return, 'http://:');
    }


    /**
     * @Feature Payments
     * @Scenario Register Payment
     * @Case Set relative Urls
     * @test
     */
    public function it_sets_valid_urls_for_relative_urls()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('transfers24.url_return')->andReturn('abc');
        $config->shouldReceive('get')->with('transfers24.url_status')->andReturn('def');

        $url = m::mock(Url::class);
        $url->shouldReceive('to')->atLeast()->once()->with('abc')
            ->andReturn('http://sample.domain/abc');
        $url->shouldReceive('to')->atLeast()->once()->with('def')
            ->andReturn('http://sample.domain/def');

        $this->request = $this->createConcreteRequest([
            'config' => $config,
            'url' => $url,
        ]);

        $this->request->setDefaultUrls();

        $url_status = $this->request->getField('url_status');
        $url_return = $this->request->getField('url_return');

        $this->assertEquals('http://sample.domain/abc', $url_return);
        $this->assertEquals('http://sample.domain/def', $url_status);
    }

    /** @test */
    public function it_sets_valid_urls_for_absolute_urls()
    {
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('transfers24.url_return')->andReturn('http://abc.example');
        $config->shouldReceive('get')->with('transfers24.url_status')->andReturn('https://def.example');

        $url = m::mock(Url::class);
        $url->shouldNotReceive('to');

        $this->request = $this->createConcreteRequest([
            'config' => $config,
            'url' => $url,
        ]);

        $this->request->setDefaultUrls();

        $url_status = $this->request->getField('url_status');
        $url_return = $this->request->getField('url_return');

        $this->assertEquals('http://abc.example', $url_return);
        $this->assertEquals('https://def.example', $url_status);
    }

    /** @test */
    public function test_execution_payment()
    {
        $this->request_concrete = $this->createConcreteRequest();
        $token = [];

        $runner = m::mock(Runner::class);
        $this->runner_factory->shouldReceive('create')
            ->once()
            ->andReturn($runner);
        $runner->shouldReceive('execute')
            ->once()
            ->andReturn('http://redirect');

        try {
            $response = $this->request_concrete->execute($token);
        } catch (\Exception $e) {
            $this->assertInstanceOf(RequestExecutionException::class, $e);
            $this->assertEquals($e->getMessage(), 'Empty or not valid Token');
        }

        $response = $this->request_concrete->execute('123456789');
        $this->assertEquals($response, 'http://redirect');
    }

    /** @test */
    public function test_receive_transfers24_request()
    {
        $translator = m::mock(ReceiveTranslator::class);
        $action = m::mock(Action::class);
        $expected_response = m::mock(IResponse::class);


        $request = m::mock(Request::class);
        $request->shouldReceive('all')
            ->once()
            ->andReturn([]);
        $this->receive_translator_factory->shouldReceive('create')->once()
            ->with([], m::any())
            ->andReturn($translator);
        $this->action_factory->shouldReceive('create')
            ->once()
            ->with($this->response_factory, $translator)
            ->andReturn($action);

        $action->shouldReceive('execute')
            ->once()
            ->andReturn($expected_response);

        $response = $this->request->receive($request);

        $this->assertEquals($expected_response, $response);

    }

    public function createConcreteRequest(array $dependent = [])
    {
        $this->credentials = m::mock(Credentials::class);
        $this->action_factory = m::mock(ActionFactory::class);
        $this->translator_factory = m::mock(RegisterTranslatorFactory::class);
        $this->response_factory = m::mock(ResponseFactory::class);
        $this->receive_translator_factory = m::mock(ReceiveTranslatorFactory::class);

        $this->runner_factory = m::mock(RunnerFactory::class);


        return $this->app->make(RequestTransfers24::class, [
            'credentials_keeper', $this->credentials,
            'action_factory' => $this->action_factory,
            'translator_factory' => $this->translator_factory,
            'response_factory' => $this->response_factory,
            'runner_factory' => $this->runner_factory,
            'receive_translator_factory' => $this->receive_translator_factory,
        ] + $dependent);
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
            'description' => '',
            'name' => 'test payment',
            'number' => 'ACG1122',
            'price' => 11111,
            'quantity' => 100,
        ];

        $this->request->setNextArticle($next_article['name'], $next_article['price'],
            $next_article['quantity'], $next_article['number']);

        $additional_articles = $this->request->getField('additional_articles');
        $first_article = array_pop($additional_articles);
        ksort($first_article);
        $this->assertSame($expected_article, $first_article);
        $this->assertEmpty($additional_articles);
    }
}
