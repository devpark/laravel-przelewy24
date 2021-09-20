<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Translators;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Exceptions\EmptyCredentialsException;
use Devpark\Transfers24\Exceptions\NoEnvironmentChosenException;
use Devpark\Transfers24\Forms\PaymentMethodsForm;
use Devpark\Transfers24\Forms\ReceiveForm;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Forms\TestForm;
use Devpark\Transfers24\Requests\PaymentMethodsRequest;
use Devpark\Transfers24\Requests\Transfers24;
use Devpark\Transfers24\Services\Crc;
use Illuminate\Config\Repository as Config;

class PaymentMethodsTranslator extends AbstractTranslator implements Translator
{
    /**
     * @var string
     */
    private $lang;

    public function init(Credentials $credentials, $lang):PaymentMethodsTranslator{

        $this->credentials_keeper = $credentials;
        $this->lang = $lang;
        return $this;
    }

    public function translate():Form
    {
        $this->form = new PaymentMethodsForm();

        $this->form->setLanguage($this->lang);

        return $this->form;
    }

    protected function getCrcParams(): array
    {
        return [];
    }
}
