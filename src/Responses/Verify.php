<?php

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Contracts\IResponse;
use Devpark\Transfers24\Forms\ReceiveForm;

/**
 * Class Verify.
 */
class Verify extends Response implements IResponse
{
    /**
     * @var ReceiveForm
     */
    protected $form;

    /**
     * Get Transaction number received from transfers24.
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->form->getOrderId();
    }

    /**
     * Get Transaction number received from transfers24.
     *
     * @return array
     */
    public function getReceiveParameters():array
    {
        return $this->form->getReceiveParameters();
    }
}
