<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Requests;

use Devpark\Transfers24\Credentials;

trait RequestCredentialsKeeperTrait
{
    /**
     * @var Credentials
     */
    protected $credentials_keeper;

    /**
     * @param int $pos_id
     *
     */
    public function setPosId(int $pos_id)
    {
        $this->credentials_keeper->setPosId($pos_id);

        return $this;
    }

    /**
     * @param string $crc
     *
     */
    public function setCrc(string $crc)
    {
        $this->credentials_keeper->setCrc($crc);

        return $this;
    }

    /**
     * @param bool $test_mode
     *
     */
    public function setTestMode(bool $test_mode)
    {
        $this->credentials_keeper->setTestMode($test_mode);

        return $this;
    }

    /**
     * @param int $merchant_id
     *
     */
    public function setMerchantId(int $merchant_id)
    {
        $this->credentials_keeper->setMerchantId($merchant_id);

        return $this;
    }
}
