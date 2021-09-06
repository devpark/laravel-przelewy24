<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Actions;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Factories\HandlerFactory;
use Devpark\Transfers24\Factories\ResponseFactory;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Services\Gateways\Transfers24;
use Devpark\Transfers24\Translators\RegisterTranslator;

class Action
{
    /**
     * @var ResponseFactory
     */
    protected $response_factory;
    /**
     * @var RegisterTranslator
     */
    protected $translator;
    /**
     * @var Transfers24
     */
    protected $gateway;
    /**
     * @var Credentials
     */
    protected $credentials;

    public function __construct(Transfers24 $gateway)
    {
        $this->gateway = $gateway;
    }

    public function init(ResponseFactory $response_factory, RegisterTranslator $translator, Credentials $credentials):Action
    {
        $this->response_factory = $response_factory;
        $this->translator = $translator;
        $this->credentials = $credentials;
        return $this;
    }

    public function execute():IResponse
    {
        $form = $this->translator->translate();
        $gateway_response = $this->gateway->callTransfers24($form);

        return $this->response_factory->create($gateway_response);
    }
}
