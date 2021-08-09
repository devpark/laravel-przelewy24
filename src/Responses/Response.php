<?php

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlerTransfers24;

/**
 * Class Response.
 */
abstract class Response
{
    /**
     * @var int
     */
    protected $status_code;

    /**
     * @var string|null
     */
    protected $token = null;

    /**
     * @var string|null
     */
    protected $order_id = null;

    /**
     * @var string|null
     */
    protected $session_id = null;

    /**
     * @var array
     */
    protected $error_message = [];

    /**
     * @var array
     */
    protected $request_parameters = [];

    /**
     * @var array
     */
    protected $receive_parameters = [];

    public function __construct(array $request_params)
    {
        $this->request_parameters = $request_params;
    }

    /**
     * Get Code for payment.
     *
     * @return int
     */
    public function getCode()
    {
        return $this->status_code;
    }

    /**
     * Get Error description for payment.
     *
     * @return array
     */
    public function getErrorDescription()
    {
        return $this->error_message;
    }

    /**
     * Get Request parameters send to Transfers24.
     *
     * @return array
     */
    public function getRequestParameters()
    {
        return $this->request_parameters;
    }

    /**
     * Get Receive parameters send from Transfers24.
     *
     * @return array
     */
    public function getReceiveParameters()
    {
        return $this->receive_parameters;
    }

    /**
     * Get Transaction number received from transfers24.
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Get Session number of payment.
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->session_id;
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
     * @return int
     */
    public function getErrorCode()
    {
        return $this->getCode();
    }

    /**
     * @param int $status_code
     */
    public function setStatusCode(int $status_code): void
    {
        $this->status_code = $status_code;
    }

    /**
     * @param string|null $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /**
     * @param string|null $order_id
     */
    public function setOrderId(?string $order_id): void
    {
        $this->order_id = $order_id;
    }

    /**
     * @param string|null $session_id
     */
    public function setSessionId(?string $session_id): void
    {
        $this->session_id = $session_id;
    }

    /**
     * @param array $error_message
     */
    public function setErrorMessage(array $error_message): void
    {
        $this->error_message = $error_message;
    }

    /**
     * @param array $receive_parameters
     */
    public function setReceiveParameters(array $receive_parameters): void
    {
        $this->receive_parameters = $receive_parameters;
    }

}
