<?php

namespace Tests\Translators;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Currency;
use Devpark\Transfers24\Exceptions\RequestExecutionException;
use Devpark\Transfers24\Factories\HandlerFactory;
use Devpark\Transfers24\Factories\RegisterTranslatorFactory;
use Devpark\Transfers24\Factories\ResponseFactory;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Requests\Transfers24 as RequestTransfers24;
use Devpark\Transfers24\Responses\Http\Response;
use Devpark\Transfers24\Services\Crc;
use Devpark\Transfers24\Services\Gateways\Transfers24;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Arr;
use stdClass;
use Tests\UnitTestCase;
use Mockery as m;
use Illuminate\Foundation\Application;
use Devpark\Transfers24\Responses\Register as RegisterResponse;
use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlersTransfers24;
use Devpark\Transfers24\Exceptions\RequestException;
use Illuminate\Http\Request;
use Illuminate\Config\Repository as Config;

class RegisterTranslatorTest extends UnitTestCase
{
    /**
     * @var m\Mock
     */
    private $credentials;
    /**
     * @var m\MockInterface
     */
    private $response;
    /**
     * @var m\MockInterface
     */
    private $translator_factory;
    /**
     * @var RegisterTranslator
     */
    private $translator;
    /**
     * @var m\MockInterface
     */
    private $handle_factory;
    /**
     * @var m\MockInterface
     */
    private $handler;
    /**
     * @var m\MockInterface
     */
    private $gateway;
    /**
     * @var m\MockInterface
     */
    private $response_factory;
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

        $this->handle_factory = m::mock(Credentials::class);
        $this->handler = m::mock(\Devpark\Transfers24\Services\Handlers\Transfers24::class);

        $this->crc = m::mock(Crc::class);

        $this->config = m::mock(Config::class);

        $this->translator = $this->app->make(RegisterTranslator::class, [
            'crc' => $this->crc,
            'config' => $this->config,
        ]);
//        $this->action->init($this->response_factory, $this->translator, $this->handle_factory);
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

        //When
        $this->config->shouldReceive('get')
            ->once()
            ->with('transfers24.version')
            ->andReturn('p24_api_version');

        $this->crc->shouldReceive('sum')
            ->once()
            ->andReturn('p24_sign');

        $form = $this->translator->translate();

        $data = $form->toArray();
        $this->assertSame($p24_api_version,Arr::get($data, $p24_api_version));
        $this->assertSame($p24_sign,Arr::get($data, $p24_sign));
    }


    /**
     * @Feature Payments
     * @Scenario Register Form
     * @Case Translate Form
     * @test
     */
    public function translate_form()
    {
        //Given

        $form = m::mock(RegisterForm::class);
        $this->translator->shouldReceive('translate')
            ->once()
            ->andReturn($form);

//        $this->handle_factory->shouldReceive('create')
//            ->with()
//            ->once()
//            ->andReturn($this->handler);
//
//        $this->handler->shouldReceive('fill')
//            ->with($form)
//            ->once();

        $gateway_response = m::mock(Response::class);

        $this->gateway->shouldReceive('callTransfers24')
            ->with($form)
            ->once()
            ->andReturn($gateway_response);

        $this->response_factory->shouldReceive('create')
            ->with($gateway_response)
            ->once()
            ->andReturn($this->response);

        //When
        $response = $this->transalor->execute();

        //Then
        $this->assertSame($this->response, $response);
    }

}
