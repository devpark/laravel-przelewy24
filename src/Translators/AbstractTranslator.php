<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Translators;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Exceptions\EmptyCredentialsException;
use Devpark\Transfers24\Exceptions\NoEnvironmentChosenException;
use Devpark\Transfers24\Requests\Transfers24;
use Devpark\Transfers24\Services\Crc;
use Illuminate\Config\Repository as Config;

abstract class AbstractTranslator
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Credentials
     */
    protected $credentials_keeper;

    /**
     * @var Crc
     */
    protected $crc;

    /**
     * @var array|mixed
     */
    protected $pos_id;

    /**
     * @var array|mixed
     */
    protected $merchant_id;

    /**
     * @var array|mixed
     */
    protected $salt;

    /**
     * @var Form
     */
    protected $form;

    public function __construct(Crc $crc, Config $config)
    {
        $this->crc = $crc;
        $this->config = $config;
    }

    abstract public function translate():Form;

    /**
     * @throws EmptyCredentialsException
     * @throws NoEnvironmentChosenException
     */
    public function configure(): Translator
    {
        if ($this->config->get('transfers24.credentials-scope')) {
            if (! isset($this->credentials_keeper)) {
                throw new EmptyCredentialsException('Empty credentials.');
            }
            $this->pos_id = $this->credentials_keeper->getPosId();
            $this->merchant_id = $this->credentials_keeper->getMerchantId();
            $this->salt = $this->credentials_keeper->getCrc();
        } else {
            $this->pos_id = $this->config->get('transfers24.pos_id');
            $this->merchant_id = $this->config->get('transfers24.merchant_id');
            $this->salt = $this->config->get('transfers24.crc');
        }

        return $this;
    }

    /**
     * Add CRC sum on params send to transfers24.
     *
     * @param array $params
     *
     * @return string
     */
    public function calculateSign()
    {
        if (! empty($this->salt)) {
            $this->crc->setSalt($this->salt);
        }

        return $this->crc->sum($this->getCrcParams(), $this->form->toArray());
    }

    public function getCredentials():Credentials
    {
        return $this->credentials_keeper;
    }

    /**
     * @return string[]
     */
    abstract protected function getCrcParams(): array;
}
