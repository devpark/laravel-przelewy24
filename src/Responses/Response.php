<?php

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Contracts\Form;
use Devpark\Transfers24\Forms\RegisterForm;
use Devpark\Transfers24\Services\DecodedBody;
use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlerTransfers24;

/**
 * Class Response.
 */
abstract class Response
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var DecodedBody
     */
    protected $decoded_body;

    public function __construct(Form $form, DecodedBody $decoded_body)
    {
        $this->form = $form;
        $this->decoded_body = $decoded_body;
    }

    /**
     * Get Code for payment.
     *
     * @return int
     */
    public function getCode()
    {
        return $this->decoded_body->getStatusCode();
    }

    /**
     * Get Error description for payment.
     *
     * @return string
     */
    public function getErrorDescription()
    {
        return $this->decoded_body->getErrorMessage();
    }

    /**
     * Get Request parameters send to Transfers24.
     *
     * @return array
     */
    public function getRequestParameters()
    {
        return $this->form->toArray();
    }

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
     * Get Session number of payment.
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->form->getSessionId();
    }

    /**
     * Get status response from transfers24.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->getCode() === '0';
    }

    /**
     * Get Error Code back from transfers24.
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->getCode();
    }

}
