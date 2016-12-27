<?php

namespace Devpark\Transfers24\Responses;

use Devpark\Transfers24\Services\Handlers\Transfers24 as HandlerTransfers24;

/**
 * Class Response.
 */
abstract class Response
{
    /**
     * @var HandlerTransfers24
     */
    protected $transfers24;

    /**
     * Response constructor.
     *
     * @param HandlerTransfers24 $transfers24
     */
    public function __construct(HandlerTransfers24 $transfers24)
    {
        $this->transfers24 = $transfers24;
    }

    /**
     * Get status response from transfers24.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->transfers24->getCode() === '0';
    }

    /**
     * Get Error Code back from transfers24.
     *
     * @return int
     */
    public function getErrorCode()
    {
        return $this->transfers24->getCode();
    }

    /**
     * Get pairs field, error description.
     *
     * @return array
     */
    public function getErrorDescription()
    {
        return $this->transfers24->getErrorDescription();
    }

    /**
     * Get all parameters send with request.
     *
     * @return array
     */
    public function getRequestParameters()
    {
        return $this->transfers24->getRequestParameters();
    }

    /**
     * Get Session number of payment.
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->transfers24->getSessionId();
    }
}
