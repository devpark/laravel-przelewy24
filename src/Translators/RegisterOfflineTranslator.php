<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Translators;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Forms\RegisterOfflineForm;

class RegisterOfflineTranslator extends AbstractTranslator implements Translator
{
    /**
     * @var string
     */
    private $token;

    public function init(Credentials $credentials, $token):RegisterOfflineTranslator
    {
        $this->credentials_keeper = $credentials;
        $this->token = $token;

        return $this;
    }

    public function translate():Form
    {
        $this->form = new RegisterOfflineForm();

        $this->form->addValue('token', $this->token);

        return $this->form;
    }

    protected function getCrcParams(): array
    {
        return [];
    }
}
