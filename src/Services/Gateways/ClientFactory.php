<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Services\Gateways;

use GuzzleHttp\Client;
use Illuminate\Contracts\Container\Container;

class ClientFactory
{
    /**
     * @var Container
     */
    protected $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function create($base_uri):Client
    {
        return $this->app->make(Client::class, [
            'config' => ['base_uri' => $base_uri],

        ]);
    }
}
