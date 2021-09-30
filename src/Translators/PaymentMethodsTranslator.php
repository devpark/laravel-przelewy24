<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Translators;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Forms\PaymentMethodsForm;

class PaymentMethodsTranslator extends AbstractTranslator implements Translator
{
    /**
     * @var string
     */
    private $lang;

    public function init(Credentials $credentials, $lang):PaymentMethodsTranslator
    {
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
