<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Factories;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Services\Crc;
use Devpark\Transfers24\Services\Handlers\Transfers24;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;

class HandlerFactory
{
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var Container
     */
    private $app;

    public function __construct(Container $app, Config $config )
    {
        $this->app = $app;
        $this->config = $config;
    }
    public function create():Transfers24
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
