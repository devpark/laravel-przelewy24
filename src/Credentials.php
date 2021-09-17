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
     * @var string
     */
    protected $report_key;

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

    /**
     * @return int
     * @throws EmptyCredentialsException
     */
    public function getPosId(): int
    {
        $this->throwEmptyCredentials();

        return $this->pos_id;
    }

    /**
     * @return int
     * @throws EmptyCredentialsException
     */
    public function getMerchantId(): int
    {
        $this->throwEmptyCredentials();

        return $this->merchant_id;
    }

    /**
     * @return string
     * @throws EmptyCredentialsException
     */
    public function getCrc(): string
    {
        $this->throwEmptyCredentials();

        return $this->crc;
    }

    /**
     * @return bool
     * @throws NoEnvironmentChosenException
     */
    public function isTestMode(): bool
    {
        if (!isset($this->test_mode))
        {
            throw new NoEnvironmentChosenException("No environment choosen.");
        }
        return $this->test_mode;
    }

    protected function throwEmptyCredentials(): void
    {
        if (!isset($this->pos_id, $this->merchant_id, $this->crc, $this->report_key)) {
            throw new EmptyCredentialsException("Empty credentials.");
        }
    }

    /**
     * @return string
     * @throws EmptyCredentialsException
     */
    public function getReportKey(): string
    {
        $this->throwEmptyCredentials();

        return $this->report_key;
    }

    /**
     * @param string $report_key
     */
    public function setReportKey(string $report_key): void
    {
        $this->report_key = $report_key;
    }

}
