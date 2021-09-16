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

        $session_id = $this->receive_parameters['sessionId'];
        $this->form->addValue('sessionId', $session_id);
        $this->form->setSessionId($session_id);

        $order_id = $this->receive_parameters['orderId'];
        $this->form->addValue('orderId', $order_id);
        $this->form->setOrderId($order_id);

        $this->form->addValue('amount', $this->receive_parameters['amount']);
        $this->form->addValue('currency', $this->receive_parameters['currency']);

        $this->form->addValue('merchantId', $this->merchant_id);
        $this->form->addValue('posId', $this->pos_id);

        $this->form->addValue('sign', $this->calculateSign());

        $this->form->setReceivedParameters($this->receive_parameters);

        return $this->form;
    }

    protected function getCrcParams(): array
    {
        return ['sessionId', 'orderId', 'amount', 'currency'];
    }
}
