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

class ReceiveTranslator extends AbstractTranslator implements Translator
{
    /**
     * @var array
     */
    private $receive_parameters;

    public function init(array $receive_data, Credentials $credentials):ReceiveTranslator{

        $this->receive_parameters = $receive_data;
        $this->credentials_keeper = $credentials;
        return $this;
    }

    public function translate():Form
    {
        $this->form = new ReceiveForm();

        $session_id = $this->receive_parameters['p24_session_id'];
        $this->form->addValue('p24_session_id', $session_id);
        $this->form->setSessionId($session_id);

        $order_id = $this->receive_parameters['p24_order_id'];
        $this->form->addValue('p24_order_id', $order_id);
        $this->form->setOrderId($order_id);

        $this->form->addValue('p24_amount', $this->receive_parameters['p24_amount']);
        $this->form->addValue('p24_currency', $this->receive_parameters['p24_currency']);

        $p24_api_version = $this->config->get('transfers24.version');
        $this->form->addValue('p24_api_version', $p24_api_version);
        $this->form->addValue('p24_merchant_id', $this->merchant_id);
        $this->form->addValue('p24_pos_id', $this->pos_id);

        $this->form->addValue('p24_sign', $this->calculateSign());

        $this->form->setReceivedParameters($this->receive_parameters);

        return $this->form;
    }

    protected function getCrcParams(): array
    {
        return ['p24_session_id', 'p24_order_id', 'p24_amount', 'p24_currency'];
    }
}
