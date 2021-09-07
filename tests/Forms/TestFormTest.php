<?php

namespace Tests\Forms;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Currency;
use Devpark\Transfers24\Exceptions\RequestExecutionException;
use Devpark\Transfers24\Factories\HandlerFactory;
use Devpark\Transfers24\Factories\RegisterTranslatorFactory;
use Devpark\Transfers24\Factories\RegisterResponseFactory;
use Devpark\Transfers24\Forms\ReceiveForm;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Forms\TestForm;
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

class TestFormTest extends UnitTestCase
{
    /**
     * @var TestForm
     */
    private $form;

    protected function setUp()
    {
        parent::setUp();

        $this->form = new TestForm();
    }

    /**
     * @Feature Payments
     * @Scenario Test Form
     * @Case get method
     * @test
     */
    public function get_method()
    {
        //When
        $method = $this->form->getMethod();

        //Then
        $this->assertSame('POST', $method);
    }

    /**
     * @Feature Payments
     * @Scenario Test Form
     * @Case get uri
     * @test
     */
    public function get_uri()
    {
        //When
        $method = $this->form->getUri();

        //Then
        $this->assertSame('testConnection', $method);
    }

}
