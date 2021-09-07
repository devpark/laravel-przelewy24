<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Translators;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Exceptions\EmptyCredentialsException;
use Devpark\Transfers24\Exceptions\NoEnvironmentChosenException;
use Devpark\Transfers24\Forms\ReceiveForm;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Requests\Transfers24;
use Devpark\Transfers24\Services\Crc;
use Illuminate\Config\Repository as Config;

class ReceiveTranslator implements Translator
{
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var array
     */
    private $receive_parameters;
    /**
     * @var Credentials
     */
    private $credentials_keeper;

    private $params = ['p24_session_id', 'p24_order_id', 'p24_amount', 'p24_currency'];

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
    private $salt;

    /**
     * @var Form
     */
    private $form;

    public function __construct(Crc $crc, Config $config){
        $this->crc = $crc;
        $this->config = $config;
    }


    public function init(array $receive_data, Credentials $credentials):ReceiveTranslator{

        $this->receive_parameters = $receive_data;
        $this->credentials_keeper = $credentials;
        return $this;
    }

    public function translate():Form
    {
        $this->form = new ReceiveForm();

        $check_sum = true;
//        $check_sum = $verify_check_sum ? $this->transfers24->checkSum($post_data) : true;

        if ($check_sum) {

            $session_id = $this->receive_parameters['p24_session_id'];
            $this->form->addValue('p24_session_id', $session_id);
            $this->form->setSessionId($session_id);

            $order_id = $this->receive_parameters['p24_order_id'];
            $this->form->addValue('p24_order_id', $order_id);
            $this->form->setOrderId($order_id);

            $this->form->addValue('p24_amount', $this->receive_parameters['p24_amount']);
            $this->form->addValue('p24_currency', $this->receive_parameters['p24_currency']);


//            $fields = [
//                'p24_session_id' => $this->session_id,
//                'p24_order_id' => $this->order_id,
//                'p24_amount' => ,
//                '' => $this->receive_parameters[''],
//            ];
//
//            $this->http_response = $this->transfers24->trnVerify($fields);

        }

        $p24_api_version = $this->config->get('transfers24.version');
        $this->form->addValue('p24_api_version', $p24_api_version);
        $this->form->addValue('p24_merchant_id', $this->merchant_id);
        $this->form->addValue('p24_pos_id', $this->pos_id);

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
//            $this->sandbox = $this->credentials_keeper->isTestMode();
        }else{
            $this->pos_id = $this->config->get('transfers24.pos_id');
            $this->merchant_id = $this->config->get('transfers24.merchant_id');
            $this->salt = $this->config->get('transfers24.crc');
//            $this->sandbox = $this->config->get('transfers24.test_server');
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

    public function getCredentials():Credentials
    {
        return $this->credentials_keeper;
    }

}
