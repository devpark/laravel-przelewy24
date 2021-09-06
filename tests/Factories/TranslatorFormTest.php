<?php

namespace Tests\Factories;

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
use Devpark\Transfers24\Services\Gateways\Transfers24;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Contracts\Container\Container;
use stdClass;
use Tests\UnitTestCase;
use Mockery as m;
use Illuminate\Foundation\Application;
use Devpark\Transfers24\Responses\Register as RegisterResponse;
use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlersTransfers24;
use Devpark\Transfers24\Exceptions\RequestException;
use Illuminate\Http\Request;
use Illuminate\Config\Repository as Config;

class TranslatorFormTest extends UnitTestCase
{

    /**
     * @var m\MockInterface
     */
    private $container;
    /**
     * @var RegisterTranslatorFactory
     */
    private $factory;

    protected function setUp()
    {
        parent::setUp();

        $this->container = m::mock(Container::class);
        $this->factory = $this->app->make(RegisterTranslatorFactory::class, [
            'app' => $this->container,
        ]);
    }

    /** @test */
    public function create()
    {
        //Given

        $request = m::mock(\Devpark\Transfers24\Requests\Transfers24::class);
        $translator = m::mock(RegisterTranslator::class);
        $this->container->shouldReceive('make')
            ->once()
            ->andReturn($translator);

        $translator->shouldReceive('init')
            ->once()
            ->with($request)
            ->andReturnSelf();

        //When
        $form = $this->factory->create($request);

        //Then
        $this->assertSame($translator, $form);
    }

}