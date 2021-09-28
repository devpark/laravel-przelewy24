<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Forms;

use Devpark\Transfers24\Contracts\Form;

class RefundInfoForm extends AbstractForm implements Form
{
    public function getUri(): string
    {
        return "refund/by/orderId/{$this->order_id}";
    }

    public function getMethod(): string
    {
        return 'GET';
    }
}
