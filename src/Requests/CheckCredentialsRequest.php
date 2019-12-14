<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Requests;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Exceptions\EmptyCredentialsException;
use Devpark\Transfers24\Exceptions\NoEnvironmentChosenException;
use Devpark\Transfers24\Responses\InvalidResponse;
use Devpark\Transfers24\Responses\TestConnection;
use Devpark\Transfers24\Services\Handlers\Transfers24 as Handler;

class CheckCredentialsRequest
{
    use RequestCredentialsKeeperTrait;

    /**
     * @var Handler
     */
    protected $handler;

    public function __construct(Handler $handler, Credentials $credentials_keeper)
    {
        $this->handler = $handler;
        $this->credentials_keeper = $credentials_keeper;
    }

    /**
     * @return TestConnection|InvalidResponse
     */
    public function execute():IResponse
    {
        return $this->handler
            ->viaCredentials($this->credentials_keeper)
            ->checkCredentials();
    }

}
