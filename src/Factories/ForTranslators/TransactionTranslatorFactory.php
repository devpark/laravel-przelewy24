<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Factories\ForTranslators;

use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Translators\TransactionTranslator;
use Illuminate\Contracts\Container\Container;

class TransactionTranslatorFactory
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
     * @return TransactionTranslator|Translator
     * @throws \Devpark\Transfers24\Exceptions\EmptyCredentialsException
     * @throws \Devpark\Transfers24\Exceptions\NoEnvironmentChosenException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function create(Credentials $credentials, $session_id):TransactionTranslator
    {
        /**
         * @var TransactionTranslator $translator
         */
        $translator = $this->app->make(TransactionTranslator::class);

        return $translator->init($credentials, $session_id);
    }
}
