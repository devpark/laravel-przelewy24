<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Factories\ForTranslators;

use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Requests\RefundRequest;
use Devpark\Transfers24\Translators\RefundTranslator;
use Illuminate\Contracts\Container\Container;

class RefundTranslatorFactory
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
     * @return RefundTranslator|Translator
     * @throws \Devpark\Transfers24\Exceptions\EmptyCredentialsException
     * @throws \Devpark\Transfers24\Exceptions\NoEnvironmentChosenException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function create(Credentials $credentials, RefundRequest $request):RefundTranslator
    {
        /**
         * @var RefundTranslator $translator
         */
        $translator = $this->app->make(RefundTranslator::class);

        return $translator->init($credentials, $request);
    }
}
