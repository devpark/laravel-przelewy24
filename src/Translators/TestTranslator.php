<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Translators;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Forms\TestForm;

class TestTranslator extends AbstractTranslator implements Translator
{
    public function init(Credentials $credentials):TestTranslator
    {
        $this->credentials_keeper = $credentials;

        return $this;
    }

    public function translate():Form
    {
        $this->form = new TestForm();

        return $this->form;
    }

    protected function getCrcParams(): array
    {
        return [];
    }
}
