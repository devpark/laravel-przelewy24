<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Factories;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Requests\Transfers24;
use Devpark\Transfers24\Translators\ReceiveTranslator;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Devpark\Transfers24\Translators\TestTranslator;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;

class TestTranslatorFactory
{
    /**
     * @var Container
     */
    private $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @param Credentials $credentials
     * @return TestTranslator|Translator
     * @throws \Devpark\Transfers24\Exceptions\EmptyCredentialsException
     * @throws \Devpark\Transfers24\Exceptions\NoEnvironmentChosenException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function create(Credentials $credentials):TestTranslator
    {
        /**
         * @var TestTranslator $translator
         */
        $translator = $this->app->make(TestTranslator::class);
        return $translator->init($credentials);

    }
}
