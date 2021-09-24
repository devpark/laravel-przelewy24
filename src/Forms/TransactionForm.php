<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Forms;

use Devpark\Transfers24\Contracts\Form;

class TransactionForm extends AbstractForm implements Form
{
    public function getUri(): string
    {
        return "transaction/by/sessionId/{$this->session_id}";
    }

    public function getMethod(): string
    {
        return 'GET';
    }
}
