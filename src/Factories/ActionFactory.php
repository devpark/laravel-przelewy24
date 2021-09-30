<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Factories;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Contracts\ResponseFactory;
use Devpark\Transfers24\Contracts\Translator;
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

    public function create(ResponseFactory $response_factory, Translator $translator):Action
    {
        /**
         * @var Action $handler
         */
        $handler = $this->app->make(Action::class);
        $handler->init($response_factory, $translator);

        return $handler;
    }
}
