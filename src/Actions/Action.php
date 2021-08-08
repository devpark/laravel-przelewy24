<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Actions;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Factories\HandlerFactory;
use Devpark\Transfers24\Factories\ResponseFactory;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Services\Gateways\Transfers24;
use Devpark\Transfers24\Translators\RegisterTranslator;

class Action
{
    /**
     * @var HandlerFactory
     */
    protected $handler_factory;
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

    public function __construct(Transfers24 $gateway)
    {
        $this->gateway = $gateway;
    }

    public function init(HandlerFactory $handler_factory, ResponseFactory $response_factory, RegisterTranslator $translator):Action
    {
        $this->handler_factory = $handler_factory;
        $this->response_factory = $response_factory;
        $this->translator = $translator;
    }

    public function execute():IResponse
    {
        //translate userdata to form
        //create request
        //fill request by form
        //create sender
        //send request
        //create response
        //fill response


    }
}
