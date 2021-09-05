<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Factories;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Contracts\Container\Container;

class ActionFactory
{
    /**
     * @var Container
     */
    private $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }
    public function create(RegisterTranslator $translator):Action
    {
        /**
         * @var Action $translator
         */
        $translator = $this->app->make(Action::class);
        return $translator->init();
        //factoryTranslator
        //translate userdata to form
        //create request
        //fill request by form
        //create sender
        //send request
        //create response
        //fill response


    }
}
