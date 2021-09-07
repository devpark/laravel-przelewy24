<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Factories;

use Devpark\Transfers24\Actions\Action;
use Devpark\Transfers24\Actions\Runner;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Exceptions\NoEnvironmentChosenException;
use Devpark\Transfers24\Translators\RegisterTranslator;
use Illuminate\Contracts\Container\Container;

class RunnerFactory
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
     * @return Runner
     * @throws NoEnvironmentChosenException
     */
    public function create(Credentials $credentials):Runner
    {
        /**
         * @var Runner $handler
         */
        $handler = $this->app->make(Runner::class);
        $handler->init($credentials);
        return $handler;
    }
}
