<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Factories;

use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Translators\ReceiveTranslator;
use Illuminate\Contracts\Container\Container;

class ReceiveTranslatorFactory
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
     * @param array $request
     * @param Credentials $credentials
     * @return ReceiveTranslator|Translator
     * @throws \Devpark\Transfers24\Exceptions\EmptyCredentialsException
     * @throws \Devpark\Transfers24\Exceptions\NoEnvironmentChosenException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function create(array $request, Credentials $credentials):ReceiveTranslator
    {
        /**
         * @var ReceiveTranslator $translator
         */
        $translator = $this->app->make(ReceiveTranslator::class);

        return $translator->init($request, $credentials);
    }
}
