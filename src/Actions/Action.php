<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Actions;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Factories\ResponseFactory;
use Devpark\Transfers24\Services\Gateways\Transfers24;

class Action
{
    /**
     * @var ResponseFactory
     */
    protected $response_factory;
    /**
     * @var Translator
     */
    protected $translator;
    /**
     * @var Transfers24
     */
    protected $gateway;

    public function __construct(Transfers24 $gateway)
    {
        $this->gateway = $gateway;
    }

    public function init(ResponseFactory $response_factory, Translator $translator):Action
    {
        $this->response_factory = $response_factory;
        $this->translator = $translator;
        $this->gateway->configureGateway($translator->getCredentials());
        return $this;
    }

    public function execute():IResponse
    {
        $form = $this->translator->translate();
        $gateway_response = $this->gateway->callTransfers24($form);

        return $this->response_factory->create($gateway_response);
    }
}
