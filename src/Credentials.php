<?php
declare(strict_types=1);

namespace Devpark\Transfers24;


use Devpark\Transfers24\Exceptions\EmptyCredentialsException;
use Devpark\Transfers24\Exceptions\NoEnvironmentChosenException;

class Credentials
{
    /**
     * @var int
     */
    protected $pos_id;
    /**
     * @var int
     */
    protected $merchant_id;
    /**
     * @var string
     */
    protected $crc;
    /**
     * @var bool
     */
    protected $test_mode;

    /**
     * @param int $pos_id
     *
     * @return void
     */
    public function setPosId(int $pos_id): void
    {
        $this->pos_id = $pos_id;
    }

    /**
     * @param int $merchant_id
     *
     * @return void
     */
    public function setMerchantId(int $merchant_id): void
    {
        $this->merchant_id = $merchant_id;
    }


    /**
     * @param bool $test_mode
     *
     * @return void
     */
    public function setTestMode(bool $test_mode): void
    {
        $this->test_mode = $test_mode;
    }

    /**
     * @param string $crc
     *
     * @return void
     */
    public function setCrc(string $crc): void
    {
        $this->crc = $crc;
    }


//        if (!isset($this->pos_id, $this->merchant_id, $this->crc))
//        {
//            throw new EmptyCredentialsException("Empty credentials.");
//        }
//        if (!isset($this->test_mode))
//        {
//            throw new NoEnvironmentChosenException("No environment choosen.");
//        }

    /**
     * @return int
     */
    public function getPosId(): int
    {
        return $this->pos_id;
    }

    /**
     * @return int
     */
    public function getMerchantId(): int
    {
        return $this->merchant_id;
    }

    /**
     * @return string
     */
    public function getCrc(): string
    {
        return $this->crc;
    }

    /**
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->test_mode;
    }

}
