<?php

declare(strict_types=1);

namespace Devpark\Transfers24\Forms;

use Devpark\Transfers24\Contracts\Form;

class RegisterOfflineForm extends AbstractForm implements Form
{
    public function getUri(): string
    {
        return 'transaction/registerOffline';
    }

    public function getMethod(): string
    {
        return 'POST';
    }
}
