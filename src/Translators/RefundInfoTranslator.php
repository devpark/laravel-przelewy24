<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Translators;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Contracts\Translator;
use Devpark\Transfers24\Credentials;
use Devpark\Transfers24\Forms\RefundInfoForm;

class RefundInfoTranslator extends AbstractTranslator implements Translator
{
    /**
     * @var string
     */
    private $order_id;

    public function init(Credentials $credentials, $order_id):RefundInfoTranslator
    {
        $this->credentials_keeper = $credentials;
        $this->order_id = $order_id;

        return $this;
    }

    public function translate():Form
    {
        $this->form = new RefundInfoForm();

        $this->form->setOrderId($this->order_id);

        return $this->form;
    }

    protected function getCrcParams(): array
    {
        return [];
    }
}
