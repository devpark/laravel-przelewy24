<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Translators;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Forms\TransactionForm;

class TransactionTranslator extends AbstractTranslator implements Translator
{
    /**
     * @var string
     */
    private $session_id;

    public function init(Credentials $credentials, $session_id):TransactionTranslator
    {
        $this->credentials_keeper = $credentials;
        $this->session_id = $session_id;

        return $this;
    }

    public function translate():Form
    {
        $this->form = new TransactionForm();

        $this->form->setSessionId($this->session_id);

        return $this->form;
    }

    protected function getCrcParams(): array
    {
        return [];
    }
}
