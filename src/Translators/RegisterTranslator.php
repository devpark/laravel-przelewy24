<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Translators;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Exceptions\EmptyCredentialsException;
use Devpark\Transfers24\Exceptions\NoEnvironmentChosenException;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Requests\Transfers24;
use Devpark\Transfers24\Services\Crc;
use Illuminate\Config\Repository as Config;

class RegisterTranslator
{
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var Transfers24
     */
    private $request;
    /**
     * @var Credentials
     */
    private $credentials_keeper;

    private $params = ['p24_session_id', 'p24_merchant_id', 'p24_amount', 'p24_currency'];
    /**
     * @var Crc
     */
    private $crc;
    /**
     * @var array|mixed
     */
    private $pos_id;
    /**
     * @var array|mixed
     */
    private $merchant_id;
    /**
     * @var array|mixed
     */
    private $sandbox;
    /**
     * @var array|mixed
     */
    private $salt;

    /**
     * @var RegisterForm
     */
    private $form;

    public function __construct(Crc $crc, Config $config){
        $this->crc = $crc;
        $this->config = $config;
    }


    public function init(Transfers24 $request, Credentials $credentials):RegisterTranslator{

        $this->request = $request;
        $this->credentials_keeper = $credentials;
        return $this;
    }

    public function translate():RegisterForm
    {
        $this->form = new RegisterForm();
        $p24_api_version = $this->config->get('transfers24.version');

        $this->form->addValue('p24_api_version', $p24_api_version);
        $this->form->addValue('p24_sign', $this->calculateSign());

        return $this->form;
    }

    /**
     * @throws EmptyCredentialsException
     * @throws NoEnvironmentChosenException
     */
    public function configure(): self
    {
        if ($this->config->get('transfers24.credentials-scope')) {
            if (!isset($this->credentials_keeper)) {
                throw new EmptyCredentialsException("Empty credentials.");
            }
            $this->pos_id = $this->credentials_keeper->getPosId();
            $this->merchant_id = $this->credentials_keeper->getMerchantId();
            $this->salt = $this->credentials_keeper->getCrc();
            $this->sandbox = $this->credentials_keeper->isTestMode();
        }else{
            $this->pos_id = $this->config->get('transfers24.pos_id');
            $this->merchant_id = $this->config->get('transfers24.merchant_id');
            $this->salt = $this->config->get('transfers24.crc');
            $this->sandbox = $this->config->get('transfers24.test_server');
        }
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
        if (!empty($this->salt)){
            $this->crc->setSalt($this->salt);
        }
        return $this->crc->sum($this->params, $this->form->toArray());

    }

}