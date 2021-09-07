<?php
declare(strict_types=1);

namespace Devpark\Transfers24\Forms;

use Devpark\Transfers24\Contracts\Form;

class ReceiveForm extends AbstractForm implements Form
{

    private $received_parameters;

    public function getUri(): string
    {
        return 'trnVerify';
    }

    public function getMethod(): string
    {
        return 'POST';
    }

    public function getReceiveParameters():array
    {
        return $this->received_parameters;
    }

    /**
     * @param mixed $received_parameters
     */
    public function setReceivedParameters($received_parameters): void
    {
        $this->received_parameters = $received_parameters;
    }

}
