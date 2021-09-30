<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Forms;

use Devpark\Transfers24\Contracts\Form;

class RefundForm extends AbstractForm implements Form
{
    public function getUri(): string
    {
        return 'transaction/refund';
    }

    public function getMethod(): string
    {
        return 'POST';
    }
}
