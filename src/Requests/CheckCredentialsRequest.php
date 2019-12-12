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
        return $this->handler->checkCredentials();
    }

    /**
     * @param int $pos_id
     *
     * @return CheckCredentialsRequest
     */
    public function setPosId(int $pos_id): CheckCredentialsRequest
    {
        $this->credentials_keeper->setPosId($pos_id);

        return $this;
    }

    /**
     * @param int $merchant_id
     *
     * @return CheckCredentialsRequest
     */
    public function setMerchantId(int $merchant_id): CheckCredentialsRequest
    {
        $this->credentials_keeper->setMerchantId($merchant_id);

        return $this;
    }


    /**
     * @param bool $test_mode
     *
     * @return CheckCredentialsRequest
     */
    public function setTestMode(bool $test_mode): CheckCredentialsRequest
    {
        $this->credentials_keeper->setTestMode($test_mode);

        return $this;
    }

    /**
     * @param string $crc
     *
     * @return CheckCredentialsRequest
     */
    public function setCrc(string $crc): CheckCredentialsRequest
    {
        $this->credentials_keeper->setCrc($crc);

        return $this;
    }

}
