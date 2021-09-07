<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Factories;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Requests\Transfers24;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Contracts\Container\Container;

class RegisterTranslatorFactory
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
     * @param Transfers24 $request
     * @param Credentials $credentials
     * @return RegisterTranslator|Translator
     * @throws \Devpark\Transfers24\Exceptions\EmptyCredentialsException
     * @throws \Devpark\Transfers24\Exceptions\NoEnvironmentChosenException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function create(Transfers24 $request, Credentials $credentials):RegisterTranslator
    {
        /**
         * @var RegisterTranslator $translator
         */
        $translator = $this->app->make(RegisterTranslator::class);
        return $translator->init($request, $credentials)->configure();

    }
}
