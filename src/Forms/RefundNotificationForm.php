<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Forms;

use Devpark\Transfers24\Contracts\Form;

class RefundNotificationForm extends AbstractForm implements Form
{

    public function getUri(): string
    {
        throw new FormException;
    }

    public function getMethod(): string
    {
        throw new FormException;
    }
}
