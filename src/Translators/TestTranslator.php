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
use Devpark\Transfers24\Forms\TestForm;
use Devpark\Transfers24\Requests\Transfers24;
use Devpark\Transfers24\Services\Crc;
use Illuminate\Config\Repository as Config;

class TestTranslator extends AbstractTranslator implements Translator
{
    public function init(Credentials $credentials):TestTranslator{

        $this->credentials_keeper = $credentials;
        return $this;
    }

    public function translate():Form
    {
        $this->form = new TestForm();

        $p24_api_version = $this->config->get('transfers24.version');
        $this->form->addValue('p24_api_version', $p24_api_version);
        $this->form->addValue('p24_merchant_id', $this->merchant_id);
        $this->form->addValue('p24_pos_id', $this->pos_id);

        $this->form->addValue('p24_sign', $this->calculateSign());

        return $this->form;
    }

    protected function getCrcParams(): array
    {
        return ['p24_merchant_id', 'p24_pos_id'];
    }
}
