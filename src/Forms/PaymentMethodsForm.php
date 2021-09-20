<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Forms;

use Devpark\Transfers24\Contracts\Form;

class PaymentMethodsForm extends AbstractForm implements Form
{
    private $lang;

    public function getUri(): string
    {
        return "payment/methods/{$this->lang}";
    }

    public function getMethod(): string
    {
        return 'GET';
    }

    public function setLanguage($lang):void{
        $this->lang = $lang;
    }
}
