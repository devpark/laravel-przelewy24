<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Actions;

use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Services\Gateways\Transfers24;

class Runner
{
    /**
     * @var Transfers24
     */
    protected $gateway;

    public function __construct(Transfers24 $gateway)
    {
        $this->gateway = $gateway;
    }

    public function init(Credentials $credentials):Runner
    {
        $this->gateway->configureGateway($credentials);

        return $this;
    }

    public function execute($token, $redirect):string
    {
        return $this->gateway->trnRequest($token, $redirect);
    }
}
