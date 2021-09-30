<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Factories\ForTranslators;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Requests\PaymentMethodsRequest;
use Devpark\Transfers24\Requests\Transfers24;
use Devpark\Transfers24\Translators\PaymentMethodsTranslator;
use Devpark\Transfers24\Translators\ReceiveTranslator;
use Devpark\Transfers24\Translators\RegisterOfflineTranslator;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Devpark\Transfers24\Translators\TestTranslator;
use Devpark\Transfers24\Translators\TransactionTranslator;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;

class RegisterOfflineTranslatorFactory
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
     * @return RegisterOfflineTranslator|Translator
     * @throws \Devpark\Transfers24\Exceptions\EmptyCredentialsException
     * @throws \Devpark\Transfers24\Exceptions\NoEnvironmentChosenException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function create(Credentials $credentials, $token):RegisterOfflineTranslator
    {
        /**
         * @var RegisterOfflineTranslator $translator
         */
        $translator = $this->app->make(RegisterOfflineTranslator::class);
        return $translator->init($credentials, $token);

    }
}