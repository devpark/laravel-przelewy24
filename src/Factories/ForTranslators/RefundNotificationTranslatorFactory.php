<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Factories\ForTranslators;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Requests\PaymentMethodsRequest;
use Devpark\Transfers24\Requests\RefundNotificationRequest;
use Devpark\Transfers24\Requests\RefundRequest;
use Devpark\Transfers24\Requests\Transfers24;
use Devpark\Transfers24\Translators\PaymentMethodsTranslator;
use Devpark\Transfers24\Translators\ReceiveTranslator;
use Devpark\Transfers24\Translators\RefundTranslator;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Devpark\Transfers24\Translators\TestTranslator;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;

class RefundNotificationTranslatorFactory
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
     * @return RefundNotificationTranslator|Translator
     * @throws \Devpark\Transfers24\Exceptions\EmptyCredentialsException
     * @throws \Devpark\Transfers24\Exceptions\NoEnvironmentChosenException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function create(Credentials $credentials, array $notification_data):RefundNotificationTranslator
    {
        /**
         * @var RefundNotificationTranslator $translator
         */
        $translator = $this->app->make(RefundNotificationTranslator::class);
        return $translator->init($credentials, $notification_data);

    }
}
